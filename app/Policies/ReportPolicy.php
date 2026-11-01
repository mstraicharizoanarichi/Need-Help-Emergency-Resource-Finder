<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProvider() || $user->isUser();
    }

    public function view(User $user, Report $report): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isProvider() && $report->resource && $report->resource->user_id === $user->id) {
            return true;
        }

        return $report->reporter_user_id === $user->id;
    }

    public function create(?User $user): bool
    {
        return true; // Any visitor or user can report bad information
    }

    public function resolve(User $user): bool
    {
        return $user->isAdmin();
    }
}
