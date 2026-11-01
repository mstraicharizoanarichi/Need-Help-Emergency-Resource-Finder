<?php

namespace Tests\Feature;

use App\Models\EmergencyIncident;
use App\Models\User;
use App\Services\Analytics\EmergencyAnalyticsService;
use Tests\TestCase;

class EmergencyAnalyticsTest extends TestCase
{
    public function test_analytics_service_calculates_correct_summary_metrics(): void
    {
        // Create sample incidents
        EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'is_sos' => true,
            'is_mci' => false,
            'district' => 'Dhaka',
            'status' => 'RESOLVED',
            'reported_at' => now()->subMinutes(30),
            'dispatched_at' => now()->subMinutes(27),
            'arrived_at' => now()->subMinutes(15),
            'resolved_at' => now(),
        ]);

        EmergencyIncident::create([
            'emergency_type' => 'ROAD_ACCIDENT',
            'severity_level' => 'P2',
            'priority_score' => 75,
            'is_sos' => false,
            'is_mci' => true,
            'district' => 'Chittagong',
            'status' => 'PRIORITIZED',
            'reported_at' => now()->subMinutes(10),
            'dispatched_at' => now()->subMinutes(8),
        ]);

        /** @var EmergencyAnalyticsService $service */
        $service = app(EmergencyAnalyticsService::class);
        $summary = $service->getAnalyticsSummary(30);

        $this->assertArrayHasKey('total_incidents', $summary);
        $this->assertArrayHasKey('avg_dispatch_latency_minutes', $summary);
        $this->assertArrayHasKey('avg_response_time_minutes', $summary);
        $this->assertArrayHasKey('sla_compliance_rate', $summary);
        $this->assertArrayHasKey('by_type', $summary);
        $this->assertArrayHasKey('by_severity', $summary);
        $this->assertArrayHasKey('by_district', $summary);

        $this->assertGreaterThanOrEqual(2, $summary['total_incidents']);
        $this->assertGreaterThanOrEqual(1, $summary['sos_incidents']);
        $this->assertGreaterThanOrEqual(1, $summary['mci_incidents']);
    }

    public function test_analytics_service_extracts_heatmap_points(): void
    {
        EmergencyIncident::create([
            'emergency_type' => 'FIRE',
            'severity_level' => 'P1',
            'latitude' => 23.7800,
            'longitude' => 90.4100,
            'status' => 'REPORTED',
        ]);

        /** @var EmergencyAnalyticsService $service */
        $service = app(EmergencyAnalyticsService::class);
        $points = $service->getHeatmapPoints(30);

        $this->assertNotEmpty($points);
        $first = $points[0];
        $this->assertArrayHasKey('lat', $first);
        $this->assertArrayHasKey('lng', $first);
        $this->assertArrayHasKey('weight', $first);
        $this->assertArrayHasKey('reference', $first);
    }

    public function test_admin_can_access_emergency_analytics_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Analytics Admin',
            'email' => 'analytics-' . uniqid() . '@needhelp.bd',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.emergency.analytics', ['days' => 30]));

        $response->assertOk();
        $response->assertViewIs('admin.emergency.analytics');
        $response->assertViewHasAll(['summary', 'heatmapPoints', 'days']);
    }

    public function test_admin_can_retrieve_heatmap_json_api(): void
    {
        $admin = User::create([
            'name' => 'Heatmap Admin',
            'email' => 'heatmap-' . uniqid() . '@needhelp.bd',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.emergency.analytics.heatmap', ['days' => 30]));

        $response->assertOk();
        $response->assertJsonStructure([
            'status',
            'points',
            'count',
        ]);
    }

    public function test_non_admin_cannot_access_analytics_dashboard(): void
    {
        $user = User::create([
            'name' => 'Citizen User',
            'email' => 'citizen-' . uniqid() . '@needhelp.bd',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get(route('admin.emergency.analytics'));

        $response->assertForbidden();
    }
}
