@extends('layouts.admin')

@section('title', 'Add Emergency Facility – Admin Console')
@section('page_title', 'Add New Emergency Facility')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.resources.store') }}" method="POST" class="space-y-8 text-xs sm:text-sm">
            @csrf

            {{-- Basic Info --}}
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">
                    1. Basic Facility Information
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Service Category *</label>
                        <select name="category_id" required class="w-full h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold text-red-600">
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Assigned Provider</label>
                        <select name="user_id" class="w-full h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <option value="">Admin / Public Service</option>
                            @foreach ($providers as $prov)
                                <option value="{{ $prov->id }}" {{ old('user_id') == $prov->id ? 'selected' : '' }}>{{ $prov->name }} ({{ $prov->organization_name ?: $prov->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Facility Name *</label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Dhaka Medical College Hospital" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Primary Hotline *</label>
                        <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="+880..." class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Emergency Hotline</label>
                        <input type="text" name="emergency_phone" value="{{ old('emergency_phone') }}" placeholder="e.g. 10616" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Website</label>
                        <input type="url" name="website" value="{{ old('website') }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Description / Specialization</label>
                    <textarea name="description" rows="2" class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Location & Map Picker --}}
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">
                    2. Location & Coordinates
                </h3>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Street Address *</label>
                    <input type="text" name="address" required value="{{ old('address') }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Area / Thana *</label>
                        <input type="text" name="area" required value="{{ old('area', 'Dhanmondi') }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">City *</label>
                        <input type="text" name="city" required value="{{ old('city', 'Dhaka') }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">District *</label>
                        <input type="text" name="district" required value="{{ old('district', 'Dhaka') }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="space-y-2">
                    <div id="picker-map" class="w-full h-64 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-100 z-0"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold mb-1">Latitude *</label>
                            <input type="text" name="latitude" id="input-lat" required value="{{ old('latitude', '23.750000') }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1">Longitude *</label>
                            <input type="text" name="longitude" id="input-lng" required value="{{ old('longitude', '90.380000') }}" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-mono text-xs">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Emergency Specs --}}
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-2">
                    3. Emergency Specs & Verification
                </h3>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block font-bold mb-1">Verification Status</label>
                        <select name="verification_status" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold text-blue-600">
                            <option value="verified" selected>Verified</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold mb-1">Availability Status</label>
                        <select name="availability_status" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <option value="available" selected>Available</option>
                            <option value="unavailable">Unavailable</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold mb-1">ICU Beds Count</label>
                        <input type="number" name="icu_beds_count" min="0" value="{{ old('icu_beds_count') }}" placeholder="e.g. 10" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold mb-1">General Beds</label>
                        <input type="number" name="general_beds_count" min="0" value="{{ old('general_beds_count') }}" placeholder="e.g. 100" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="is_24_7" value="1" {{ old('is_24_7', 1) ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">24/7 Open</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="icu_available" value="1" {{ old('icu_available') ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">ICU Unit</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="oxygen_available" value="1" {{ old('oxygen_available') ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">Oxygen Supply</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="ambulance_available" value="1" {{ old('ambulance_available') ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">Ambulance</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="blood_bank_available" value="1" {{ old('blood_bank_available') ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">Blood Bank</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                        <input type="checkbox" name="emergency_ward" value="1" {{ old('emergency_ward') ? 'checked' : '' }} class="rounded text-red-600">
                        <span class="font-bold text-xs">Trauma Ward</span>
                    </label>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Services Offered (Comma separated)</label>
                    <input type="text" name="services_offered" value="{{ old('services_offered') }}" placeholder="e.g. ICU, Emergency Surgery, NICU, Dialysis" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.resources.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 font-semibold">Cancel</a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold shadow-md">
                    Create Facility
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

    const map = L.map('picker-map').setView([curLat, curLng], 13);

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
