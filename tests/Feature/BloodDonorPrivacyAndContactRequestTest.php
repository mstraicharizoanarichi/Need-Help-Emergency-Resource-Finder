<?php

namespace Tests\Feature;

use App\Models\BloodDonor;
use Tests\TestCase;

class BloodDonorPrivacyAndContactRequestTest extends TestCase
{
    public function test_blood_donors_directory_is_publicly_accessible(): void
    {
        $response = $this->get('/blood-donors');

        $response->assertStatus(200);
        $response->assertSee('Emergency Blood Donor Network');
    }

    public function test_private_blood_donor_phone_is_masked_in_public_view(): void
    {
        $privateDonor = BloodDonor::where('privacy_setting', 'contact_request')->first();

        if ($privateDonor) {
            $response = $this->get('/blood-donors');
            $response->assertStatus(200);
            $response->assertSee('Request Emergency Contact');
            $response->assertDontSee("CALL: {$privateDonor->phone}");
        }
    }

    public function test_user_can_submit_emergency_contact_request(): void
    {
        $donor = BloodDonor::first();

        $response = $this->post('/blood-donors/request', [
            'blood_donor_id' => $donor->id,
            'requester_name' => 'Dr. Kamal Hossain',
            'requester_phone' => '+8801700112233',
            'patient_name' => 'Sultana Begum',
            'hospital_name' => 'Dhaka Medical College Hospital',
            'hospital_location' => 'Shahbagh, Dhaka',
            'units_needed' => 2,
            'urgency_level' => 'emergency',
            'message' => 'Urgent cardiac surgery at 3 PM today',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('donor_contact_requests', [
            'blood_donor_id' => $donor->id,
            'requester_name' => 'Dr. Kamal Hossain',
            'hospital_name' => 'Dhaka Medical College Hospital',
            'urgency_level' => 'emergency',
        ]);
    }
}
