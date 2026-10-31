<?php

namespace App\Services\Dispatch;

use App\Models\EmergencyDispatch;
use App\Models\EmergencyIncident;
use App\Models\Resource;
use App\Models\User;

class DispatchExecutionService
{
    public function __construct(
        private ResourceMatchingService $matchingService
    ) {}

    /**
     * Dispatch a specific matching resource to an incident (Human-in-the-loop Operator or System)
     */
    public function executeDispatch(
        EmergencyIncident $incident,
        Resource $resource,
        ?User $operator = null,
        array $matchData = []
    ): EmergencyDispatch {
        $oldStatus = $incident->status;

        $distanceKm = $matchData['distance_km'] ?? ($resource->distance ?? 5.0);
        $etaMinutes = $matchData['estimated_eta_minutes'] ?? 15.0;
        $trafficMultiplier = $matchData['traffic_multiplier'] ?? 1.0;

        $dispatch = EmergencyDispatch::create([
            'incident_id' => $incident->id,
            'resource_id' => $resource->id,
            'dispatched_by_user_id' => $operator?->id,
            'status' => 'PENDING',
            'match_score' => $matchData['total_score'] ?? 85.0,
            'match_rank' => $matchData['rank'] ?? 1,
            'match_explanation' => $matchData['match_explanation'] ?? ['Direct emergency dispatch assignment'],
            'estimated_distance_km' => $distanceKm,
            'estimated_eta_minutes' => $etaMinutes,
            'traffic_multiplier_applied' => $trafficMultiplier,
            'dispatched_at' => now(),
        ]);

        // Update Incident
        $incident->assigned_resource_id = $resource->id;
        if ($resource->category?->slug === 'hospital') {
            $incident->assigned_hospital_id = $resource->id;
        }
        $incident->status = 'DISPATCHED';
        $incident->dispatched_at = now();
        $incident->save();

        // Log Timeline Event
        $incident->logEvent(
            eventType: 'DISPATCHED',
            title: sprintf('Emergency Responder Dispatched: %s', $resource->name),
            description: sprintf(
                'Assigned %s. Estimated arrival in ~%.0f minutes (Distance: %.1f km). Phone: %s',
                $resource->category?->name ?? 'Service',
                $etaMinutes,
                $distanceKm,
                $resource->emergency_phone ?: $resource->phone
            ),
            metadata: [
                'dispatch_id' => $dispatch->id,
                'resource_id' => $resource->id,
                'match_score' => $dispatch->match_score,
                'eta_minutes' => $etaMinutes,
                'distance_km' => $distanceKm,
            ],
            actor: $operator,
            fromStatus: $oldStatus,
            toStatus: 'DISPATCHED'
        );

        return $dispatch;
    }

    /**
     * Auto-dispatch the highest ranked resource for an incident
     */
    public function autoDispatchTopMatch(EmergencyIncident $incident, ?User $operator = null): ?EmergencyDispatch
    {
        $matches = $this->matchingService->findMatches($incident, ['limit' => 1]);

        if ($matches->isEmpty()) {
            return null;
        }

        $topMatch = $matches->first();
        /** @var Resource $resource */
        $resource = $topMatch['resource'];

        return $this->executeDispatch($incident, $resource, $operator, $topMatch);
    }
}
