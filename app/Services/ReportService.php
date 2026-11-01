<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;

class ReportService
{
    /**
     * Submit a user report on an emergency resource or blood donor.
     */
    public function createReport(array $data, ?User $user = null): Report
    {
        return Report::create([
            'resource_id' => $data['resource_id'] ?? null,
            'blood_donor_id' => $data['blood_donor_id'] ?? null,
            'reporter_user_id' => $user?->id,
            'reporter_name' => $data['reporter_name'] ?? ($user ? $user->name : 'Anonymous Reporter'),
            'reporter_email' => $data['reporter_email'] ?? $user?->email,
            'reporter_phone' => $data['reporter_phone'] ?? $user?->phone,
            'report_type' => $data['report_type'],
            'description' => $data['description'],
            'status' => 'pending',
        ]);
    }

    /**
     * Update report resolution status.
     */
    public function resolveReport(Report $report, string $status, ?string $adminNotes, User $admin): bool
    {
        if (!in_array($status, ['pending', 'reviewing', 'resolved', 'rejected'])) {
            return false;
        }

        return $report->update([
            'status' => $status,
            'admin_notes' => $adminNotes,
            'resolved_by' => $admin->id,
            'resolved_at' => in_array($status, ['resolved', 'rejected']) ? now() : null,
        ]);
    }
}
