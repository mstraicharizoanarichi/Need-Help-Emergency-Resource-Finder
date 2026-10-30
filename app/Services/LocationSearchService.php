<?php

namespace App\Services;

use App\Models\BloodDonor;
use App\Models\Resource;
use App\Models\SearchLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LocationSearchService
{
    /**
     * Search emergency resources with optional Haversine distance calculation and filtering.
     *
     * @param array $filters [
     *   'category' => slug|id,
     *   'lat' => float,
     *   'lng' => float,
     *   'radius' => float (in km),
     *   'query' => string,
     *   'availability' => string ('all', 'available', '24_7', 'open_now'),
     *   'verified_only' => bool,
     *   'city' => string,
     *   'district' => string,
     *   'per_page' => int,
     *   'user_id' => int|null,
     *   'ip' => string|null,
     * ]
     */
    public function searchResources(array $filters): LengthAwarePaginator
    {
        $lat = isset($filters['lat']) && is_numeric($filters['lat']) ? (float) $filters['lat'] : null;
        $lng = isset($filters['lng']) && is_numeric($filters['lng']) ? (float) $filters['lng'] : null;
        $radius = isset($filters['radius']) && is_numeric($filters['radius']) ? (float) $filters['radius'] : null;
        $categorySlugOrId = $filters['category'] ?? null;
        $searchQuery = trim($filters['query'] ?? '');
        $availability = $filters['availability'] ?? 'all';
        $verifiedOnly = $filters['verified_only'] ?? false;
        $perPage = (int) ($filters['per_page'] ?? 12);

        $query = Resource::with(['category', 'details', 'operatingHours'])
            ->whereNull('deleted_at');

        // By default, public search shows verified resources and provider active resources
        if ($verifiedOnly) {
            $query->where('verification_status', 'verified');
        } else {
            $query->whereIn('verification_status', ['verified', 'pending']);
        }

        // Filter by category
        if (!empty($categorySlugOrId) && $categorySlugOrId !== 'all') {
            if (is_numeric($categorySlugOrId)) {
                $query->where('category_id', (int) $categorySlugOrId);
            } else {
                $query->whereHas('category', function ($q) use ($categorySlugOrId) {
                    $q->where('slug', $categorySlugOrId);
                });
            }
        }

        // Filter by text search (name, area, address, city, description)
        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('area', 'like', "%{$searchQuery}%")
                    ->orWhere('address', 'like', "%{$searchQuery}%")
                    ->orWhere('city', 'like', "%{$searchQuery}%")
                    ->orWhere('district', 'like', "%{$searchQuery}%")
                    ->orWhere('phone', 'like', "%{$searchQuery}%")
                    ->orWhere('emergency_phone', 'like', "%{$searchQuery}%");
            });
        }

        // Availability filters
        if ($availability === 'available') {
            $query->where('availability_status', 'available');
        } elseif ($availability === '24_7') {
            $query->where('is_24_7', true);
        }

        // District or City filter
        if (!empty($filters['district'])) {
            $query->where('district', $filters['district']);
        }
        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        // Location & Haversine Distance
        $hasLocation = ($lat !== null && $lng !== null && $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180);

        if ($hasLocation) {
            // Apply bounding box pre-filter for index optimization if radius is specified
            if ($radius && $radius > 0) {
                $latDelta = $radius / 111.0;
                $lngDelta = $radius / (111.0 * max(0.1, cos(deg2rad($lat))));

                $query->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
                      ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);
            }

            // Haversine formula select in MySQL
            $haversineSql = "(6371 * acos(
                LEAST(1.0, GREATEST(-1.0, 
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))
                ))
            ))";

            $query->select('*')
                ->selectRaw("{$haversineSql} AS distance", [$lat, $lng, $lat]);

            if ($radius && $radius > 0) {
                $query->havingRaw("distance <= ?", [$radius]);
            }

            $query->orderBy('distance', 'asc');
        } else {
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('verification_status', 'desc')
                  ->orderBy('name', 'asc');
        }

        $results = $query->paginate($perPage)->withQueryString();

        // Log search query for analytics
        if (!empty($searchQuery) || $hasLocation || !empty($categorySlugOrId)) {
            $this->logSearch([
                'user_id' => $filters['user_id'] ?? null,
                'category_id' => is_numeric($categorySlugOrId) ? (int) $categorySlugOrId : null,
                'search_query' => $searchQuery ?: null,
                'latitude' => $lat,
                'longitude' => $lng,
                'radius_km' => $radius,
                'results_count' => $results->total(),
                'ip_address' => $filters['ip'] ?? null,
            ]);
        }

        return $results;
    }

    /**
     * Search blood donors with privacy-conscious approximate distance ranking.
     */
    public function searchBloodDonors(array $filters): LengthAwarePaginator
    {
        $bloodGroup = $filters['blood_group'] ?? null;
        $lat = isset($filters['lat']) && is_numeric($filters['lat']) ? (float) $filters['lat'] : null;
        $lng = isset($filters['lng']) && is_numeric($filters['lng']) ? (float) $filters['lng'] : null;
        $radius = isset($filters['radius']) && is_numeric($filters['radius']) ? (float) $filters['radius'] : null;
        $area = trim($filters['area'] ?? '');
        $availableOnly = !empty($filters['available_only']);
        $perPage = (int) ($filters['per_page'] ?? 12);

        $query = BloodDonor::whereNull('deleted_at');

        if (!empty($bloodGroup) && $bloodGroup !== 'all') {
            $query->where('blood_group', $bloodGroup);
        }

        if ($availableOnly) {
            $query->where('is_available', true);
        }

        if (!empty($area)) {
            $query->where(function ($q) use ($area) {
                $q->where('area', 'like', "%{$area}%")
                  ->orWhere('city', 'like', "%{$area}%")
                  ->orWhere('district', 'like', "%{$area}%");
            });
        }

        $hasLocation = ($lat !== null && $lng !== null && $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180);

        if ($hasLocation) {
            if ($radius && $radius > 0) {
                $latDelta = $radius / 111.0;
                $lngDelta = $radius / (111.0 * max(0.1, cos(deg2rad($lat))));

                $query->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
                      ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);
            }

            $haversineSql = "(6371 * acos(
                LEAST(1.0, GREATEST(-1.0, 
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))
                ))
            ))";

            $query->select('*')
                ->selectRaw("{$haversineSql} AS distance", [$lat, $lng, $lat]);

            if ($radius && $radius > 0) {
                $query->havingRaw("distance <= ?", [$radius]);
            }

            $query->orderBy('distance', 'asc');
        } else {
            $query->orderBy('is_available', 'desc')
                  ->orderBy('total_donations', 'desc')
                  ->orderBy('full_name', 'asc');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get nearby resources for interactive Leaflet Map pins.
     */
    public function getMapResources(array $filters): Collection
    {
        $lat = isset($filters['lat']) && is_numeric($filters['lat']) ? (float) $filters['lat'] : null;
        $lng = isset($filters['lng']) && is_numeric($filters['lng']) ? (float) $filters['lng'] : null;
        $radius = isset($filters['radius']) && is_numeric($filters['radius']) ? (float) $filters['radius'] : 50;
        $categorySlugOrId = $filters['category'] ?? null;
        $searchQuery = trim($filters['query'] ?? '');

        $query = Resource::with(['category', 'details'])
            ->whereNull('deleted_at')
            ->whereIn('verification_status', ['verified', 'pending']);

        if (!empty($categorySlugOrId) && $categorySlugOrId !== 'all') {
            if (is_numeric($categorySlugOrId)) {
                $query->where('category_id', (int) $categorySlugOrId);
            } else {
                $query->whereHas('category', function ($q) use ($categorySlugOrId) {
                    $q->where('slug', $categorySlugOrId);
                });
            }
        }

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('area', 'like', "%{$searchQuery}%")
                    ->orWhere('address', 'like', "%{$searchQuery}%");
            });
        }

        if ($lat !== null && $lng !== null) {
            $latDelta = $radius / 111.0;
            $lngDelta = $radius / (111.0 * max(0.1, cos(deg2rad($lat))));

            $query->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
                  ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);

            $haversineSql = "(6371 * acos(
                LEAST(1.0, GREATEST(-1.0, 
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))
                ))
            ))";

            $query->select('*')
                ->selectRaw("{$haversineSql} AS distance", [$lat, $lng, $lat])
                ->havingRaw("distance <= ?", [$radius])
                ->orderBy('distance', 'asc');
        } else {
            $query->limit(100);
        }

        return $query->get()->map(function ($resource) {
            return [
                'id' => $resource->id,
                'name' => $resource->name,
                'slug' => $resource->slug,
                'category_name' => $resource->category?->name,
                'category_slug' => $resource->category?->slug,
                'category_color' => $resource->category?->color,
                'category_icon' => $resource->category?->icon,
                'address' => $resource->address,
                'area' => $resource->area,
                'latitude' => (float) $resource->latitude,
                'longitude' => (float) $resource->longitude,
                'phone' => $resource->phone,
                'emergency_phone' => $resource->emergency_phone,
                'clean_phone' => $resource->getCleanPhone(),
                'is_verified' => $resource->isVerified(),
                'is_available' => $resource->isAvailable(),
                'is_24_7' => (bool) $resource->is_24_7,
                'distance' => isset($resource->distance) ? (float) $resource->distance : null,
                'formatted_distance' => $resource->formatted_distance,
                'url' => route('resources.show', $resource->slug),
                'directions_url' => $resource->getDirectionsUrl(),
            ];
        });
    }

    private function logSearch(array $data): void
    {
        try {
            SearchLog::create($data);
        } catch (\Throwable $e) {
            // Silently log failure to prevent search interruption
        }
    }
}
