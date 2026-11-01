<?php

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = ResourceCategory::active()
            ->withCount(['resources' => function ($q) {
                $q->where('verification_status', 'verified');
            }])
            ->get();

        $featuredResources = Resource::with(['category', 'details'])
            ->verified()
            ->available()
            ->featured()
            ->limit(6)
            ->get();

        $stats = [
            'total_resources' => Resource::verified()->count(),
            'total_hospitals' => Resource::whereHas('category', fn($q) => $q->where('slug', 'hospital'))->verified()->count(),
            'total_ambulances' => Resource::whereHas('category', fn($q) => $q->where('slug', 'ambulance'))->verified()->count(),
            'total_donors' => BloodDonor::verified()->available()->count(),
            'services_24_7' => Resource::verified()->where('is_24_7', true)->count(),
        ];

        return view('home', compact('categories', 'featuredResources', 'stats'));
    }
}
