<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodDonor;
use App\Models\Report;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_providers' => User::where('role', 'provider')->count(),
            'pending_providers' => User::where('role', 'provider')->where('status', 'pending')->count(),
            'total_resources' => Resource::count(),
            'verified_resources' => Resource::where('verification_status', 'verified')->count(),
            'pending_resources' => Resource::where('verification_status', 'pending')->count(),
            'pending_verification' => Resource::where('verification_status', 'pending')->count(),
            'rejected_resources' => Resource::where('verification_status', 'rejected')->count(),
            'total_donors' => BloodDonor::count(),
            'available_donors' => BloodDonor::where('is_available', true)->count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'resolved_reports' => Report::where('status', 'resolved')->count(),
        ];

        $categoryCounts = ResourceCategory::withCount('resources')
            ->orderBy('resources_count', 'desc')
            ->get();

        $categoryDistribution = $categoryCounts;

        $pendingResources = Resource::with(['category', 'user'])
            ->where('verification_status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $recentReports = Report::with(['resource', 'bloodDonor'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'categoryCounts', 'categoryDistribution', 'pendingResources', 'recentReports'));
    }
}
