<?php

namespace Tests\Feature;

use App\Models\CfrAlert;
use App\Models\CommunityFirstResponder;
use App\Models\EmergencyIncident;
use App\Models\User;
use App\Services\Emergency\CommunityResponderService;
use Tests\TestCase;

class CommunityFirstResponderTest extends TestCase
{
    public function test_volunteer_can_register_as_community_first_responder(): void
    {
        $response = $this->post(route('community.responder.store'), [
            'name' => 'Dr. Fahim Ahmed',
            'phone' => '01711223344',
            'email' => 'fahim@emergency.bd',
            'skills' => ['CPR', 'AED', 'BLEEDING_CONTROL'],
            'certification_level' => 'DOCTOR',
            'certification_details' => 'BMDC Reg A-84210',
            'city' => 'Dhaka',
            'area' => 'Dhanmondi',
            'latitude' => 23.7465,
            'longitude' => 90.3760,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('community_first_responders', [
            'name' => 'Dr. Fahim Ahmed',
            'phone' => '01711223344',
            'certification_level' => 'DOCTOR',
            'city' => 'Dhaka',
            'area' => 'Dhanmondi',
            'status' => 'AVAILABLE',
        ]);
    }

    public function test_nearby_community_responders_can_be_found_by_incident_proximity(): void
    {
        // Responders in Dhanmondi, Dhaka
        $cfrClose = CommunityFirstResponder::create([
            'name' => 'Volunteer Near',
            'phone' => '01811111111',
            'skills' => ['CPR', 'FIRST_AID'],
            'certification_level' => 'CPR_AED_CERTIFIED',
            'is_available' => true,
            'is_verified' => true,
            'status' => 'AVAILABLE',
            'latitude' => 23.7460,
            'longitude' => 90.3750,
            'city' => 'Dhaka',
            'area' => 'Dhanmondi',
        ]);

        // Incident in Dhanmondi (~100 meters away)
        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 98,
            'latitude' => 23.7465,
            'longitude' => 90.3760,
            'city' => 'Dhaka',
            'status' => 'PRIORITIZED',
        ]);

        /** @var CommunityResponderService $service */
        $service = app(CommunityResponderService::class);
        $matches = $service->findNearbyResponders($incident, radiusKm: 3.0);

        $this->assertNotEmpty($matches);
        $this->assertEquals($cfrClose->id, $matches->first()['responder']->id);
        $this->assertLessThan(1.0, $matches->first()['distance_km']);
    }

    public function test_system_can_alert_nearby_responders_and_log_event(): void
    {
        $cfr = CommunityFirstResponder::create([
            'name' => 'Red Crescent Volunteer',
            'phone' => '01922222222',
            'skills' => ['BLEEDING_CONTROL', 'FIRST_AID'],
            'certification_level' => 'RED_CRESCENT_VOLUNTEER',
            'is_available' => true,
            'is_verified' => true,
            'status' => 'AVAILABLE',
            'latitude' => 23.7500,
            'longitude' => 90.3800,
            'city' => 'Dhaka',
        ]);

        $incident = EmergencyIncident::create([
            'emergency_type' => 'TRAUMA',
            'severity_level' => 'P1',
            'priority_score' => 90,
            'latitude' => 23.7510,
            'longitude' => 90.3810,
            'city' => 'Dhaka',
            'status' => 'REPORTED',
        ]);

        /** @var CommunityResponderService $service */
        $service = app(CommunityResponderService::class);
        $alerts = $service->alertNearbyResponders($incident, radiusKm: 2.0);

        $this->assertNotEmpty($alerts);
        $this->assertDatabaseHas('cfr_alerts', [
            'emergency_incident_id' => $incident->id,
            'community_first_responder_id' => $cfr->id,
            'status' => 'ALERTED',
        ]);

        $this->assertDatabaseHas('emergency_incident_events', [
            'incident_id' => $incident->id,
            'event_type' => 'CFR_ALERTED',
        ]);
    }

