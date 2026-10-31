<?php

namespace App\Services;

use App\Models\BloodDonor;
use App\Models\DonorContactRequest;
use App\Models\User;

class BloodDonorService
{
    /**
     * Register or update a blood donor profile.
     */
    public function saveDonorProfile(array $data, ?User $user = null): BloodDonor
    {
        $donorData = [
            'user_id' => $user?->id,
            'full_name' => $data['full_name'],
            'blood_group' => $data['blood_group'],
            'phone' => $data['phone'],
            'alternative_phone' => $data['alternative_phone'] ?? null,
            'email' => $data['email'] ?? $user?->email,
            'district' => $data['district'] ?? 'Dhaka',
            'city' => $data['city'] ?? 'Dhaka',
            'area' => $data['area'] ?? null,
            'latitude' => $data['latitude'] ?? 23.7500,
            'longitude' => $data['longitude'] ?? 90.3800,
            'is_available' => !empty($data['is_available']),
            'last_donation_date' => $data['last_donation_date'] ?? null,
            'total_donations' => (int) ($data['total_donations'] ?? 0),
            'verification_status' => ($user && $user->isAdmin()) ? 'verified' : 'pending',
            'privacy_setting' => $data['privacy_setting'] ?? 'contact_request',
            'notes' => $data['notes'] ?? null,
        ];

        if ($user && $user->bloodDonorProfile) {
            $user->bloodDonorProfile->update($donorData);
            return $user->bloodDonorProfile;
        }

        return BloodDonor::create($donorData);
    }

    /**
     * Submit an emergency blood contact request.
     */
    public function createContactRequest(BloodDonor $donor, array $data, ?User $requester = null): DonorContactRequest
    {
        return DonorContactRequest::create([
            'blood_donor_id' => $donor->id,
            'user_id' => $requester?->id,
            'requester_name' => $data['requester_name'],
            'requester_phone' => $data['requester_phone'],
            'patient_condition' => $data['patient_condition'] ?? null,
            'hospital_name' => $data['hospital_name'],
            'hospital_location' => $data['hospital_location'],
            'urgency_level' => $data['urgency_level'] ?? 'urgent',
            'units_needed' => (int) ($data['units_needed'] ?? 1),
            'message' => $data['message'] ?? null,
            'status' => 'pending',
        ]);
    }
}
