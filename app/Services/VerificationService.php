<?php

namespace App\Services;

use App\Models\BloodDonor;
use App\Models\Resource;
use App\Models\User;

class VerificationService
{
    /**
     * Verify a pending emergency resource.
     */
    public function verifyResource(Resource $resource, User $admin): bool
    {
        return $resource->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $admin->id,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject a resource with explanation.
     */
    public function rejectResource(Resource $resource, string $reason, User $admin): bool
    {
        return $resource->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $reason,
            'verified_by' => $admin->id,
        ]);
    }

    /**
     * Verify or reject a blood donor.
     */
    public function updateDonorVerification(BloodDonor $donor, string $status): bool
    {
        if (!in_array($status, ['verified', 'rejected', 'pending'])) {
            return false;
        }

        return $donor->update(['verification_status' => $status]);
    }

    /**
     * Approve or suspend a user/provider account.
     */
    public function updateUserStatus(User $user, ?string $status = null): bool
    {
        if ($status === null) {
            $status = ($user->status === 'active') ? 'suspended' : 'active';
        }

        if (!in_array($status, ['active', 'pending', 'suspended'])) {
            return false;
        }

        return $user->update(['status' => $status]);
    }
}
