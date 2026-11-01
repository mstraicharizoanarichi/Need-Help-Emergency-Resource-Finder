@extends('layouts.app')

@section('title', 'Join Community First Responders (CFR) – Need Help')

@section('content')
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Hero Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider mb-3">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Community First Responder Network (CFR)
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Save Lives in Your Neighborhood
            </h1>
            <p class="mt-3 text-base sm:text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                In dense traffic in Dhaka and across Bangladesh, ambulances can take 20–30 minutes to arrive. 
                Immediate bystander CPR, bleeding control, and first aid within 3–5 minutes saves lives.
            </p>
        </div>

        <!-- Registration Form Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <form action="{{ route('community.responder.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Personal Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Full Name *</label>
                        <input type="text" name="name" required value="{{ old('name', auth()->user()?->name) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            placeholder="e.g. Dr. Tanvir Ahmed / Farhana Akter">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Emergency Mobile Phone *</label>
                        <input type="tel" name="phone" required value="{{ old('phone', auth()->user()?->phone) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            placeholder="e.g. 01712345678">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Email Address (Optional)</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        placeholder="yourname@gmail.com">
                </div>

                <!-- Certification Level -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Certification & Medical Background *</label>
                    <select name="certification_level" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="BASIC_FIRST_AID">Basic First Aid Trained (Red Crescent, Scouts, Rover, School)</option>
                        <option value="CPR_AED_CERTIFIED">CPR & AED Certified Responder</option>
                        <option value="RED_CRESCENT_VOLUNTEER">Bangladesh Red Crescent Society (BDRCS) Volunteer</option>
                        <option value="MEDICAL_STUDENT">Medical / Nursing Student (MBBS / BDS / B.Sc Nursing)</option>
                        <option value="NURSE">Registered Nurse / Paramedic</option>
                        <option value="DOCTOR">Registered Physician / Doctor (BMDC)</option>
                    </select>
                </div>

                <!-- Skills Selection -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Trained Skills & Readiness *</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php
                            $skillOptions = [
                                'CPR' => 'Adult/Child CPR',
                                'AED' => 'Automated External Defibrillator',
                                'BLEEDING_CONTROL' => 'Severe Bleeding / Tourniquet',
                                'CHOKING' => 'Choking / Heimlich Maneuver',
                                'BURNS' => 'Burn Care & Cooling',
                                'FRACTURE' => 'Splinting & Fracture Immobilization',
                            ];
                        @endphp
                        @foreach ($skillOptions as $code => $label)
                            <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 cursor-pointer text-sm">
                                <input type="checkbox" name="skills[]" value="{{ $code }}" checked
                                    class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-slate-800 dark:text-slate-200 text-xs sm:text-sm font-medium">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Location -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">City / District *</label>
                        <input type="text" name="city" required value="{{ old('city', 'Dhaka') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            placeholder="Dhaka, Chittagong, Sylhet...">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">Area / Thana *</label>
                        <input type="text" name="area" required value="{{ old('area', 'Dhanmondi') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            placeholder="e.g. Dhanmondi, Mirpur 10, Agrabad...">
                    </div>
                </div>

                <!-- GPS Coordinates (Auto-detected or manual) -->
                <div x-data="{
                    lat: '23.7808',
                    lng: '90.4152',
                    locating: false,
                    detected: false,
                    detect() {
                        if (!navigator.geolocation) {
                            alert('Geolocation is not supported by your browser.');
                            return;
                        }
                        this.locating = true;
                        navigator.geolocation.getCurrentPosition(
                            pos => {
                                this.lat = pos.coords.latitude.toFixed(6);
                                this.lng = pos.coords.longitude.toFixed(6);
                                this.locating = false;
                                this.detected = true;
                            },
                            err => {
                                this.locating = false;
                                alert('Could not retrieve GPS location: ' + err.message);
                            }
                        );
                    }
                }" class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">Neighborhood Coordinates (GPS)</span>
                        <button type="button" @click="detect()" :disabled="locating"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow transition disabled:opacity-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span x-text="locating ? 'Detecting...' : (detected ? 'GPS Detected ✓' : 'Detect My Location')"></span>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <input type="text" name="latitude" x-model="lat" readonly
                                class="w-full px-3 py-1.5 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300">
                        </div>
                        <div>
                            <input type="text" name="longitude" x-model="lng" readonly
                                class="w-full px-3 py-1.5 rounded-lg text-xs bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300">
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                        Your GPS coordinates are used exclusively to dispatch critical alerts when someone collapses or suffers life-threatening trauma within 1–2 km of your location.
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        ← Back to Home
                    </a>
                    <button type="submit"
                        class="px-8 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition transform hover:-translate-y-0.5">
                        Register as First Responder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
