<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Resource;
use App\Models\User;
use Tests\TestCase;

class DoctorDirectoryAndSearchTest extends TestCase
{
    public function test_public_doctor_directory_is_accessible(): void
    {
        $response = $this->get('/doctors');
        $response->assertStatus(200);
        $response->assertSee('Find Specialist Doctors');
    }

    public function test_can_filter_doctors_by_specialization(): void
    {
        $response = $this->get('/doctors?specialization=Cardiology');
        $response->assertStatus(200);
        $response->assertSee('Cardiology');
    }

    public function test_can_filter_emergency_on_duty_doctors(): void
    {
        $response = $this->get('/doctors?emergency=1');
        $response->assertStatus(200);
        $response->assertSee('Emergency');
    }

    public function test_doctor_profile_page_is_accessible_by_slug(): void
    {
        $doctor = Doctor::where('specialization', 'Cardiology')->first();
        $this->assertNotNull($doctor);

        $response = $this->get("/doctors/{$doctor->slug}");
        $response->assertStatus(200);
        $response->assertSee($doctor->name);
        $response->assertSee($doctor->qualifications);
        $response->assertSee('Visiting Schedule');
    }

    public function test_hospital_detail_page_shows_affiliated_doctors(): void
    {
        $hospital = Resource::whereHas('doctors')->first();
        $this->assertNotNull($hospital);

        $response = $this->get("/resources/{$hospital->slug}");
        $response->assertStatus(200);
        $response->assertSeeText('Doctors & Specialists Directory', false);
        
        $firstDoc = $hospital->doctors()->first();
        if ($firstDoc) {
            $response->assertSeeText($firstDoc->name, false);
        }
    }

    public function test_provider_can_manage_doctors_for_their_facility(): void
    {
        $provider = User::where('role', 'provider')->first();
        $this->assertNotNull($provider);

        $hospital = Resource::where('user_id', $provider->id)->first();
        $this->assertNotNull($hospital);

        // Provider index
        $indexResponse = $this->actingAs($provider)->get('/provider/doctors');
        $indexResponse->assertStatus(200);

        // Provider create doctor
        $createResponse = $this->actingAs($provider)->post('/provider/doctors', [
            'resource_id' => $hospital->id,
            'name' => 'Dr. Test Specialist',
            'specialization' => 'Cardiology',
            'department' => 'Cardiology Dept',
            'designation' => 'Associate Consultant',
            'qualifications' => 'MBBS, FCPS, MD',
            'experience_years' => 10,
            'visiting_days' => ['Sun', 'Mon', 'Tue', 'Wed'],
            'visiting_hours' => '06:00 PM - 09:00 PM',
            'appointment_phone' => '+8801700112233',
            'consultation_fee' => 1200,
            'is_emergency_available' => 1,
            'room_number' => 'Room 205',
        ]);

        $createResponse->assertRedirect('/provider/doctors');
        $this->assertDatabaseHas('doctors', [
            'name' => 'Dr. Test Specialist',
            'resource_id' => $hospital->id,
            'is_emergency_available' => 1,
        ]);

        $doctor = Doctor::where('name', 'Dr. Test Specialist')->first();

        // Toggle emergency status
        $toggleResponse = $this->actingAs($provider)->post("/provider/doctors/{$doctor->id}/emergency");
        $toggleResponse->assertRedirect();

        $doctor->refresh();
        $this->assertFalse($doctor->is_emergency_available);
    }
}
