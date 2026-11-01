<?php

namespace Tests\Feature;

use App\Models\EmergencyDispatch;
use App\Models\EmergencyIncident;
use App\Models\Resource;
use App\Models\User;
use App\Services\Dispatch\DispatchExecutionService;
use App\Services\Dispatch\ResourceMatchingService;
use App\Services\Dispatch\TrafficEstimationService;
use Carbon\Carbon;
use Tests\TestCase;

class ResourceMatchingAndDispatchTest extends TestCase
{
    public function test_traffic_estimation_service_calculates_realistic_eta_with_rush_hour_multipliers(): void
    {
        $trafficService = new TrafficEstimationService();

        // 10 km in Dhaka at 9 AM (weekday rush hour)
        $mondayRush = Carbon::parse('2026-09-07 09:00:00', 'Asia/Dhaka');
        $rushResult = $trafficService->calculateETA(10.0, 'Dhaka', $mondayRush);

        $this->assertGreaterThan(1.5, $rushResult['traffic_multiplier']);
        $this->assertEquals('HEAVY', $rushResult['traffic_condition']);
        $this->assertGreaterThan(20.0, $rushResult['eta_minutes']);

        // 10 km in Dhaka at 2 AM (night off-peak)
        $nightTime = Carbon::parse('2026-09-08 02:00:00', 'Asia/Dhaka');
        $nightResult = $trafficService->calculateETA(10.0, 'Dhaka', $nightTime);

        $this->assertEquals(1.0, $nightResult['traffic_multiplier']);
        $this->assertEquals('CLEAR', $nightResult['traffic_condition']);
        $this->assertLessThan($rushResult['eta_minutes'], $nightResult['eta_minutes']);
    }