    public function test_first_responder_can_accept_alert_and_transition_to_responding(): void
    {
        $cfr = CommunityFirstResponder::create([
            'name' => 'Medical Student Volunteer',
            'phone' => '01633333333',
            'skills' => ['CPR', 'FIRST_AID'],
            'certification_level' => 'MEDICAL_STUDENT',
            'is_available' => true,
            'status' => 'AVAILABLE',
            'latitude' => 23.7400,
            'longitude' => 90.3700,
            'city' => 'Dhaka',
        ]);

        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'latitude' => 23.7410,
            'longitude' => 90.3710,
            'city' => 'Dhaka',
            'status' => 'PRIORITIZED',
        ]);

        $alert = CfrAlert::create([
            'emergency_incident_id' => $incident->id,
            'community_first_responder_id' => $cfr->id,
            'status' => 'ALERTED',
            'distance_km' => 0.4,
            'alerted_at' => now(),
        ]);

        $response = $this->postJson(route('community.responder.alerts.respond', $alert->id), [
            'decision' => 'ACCEPT',
            'notes' => 'Rushing to victim with portable first aid kit on foot.',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true, 'status' => 'ACCEPTED']);

        $this->assertEquals('ACCEPTED', $alert->fresh()->status);
        $this->assertEquals('RESPONDING', $cfr->fresh()->status);

        $this->assertDatabaseHas('emergency_incident_events', [
            'incident_id' => $incident->id,
            'event_type' => 'CFR_ACCEPTED',
        ]);
    }

    public function test_first_responder_can_mark_arrived_on_scene(): void
    {
        $cfr = CommunityFirstResponder::create([
            'name' => 'On Scene Hero',
            'phone' => '01544444444',
            'skills' => ['CPR', 'AED'],
            'certification_level' => 'NURSE',
            'status' => 'RESPONDING',
            'total_responses' => 2,
        ]);

        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'status' => 'DISPATCHED',
        ]);

        $alert = CfrAlert::create([
            'emergency_incident_id' => $incident->id,
            'community_first_responder_id' => $cfr->id,
            'status' => 'ACCEPTED',
            'alerted_at' => now()->subMinutes(5),
            'responded_at' => now()->subMinutes(4),
        ]);

        $response = $this->postJson(route('community.responder.alerts.arrived', $alert->id));

        $response->assertOk();
        $response->assertJson(['success' => true, 'status' => 'ON_SCENE']);

        $this->assertEquals('ON_SCENE', $alert->fresh()->status);
        $this->assertEquals('ON_SCENE', $cfr->fresh()->status);
        $this->assertEquals(3, $cfr->fresh()->total_responses);

        $this->assertDatabaseHas('emergency_incident_events', [
            'incident_id' => $incident->id,
            'event_type' => 'CFR_ON_SCENE',
        ]);
    }

    public function test_public_incident_status_includes_active_cfr(): void
    {
        $cfr = CommunityFirstResponder::create([
            'name' => 'Active CFR Bystander',
            'phone' => '01755555555',
            'skills' => ['CPR', 'BLEEDING_CONTROL'],
            'certification_level' => 'CPR_AED_CERTIFIED',
            'status' => 'ON_SCENE',
        ]);

        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'status' => 'DISPATCHED',
        ]);

        CfrAlert::create([
            'emergency_incident_id' => $incident->id,
            'community_first_responder_id' => $cfr->id,
            'status' => 'ON_SCENE',
            'distance_km' => 0.3,
            'alerted_at' => now()->subMinutes(5),
        ]);

        $response = $this->getJson(route('emergency.status', $incident->public_reference));

        $response->assertOk();
        $response->assertJsonPath('active_cfr.name', 'Active CFR Bystander');
        $response->assertJsonPath('active_cfr.status', 'ON_SCENE');
    }
}
