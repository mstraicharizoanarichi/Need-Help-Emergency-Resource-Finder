<?php

namespace App\Services\Dispatch;

use App\Models\EmergencyIncident;
use App\Models\Resource;
use App\Services\Analytics\ResponseTimePredictionService;
use App\Services\LocationSearchService;
use Illuminate\Support\Collection;

class ResourceMatchingService
{
    public function __construct(
        private LocationSearchService $locationSearchService,
        private TrafficEstimationService $trafficService,
        private ?ResponseTimePredictionService $predictionService = null
    ) {
        $this->predictionService = $this->predictionService ?? app(ResponseTimePredictionService::class);
    }

    /**
     * Find and rank matching emergency resources for an incident
     *
     * @param EmergencyIncident $incident
     * @param array $options [
     *   'category' => string|null,
     *   'radius_km' => float,
     *   'limit' => int,
     *   'verified_only' => bool,
     * ]
     * @return Collection<int, array>
     */
    public function findMatches(EmergencyIncident $incident, array $options = []): Collection
    {
        $radiusKm = (float) ($options['radius_km'] ?? 30.0);
        $limit = (int) ($options['limit'] ?? 6);
        $category = $options['category'] ?? $this->suggestCategoryForIncident($incident);
        $verifiedOnly = $options['verified_only'] ?? true;

        $lat = $incident->latitude ?? 23.7808; // Default to Dhaka center if null
        $lng = $incident->longitude ?? 90.4152;

        // Fetch candidate resources via Haversine location query
        $filters = [
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radiusKm,
            'category' => $category,
            'verified_only' => $verifiedOnly,
            'per_page' => 50,
        ];

        $paginated = $this->locationSearchService->searchResources($filters);
        $candidates = collect($paginated->items());

        // If no verified resources found within radius, fallback to all availability/pending
        if ($candidates->isEmpty() && $verifiedOnly) {
            $filters['verified_only'] = false;
            $paginated = $this->locationSearchService->searchResources($filters);
            $candidates = collect($paginated->items());
        }

        $ranked = $candidates->map(function (Resource $resource) use ($incident, $radiusKm) {
            $distanceKm = isset($resource->distance) ? (float) $resource->distance : 5.0;

            // Calculate Traffic and ETA
            $trafficResult = $this->trafficService->calculateETA(
                distanceKm: $distanceKm,
                city: $incident->city ?: $resource->city
            );

            // Multi-Factor Score Calculation
            $scoreData = $this->calculateScore($incident, $resource, $distanceKm, $radiusKm, $trafficResult);

            // ML/Heuristic Response Time Prediction
            $prediction = $this->predictionService->predict(
                distanceKm: $distanceKm,
                context: [
                    'city' => $incident->city ?: $resource->city,
                    'resource_type' => $resource->category?->slug ?? 'ambulance',
                ]
            );

            return [
                'resource' => $resource,
                'resource_id' => $resource->id,
                'name' => $resource->name,
                'category' => $resource->category?->name,
                'category_slug' => $resource->category?->slug,
                'phone' => $resource->emergency_phone ?: $resource->phone,
                'distance_km' => round($distanceKm, 2),
                'estimated_eta_minutes' => $prediction['predicted_eta_minutes'],
                'confidence_interval' => $prediction['confidence_interval'] ?? null,
                'congestion_risk' => $prediction['congestion_risk'] ?? 'LOW',
                'delay_risk' => $prediction['congestion_risk'] ?? 'LOW',
                'prediction' => $prediction,
                'traffic_multiplier' => $trafficResult['traffic_multiplier'],
                'traffic_condition' => $trafficResult['traffic_condition'],
                'total_score' => $scoreData['total_score'],
                'score_breakdown' => $scoreData['breakdown'],
                'match_explanation' => $scoreData['explanation'],
            ];
        });

        // Sort by total score descending, distance ascending
        $sorted = $ranked->sortBy([
            ['total_score', 'desc'],
            ['estimated_eta_minutes', 'asc'],
            ['distance_km', 'asc'],
        ])->values();

        // Assign Rank (1, 2, 3...)
        return $sorted->take($limit)->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });
    }

    /**
     * Calculate multi-factor match score (0 - 100)
     */
    private function calculateScore(
        EmergencyIncident $incident,
        Resource $resource,
        float $distanceKm,
        float $radiusKm,
        array $trafficResult
    ): array {
        $breakdown = [];
        $explanation = [];

        // 1. Distance Proximity (0 - 35 points)
        $distanceRatio = max(0, 1 - ($distanceKm / max(1.0, $radiusKm)));
        $distanceScore = round($distanceRatio * 35, 1);
        $breakdown['distance'] = $distanceScore;
        $explanation[] = sprintf('Distance %.1f km from incident (%.1f/35 pts)', $distanceKm, $distanceScore);

        // 2. Capability & Equipment Match (0 - 25 points)
        $capabilityScore = 0.0;
        $details = $resource->details;
        $reqEquipment = (array) ($incident->required_equipment ?? []);

        // ICU Match
        if ($this->hasRequirement($reqEquipment, ['icu', 'ventilator', 'critical'])) {
            if ($details && $details->icu_available) {
                $capabilityScore += 10.0;
                $explanation[] = 'ICU beds available for critical triage (+10 pts)';
            }
        } else {
            $capabilityScore += 5.0; // Standard case
        }

        // Oxygen Match
        if ($this->hasRequirement($reqEquipment, ['oxygen', 'respiratory', 'breathing'])) {
            if ($details && $details->oxygen_available) {
                $capabilityScore += 5.0;
                $explanation[] = 'Oxygen therapy equipment in readiness (+5 pts)';
            }
        } else {
            $capabilityScore += 3.0;
        }

        // Paramedic on board
        if ($details && $details->paramedic_onboard) {
            $capabilityScore += 5.0;
            $explanation[] = 'Certified emergency paramedic onboard vehicle (+5 pts)';
        }

        // Category relevance
        if ($this->matchesCategoryNeed($incident, $resource)) {
            $capabilityScore += 5.0;
            $explanation[] = 'Direct category requirement match (+5 pts)';
        }

        $capabilityScore = min(25.0, $capabilityScore);
        $breakdown['capability'] = $capabilityScore;

        // 3. Availability & Operating Readiness (0 - 20 points)
        $availabilityScore = 0.0;
        if ($resource->isAvailable()) {
            $availabilityScore += 14.0;
        }
        if ($resource->is_24_7 || $resource->isOpenNow()) {
            $availabilityScore += 6.0;
            $explanation[] = '24/7 operating availability verified (+6 pts)';
        } else {
            $explanation[] = 'Standard daytime shift operating hours';
        }
        $breakdown['availability'] = $availabilityScore;

        // 4. Verification & Reliability (0 - 10 points)
        $reliabilityScore = 0.0;
        if ($resource->isVerified()) {
            $reliabilityScore += 10.0;
            $explanation[] = 'Government/Admin verified resource credentials (+10 pts)';
        } else {
            $reliabilityScore += 4.0;
            $explanation[] = 'Community registered facility (+4 pts)';
        }
        $breakdown['reliability'] = $reliabilityScore;

        // 5. Traffic & ETA Feasibility (0 - 10 points)
        $eta = $trafficResult['eta_minutes'];
        $trafficScore = match (true) {
            $eta <= 10.0 => 10.0,
            $eta <= 20.0 => 8.0,
            $eta <= 35.0 => 5.0,
            default => 2.0,
        };
        $breakdown['traffic_feasibility'] = $trafficScore;
        $explanation[] = sprintf('ETA ~%.0f min with %s traffic multiplier %.2fx (+%.0f pts)', $eta, $trafficResult['traffic_condition'], $trafficResult['traffic_multiplier'], $trafficScore);

        $totalScore = round(min(100.0, array_sum($breakdown)), 1);

        return [
            'total_score' => $totalScore,
            'breakdown' => $breakdown,
            'explanation' => $explanation,
        ];
    }

    private function hasRequirement(array $requirements, array $terms): bool
    {
        foreach ($requirements as $req) {
            $reqLower = strtolower($req);
            foreach ($terms as $t) {
                if (str_contains($reqLower, $t)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function matchesCategoryNeed(EmergencyIncident $incident, Resource $resource): bool
    {
        $catSlug = $resource->category?->slug;
        $type = $incident->emergency_type;

        return match ($type) {
            'FIRE' => $catSlug === 'fire-service',
            'POLICE' => $catSlug === 'police-station',
            'ROAD_ACCIDENT', 'TRAUMA' => in_array($catSlug, ['ambulance', 'hospital'], true),
            'CARDIAC_STROKE' => in_array($catSlug, ['ambulance', 'hospital'], true),
            default => true,
        };
    }

    private function suggestCategoryForIncident(EmergencyIncident $incident): ?string
    {
        return match ($incident->emergency_type) {
            'FIRE' => 'fire-service',
            'POLICE' => 'police-station',
            'CARDIAC_STROKE', 'ROAD_ACCIDENT' => 'ambulance',
            default => null, // all categories
        };
    }
}
