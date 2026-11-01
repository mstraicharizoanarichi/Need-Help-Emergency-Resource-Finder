<?php

namespace App\Services;

use App\Models\Resource;
use App\Models\ResourceDetail;
use App\Models\ResourceOperatingHour;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResourceService
{
    /**
     * Create a new emergency resource along with details and operating hours.
     */
    public function createResource(array $data, User $creator): Resource
    {
        return DB::transaction(function () use ($data, $creator) {
            $verificationStatus = $creator->isAdmin() ? 'verified' : 'pending';
            $verifiedAt = $creator->isAdmin() ? now() : null;
            $verifiedBy = $creator->isAdmin() ? $creator->id : null;

            $slug = Str::slug($data['name']) . '-' . Str::lower(Str::random(5));

            $resource = Resource::create([
                'user_id' => $creator->isProvider() ? $creator->id : ($data['user_id'] ?? null),
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'address' => $data['address'],
                'area' => $data['area'] ?? null,
                'city' => $data['city'] ?? 'Dhaka',
                'district' => $data['district'] ?? 'Dhaka',
                'postal_code' => $data['postal_code'] ?? null,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'phone' => $data['phone'],
                'emergency_phone' => $data['emergency_phone'] ?? null,
                'email' => $data['email'] ?? null,
                'website' => $data['website'] ?? null,
                'verification_status' => $verificationStatus,
                'verified_at' => $verifiedAt,
                'verified_by' => $verifiedBy,
                'availability_status' => $data['availability_status'] ?? 'available',
                'is_24_7' => !empty($data['is_24_7']),
                'operating_hours_text' => $data['operating_hours_text'] ?? null,
                'is_featured' => !empty($data['is_featured']) && $creator->isAdmin(),
            ]);

            // Save details
            $details = [
                'icu_available' => !empty($data['icu_available']),
                'icu_beds_count' => !empty($data['icu_beds_count']) ? (int) $data['icu_beds_count'] : null,
                'ambulance_available' => !empty($data['ambulance_available']),
                'oxygen_available' => !empty($data['oxygen_available']),
                'blood_bank_available' => !empty($data['blood_bank_available']),
                'emergency_ward' => !empty($data['emergency_ward']),
                'general_beds_count' => !empty($data['general_beds_count']) ? (int) $data['general_beds_count'] : null,
                'vehicle_types' => $data['vehicle_types'] ?? null,
                'paramedic_onboard' => !empty($data['paramedic_onboard']),
                'home_delivery' => !empty($data['home_delivery']),
                'online_reports' => !empty($data['online_reports']),
                'services_offered' => is_array($data['services_offered'] ?? null) 
                    ? $data['services_offered'] 
                    : (isset($data['services_offered_text']) ? array_filter(array_map('trim', explode(',', $data['services_offered_text']))) : null),
                'facilities' => is_array($data['facilities'] ?? null) 
                    ? $data['facilities'] 
                    : (isset($data['facilities_text']) ? array_filter(array_map('trim', explode(',', $data['facilities_text']))) : null),
                'additional_info' => $data['additional_info'] ?? null,
            ];

            $resource->details()->create($details);

            // Save operating hours (0 to 6)
            $this->syncOperatingHours($resource, $data['operating_hours'] ?? []);

            return $resource;
        });
    }

    /**
     * Update an existing resource.
     */
    public function updateResource(Resource $resource, array $data, User $updater): Resource
    {
        return DB::transaction(function () use ($resource, $data, $updater) {
            $resourceData = [
                'category_id' => $data['category_id'] ?? $resource->category_id,
                'name' => $data['name'] ?? $resource->name,
                'description' => $data['description'] ?? $resource->description,
                'address' => $data['address'] ?? $resource->address,
                'area' => $data['area'] ?? $resource->area,
                'city' => $data['city'] ?? $resource->city,
                'district' => $data['district'] ?? $resource->district,
                'postal_code' => $data['postal_code'] ?? $resource->postal_code,
                'latitude' => $data['latitude'] ?? $resource->latitude,
                'longitude' => $data['longitude'] ?? $resource->longitude,
                'phone' => $data['phone'] ?? $resource->phone,
                'emergency_phone' => $data['emergency_phone'] ?? $resource->emergency_phone,
                'email' => $data['email'] ?? $resource->email,
                'website' => $data['website'] ?? $resource->website,
                'availability_status' => $data['availability_status'] ?? $resource->availability_status,
                'is_24_7' => isset($data['is_24_7']) ? (bool) $data['is_24_7'] : $resource->is_24_7,
                'operating_hours_text' => $data['operating_hours_text'] ?? $resource->operating_hours_text,
            ];

            if ($updater->isAdmin()) {
                if (isset($data['verification_status'])) {
                    $resourceData['verification_status'] = $data['verification_status'];
                    if ($data['verification_status'] === 'verified') {
                        $resourceData['verified_at'] = now();
                        $resourceData['verified_by'] = $updater->id;
                    }
                }
                if (isset($data['is_featured'])) {
                    $resourceData['is_featured'] = (bool) $data['is_featured'];
                }
                if (isset($data['rejection_reason'])) {
                    $resourceData['rejection_reason'] = $data['rejection_reason'];
                }
            }

            $resource->update($resourceData);

            // Update details
            $details = [
                'icu_available' => !empty($data['icu_available']),
                'icu_beds_count' => !empty($data['icu_beds_count']) ? (int) $data['icu_beds_count'] : null,
                'ambulance_available' => !empty($data['ambulance_available']),
                'oxygen_available' => !empty($data['oxygen_available']),
                'blood_bank_available' => !empty($data['blood_bank_available']),
                'emergency_ward' => !empty($data['emergency_ward']),
                'general_beds_count' => !empty($data['general_beds_count']) ? (int) $data['general_beds_count'] : null,
                'vehicle_types' => $data['vehicle_types'] ?? null,
                'paramedic_onboard' => !empty($data['paramedic_onboard']),
                'home_delivery' => !empty($data['home_delivery']),
                'online_reports' => !empty($data['online_reports']),
                'additional_info' => $data['additional_info'] ?? null,
            ];

            if (isset($data['services_offered_text'])) {
                $details['services_offered'] = array_filter(array_map('trim', explode(',', $data['services_offered_text'])));
            } elseif (isset($data['services_offered'])) {
                $details['services_offered'] = $data['services_offered'];
            }

            if (isset($data['facilities_text'])) {
                $details['facilities'] = array_filter(array_map('trim', explode(',', $data['facilities_text'])));
            } elseif (isset($data['facilities'])) {
                $details['facilities'] = $data['facilities'];
            }

            $resource->details()->updateOrCreate(
                ['resource_id' => $resource->id],
                $details
            );

            if (isset($data['operating_hours'])) {
                $this->syncOperatingHours($resource, $data['operating_hours']);
            }

            return $resource->fresh(['category', 'details', 'operatingHours']);
        });
    }

    /**
     * Toggle resource availability status.
     */
    public function toggleAvailability(Resource $resource, ?string $status = null): bool
    {
        if ($status === null) {
            $status = ($resource->availability_status === 'available') ? 'unavailable' : 'available';
        }

        if (!in_array($status, ['available', 'unavailable', 'busy', 'closed', 'unknown'])) {
            return false;
        }

        return $resource->update(['availability_status' => $status]);
    }

    private function syncOperatingHours(Resource $resource, array $hoursData): void
    {
        for ($day = 0; $day <= 6; $day++) {
            $dayData = $hoursData[$day] ?? [];

            ResourceOperatingHour::updateOrCreate(
                [
                    'resource_id' => $resource->id,
                    'day_of_week' => $day,
                ],
                [
                    'open_time' => !empty($dayData['open_time']) ? $dayData['open_time'] : null,
                    'close_time' => !empty($dayData['close_time']) ? $dayData['close_time'] : null,
                    'is_closed' => !empty($dayData['is_closed']),
                    'is_24_hours' => !empty($dayData['is_24_hours']) || $resource->is_24_7,
                ]
            );
        }
    }
}
