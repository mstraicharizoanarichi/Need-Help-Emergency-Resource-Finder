@extends('layouts.app')

@section('title', 'Find Specialist Doctors & Emergency Physicians – Need Help')
@section('meta_description', 'Search verified specialist doctors in Bangladesh. Find Cardiologists, Neurologists, Pediatricians, visiting schedules, chambers, and emergency hotlines.')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

    {{-- Hero & Search Header --}}
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 text-white rounded-3xl p-6 sm:p-10 shadow-xl border border-slate-700/60 relative overflow-hidden">
        {{-- Decorative Background Elements --}}
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-72 h-72 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold border border-blue-400/30">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                <span>Verified Hospital Specialists & Emergency Physicians</span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
                Find Specialist Doctors & Check Visiting Hours
            </h1>

            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-2xl">
                Search verified Cardiologists, Neurologists, Pediatricians, and Surgeons across Bangladesh. View weekly visiting days, room numbers, and call direct hospital appointment hotlines.
            </p>

            {{-- Universal Search Form --}}
            <form action="{{ route('doctors.index') }}" method="GET" class="pt-2">
                <div class="w-fit bg-white dark:bg-slate-900 rounded-2xl p-2 sm:p-2.5 shadow-2xl border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-2 text-slate-900 dark:text-white items-center">
                    <div class="relative flex-1">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Doctor name, specialty (e.g. Cardiology), degree..." class="w-40% h-11 pl-10 pr-3 rounded-xl bg-slate-50 dark:bg-slate-800 border-0 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 outline-none text-slate-900 dark:text-white">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>

                    <div class="grid grid-cols-2 sm:flex items-center gap-2">
                        <select name="specialization" class="h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-xs border-0 outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200">
                            <option value="">All Specialties</option>
                            @foreach ($specializations as $spec)
                                <option value="{{ $spec }}" {{ request('specialization') === $spec ? 'selected' : '' }}>{{ $spec }}</option>
                            @endforeach
                        </select>

                        <select name="hospital_id" class="h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 text-xs border-0 outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200">
                            <option value="">All Hospitals</option>
                            @foreach ($hospitals as $hosp)
                                <option value="{{ $hosp->id }}" {{ request('hospital_id') == $hosp->id ? 'selected' : '' }}>{{ $hosp->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="h-11 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-600/30 flex items-center justify-center gap-2 transition-all shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <span>Search Doctors</span>
                    </button>
                </div>

                {{-- Quick Filter Chips --}}
                <div class="flex flex-wrap items-center gap-2 pt-3 text-xs">
                    <span class="text-slate-400 font-semibold">Popular Specialties:</span>
                    <a href="{{ route('doctors.index', ['specialization' => 'Cardiology']) }}" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 transition-colors {{ request('specialization') === 'Cardiology' ? 'bg-blue-600 text-white font-bold' : '' }}">❤️ Cardiology</a>
                    <a href="{{ route('doctors.index', ['specialization' => 'Neurology']) }}" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 transition-colors {{ request('specialization') === 'Neurology' ? 'bg-blue-600 text-white font-bold' : '' }}">🧠 Neurology</a>
                    <a href="{{ route('doctors.index', ['emergency' => 1]) }}" class="px-2.5 py-1 rounded-lg bg-red-600/80 hover:bg-red-600 text-white font-bold transition-colors {{ request('emergency') ? 'ring-2 ring-white' : '' }}">🚨 Emergency On-Duty</a>
                    <a href="{{ route('doctors.index', ['specialization' => 'Pediatrics']) }}" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 transition-colors {{ request('specialization') === 'Pediatrics' ? 'bg-blue-600 text-white font-bold' : '' }}">👶 Pediatrics</a>
                    <a href="{{ route('doctors.index', ['specialization' => 'Orthopedics']) }}" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 transition-colors {{ request('specialization') === 'Orthopedics' ? 'bg-blue-600 text-white font-bold' : '' }}">🦴 Orthopedics</a>
                    <a href="{{ route('doctors.index', ['specialization' => 'Gynecology']) }}" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 transition-colors {{ request('specialization') === 'Gynecology' ? 'bg-blue-600 text-white font-bold' : '' }}">🌸 Gynecology</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Results Header & Active Filters --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <span>Doctor Directory Results</span>
                <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ $doctors->total() }} Doctor{{ $doctors->total() !== 1 ? 's' : '' }} Found
                </span>
            </h2>
            <p class="text-xs text-slate-500">Verified medical professionals practicing at accredited healthcare centers.</p>
        </div>

        <div class="flex items-center gap-2">
            @if (request()->hasAny(['q', 'specialization', 'hospital_id', 'emergency', 'city']))
                <a href="{{ route('doctors.index') }}" class="text-xs font-bold text-red-600 hover:underline">
                    ✕ Clear Filters
                </a>
            @endif

            <div class="flex items-center gap-1.5 text-xs">
                <span class="text-slate-400">Sort:</span>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" class="px-2.5 py-1 rounded-lg {{ request('sort', 'popular') === 'popular' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">Featured</a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" class="px-2.5 py-1 rounded-lg {{ request('sort') === 'rating' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' }}">Top Rated</a>
            </div>
        </div>
    </div>

    {{-- Doctor Cards Grid --}}
    @if ($doctors->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($doctors as $doctor)
                <x-scroll-reveal :delay="$loop->index * 100" class="h-full">
                    <div class="h-full bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                        
                        <div class="space-y-3">
                            {{-- Badges Header --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold border {{ $doctor->getSpecialtyBadgeColor() }}">
                                    {{ $doctor->specialization }}
                                </span>

                                @if ($doctor->is_emergency_available)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-600 text-white text-[10px] font-black uppercase tracking-wider animate-pulse">
                                        🚨 Emergency Duty
                                    </span>
                                @elseif ($doctor->isVisitingToday())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-bold">
                                        ● Visiting Today
                                    </span>
                                @endif
                            </div>

                            {{-- Doctor Name & Hospital Affiliation --}}
                            <div>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white hover:text-blue-600 leading-snug">
                                    <a href="{{ route('doctors.show', $doctor->slug) }}">
                                        {{ $doctor->name }}
                                    </a>
                                </h3>
                                @if ($doctor->designation || $doctor->department)
                                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 mt-0.5">
                                        {{ $doctor->designation }}{{ $doctor->department ? ' • ' . $doctor->department : '' }}
                                    </p>
                                @endif

                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">
                                    🎓 {{ $doctor->qualifications }}
                                </p>
                            </div>

                            {{-- Affiliated Hospital Box --}}
                            @if ($doctor->resource)
                                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-1">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                                        <svg class="w-3.5 h-3.5 text-red-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v12M6 12h12"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
                                        <a href="{{ route('resources.show', $doctor->resource->slug) }}" target="_blank" class="hover:text-red-600 truncate">
                                            {{ $doctor->resource->name }}
                                        </a>
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate pl-5">
                                        📍 {{ $doctor->resource->address }}, {{ $doctor->resource->city }}
                                    </p>
                                </div>
                            @endif

                            {{-- Visiting Schedule Box --}}
                            <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400 pt-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                        Visiting Days:
                                    </span>
                                    <span class="font-bold text-slate-900 dark:text-white text-right">
                                        @if (!empty($doctor->visiting_days))
                                            {{ implode(', ', (array)$doctor->visiting_days) }}
                                        @else
                                            Daily / On-Call
                                        @endif
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="font-medium flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                                        Visiting Hours:
                                    </span>
                                    <span class="font-bold text-slate-900 dark:text-white">
                                        {{ $doctor->visiting_hours ?: 'Standard Hours' }}
                                    </span>
                                </div>

                                @if ($doctor->room_number)
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">🏥 Chamber:</span>
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $doctor->room_number }}</span>
                                    </div>
                                @endif

                                @if ($doctor->consultation_fee)
                                    <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800">
                                        <span class="font-medium">Consultation Fee:</span>
                                        <span class="font-black text-emerald-600 dark:text-emerald-400">৳ {{ number_format($doctor->consultation_fee) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Actions: Rating & Instant Call Hotline --}}
                        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1 text-amber-500 text-xs font-bold">
                                    <span>★ {{ number_format($doctor->rating, 1) }}</span>
                                    <span class="text-slate-400 font-normal">({{ $doctor->total_reviews }} reviews)</span>
                                </div>

                                <a href="{{ route('doctors.show', $doctor->slug) }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                                    <span>Doctor Profile</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>

                            @php $phone = $doctor->getCleanPhone(); @endphp
                            <a href="tel:{{ $phone }}" class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all hover:scale-[1.01]">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <span>Call Appointment: {{ $doctor->appointment_phone ?: optional($doctor->resource)->phone }}</span>
                            </a>
                        </div>

                    </div>
                </x-scroll-reveal>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if ($doctors->hasPages())
            <div class="pt-6">
                {{ $doctors->links() }}
            </div>
        @endif

    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 max-w-xl mx-auto">
            <div class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto text-2xl font-black">
                🩺
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Specialist Doctors Found</h3>
            <p class="text-xs text-slate-500">We couldn't find any doctor matching your specific search filter criteria. Try adjusting your specialty or search query.</p>
            <a href="{{ route('doctors.index') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-xs font-bold">
                Reset All Filters
            </a>
        </div>
    @endif

</div>
@endsection
