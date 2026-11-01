@extends('layouts.app')

@section('title', "{$resource->name} – Emergency Services & Contact")
@section('meta_description', "Contact {$resource->name} in {$resource->city}, Bangladesh. Phone: " . ($resource->emergency_phone ?: $resource->phone) . ". Emergency hospital services, ICU, and directions.")

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8">

    {{-- Top Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
        <a href="{{ route('home') }}" class="hover:text-slate-800 dark:hover:text-white">Home</a>
        <span>/</span>
        <a href="{{ route('search', ['category' => optional($resource->category)->slug]) }}" class="hover:text-slate-800 dark:hover:text-white">{{ optional($resource->category)->name }}</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-slate-200 font-semibold truncate">{{ $resource->name }}</span>
    </nav>

    {{-- Main Emergency Header & Action Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            
            {{-- Left Info --}}
            <div class="space-y-3 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider {{ optional($resource->category)->getIconColorClasses() }} border">
                        <x-category-icon :icon="optional($resource->category)->icon" class="w-3.5 h-3.5" />
                        {{ optional($resource->category)->name }}
                    </span>

                    @if ($resource->isVerified())
                        <x-status-badge type="verified" />
                    @else
                        <x-status-badge type="pending" />
                    @endif

                    @if ($resource->is_24_7)
                        <x-status-badge type="24_7" />
                    @elseif ($resource->isOpenNow())
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Open Now
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            Closed Now
                        </span>
                    @endif

                    @if (isset($resource->distance))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900">
                            📍 {{ $resource->formatted_distance }} away
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white leading-tight">
                    {{ $resource->name }}
                </h1>

                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $resource->address }}{{ $resource->area ? ', ' . $resource->area : '' }}, {{ $resource->city }}, {{ $resource->district }}</span>
                </p>

                @if ($resource->description)
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed pt-1">
                        {{ $resource->description }}
                    </p>
                @endif
            </div>

            {{-- Right Emergency Action Buttons --}}
            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0 lg:w-72">
                {{-- Giant CALL NOW Button --}}
                <a href="tel:{{ $resource->getCleanPhone() }}" class="w-full py-4 px-6 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-black text-base sm:text-lg shadow-lg shadow-red-600/30 flex items-center justify-center gap-3 transition-all hover:scale-[1.02]">
                    <svg class="w-6 h-6 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <span>CALL: {{ $resource->emergency_phone ?: $resource->phone }}</span>
                </a>

                {{-- GET DIRECTIONS Navigation --}}
                <a href="{{ $resource->getDirectionsUrl() }}" target="_blank" rel="noopener noreferrer" class="w-full py-3.5 px-6 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-100 dark:text-slate-900 font-bold text-sm flex items-center justify-center gap-2 shadow-sm transition-all">
                    <svg class="w-5 h-5 text-blue-400 dark:text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    <span>GET DIRECTIONS</span>
                </a>

                {{-- Secondary Action Bar (Bookmark & Report) --}}
                <div class="flex items-center justify-between gap-2 pt-1">
                    <form action="{{ route('favorites.toggle', $resource->id) }}" method="POST" class="w-1/2">
                        @csrf
                        <button type="submit" class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 {{ $isFavorited ? 'text-red-500 fill-red-500' : 'text-slate-400' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.16-1.026c-1.42-1.38-3.08-3.32-3.468-5.376C4.5 7.8 6.1 5.5 8.2 5.5c1.1 0 2.1.6 2.8 1.5.7-.9 1.7-1.5 2.8-1.5 2.1 0 3.7 2.3 3.2 5.0-.388 2.056-2.048 3.996-3.468 5.376a20.76 20.76 0 01-1.16 1.026l-.019.01-.005.003h-.002a.75.75 0 01-.698 0z"/></svg>
                            <span>{{ $isFavorited ? 'Saved' : 'Save Favorite' }}</span>
                        </button>
                    </form>

                    <button type="button" onclick="document.getElementById('report-modal').showModal()" class="w-1/2 py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Report Issue</span>
                    </button>
                </div>

            </div>

        </div>
    </div>

    {{-- Details Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left 2 Columns: Facilities & Schedule --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Medical & Emergency Capabilities --}}
            <x-scroll-reveal direction="up">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v12M6 12h12"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
                        Emergency Capabilities & Facilities
                    </h2>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs sm:text-sm">
                        
                        {{-- ICU --}}
                        @php $hasIcu = (bool) optional($resource->details)->icu_available; @endphp
                        <div class="p-4 rounded-2xl {{ $hasIcu ? 'bg-red-50 text-red-950 dark:bg-red-950/40 dark:text-red-200 border border-red-200 dark:border-red-900' : 'bg-slate-50 text-slate-400 dark:bg-slate-800/40 dark:text-slate-500' }}">
                            <p class="font-bold text-xs uppercase tracking-wider mb-1">ICU Facility</p>
                            <p class="font-extrabold text-base">
                                @if ($hasIcu)
                                    {{ optional($resource->details)->icu_beds_count ? optional($resource->details)->icu_beds_count . ' Beds Available' : 'Available' }}
                                @else
                                    Not Available
                                @endif
                            </p>
                        </div>

                        {{-- Ambulance --}}
                        @php $hasAmb = (bool) optional($resource->details)->ambulance_available; @endphp
                        <div class="p-4 rounded-2xl {{ $hasAmb ? 'bg-orange-50 text-orange-950 dark:bg-orange-950/40 dark:text-orange-200 border border-orange-200 dark:border-orange-900' : 'bg-slate-50 text-slate-400 dark:bg-slate-800/40 dark:text-slate-500' }}">
                            <p class="font-bold text-xs uppercase tracking-wider mb-1">Ambulance</p>
                            <p class="font-extrabold text-base">{{ $hasAmb ? '24/7 Available' : 'None on site' }}</p>
                        </div>

                        {{-- Oxygen --}}
                        @php $hasOxy = (bool) optional($resource->details)->oxygen_available; @endphp
                        <div class="p-4 rounded-2xl {{ $hasOxy ? 'bg-blue-50 text-blue-950 dark:bg-blue-950/40 dark:text-blue-200 border border-blue-200 dark:border-blue-900' : 'bg-slate-50 text-slate-400 dark:bg-slate-800/40 dark:text-slate-500' }}">
                            <p class="font-bold text-xs uppercase tracking-wider mb-1">Oxygen Supply</p>
                            <p class="font-extrabold text-base">{{ $hasOxy ? 'Central Pipeline' : 'Standard' }}</p>
                        </div>

                        {{-- Blood Bank --}}
                        @php $hasBlood = (bool) optional($resource->details)->blood_bank_available; @endphp
                        <div class="p-4 rounded-2xl {{ $hasBlood ? 'bg-rose-50 text-rose-950 dark:bg-rose-950/40 dark:text-rose-200 border border-rose-200 dark:border-rose-900' : 'bg-slate-50 text-slate-400 dark:bg-slate-800/40 dark:text-slate-500' }}">
                            <p class="font-bold text-xs uppercase tracking-wider mb-1">Blood Bank</p>
                            <p class="font-extrabold text-base">{{ $hasBlood ? 'In-house Available' : 'None' }}</p>
                        </div>

                        {{-- Emergency Ward --}}
                        @php $hasWard = (bool) optional($resource->details)->emergency_ward; @endphp
                        <div class="p-4 rounded-2xl {{ $hasWard ? 'bg-emerald-50 text-emerald-950 dark:bg-emerald-950/40 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-900' : 'bg-slate-50 text-slate-400 dark:bg-slate-800/40 dark:text-slate-500' }}">
                            <p class="font-bold text-xs uppercase tracking-wider mb-1">Emergency Ward</p>
                            <p class="font-extrabold text-base">{{ $hasWard ? 'Dedicated 24/7' : 'Standard' }}</p>
                        </div>

                        {{-- Total Beds --}}
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                            <p class="font-bold text-xs uppercase tracking-wider mb-1 text-slate-500">General Capacity</p>
                            <p class="font-extrabold text-base text-slate-900 dark:text-white">{{ optional($resource->details)->general_beds_count ? optional($resource->details)->general_beds_count . ' Beds' : 'Verified Facility' }}</p>
                        </div>

                    </div>

                    {{-- Services Offered Tags --}}
                    @if (!empty(optional($resource->details)->services_offered))
                        <div class="space-y-2 pt-2">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Services & Specialties</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($resource->details->services_offered as $service)
                                    <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-semibold">
                                        ✓ {{ $service }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Facility Tags --}}
                    @if (!empty(optional($resource->details)->facilities))
                        <div class="space-y-2 pt-2">
                            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">On-site Facilities</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($resource->details->facilities as $facility)
                                    <span class="px-3 py-1 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-800 dark:text-blue-200 text-xs font-semibold border border-blue-200/60 dark:border-blue-900/60">
                                        ★ {{ $facility }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-scroll-reveal>

            {{-- 7-Day Operating Hours Schedule --}}
            <x-scroll-reveal direction="up" delay="100">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        Operating Schedule & Duty Hours
                    </h2>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @php $currentDay = now()->dayOfWeek; @endphp
                        @foreach ($resource->operatingHours as $schedule)
                            <div class="py-2.5 flex items-center justify-between {{ $schedule->day_of_week === $currentDay ? 'font-bold text-red-600 dark:text-red-400 bg-red-50/50 dark:bg-red-950/30 px-3 rounded-lg' : 'text-slate-600 dark:text-slate-400' }}">
                                <span class="flex items-center gap-2">
                                    @if ($schedule->day_of_week === $currentDay)
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                    @endif
                                    {{ $schedule->day_name }}
                                </span>
                                <span>{{ $schedule->formatted_hours }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-scroll-reveal>

            {{-- Specialist & Emergency Doctors Directory Section --}}
            <x-scroll-reveal direction="up" delay="200">
                <div id="hospital-doctors" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    Doctors & Specialists Directory
                                </h2>
                                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 text-xs font-black">
                                    {{ $resource->doctors->count() }} Available
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Consultation schedules, chamber room numbers, visiting hours, and instant appointment hotlines.</p>
                        </div>

                        @php $emergencyDocsCount = $resource->doctors->where('is_emergency_available', true)->count(); @endphp
                        @if ($emergencyDocsCount > 0)
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-300 text-xs font-bold shrink-0 self-start sm:self-auto">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                <span>🚨 {{ $emergencyDocsCount }} Emergency Doctor{{ $emergencyDocsCount > 1 ? 's' : '' }} On Duty</span>
                            </div>
                        @endif
                    </div>

                    @if ($resource->doctors->count() > 0)
                        {{-- Interactive Filters --}}
                        <div class="space-y-3">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="relative flex-1">
                                    <input type="text" id="doctor-search-input" onkeyup="filterDoctors()" placeholder="Search doctor by name, qualification, or department..." class="w-full h-10 pl-9 pr-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:ring-2 focus:ring-blue-500 outline-none text-slate-900 dark:text-white">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                </div>
                            </div>

                            {{-- Specialization Filter Chips --}}
                            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                                <button type="button" onclick="setSpecialtyFilter('all')" class="specialty-chip px-3 py-1.5 rounded-xl font-bold transition-all bg-slate-900 text-white dark:bg-white dark:text-slate-900" data-specialty="all">
                                    All ({{ $resource->doctors->count() }})
                                </button>
                                @if ($emergencyDocsCount > 0)
                                    <button type="button" onclick="setSpecialtyFilter('emergency')" class="specialty-chip px-3 py-1.5 rounded-xl font-bold transition-all bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800" data-specialty="emergency">
                                        🚨 Emergency Duty ({{ $emergencyDocsCount }})
                                    </button>
                                @endif
                                @foreach ($doctorSpecialties as $specialty)
                                    @php $count = $resource->doctors->where('specialization', $specialty)->count(); @endphp
                                    <button type="button" onclick="setSpecialtyFilter('{{ addslashes($specialty) }}')" class="specialty-chip px-3 py-1.5 rounded-xl font-medium transition-all bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 whitespace-nowrap" data-specialty="{{ $specialty }}">
                                        {{ $specialty }} ({{ $count }})
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Doctor Cards Grid --}}
                        <div id="doctors-list-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            @foreach ($resource->doctors as $doc)
                                <div class="doctor-card p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex flex-col justify-between space-y-4 hover:shadow-md transition-shadow" 
                                     data-name="{{ strtolower($doc->name) }}"
                                     data-specialty="{{ strtolower($doc->specialization) }}"
                                     data-department="{{ strtolower($doc->department ?: '') }}"
                                     data-qualifications="{{ strtolower($doc->qualifications) }}"
                                     data-emergency="{{ $doc->is_emergency_available ? '1' : '0' }}">
                                    
                                    <div class="space-y-2.5">
                                        {{-- Specialty & Emergency Badges --}}
                                        <div class="flex flex-wrap items-center justify-between gap-1.5">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[11px] font-bold border {{ $doc->getSpecialtyBadgeColor() }}">
                                                {{ $doc->specialization }}
                                            </span>

                                            @if ($doc->is_emergency_available)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-600 text-white text-[10px] font-black uppercase tracking-wider animate-pulse">
                                                    🚨 On Emergency Duty
                                                </span>
                                            @elseif ($doc->isVisitingToday())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-bold">
                                                    ● Visiting Today
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Doctor Name & Designation --}}
                                        <div>
                                            <h3 class="font-extrabold text-base text-slate-900 dark:text-white hover:text-blue-600">
                                                <a href="{{ route('doctors.show', $doc->slug) }}">
                                                    {{ $doc->name }}
                                                </a>
                                            </h3>
                                            @if ($doc->designation || $doc->department)
                                                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
                                                    {{ $doc->designation }}{{ $doc->department ? ' • ' . $doc->department : '' }}
                                                </p>
                                            @endif
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                                🎓 {{ $doc->qualifications }}
                                            </p>
                                        </div>

                                        {{-- Schedule & Chamber Info Box --}}
                                        <div class="bg-white dark:bg-slate-900/80 p-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60 space-y-1.5 text-xs">
                                            <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                                <span class="flex items-center gap-1 font-medium">
                                                    <svg class="w-3.5 h-3.5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                                    Visiting Days:
                                                </span>
                                                <span class="font-bold text-slate-900 dark:text-white text-right">
                                                    @if (!empty($doc->visiting_days))
                                                        {{ implode(', ', (array)$doc->visiting_days) }}
                                                    @else
                                                        Daily / On-Call
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                                <span class="flex items-center gap-1 font-medium">
                                                    <svg class="w-3.5 h-3.5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                                                    Visiting Time:
                                                </span>
                                                <span class="font-bold text-slate-900 dark:text-white">
                                                    {{ $doc->visiting_hours ?: 'Standard Ward Hours' }}
                                                </span>
                                            </div>

                                            @if ($doc->room_number)
                                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                                                    <span class="font-medium">🏥 Chamber / Room:</span>
                                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $doc->room_number }}</span>
                                                </div>
                                            @endif

                                            @if ($doc->consultation_fee)
                                                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
                                                    <span class="font-medium">Fee:</span>
                                                    <span class="font-black text-emerald-600 dark:text-emerald-400">৳ {{ number_format($doc->consultation_fee) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Action Buttons: Call Appointment & View Profile --}}
                                    <div class="space-y-2 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-1 text-amber-500 text-xs font-bold">
                                                <span>★ {{ number_format($doc->rating, 1) }}</span>
                                                <span class="text-slate-400 font-normal">({{ $doc->total_reviews }})</span>
                                            </div>

                                            <a href="{{ route('doctors.show', $doc->slug) }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                                <span>Full Profile</span>
                                                <span>&rarr;</span>
                                            </a>
                                        </div>

                                        @php $phone = $doc->getCleanPhone(); @endphp
                                        <a href="tel:{{ $phone }}" class="w-full py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all hover:scale-[1.01]">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            <span>Call Appointment: {{ $doc->appointment_phone ?: $resource->phone }}</span>
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        <div id="no-doctors-found" class="hidden text-center py-10 text-slate-400 space-y-2">
                            <p class="text-sm font-semibold">No doctors found matching your filter criteria.</p>
                            <button type="button" onclick="setSpecialtyFilter('all'); document.getElementById('doctor-search-input').value=''; filterDoctors();" class="text-xs text-blue-600 font-bold hover:underline">Reset Filters</button>
                        </div>

                    @else
                        <div class="text-center py-10 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200/60 dark:border-slate-800 text-slate-400 space-y-2">
                            <p class="text-sm font-semibold">No specialist doctor directory has been published for this facility yet.</p>
                            <p class="text-xs">Contact the hospital reception hotline at <a href="tel:{{ $resource->getCleanPhone() }}" class="font-bold text-red-600">{{ $resource->phone }}</a> for on-call doctor availability.</p>
                        </div>
                    @endif
                </div>
            </x-scroll-reveal>

        </div>

        {{-- Right Column: Map Location Preview & Contacts --}}
        <div class="space-y-6">
            
            {{-- Map Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2 px-1">
                    <svg class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    Location Map
                </h3>

                @php
                    $singleMarker = [[
                        'id' => $resource->id,
                        'name' => $resource->name,
                        'category_slug' => optional($resource->category)->slug,
                        'category_name' => optional($resource->category)->name,
                        'address' => $resource->address,
                        'area' => $resource->area,
                        'latitude' => (float) $resource->latitude,
                        'longitude' => (float) $resource->longitude,
                        'phone' => $resource->phone,
                        'clean_phone' => $resource->getCleanPhone(),
                        'is_verified' => $resource->isVerified(),
                        'is_24_7' => (bool) $resource->is_24_7,
                        'directions_url' => $resource->getDirectionsUrl(),
                    ]];
                @endphp

                <x-map-view 
                    :lat="(float)$resource->latitude" 
                    :lng="(float)$resource->longitude" 
                    :zoom="15" 
                    :markers="$singleMarker" 
                    height="280px" 
                    mapId="resource-detail-map" 
                    :showLocateButton="false"
                />

                <div class="pt-2 px-1">
                    <a href="{{ $resource->getDirectionsUrl() }}" target="_blank" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white text-xs font-bold text-center block">
                        Open in Google Maps &rarr;
                    </a>
                </div>
            </div>

            {{-- Verified Provider Contact Card --}}
            <x-scroll-reveal direction="up" delay="100">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 text-xs">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white">Facility Contacts</h3>
                
                <div class="space-y-2.5">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-500">📞</span>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">Primary Hotline</span>
                            <a href="tel:{{ $resource->getCleanPhone() }}" class="font-bold text-sm text-slate-900 dark:text-white hover:text-red-600">{{ $resource->phone }}</a>
                        </div>
                    </div>

                    @if ($resource->emergency_phone)
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-lg bg-red-100 dark:bg-red-950/60 flex items-center justify-center font-bold text-red-600">🚨</span>
                            <div>
                                <span class="text-red-500 block text-[10px] uppercase font-bold">24/7 Emergency Dispatch</span>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $resource->emergency_phone) }}" class="font-bold text-sm text-red-600 hover:underline">{{ $resource->emergency_phone }}</a>
                            </div>
                        </div>
                    @endif

                    @if ($resource->email)
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-500">✉️</span>
                            <div class="truncate">
                                <span class="text-slate-400 block text-[10px] uppercase">Email</span>
                                <span class="font-semibold text-slate-700 dark:text-slate-300 truncate">{{ $resource->email }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($resource->website)
                        <div class="flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-slate-500">🌐</span>
                            <div class="truncate">
                                <span class="text-slate-400 block text-[10px] uppercase">Website</span>
                                <a href="{{ $resource->website }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-600 hover:underline truncate">{{ $resource->website }}</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            </x-scroll-reveal>

        </div>

    </div>

    {{-- Nearby Similar Resources --}}
    @if ($nearbyResources->count() > 0)
        <x-scroll-reveal direction="up" delay="200">
        <div class="space-y-4 pt-6">
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Other Emergency {{ optional($resource->category)->name }} Near Area</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($nearbyResources as $nearby)
                    <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 flex flex-col justify-between space-y-3">
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white line-clamp-1">
                                <a href="{{ route('resources.show', $nearby->slug) }}" class="hover:text-red-600">{{ $nearby->name }}</a>
                            </h4>
                            <p class="text-xs text-slate-500 line-clamp-1">{{ $nearby->address }}, {{ $nearby->city }}</p>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">📞 {{ $nearby->phone }}</span>
                            <a href="tel:{{ $nearby->getCleanPhone() }}" class="py-1 px-2.5 rounded-lg bg-red-600 text-white font-bold text-xs">Call</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        </x-scroll-reveal>
    @endif

</div>

{{-- Report Inaccurate Information Modal --}}
<dialog id="report-modal" class="backdrop:bg-slate-900/60 rounded-3xl p-0 shadow-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full m-auto">
    <div class="p-6 sm:p-8 bg-white dark:bg-slate-900 text-slate-900 dark:text-white space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Report Information Issue
            </h3>
            <button type="button" onclick="document.getElementById('report-modal').close()" class="p-1 text-slate-400 hover:text-slate-700">✕</button>
        </div>

        <p class="text-xs text-slate-500 dark:text-slate-400">
            Help keep emergency records accurate for citizens across Bangladesh. Your report will be immediately reviewed by our moderators.
        </p>

        <form action="{{ route('reports.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="resource_id" value="{{ $resource->id }}">

            <div>
                <label class="block font-semibold mb-1">Issue Category *</label>
                <select name="report_type" required class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <option value="incorrect_phone">Incorrect / Dead Phone Number</option>
                    <option value="wrong_location">Wrong Map Location / Coordinates</option>
                    <option value="closed_service">Facility Permanently Closed</option>
                    <option value="outdated_info">Outdated ICU / Medical Info</option>
                    <option value="duplicate_resource">Duplicate Facility Entry</option>
                    <option value="incorrect_availability">Incorrect Availability Status</option>
                    <option value="other">Other Issue</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-1">Your Name *</label>
                <input type="text" name="reporter_name" required value="{{ optional(Auth::user())->name }}" placeholder="Your name" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-semibold mb-1">Phone Number</label>
                    <input type="text" name="reporter_phone" value="{{ optional(Auth::user())->phone }}" placeholder="+88017..." class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Email</label>
                    <input type="email" name="reporter_email" value="{{ optional(Auth::user())->email }}" placeholder="your@email.com" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
            </div>

            <div>
                <label class="block font-semibold mb-1">Description of the problem *</label>
                <textarea name="description" required rows="3" placeholder="Please describe what information is inaccurate..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('report-modal').close()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700">Submit Report</button>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
let currentSpecialty = 'all';

function setSpecialtyFilter(specialty) {
    currentSpecialty = specialty;
    
    // Update chip styling
    document.querySelectorAll('.specialty-chip').forEach(btn => {
        const chipSpecialty = btn.getAttribute('data-specialty');
        if (chipSpecialty === specialty) {
            btn.className = 'specialty-chip px-3 py-1.5 rounded-xl font-bold transition-all bg-slate-900 text-white dark:bg-white dark:text-slate-900 whitespace-nowrap';
        } else {
            btn.className = 'specialty-chip px-3 py-1.5 rounded-xl font-medium transition-all bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 whitespace-nowrap';
        }
    });

    filterDoctors();
}

function filterDoctors() {
    const searchInput = document.getElementById('doctor-search-input');
    const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
    const cards = document.querySelectorAll('.doctor-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const specialty = card.getAttribute('data-specialty') || '';
        const dept = card.getAttribute('data-department') || '';
        const qual = card.getAttribute('data-qualifications') || '';
        const isEmergency = card.getAttribute('data-emergency') === '1';

        let matchesSpecialty = true;
        if (currentSpecialty === 'emergency') {
            matchesSpecialty = isEmergency;
        } else if (currentSpecialty !== 'all') {
            matchesSpecialty = specialty.includes(currentSpecialty.toLowerCase());
        }

        let matchesSearch = true;
        if (query) {
            matchesSearch = name.includes(query) || specialty.includes(query) || dept.includes(query) || qual.includes(query);
        }

        if (matchesSpecialty && matchesSearch) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const noFound = document.getElementById('no-doctors-found');
    if (noFound) {
        noFound.classList.toggle('hidden', visibleCount > 0);
    }
}
</script>
@endpush
@endsection
