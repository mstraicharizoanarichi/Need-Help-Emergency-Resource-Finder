<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Http\Request;

class ResourcePublicController extends Controller
{
    public function show(string $slug, Request $request)
    {
        $resource = Resource::with(['category', 'details', 'operatingHours', 'doctors' => function ($q) {
            $q->active()->orderBy('is_emergency_available', 'desc')->orderBy('name');
        }])
            ->where('slug', $slug)
            ->firstOrFail();

        // Calculate distance if visitor coordinates provided
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if ($lat && $lng && is_numeric($lat) && is_numeric($lng)) {
            $lat1 = deg2rad((float)$lat);
            $lng1 = deg2rad((float)$lng);
            $lat2 = deg2rad($resource->latitude);
            $lng2 = deg2rad($resource->longitude);

            $distanceKm = 6371 * acos(
                max(-1.0, min(1.0, cos($lat1) * cos($lat2) * cos($lng2 - $lng1) + sin($lat1) * sin($lat2)))
            );
            $resource->distance = $distanceKm;
        }

        // Nearby same category resources
        $nearbyResources = Resource::with(['category'])
            ->where('category_id', $resource->category_id)
            ->where('id', '!=', $resource->id)
            ->verified()
            ->available()
            ->limit(4)
            ->get();

        $isFavorited = false;
        if ($request->user()) {
            $isFavorited = $request->user()->favorites()->where('resource_id', $resource->id)->exists();
        }

        $doctorSpecialties = $resource->doctors->pluck('specialization')->unique()->values();

        return view('resources.show', compact('resource', 'nearbyResources', 'isFavorited', 'doctorSpecialties'));
    }
}
