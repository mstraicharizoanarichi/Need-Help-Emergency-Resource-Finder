<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\Resource;
use App\Models\User;
use Tests\TestCase;

class AdminVerificationAndModerationTest extends TestCase
{
    public function test_admin_can_approve_pending_emergency_facility(): void
    {
        $admin = User::where('role', 'admin')->first();
        $pendingResource = Resource::where('verification_status', 'pending')->first();

        if (!$pendingResource) {
            $pendingResource = Resource::factory()->create(['verification_status' => 'pending']);
        }

        $response = $this->actingAs($admin)->patch("/admin/resources/{$pendingResource->id}/verify");

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', [
            'id' => $pendingResource->id,
            'verification_status' => 'verified',
        ]);
    }

    public function test_admin_can_reject_emergency_facility_with_reason(): void
    {
        $admin = User::where('role', 'admin')->first();
        $resource = Resource::first();

        $response = $this->actingAs($admin)->patch("/admin/resources/{$resource->id}/reject", [
            'rejection_reason' => 'Invalid hotline and unverified clinic address.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'verification_status' => 'rejected',
            'rejection_reason' => 'Invalid hotline and unverified clinic address.',
        ]);
    }

    public function test_citizen_can_submit_issue_report_and_admin_can_resolve(): void
    {
        $resource = Resource::first();
        $admin = User::where('role', 'admin')->first();

        // 1. Submit report
        $reportResponse = $this->post('/reports', [
            'resource_id' => $resource->id,
            'report_type' => 'incorrect_phone',
            'reporter_name' => 'Dr. Sadia',
            'reporter_phone' => '+8801755566677',
            'description' => 'The telephone number was out of order this morning.',
        ]);

        $reportResponse->assertRedirect();
        $reportResponse->assertSessionHas('success');

        $report = Report::where('resource_id', $resource->id)->latest()->first();
        $this->assertNotNull($report);

        // 2. Admin resolves report
        $resolveResponse = $this->actingAs($admin)->patch("/admin/reports/{$report->id}/resolve", [
            'status' => 'resolved',
            'admin_notes' => 'Verified hotline with emergency desk, operational now.',
        ]);

        $resolveResponse->assertRedirect();
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'resolved',
            'admin_notes' => 'Verified hotline with emergency desk, operational now.',
        ]);
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();

        $initialStatus = $user->status;

        $response = $this->actingAs($admin)->patch("/admin/users/{$user->id}/status");
        $response->assertRedirect();

        $user->refresh();
        $this->assertNotEquals($initialStatus, $user->status);
    }
}
