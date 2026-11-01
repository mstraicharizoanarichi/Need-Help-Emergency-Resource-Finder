<?php

namespace App\Services\Emergency;

use App\Models\EmergencyIncident;
use App\Models\EmergencyIncidentEvent;
use App\Models\Resource;
use App\Models\User;
use App\Services\Hospital\HospitalRecommendationService;
use App\Services\LocationSearchService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MassCasualtyProtocolService
{
    public function __construct(
        private LocationSearchService $locationSearchService,
        private HospitalRecommendationService $hospitalService
    ) {}

    /**
     * Declare an incident as a Mass Casualty Incident (MCI)
     * and activate multi-hospital surge protocol.
     */
    public function declareMci(
        EmergencyIncident $incident,
        array $data,
        ?User $operator = null
    ): EmergencyIncident {
        $victimsCount = (int) ($data['number_of_victims'] ?? max(5, $incident->number_of_victims));

        $mciLevel = $data['mci_level'] ?? match (true) {
            $victimsCount >= 50 => 'LEVEL_4',
            $victimsCount >= 26 => 'LEVEL_3',
            $victimsCount >= 11 => 'LEVEL_2',
            default => 'LEVEL_1',
        };

        $triageCounts = $data['triage_counts'] ?? [
            'red' => (int) round($victimsCount * 0.35),
            'yellow' => (int) round($victimsCount * 0.40),
            'green' => (int) round($victimsCount * 0.20),
            'black' => max(0, $victimsCount - (int) round($victimsCount * 0.95)),
        ];

        $red = $triageCounts['red'] ?? 0;
        $yellow = $triageCounts['yellow'] ?? 0;

        $hospitalAllocations = $this->calculateSurgeBedDistribution(
            $incident,
            $red,
            $yellow
        );

        $incident->update([
            'is_mci' => true,
            'mci_level' => $mciLevel,
            'number_of_victims' => $victimsCount,
            'mci_triage_counts' => $triageCounts,
            'mci_hospital_allocations' => $hospitalAllocations,
            'mci_declared_at' => Carbon::now(),
            'severity_level' => 'P1',
            'priority_score' => 100,
            'status' => 'PRIORITIZED',
        ]);

        EmergencyIncidentEvent::create([
            'incident_id' => $incident->id,
            'event_type' => 'MCI_DECLARED',
            'title' => sprintf('Mass Casualty Incident Declared (%s)', $mciLevel),
            'description' => sprintf(
                'CRITICAL ALERT: %s declared for %d victims (%d RED, %d YELLOW). Surge protocol activated across %d regional hospitals.',
                str_replace('_', ' ', $mciLevel),
                $victimsCount,
                $red,
                $yellow,
                count($hospitalAllocations)
            ),
            'actor_user_id' => $operator?->id,
            'payload' => [
                'mci_level' => $mciLevel,
                'number_of_victims' => $victimsCount,
                'triage_counts' => $triageCounts,
                'hospital_allocations' => $hospitalAllocations,
            ],
        ]);

        return $incident;
    }

    /**
     * Distribute casualties across multiple regional hospitals.
     */
    public function calculateSurgeBedDistribution(
        EmergencyIncident $incident,
        int $redVictims,
        int $yellowVictims
    ): array {
        $lat = $incident->latitude ?? 23.7808;
        $lng = $incident->longitude ?? 90.4152;

        $distanceSql = '(6371 * acos(
            cos(radians(?))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians(?))
            + sin(radians(?))
            * sin(radians(latitude))
        ))';

        $hospitals = Resource::query()
            ->whereHas('category', fn ($q) => $q->where('slug', 'hospital'))
            ->where('availability_status', '!=', 'unavailable')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('*')
            ->selectRaw("{$distanceSql} AS distance", [$lat, $lng, $lat])
            ->whereRaw("{$distanceSql} <= ?", [$lat, $lng, $lat, 40.0])
            ->orderBy('distance', 'asc')
            ->limit(6)
            ->get();

        if ($hospitals->isEmpty()) {
            $hospitals = Resource::query()
                ->whereHas('category', fn ($q) => $q->where('slug', 'hospital'))
                ->limit(4)
                ->get();
        }

        $allocations = [];
        $totalHospitals = max(1, $hospitals->count());

        $remainingRed = $redVictims;
        $remainingYellow = $yellowVictims;

        $isBurnIncident = in_array(
            $incident->emergency_type,
            ['FIRE', 'BURN'],
            true
        );

        foreach ($hospitals as $index => $hospital) {
            $isLast = ($index === $totalHospitals - 1);

            if ($isLast) {
                $assignedRed = $remainingRed;
                $assignedYellow = $remainingYellow;
            } else {
                $shareFraction = 1.0 / ($totalHospitals - $index);

                $assignedRed = min(
                    $remainingRed,
                    (int) ceil($remainingRed * $shareFraction)
                );

                $assignedYellow = min(
                    $remainingYellow,
                    (int) ceil($remainingYellow * $shareFraction)
                );
            }

            $remainingRed -= $assignedRed;
            $remainingYellow -= $assignedYellow;

            $allocations[] = [
                'hospital_id' => $hospital->id,
                'hospital_name' => $hospital->name,
                'hospital_phone' => $hospital->emergency_phone ?: $hospital->phone,
                'distance_km' => isset($hospital->distance)
                    ? round((float) $hospital->distance, 2)
                    : 5.0,
                'address' => $hospital->address ?: ($hospital->city . ', Bangladesh'),
                'allocated_red' => $assignedRed,
                'allocated_yellow' => $assignedYellow,
                'total_allocated' => $assignedRed + $assignedYellow,
                'icu_available' => (bool) $hospital->details?->icu_available,
            ];
        }

        return $allocations;
    }

    /**
     * Update on-scene dynamic casualty triage counts.
     */
    public function updateTriageCounts(
        EmergencyIncident $incident,
        array $triageCounts,
        ?User $operator = null
    ): EmergencyIncident {
        $incident->update([
            'mci_triage_counts' => $triageCounts,
            'number_of_victims' => array_sum($triageCounts),
        ]);

        EmergencyIncidentEvent::create([
            'incident_id' => $incident->id,
            'event_type' => 'MCI_TRIAGE_UPDATED',
            'title' => 'MCI On-Scene Triage Counts Updated',
            'description' => sprintf(
                'Updated casualty triage: %d RED (Immediate), %d YELLOW (Delayed), %d GREEN (Minor), %d BLACK (Deceased).',
                $triageCounts['red'] ?? 0,
                $triageCounts['yellow'] ?? 0,
                $triageCounts['green'] ?? 0,
                $triageCounts['black'] ?? 0
            ),
            'actor_user_id' => $operator?->id,
            'payload' => [
                'triage_counts' => $triageCounts,
            ],
        ]);

        return $incident;
    }
}