<?php

namespace Tests\Feature;

use App\Models\EmergencyIncident;
use App\Models\User;
use Tests\TestCase;

class EmergencyIncidentTest extends TestCase
{
    public function test_citizen_can_trigger_one_tap_sos_and_incident_is_created_with_p1_priority(): void
    {
        $payload = [
            'latitude' => 23.7925,
            'longitude' => 90.4078,
            'address' => 'Road 11, Banani, Dhaka',
            'emergency_type' => 'MEDICAL_AMBULANCE',
            'reporter_name' => 'Tanvir Ahmed',
            'reporter_phone' => '01711000000',
            'description' => 'Acute chest pain and shortness of breath.',
        ];

        $response = $this->postJson(route('emergency.sos'), $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'message',
            'reference',
            'tracking_url',
            'incident' => ['reference', 'severity', 'status'],
        ]);

        $this->assertDatabaseHas('emergency_incidents', [
            'emergency_type' => 'MEDICAL_AMBULANCE',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'is_sos' => 1,
            'reporter_name' => 'Tanvir Ahmed',
            'reporter_phone' => '01711000000',
            'status' => 'REPORTED',
        ]);
    }

    public function test_sos_generates_unique_public_reference_in_format_nh_yyyy_xxxxxx(): void
    {
        $payload = [
            'latitude' => 23.7465,
            'longitude' => 90.3760,
            'emergency_type' => 'CARDIAC_STROKE',
        ];

        $response = $this->postJson(route('emergency.sos'), $payload);
        $response->assertStatus(201);

        $ref = $response->json('reference');
        $this->assertMatchesRegularExpression('/^NH-\d{4}-\d{6}$/', $ref);

        $incident = EmergencyIncident::where('public_reference', $ref)->first();
        $this->assertNotNull($incident);
        $this->assertTrue($incident->is_sos);
        $this->assertEquals('P1', $incident->severity_level);
    }

    public function test_sos_logs_created_event_in_timeline(): void
    {
        $response = $this->postJson(route('emergency.sos'), [
            'latitude' => 23.7500,
            'longitude' => 90.3800,
            'emergency_type' => 'FIRE',
        ]);

        $ref = $response->json('reference');
        $incident = EmergencyIncident::where('public_reference', $ref)->first();

        $this->assertNotEmpty($incident->events);
        $event = $incident->events->first();
        $this->assertEquals('CREATED', $event->event_type);
        $this->assertEquals('One-Tap Emergency SOS Triggered', $event->title);
    }

    public function test_citizen_can_view_emergency_tracking_timeline(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'MEDICAL_AMBULANCE',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'status' => 'REPORTED',
            'is_sos' => true,
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'address' => 'Gulshan 2, Dhaka',
        ]);

        $response = $this->get(route('emergency.show', $incident->public_reference));

        $response->assertStatus(200);
        $response->assertSee($incident->public_reference);
        $response->assertSee('999');
        $response->assertSee('One-Tap Emergency SOS');
    }

    public function test_polling_status_endpoint_returns_json_with_live_status(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'ROAD_ACCIDENT',
            'severity_level' => 'P1',
            'priority_score' => 90,
            'status' => 'ANALYZING',
            'is_sos' => true,
        ]);

        $response = $this->getJson(route('emergency.status', $incident->public_reference));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ANALYZING',
            'severity_level' => 'P1',
            'priority_score' => 90,
            'is_sos' => true,
        ]);
    }

    public function test_citizen_can_cancel_emergency_incident(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'GENERAL_SOS',
            'severity_level' => 'P1',
            'priority_score' => 95,
            'status' => 'REPORTED',
            'is_sos' => true,
        ]);

        $response = $this->postJson(route('emergency.cancel', $incident->public_reference), [
            'reason' => 'Accidental SOS trigger / False alarm',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $incident->refresh();
        $this->assertEquals('CANCELLED', $incident->status);
        $this->assertNotNull($incident->resolved_at);

        $cancelEvent = $incident->events()->where('event_type', 'CANCELLED')->first();
        $this->assertNotNull($cancelEvent);
    }
}
