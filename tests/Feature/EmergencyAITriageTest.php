<?php

namespace Tests\Feature;

use App\Models\EmergencyIncident;
use App\Services\AI\DeterministicEmergencyParser;
use App\Services\AI\EmergencyIntakeService;
use Tests\TestCase;

class EmergencyAITriageTest extends TestCase
{
    public function test_deterministic_ai_parser_triages_cardiac_emergency_in_bangla(): void
    {
        $parser = new DeterministicEmergencyParser();
        $result = $parser->analyzeIncident('রোগীর তীব্র বুকে ব্যথা এবং শ্বাসকষ্ট হচ্ছে');

        $this->assertEquals('CARDIAC_STROKE', $result['emergency_type']);
        $this->assertEquals('P1', $result['severity_level']);
        $this->assertGreaterThanOrEqual(95, $result['priority_score']);
        $this->assertContains('ICU Bed', $result['required_equipment']);
        $this->assertContains('Oxygen Support', $result['required_equipment']);
        $this->assertEquals('bn', $result['language_detected']);
    }

    public function test_deterministic_ai_parser_triages_road_accident_in_english(): void
    {
        $parser = new DeterministicEmergencyParser();
        $result = $parser->analyzeIncident('Major road collision on highway, two victims unconscious with heavy bleeding');

        $this->assertEquals('ROAD_ACCIDENT', $result['emergency_type']);
        $this->assertEquals('P1', $result['severity_level']);
        $this->assertGreaterThanOrEqual(90, $result['priority_score']);
        $this->assertContains('Trauma Stretcher', $result['required_equipment']);
        $this->assertEquals('en', $result['language_detected']);
    }

    public function test_deterministic_ai_parser_triages_fire_emergency(): void
    {
        $parser = new DeterministicEmergencyParser();
        $result = $parser->analyzeIncident('ভবনের তৃতীয় তলায় গ্যাস সিলিন্ডার বিস্ফোরণে আগুন লেগেছে');

        $this->assertEquals('FIRE', $result['emergency_type']);
        $this->assertEquals('P1', $result['severity_level']);
        $this->assertContains('Burn Unit Bed', $result['required_equipment']);
    }

    public function test_deterministic_ai_parser_triages_low_urgency_inquiry(): void
    {
        $parser = new DeterministicEmergencyParser();
        $result = $parser->analyzeIncident('হালকা জ্বর ও কাশি দুই দিন ধরে');

        $this->assertEquals('P3', $result['severity_level']);
        $this->assertLessThanOrEqual(50, $result['priority_score']);
    }

    public function test_emergency_intake_controller_processes_report_and_creates_prioritized_incident(): void
    {
        $payload = [
            'description' => 'রোগীর বুকে প্রচণ্ড ব্যথা ও বাঁ হাত অবশ হয়ে যাচ্ছে, জরুরি ডাক্তার ও আইসিইউ অ্যাম্বুলেন্স প্রয়োজন',
            'latitude' => 23.7808,
            'longitude' => 90.4152,
            'address' => 'Gulshan 1, Dhaka',
            'reporter_name' => 'Kazi Farhan',
            'reporter_phone' => '01812345678',
            'number_of_victims' => 1,
        ];

        $response = $this->postJson(route('emergency.report.store'), $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'status',
            'reference',
            'tracking_url',
            'incident' => ['reference', 'severity', 'priority_score', 'required_service', 'status'],
        ]);

        $incident = EmergencyIncident::where('public_reference', $response->json('reference'))->first();
        $this->assertNotNull($incident);
        $this->assertEquals('CARDIAC_STROKE', $incident->emergency_type);
        $this->assertEquals('P1', $incident->severity_level);
        $this->assertEquals('PRIORITIZED', $incident->status);
        $this->assertGreaterThanOrEqual(95, $incident->priority_score);

        // Assert AI_TRIAGED event was logged
        $triageEvent = $incident->events()->where('event_type', 'AI_TRIAGED')->first();
        $this->assertNotNull($triageEvent);
        $this->assertStringContainsString('P1', $triageEvent->description);
    }

    public function test_preview_triage_api_returns_structured_metrics(): void
    {
        $response = $this->postJson(route('emergency.preview-triage'), [
            'text' => 'Head injury with fracture and bleeding after motorcycle crash',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'triage' => [
                'emergency_type' => 'ROAD_ACCIDENT',
                'severity_level' => 'P1',
            ],
        ]);
    }

    public function test_report_intake_page_is_publicly_accessible(): void
    {
        $response = $this->get(route('emergency.report'));

        $response->assertStatus(200);
        $response->assertSee('AI-Assisted Emergency Dispatch');
        $response->assertSee('999');
        $response->assertSee('Quick Symptom');
    }
}