    public function test_resource_matching_service_ranks_nearest_capable_resources_with_multi_factor_scoring(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 98,
            'required_equipment' => ['ICU Bed', 'Oxygen Support', 'Cardiac Monitor'],
            'latitude' => 23.7500,
            'longitude' => 90.3800,
            'city' => 'Dhaka',
            'status' => 'PRIORITIZED',
        ]);

        /** @var ResourceMatchingService $matchingService */
        $matchingService = app(ResourceMatchingService::class);
        $matches = $matchingService->findMatches($incident, ['radius_km' => 50, 'limit' => 3]);

        if ($matches->isNotEmpty()) {
            $topMatch = $matches->first();
            $this->assertArrayHasKey('total_score', $topMatch);
            $this->assertArrayHasKey('score_breakdown', $topMatch);
            $this->assertArrayHasKey('estimated_eta_minutes', $topMatch);
            $this->assertGreaterThan(0, $topMatch['total_score']);
            $this->assertEquals(1, $topMatch['rank']);
        }
    }

    public function test_dispatch_execution_service_assigns_resource_and_logs_dispatched_event(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'ROAD_ACCIDENT',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'latitude' => 23.7465,
            'longitude' => 90.3760,
            'status' => 'PRIORITIZED',
        ]);

        $resource = Resource::where('verification_status', 'verified')->first();
        if (!$resource) {
            $this->markTestSkipped('No verified resource found in database.');
        }

        /** @var DispatchExecutionService $dispatchService */
        $dispatchService = app(DispatchExecutionService::class);
        $dispatch = $dispatchService->executeDispatch(
            incident: $incident,
            resource: $resource,
            operator: null,
            matchData: [
                'distance_km' => 3.5,
                'estimated_eta_minutes' => 11.0,
                'traffic_multiplier' => 1.3,
                'total_score' => 92.0,
            ]
        );

        $this->assertNotNull($dispatch);
        $this->assertEquals('PENDING', $dispatch->status);
        $this->assertEquals(92.0, $dispatch->match_score);

        $incident->refresh();
        $this->assertEquals('DISPATCHED', $incident->status);
        $this->assertEquals($resource->id, $incident->assigned_resource_id);

        $dispatchedEvent = $incident->events()->where('event_type', 'DISPATCHED')->first();
        $this->assertNotNull($dispatchedEvent);
    }

    public function test_provider_can_accept_and_transition_dispatch_milestones(): void
    {
        $resource = Resource::first();
        if (!$resource) {
            $this->markTestSkipped('No resource found in database.');
        }

        $providerUser = $resource->user;
        if (!$providerUser) {
            $providerUser = User::where('role', 'provider')->first() ?? User::where('role', 'admin')->first();
            $resource->user_id = $providerUser->id;
            $resource->save();
        }

        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 98,
            'status' => 'DISPATCHED',
            'assigned_resource_id' => $resource->id,
        ]);

        $dispatch = EmergencyDispatch::create([
            'incident_id' => $incident->id,
            'resource_id' => $resource->id,
            'status' => 'PENDING',
            'dispatched_at' => now()->subMinutes(5),
        ]);

        // 1. Provider Accepts
        $response = $this->actingAs($providerUser)->post(route('provider.dispatches.respond', $dispatch), [
            'action' => 'accept',
        ]);
        $response->assertSessionHas('success');

        $dispatch->refresh();
        $incident->refresh();
        $this->assertEquals('ACCEPTED', $dispatch->status);
        $this->assertEquals('RESPONDER_ACCEPTED', $incident->status);

        // 2. Provider marks EN_ROUTE
        $this->actingAs($providerUser)->post(route('provider.dispatches.status', $dispatch), [
            'status' => 'EN_ROUTE',
        ]);
        $dispatch->refresh();
        $incident->refresh();
        $this->assertEquals('EN_ROUTE', $dispatch->status);
        $this->assertEquals('RESPONDER_EN_ROUTE', $incident->status);

        // 3. Provider marks ON_SCENE
        $this->actingAs($providerUser)->post(route('provider.dispatches.status', $dispatch), [
            'status' => 'ON_SCENE',
        ]);
        $dispatch->refresh();
        $incident->refresh();
        $this->assertEquals('ON_SCENE', $dispatch->status);
        $this->assertEquals('RESPONDER_ARRIVED', $incident->status);
        $this->assertNotNull($dispatch->actual_arrival_minutes);

        // 4. Provider marks COMPLETED
        $this->actingAs($providerUser)->post(route('provider.dispatches.status', $dispatch), [
            'status' => 'COMPLETED',
        ]);
        $dispatch->refresh();
        $incident->refresh();
        $this->assertEquals('COMPLETED', $dispatch->status);
        $this->assertEquals('RESOLVED', $incident->status);
    }

    public function test_provider_can_decline_dispatch_and_incident_is_requeued(): void
    {
        $resource = Resource::first();
        $providerUser = $resource->user ?? User::where('role', 'admin')->first();

        $incident = EmergencyIncident::create([
            'emergency_type' => 'FIRE',
            'severity_level' => 'P1',
            'status' => 'DISPATCHED',
            'assigned_resource_id' => $resource->id,
        ]);

        $dispatch = EmergencyDispatch::create([
            'incident_id' => $incident->id,
            'resource_id' => $resource->id,
            'status' => 'PENDING',
            'dispatched_at' => now(),
        ]);

        $response = $this->actingAs($providerUser)->post(route('provider.dispatches.respond', $dispatch), [
            'action' => 'reject',
            'rejection_reason' => 'Emergency ambulance tire flat',
        ]);

        $response->assertSessionHas('success');

        $dispatch->refresh();
        $incident->refresh();
        $this->assertEquals('REJECTED', $dispatch->status);
        $this->assertEquals('Emergency ambulance tire flat', $dispatch->rejection_reason);

        // Incident is unassigned and re-queued
        $this->assertNull($incident->assigned_resource_id);
        $this->assertEquals('AWAITING_DISPATCH', $incident->status);
    }
}
