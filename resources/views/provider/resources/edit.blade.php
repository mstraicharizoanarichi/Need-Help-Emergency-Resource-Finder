@extends('layouts.provider')

@section('title', "Edit {$resource->name} – Provider Portal")
@section('page_title', "Edit: {$resource->name}")

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form action="{{ route('provider.resources.update', $resource->id) }}" method="POST" class="space-y-8 text-xs sm:text-sm">
            @csrf
            @method('PUT')

            {{-- Basic Info Section --}}
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">
                    1. Basic Facility Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Service Category *</label>
                        <select name="category_id" required class="w-full h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold text-red-600">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (old('category_id', $resource->category_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Facility Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $resource->name) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Primary Hotline *</label>
                        <input type="text" name="phone" required value="{{ old('phone', $resource->phone) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Emergency Dispatch Phone</label>
                        <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $resource->emergency_phone) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Official Email</label>
                        <input type="email" name="email" value="{{ old('email', $resource->email) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Website URL</label>
                        <input type="url" name="website" value="{{ old('website', $resource->website) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Description / Key Specialties</label>
                    <textarea name="description" rows="2" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">{{ old('description', $resource->description) }}</textarea>
                </div>
            </div>

            {{-- Location & Interactive Map Coordinate Picker --}}
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">
                    2. Location & Map Pinpoint
                </h3>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Street Address *</label>
                    <input type="text" name="address" required value="{{ old('address', $resource->address) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Area / Thana *</label>
                        <input type="text" name="area" required value="{{ old('area', $resource->area) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">City *</label>
                        <input type="text" name="city" required value="{{ old('city', $resource->city) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">District *</label>
                        <input type="text" name="district" required value="{{ old('district', $resource->district) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700 dark:text-slate-300 text-xs">Pinpoint Coordinates on Map:</span>
                        <span class="text-[11px] text-slate-400">Drag marker to change</span>
                    </div>

                    <div id="picker-map" class="w-full h-64 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-100 z-0"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1">Latitude *</label>
                            <input type="text" name="latitude" id="input-lat" required value="{{ old('latitude', $resource->latitude) }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Longitude *</label>
                            <input type="text" name="longitude" id="input-lng" required value="{{ old('longitude', $resource->longitude) }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-xs">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Emergency Specs Section --}}
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">
                    3. Emergency Specs & Availability
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="is_24_7" value="1" {{ old('is_24_7', $resource->is_24_7) ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">24/7 Non-Stop Open</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="icu_available" value="1" {{ old('icu_available', $resource->details?->icu_available) ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">ICU Unit Available</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="oxygen_available" value="1" {{ old('oxygen_available', $resource->details?->oxygen_available) ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">Central Oxygen Supply</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="ambulance_available" value="1" {{ old('ambulance_available', $resource->details?->ambulance_available) ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">Ambulance Fleet on Site</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="blood_bank_available" value="1" {{ old('blood_bank_available', $resource->details?->blood_bank_available) ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">Blood Bank Available</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="emergency_ward" value="1" {{ old('emergency_ward', $resource->details?->emergency_ward) ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">Dedicated Trauma Ward</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold mb-1">Availability Status</label>
                        <select name="availability_status" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <option value="available" {{ old('availability_status', $resource->availability_status) === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="unavailable" {{ old('availability_status', $resource->availability_status) === 'unavailable' ? 'selected' : '' }}>Unavailable / Full</option>
                            <option value="closed" {{ old('availability_status', $resource->availability_status) === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">ICU Beds Count</label>
                        <input type="number" name="icu_beds_count" min="0" value="{{ old('icu_beds_count', $resource->details?->icu_beds_count) }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">General Emergency Beds</label>
                        <input type="number" name="general_beds_count" min="0" value="{{ old('general_beds_count', $resource->details?->general_beds_count) }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                @php
                    $servicesStr = is_array($resource->details?->services_offered) ? implode(', ', $resource->details->services_offered) : '';
                @endphp
                <div>
                    <label class="block font-semibold mb-1">Services & Specialized Treatments (Comma separated)</label>
                    <input type="text" name="services_offered" value="{{ old('services_offered', $servicesStr) }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
            </div>

            {{-- Submit Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('provider.resources.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 font-semibold">Cancel</a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-md">
                    Update Resource
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('input-lat');
    const lngInput = document.getElementById('input-lng');

    let curLat = parseFloat(latInput.value) || 23.7500;
    let curLng = parseFloat(lngInput.value) || 90.3800;

    const map = L.map('picker-map').setView([curLat, curLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([curLat, curLng], { draggable: true }).addTo(map);

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        latInput.value = pos.lat.toFixed(6);
        lngInput.value = pos.lng.toFixed(6);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        latInput.value = e.latlng.lat.toFixed(6);
        lngInput.value = e.latlng.lng.toFixed(6);
    });
});
</script>
@endpush
@endsection
