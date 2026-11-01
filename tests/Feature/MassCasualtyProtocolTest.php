<?php

namespace Tests\Feature;

use App\Models\EmergencyIncident;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\User;
use App\Services\Emergency\MassCasualtyProtocolService;
use Tests\TestCase;

class MassCasualtyProtocolTest extends TestCase
{
    public function test_operator_can_declare_mci_and_activate_multi_hospital_surge_distribution(): void
    {
        $category = ResourceCategory::firstOrCreate(
            ['slug' => 'hospital'],
            ['name' => 'Hospital', 'icon' => 'hospital', 'is_active' => true]
        );

        $hosp1 = Resource::create([
            'category_id' => $category->id,
            'name' => 'Dhaka Medical College Hospital',
            'slug' => 'dmch-test-' . uniqid(),
            'phone' => '01700000001',
            'emergency_phone' => '01700000001',
            'address' => 'Secretariat Rd, Dhaka',
            'latitude' => 23.7250,
            'longitude' => 90.3970,
            'city' => 'Dhaka',
            'availability_status' => 'available',
        ]);

        $hosp2 = Resource::create([
            'category_id' => $category->id,
            'name' => 'Shaheed Suhrawardy Hospital',
            'slug' => 'ssmch-test-' . uniqid(),
            'phone' => '01700000002',
            'emergency_phone' => '01700000002',
            'address' => 'Sher-e-Bangla Nagar, Dhaka',
            'latitude' => 23.7690,
            'longitude' => 90.3710,
            'city' => 'Dhaka',
            'availability_status' => 'available',
        ]);

        $incident = EmergencyIncident::create([
            'emergency_type' => 'FIRE',
            'severity_level' => 'P1',
            'priority_score' => 90,
            'latitude' => 23.7400,
            'longitude' => 90.3800,
            'city' => 'Dhaka',
            'number_of_victims' => 2,
            'status' => 'REPORTED',
        ]);

        /** @var MassCasualtyProtocolService $mciService */
        $mciService = app(MassCasualtyProtocolService::class);
        $updated = $mciService->declareMci($incident, [
            'number_of_victims' => 15,
            'mci_level' => 'LEVEL_2',
            'triage_counts' => [
                'red' => 5,
                'yellow' => 6,
                'green' => 4,
                'black' => 0,
            ],
        ]);

        $this->assertTrue($updated->is_mci);
        $this->assertEquals('LEVEL_2', $updated->mci_level);
        $this->assertEquals(15, $updated->number_of_victims);
        $this->assertEquals(100, $updated->priority_score);
        $this->assertNotEmpty($updated->mci_hospital_allocations);

        // Verify allocations distribute casualties
        $totalRedAllocated = collect($updated->mci_hospital_allocations)->sum('allocated_red');
        $this->assertEquals(5, $totalRedAllocated);

        $this->assertDatabaseHas('emergency_incident_events', [
            'incident_id' => $incident->id,
            'event_type' => 'MCI_DECLARED',
        ]);
    }

    public function test_mci_level_scales_with_victim_count(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'ROAD_ACCIDENT',
            'latitude' => 23.7400,
            'longitude' => 90.3800,
            'city' => 'Dhaka',
            'status' => 'REPORTED',
        ]);

        /** @var MassCasualtyProtocolService $mciService */
        $mciService = app(MassCasualtyProtocolService::class);

        // >= 50 victims => LEVEL_4
        $majorDisaster = $mciService->declareMci($incident, ['number_of_victims' => 60]);
        $this->assertEquals('LEVEL_4', $majorDisaster->mci_level);
    }

    public function test_triage_counts_can_be_updated_dynamically_on_scene(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'BUILDING_COLLAPSE',
            'is_mci' => true,
            'mci_level' => 'LEVEL_2',
            'number_of_victims' => 12,
            'status' => 'PRIORITIZED',
        ]);

        /** @var MassCasualtyProtocolService $mciService */
        $mciService = app(MassCasualtyProtocolService::class);
        $mciService->updateTriageCounts($incident, [
            'red' => 4,
            'yellow' => 8,
            'green' => 6,
            'black' => 1,
        ]);

        $fresh = $incident->fresh();
        $this->assertEquals(19, $fresh->number_of_victims);
        $this->assertEquals(4, $fresh->mci_triage_counts['red']);
        $this->assertEquals(8, $fresh->mci_triage_counts['yellow']);

        $this->assertDatabaseHas('emergency_incident_events', [
            'incident_id' => $incident->id,
            'event_type' => 'MCI_TRIAGE_UPDATED',
        ]);
    }

    public function test_admin_endpoint_declares_mci_via_http(): void
    {
        $admin = User::create([
            'name' => 'EOC Commander',
            'email' => 'commander-' . uniqid() . '@needhelp.bd',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $incident = EmergencyIncident::create([
            'emergency_type' => 'FACTORY_FIRE',
            'severity_level' => 'P1',
            'priority_score' => 92,
            'latitude' => 23.7500,
            'longitude' => 90.3900,
            'city' => 'Dhaka',
            'status' => 'REPORTED',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.emergency.mci', $incident->id), [
            'number_of_victims' => 28,
            'mci_level' => 'LEVEL_3',
            'triage_counts' => [
                'red' => 8,
                'yellow' => 12,
                'green' => 8,
                'black' => 0,
            ],
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'success']);

        $fresh = $incident->fresh();
        $this->assertTrue($fresh->is_mci);
        $this->assertEquals('LEVEL_3', $fresh->mci_level);
        $this->assertEquals(28, $fresh->number_of_victims);
    }
}
