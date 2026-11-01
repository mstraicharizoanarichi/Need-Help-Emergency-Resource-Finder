<?php

namespace App\Services\Emergency;

use App\Models\CfrAlert;
use App\Models\CommunityFirstResponder;
use App\Models\EmergencyIncident;
use App\Models\EmergencyIncidentEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CommunityResponderService
{
    /**
     * Find available community first responders near the incident location
     */
    public function findNearbyResponders(
        EmergencyIncident $incident,
        float $radiusKm = 3.0,
        int $limit = 5
    ): Collection {
        $lat = $incident->latitude ?? 23.7808; // Default to Dhaka center
        $lng = $incident->longitude ?? 90.4152;

        $responders = CommunityFirstResponder::query()
            ->available()
            ->nearby($lat, $lng, $radiusKm)
            ->limit($limit * 2)
            ->get();

        // If incident requires specific skills (e.g. CPR for Cardiac, Bleeding Control for Trauma)
        $prioritizedSkills = match ($incident->emergency_type) {
            'CARDIAC_STROKE' => ['CPR', 'AED'],
            'ROAD_ACCIDENT', 'TRAUMA' => ['BLEEDING_CONTROL', 'FIRST_AID'],
            'BURN' => ['BURNS', 'FIRST_AID'],
            default => ['FIRST_AID'],
        };

        // Score/rank responders by skill match and proximity
        return $responders->map(function (CommunityFirstResponder $responder) use ($prioritizedSkills) {
            $skills = (array) ($responder->skills ?? []);
            $matchingSkills = array_intersect($skills, $prioritizedSkills);
            $hasPrioritySkill = !empty($matchingSkills);

            // Estimated arrival time on foot / bicycle in congested city (approx 12 km/h = 5 min per km)
            $dist = (float) ($responder->distance ?? 1.0);
            $etaMinutes = max(1.5, round($dist * 5.0, 1));

            return [
                'responder' => $responder,
                'distance_km' => round($dist, 2),
                'estimated_eta_minutes' => $etaMinutes,
                'has_priority_skill' => $hasPrioritySkill,
                'matched_skills' => $matchingSkills,
                'certification' => $responder->certification_level,
            ];
        })->sortBy([
            ['has_priority_skill', 'desc'],
            ['distance_km', 'asc'],
        ])->take($limit)->values();
    }

    /**
     * Mobilize and send emergency alerts to nearby Community First Responders
     */
    public function alertNearbyResponders(
        EmergencyIncident $incident,
        float $radiusKm = 3.0,
        int $maxAlerts = 3
    ): Collection {
        $nearby = $this->findNearbyResponders($incident, $radiusKm, $maxAlerts);
        $alerts = collect();

        foreach ($nearby as $candidate) {
            /** @var CommunityFirstResponder $responder */
            $responder = $candidate['responder'];

            // Prevent duplicate active alerts for the same incident
            $existing = CfrAlert::where('emergency_incident_id', $incident->id)
                ->where('community_first_responder_id', $responder->id)
                ->first();

            if ($existing) {
                $alerts->push($existing);
                continue;
            }

            $alert = CfrAlert::create([
                'emergency_incident_id' => $incident->id,
                'community_first_responder_id' => $responder->id,
                'status' => 'ALERTED',
                'distance_km' => $candidate['distance_km'],
                'alerted_at' => Carbon::now(),
            ]);

            $alerts->push($alert);
        }

        if ($alerts->isNotEmpty()) {
            EmergencyIncidentEvent::create([
                'incident_id' => $incident->id,
                'event_type' => 'CFR_ALERTED',
                'title' => 'Community First Responders Mobilized',
                'description' => sprintf(
                    'AI dispatch alerted %d nearby certified community responders within %.1f km for immediate bystander CPR / first aid intervention.',
                    $alerts->count(),
                    $radiusKm
                ),
                'payload' => [
                    'alerted_count' => $alerts->count(),
                    'responder_ids' => $alerts->pluck('community_first_responder_id')->all(),
                ],
            ]);
        }

        return $alerts;
    }

    /**
     * Accept an emergency CFR alert and pledge rapid response
     */
    public function acceptAlert(CfrAlert $alert, ?string $notes = null): bool
    {
        $alert->update([
            'status' => 'ACCEPTED',
            'responded_at' => Carbon::now(),
            'notes' => $notes,
        ]);

        $responder = $alert->responder;
        if ($responder) {
            $responder->update([
                'status' => 'RESPONDING',
                'last_active_at' => Carbon::now(),
            ]);
        }

        $incident = $alert->incident;
        if ($incident) {
            $skillsList = implode(', ', (array) ($responder?->skills ?? ['First Aid']));
            EmergencyIncidentEvent::create([
                'incident_id' => $incident->id,
                'event_type' => 'CFR_ACCEPTED',
                'title' => 'Community First Responder En Route',
                'description' => sprintf(
                    'Volunteer responder %s (%s) accepted the alert and is rushing to the victim. Equipped skills: %s.',
                    $responder?->name ?? 'Community Responder',
                    $responder?->certification_level ?? 'Certified',
                    $skillsList
                ),
                'payload' => [
                    'responder_id' => $responder?->id,
                    'responder_name' => $responder?->name,
                    'distance_km' => $alert->distance_km,
                ],
            ]);
        }

        return true;
    }

    /**
     * Mark CFR as arrived on scene to initiate life support before ambulance
     */
    public function markArrivedOnScene(CfrAlert $alert): bool
    {
        $alert->update([
            'status' => 'ON_SCENE',
            'arrived_at' => Carbon::now(),
        ]);

        $responder = $alert->responder;
        if ($responder) {
            $responder->update([
                'status' => 'ON_SCENE',
                'total_responses' => ($responder->total_responses ?? 0) + 1,
            ]);
        }

        $incident = $alert->incident;
        if ($incident) {
            EmergencyIncidentEvent::create([
                'incident_id' => $incident->id,
                'event_type' => 'CFR_ON_SCENE',
                'title' => 'First Responder On Scene',
                'description' => sprintf(
                    'Community Responder %s is ON SCENE administering life support and stabilizing the victim prior to ambulance arrival.',
                    $responder?->name ?? 'Community Responder'
                ),
                'payload' => [
                    'responder_id' => $responder?->id,
                    'arrived_at' => Carbon::now()->toIso8601String(),
                ],
            ]);
        }

        return true;
    }

    /**
     * Register a new volunteer community first responder
     */
    public function registerResponder(array $data, ?User $user = null): CommunityFirstResponder
    {
        return CommunityFirstResponder::create([
            'user_id' => $user?->id ?? $data['user_id'] ?? null,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'skills' => $data['skills'] ?? ['CPR', 'FIRST_AID'],
            'certification_level' => $data['certification_level'] ?? 'BASIC_FIRST_AID',
            'certification_details' => $data['certification_details'] ?? null,
            'is_verified' => $data['is_verified'] ?? false,
            'is_available' => true,
            'status' => 'AVAILABLE',
            'latitude' => $data['latitude'] ?? 23.7808,
            'longitude' => $data['longitude'] ?? 90.4152,
            'city' => $data['city'] ?? 'Dhaka',
            'area' => $data['area'] ?? 'Dhanmondi',
            'last_active_at' => Carbon::now(),
        ]);
    }
}
