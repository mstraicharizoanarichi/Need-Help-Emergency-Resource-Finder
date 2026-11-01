<?php

namespace Tests\Feature;

use App\Models\EmergencyIncident;
use App\Models\Resource;
use App\Models\User;
use Tests\TestCase;

class EmergencyCommandCenterTest extends TestCase
{
    public function test_admin_can_access_emergency_command_center(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        }

        $response = $this->actingAs($admin)->get(route('admin.emergency.index'));

        $response->assertStatus(200);
        $response->assertSee('Emergency Operations Command Center');
        $response->assertSee('Live Situational GIS Radar');
        $response->assertSee('Prioritized Emergency Queue');
    }

    public function test_non_admin_cannot_access_emergency_command_center(): void
    {
        $user = User::where('role', 'user')->first();
        if (!$user) {
            $user = User::factory()->create(['role' => 'user', 'status' => 'active']);
        }

        $response = $this->actingAs($user)->get(route('admin.emergency.index'));

        $response->assertStatus(403);
    }

    public function test_command_center_feed_endpoint_returns_json_with_active_incidents_and_kpis(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Create an active incident
        EmergencyIncident::create([
            'emergency_type' => 'ROAD_ACCIDENT',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'status' => 'PRIORITIZED',
            'latitude' => 23.7500,
            'longitude' => 90.3800,
            'address' => 'Farmgate, Dhaka',
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.emergency.feed'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'kpis' => [
                'critical_count',
                'awaiting_dispatch_count',
                'active_en_route_count',
                'resolved_today_count',
                'available_ambulances',
                'available_icu_hospitals',
            ],
            'incidents',
            'timestamp',
        ]);
    }

    public function test_command_center_recommendations_endpoint_returns_matching_ambulances_and_hospitals(): void
    {
        $admin = User::where('role', 'admin')->first();

        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 98,
            'status' => 'PRIORITIZED',
            'latitude' => 23.7500,
            'longitude' => 90.3800,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.emergency.recommendations', $incident));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'incident' => ['reference', 'emergency_type', 'severity_level', 'priority_score'],
            'recommended_ambulances',
            'recommended_hospitals',
        ]);
    }

    public function test_admin_operator_can_approve_and_dispatch_responder(): void
    {
        $admin = User::where('role', 'admin')->first();
        $resource = Resource::first();

        $incident = EmergencyIncident::create([
            'emergency_type' => 'MEDICAL_AMBULANCE',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'status' => 'AWAITING_DISPATCH',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.emergency.dispatch', $incident), [
            'resource_id' => $resource->id,
            'distance_km' => 4.2,
            'estimated_eta_minutes' => 12.0,
            'match_score' => 90.5,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $incident->refresh();
        $this->assertEquals('DISPATCHED', $incident->status);
        $this->assertEquals($resource->id, $incident->assigned_resource_id);

        $this->assertDatabaseHas('emergency_dispatches', [
            'incident_id' => $incident->id,
            'resource_id' => $resource->id,
            'dispatched_by_user_id' => $admin->id,
            'status' => 'PENDING',
        ]);
    }
}
