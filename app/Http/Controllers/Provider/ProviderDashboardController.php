<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Resource;
use Illuminate\Http\Request;

class ProviderDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $resourcesQuery = Resource::where('user_id', $user->id);

        $stats = [
            'total_resources' => (clone $resourcesQuery)->count(),
            'verified_resources' => (clone $resourcesQuery)->where('verification_status', 'verified')->count(),
            'pending_resources' => (clone $resourcesQuery)->where('verification_status', 'pending')->count(),
            'rejected_resources' => (clone $resourcesQuery)->where('verification_status', 'rejected')->count(),
            'available_now' => (clone $resourcesQuery)->where('availability_status', 'available')->count(),
        ];

        $myResourceIds = Resource::where('user_id', $user->id)->pluck('id');
        $stats['total_reports'] = Report::whereIn('resource_id', $myResourceIds)->count();

        $resources = Resource::with(['category', 'details'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $recentResources = $resources;

        return view('provider.dashboard', compact('user', 'stats', 'resources', 'recentResources'));
    }
}
