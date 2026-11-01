<?php

namespace Tests\Feature;

use App\Models\EmergencyIncident;
use App\Models\Resource;
use App\Services\Hospital\HospitalRecommendationService;
use Tests\TestCase;

class HospitalRecommendationTest extends TestCase
{
    public function test_hospital_recommendation_service_ranks_nearby_hospitals_with_icu_and_oxygen(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 98,
            'latitude' => 23.7500,
            'longitude' => 90.3800,
            'city' => 'Dhaka',
            'status' => 'PRIORITIZED',
        ]);

        /** @var HospitalRecommendationService $hospitalService */
        $hospitalService = app(HospitalRecommendationService::class);
        $hospitals = $hospitalService->getRecommendedHospitals($incident, radiusKm: 50.0, limit: 3);

        $this->assertNotEmpty($hospitals);
        $topHosp = $hospitals->first();
        $this->assertArrayHasKey('name', $topHosp);
        $this->assertArrayHasKey('distance_km', $topHosp);
        $this->assertArrayHasKey('estimated_eta_minutes', $topHosp);
        $this->assertArrayHasKey('recommendation_score', $topHosp);
        $this->assertArrayHasKey('has_icu', $topHosp);
        $this->assertGreaterThan(0, $topHosp['recommendation_score']);
    }

    public function test_pre_arrival_advisory_generates_bilingual_instructions_for_cardiac_emergency(): void
    {
        /** @var HospitalRecommendationService $hospitalService */
        $hospitalService = app(HospitalRecommendationService::class);
        $advisory = $hospitalService->getPreArrivalAdvisory('CARDIAC_STROKE');

        $this->assertArrayHasKey('critical_warning', $advisory);
        $this->assertArrayHasKey('en', $advisory);
        $this->assertArrayHasKey('bn', $advisory);
        $this->assertNotEmpty($advisory['en']);
        $this->assertNotEmpty($advisory['bn']);

        // Check for specific clinical first-aid keywords
        $enJoined = implode(' ', $advisory['en']);
        $bnJoined = implode(' ', $advisory['bn']);
        $this->assertStringContainsString('seated upright', $enJoined);
        $this->assertStringContainsString('শোয়াবেন না', $bnJoined);
    }

    public function test_pre_arrival_advisory_generates_burn_care_guidance_for_fire(): void
    {
        /** @var HospitalRecommendationService $hospitalService */
        $hospitalService = app(HospitalRecommendationService::class);
        $advisory = $hospitalService->getPreArrivalAdvisory('FIRE');

        $this->assertStringContainsString('ice', strtolower($advisory['critical_warning']));
        $enJoined = implode(' ', $advisory['en']);
        $this->assertStringContainsString('water', strtolower($enJoined));
    }

    public function test_emergency_tracking_page_displays_recommended_hospitals_and_pre_arrival_guidance(): void
    {
        $incident = EmergencyIncident::create([
            'emergency_type' => 'CARDIAC_STROKE',
            'severity_level' => 'P1',
            'priority_score' => 98,
            'latitude' => 23.7500,
            'longitude' => 90.3800,
            'city' => 'Dhaka',
            'status' => 'PRIORITIZED',
            'is_sos' => true,
        ]);

        $response = $this->get(route('emergency.show', $incident->public_reference));

        $response->assertStatus(200);
        $response->assertSee('Pre-Arrival Medical Advisory');
        $response->assertSee('Recommended Receiving Hospitals');
        $response->assertSee('Call Desk');
    }
}
