@extends('layouts.app')

@section('title', "{$doctor->name} – {$doctor->specialization} Specialist")
@section('meta_description', "Consult {$doctor->name}, {$doctor->specialization} specialist at {$hospital->name}, {$hospital->city}. Visiting days: " . implode(', ', (array)($doctor->visiting_days ?? [])) . ". Appointment: " . ($doctor->appointment_phone ?: $hospital->phone))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
        <a href="{{ route('home') }}" class="hover:text-slate-800 dark:hover:text-white">Home</a>
        <span>/</span>
        <a href="{{ route('doctors.index') }}" class="hover:text-slate-800 dark:hover:text-white">Doctors</a>
        <span>/</span>
        <a href="{{ route('doctors.index', ['specialization' => $doctor->specialization]) }}" class="hover:text-slate-800 dark:hover:text-white">{{ $doctor->specialization }}</a>
        <span>/</span>
        <span class="text-slate-800 dark:text-slate-200 font-semibold truncate">{{ $doctor->name }}</span>
    </nav>

    {{-- Doctor Hero Profile Card --}}
    <x-scroll-reveal>
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                {{-- Doctor Avatar or Initial Icon --}}
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-3xl font-black shadow-lg shadow-blue-500/20 shrink-0">
                    {{ strtoupper(substr($doctor->name, 0, 1)) }}
                </div>

                <div class="space-y-2 max-w-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border {{ $doctor->getSpecialtyBadgeColor() }}">
                            {{ $doctor->specialization }}
                        </span>

                        @if ($doctor->is_emergency_available)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-600 text-white text-xs font-black uppercase tracking-wider animate-pulse">
                                🚨 On Emergency Duty Now
                            </span>
                        @elseif ($doctor->isVisitingToday())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-xs font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Visiting Today
                            </span>
                        @endif

                        <div class="inline-flex items-center gap-1 text-amber-500 text-xs font-bold px-2.5 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900">
                            <span>★ {{ number_format($doctor->rating, 1) }}</span>
                            <span class="text-slate-400 font-normal">({{ $doctor->total_reviews }} reviews)</span>
                        </div>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white leading-tight">
                        {{ $doctor->name }}
                    </h1>

                    @if ($doctor->designation || $doctor->department)
                        <p class="text-sm sm:text-base font-semibold text-slate-700 dark:text-slate-300">
                            {{ $doctor->designation }}{{ $doctor->department ? ' • ' . $doctor->department : '' }}
                        </p>
                    @endif

                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        🎓 <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $doctor->qualifications }}</span>
                        @if ($doctor->experience_years)
                            <span class="mx-1.5">•</span>
                            <span>{{ $doctor->experience_years }}+ Years Experience</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Emergency Action CTA --}}
            <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0 lg:w-72">
                @php $phone = $doctor->getCleanPhone(); @endphp
                <a href="tel:{{ $phone }}" class="w-full py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-base sm:text-lg shadow-lg shadow-blue-600/30 flex items-center justify-center gap-3 transition-all hover:scale-[1.02]">
                    <svg class="w-6 h-6 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>CALL APPOINTMENT</span>
                </a>

                @if ($hospital)
                    <a href="{{ route('resources.show', $hospital->slug) }}" class="w-full py-3 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-white font-bold text-xs flex items-center justify-center gap-2 transition-all">
                        <svg class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v12M6 12h12"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
                        <span>View Hospital Profile & Facilities</span>
                    </a>
                @endif
            </div>

        </div>
    </div>
    </x-scroll-reveal>

    {{-- Detailed Info Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left 2 Columns: Visiting Schedule, Bio, Chamber --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Weekly Visiting Schedule & Chamber Info --}}
            <x-scroll-reveal delay="100">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        Visiting Schedule & Consultation Details
                    </h2>
                    @if ($doctor->consultation_fee)
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            Fee: ৳ {{ number_format($doctor->consultation_fee) }}
                        </span>
                    @endif
                </div>

                {{-- Schedule Summary Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                        <p class="text-slate-400 text-xs font-bold uppercase">Visiting Days</p>
                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">
                            @if (!empty($doctor->visiting_days))
                                {{ implode(', ', (array)$doctor->visiting_days) }}
                            @else
                                Daily / On-Call
                            @endif
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                        <p class="text-slate-400 text-xs font-bold uppercase">Visiting Time</p>
                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">
                            {{ $doctor->visiting_hours ?: 'Standard Ward Schedule' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-1">
                        <p class="text-slate-400 text-xs font-bold uppercase">Chamber / Room</p>
                        <p class="text-sm font-extrabold text-slate-900 dark:text-white">
                            {{ $doctor->room_number ?: 'Hospital OPD / Emergency Ward' }}
                        </p>
                    </div>
                </div>

                {{-- Weekly Days Table --}}
                @php
                    $allDays = [
                        'Sun' => 'Sunday',
                        'Mon' => 'Monday',
                        'Tue' => 'Tuesday',
                        'Wed' => 'Wednesday',
                        'Thu' => 'Thursday',
                        'Fri' => 'Friday',
                        'Sat' => 'Saturday',
                    ];
                    $doctorDays = (array)($doctor->visiting_days ?? []);
                    $todayShort = now()->format('D');
                @endphp

                <div class="space-y-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Weekly Day-by-Day Availability</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 text-center text-xs">
                        @foreach ($allDays as $short => $full)
                            @php
                                $isAvailableDay = empty($doctorDays) || in_array($short, $doctorDays) || in_array($full, $doctorDays);
                                $isToday = ($short === $todayShort);
                            @endphp
                            <div class="p-3 rounded-2xl border {{ $isAvailableDay ? ($isToday ? 'bg-blue-50 border-blue-300 dark:bg-blue-950/60 dark:border-blue-700 text-blue-900 dark:text-blue-200 font-black ring-2 ring-blue-500' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200/80 dark:border-slate-700 text-slate-900 dark:text-white font-bold') : 'bg-slate-50/40 dark:bg-slate-900/40 border-slate-100 dark:border-slate-800/50 text-slate-400 opacity-60' }}">
                                <p class="text-[11px] uppercase tracking-wider">{{ $short }}</p>
                                <p class="text-xs font-black mt-1">
                                    {{ $isAvailableDay ? 'Open' : 'Off' }}
                                </p>
                                @if ($isToday)
                                    <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold block mt-0.5">Today</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            </x-scroll-reveal>

            {{-- Doctor Biography / Professional Background --}}
            <x-scroll-reveal delay="150">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/></svg>
                    Professional Profile & Specialization
                </h2>

                <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    @if ($doctor->bio)
                        <p>{{ $doctor->bio }}</p>
                    @else
                        <p>
                            {{ $doctor->name }} is a specialist consultant in <strong>{{ $doctor->specialization }}</strong> practicing at <strong>{{ optional($doctor->resource)->name }}</strong>. With qualifications in <em>{{ $doctor->qualifications }}</em>{{ $doctor->experience_years ? ' and over ' . $doctor->experience_years . ' years of clinical experience' : '' }}, {{ $doctor->name }} provides patient consultations and emergency care in {{ optional($doctor->resource)->city }}.
                        </p>
                    @endif

                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-400 block font-medium">Department</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $doctor->department ?: $doctor->specialization }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-medium">Designation</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $doctor->designation ?: 'Consultant' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            </x-scroll-reveal>

        </div>

        {{-- Right Column: Affiliated Hospital Card & Other Doctors --}}
        <div class="space-y-6">
            
            {{-- Affiliated Hospital Card --}}
            @if ($hospital)
                <x-scroll-reveal delay="100">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v12M6 12h12"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>
                        <span>Affiliated Hospital / Chamber</span>
                    </div>

                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900 dark:text-white">
                            <a href="{{ route('resources.show', $hospital->slug) }}" class="hover:text-red-600">
                                {{ $hospital->name }}
                            </a>
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            📍 {{ $hospital->address }}{{ $hospital->area ? ', ' . $hospital->area : '' }}, {{ $hospital->city }}
                        </p>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Hospital Category:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ optional($hospital->category)->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Hospital Phone:</span>
                            <a href="tel:{{ $hospital->getCleanPhone() }}" class="font-bold text-blue-600 hover:underline">{{ $hospital->phone }}</a>
                        </div>
                        @if ($hospital->emergency_phone)
                            <div class="flex items-center justify-between text-red-600 font-bold">
                                <span>24/7 Emergency:</span>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hospital->emergency_phone) }}" class="hover:underline">{{ $hospital->emergency_phone }}</a>
                            </div>
                        @endif
                    </div>

                    <div class="pt-2 flex flex-col gap-2">
                        <a href="{{ route('resources.show', $hospital->slug) }}" class="w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs text-center">
                            View Full Hospital Directory &rarr;
                        </a>
                        <a href="{{ $hospital->getDirectionsUrl() }}" target="_blank" rel="noopener noreferrer" class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-xs text-center flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                            <span>Get GPS Directions</span>
                        </a>
                    </div>
                </div>
                </x-scroll-reveal>
            @endif

            {{-- Other Specialist Doctors in Area --}}
            @if ($otherDoctors->count() > 0)
                <x-scroll-reveal delay="200">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">
                        Other Doctors in {{ $doctor->specialization }}
                    </h3>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($otherDoctors as $other)
                            <div class="py-3 space-y-1">
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white hover:text-blue-600">
                                    <a href="{{ route('doctors.show', $other->slug) }}">
                                        {{ $other->name }}
                                    </a>
                                </h4>
                                <p class="text-[11px] text-slate-500 line-clamp-1">
                                    {{ $other->specialization }} • {{ optional($other->resource)->name }}
                                </p>
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $other->is_emergency_available ? '🚨 Emergency' : ($other->isVisitingToday() ? '● Today' : 'Scheduled') }}
                                    </span>
                                    <a href="{{ route('doctors.show', $other->slug) }}" class="text-[11px] text-blue-600 hover:underline font-semibold">
                                        View &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                </x-scroll-reveal>
            @endif

        </div>

    </div>

</div>
@endsection
