@extends('layouts.app')

@section('title', 'Emergency Status: ' . $incident->public_reference . ' – Need Help')

@section('content')
<div class="py-8 bg-slate-50 dark:bg-slate-950 min-h-[80vh]" x-data="emergencyTracker('{{ $incident->public_reference }}', '{{ $incident->status }}')">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Urgent 999 Life-Safety Alert Banner --}}
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white shadow-xl shadow-red-500/20 flex flex-col md:flex-row items-center justify-between gap-4 border border-red-400/30 animate-pulse-slow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/30 text-2xl font-black">
                    🚨
                </div>
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        Life in Imminent Danger?
                        <span class="text-xs bg-white text-red-700 font-extrabold uppercase px-2 py-0.5 rounded-full">National Hotline</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-red-100">
                        Bangladesh Emergency Helpline: Dial <strong>999</strong> immediately for Police, Fire, or Ambulance service dispatch.
                    </p>
                </div>
            </div>
            <a href="tel:999" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-red-700 hover:bg-red-50 rounded-xl font-bold shadow-lg transition-all transform active:scale-95 text-base shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Call 999 Now
            </a>
        </div>

        {{-- Incident Overview Header Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-800">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <div class="flex items-center flex-wrap gap-2.5 mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $incident->getSeverityColorClasses() }}">
                            <span class="w-2 h-2 rounded-full bg-current mr-1.5 animate-ping"></span>
                            Priority {{ $incident->severity_level }} Emergency
                        </span>

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $incident->getStatusColorClasses() }}" x-text="currentStatusLabel">
                            {{ str_replace('_', ' ', $incident->status) }}
                        </span>

                        @if($incident->is_sos)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500 text-white shadow-sm">
                                ONE-TAP SOS
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                        Incident: <span class="font-mono text-red-600 dark:text-red-400" x-text="reference">{{ $incident->public_reference }}</span>
                        <button type="button" @click="copyReference()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm p-1 rounded-md transition" title="Copy Reference">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </h1>

                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Reported {{ $incident->reported_at?->format('M d, Y h:i A') }} ({{ $incident->reported_at?->diffForHumans() }})
                        @if($incident->address)
                            • <span class="text-slate-700 dark:text-slate-300">{{ $incident->address }}</span>
                        @endif
                    </p>
                </div>

                <div class="flex items-center flex-wrap gap-3">
                    <button type="button" @click="shareTracking()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        Share Live Tracking
                    </button>

                    @if($incident->isActive())
                        <button type="button" @click="cancelModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-sm font-semibold hover:bg-rose-100 dark:hover:bg-rose-900/60 transition">
                            Cancel Incident
                        </button>
                    @endif
                </div>
            </div>

            {{-- Live Pulse Status Box --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Emergency Category</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white mt-1">{{ str_replace('_', ' ', $incident->emergency_type) }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">{{ $incident->required_service ?: 'Emergency Response' }}</div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Estimated Responder Arrival</div>
                    <div class="text-lg font-bold text-slate-900 dark:text-white mt-1" x-text="etaDisplay">
                        @if($incident->activeDispatch?->estimated_eta_minutes)
                            ~{{ round($incident->activeDispatch->estimated_eta_minutes) }} Mins
                        @else
                            Calculating ETA...
                        @endif
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5">Real-time traffic adjusted</div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Live System Sync</div>
                    <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Active Radar
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5" x-text="'Last updated: ' + lastSyncTime">Syncing...</div>
                </div>
            </div>
        </div>

        {{-- Main Content Grid: Responder & Map (Left), Timeline (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left 2 Columns: Responder Card & Map --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Assigned Responder Card --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-between">
                        <span>Assigned Emergency Responder</span>
                        <span class="text-xs font-semibold text-slate-500" x-show="responder">Direct Dispatch</span>
                    </h3>

                    <template x-if="responder">
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-blue-50/50 to-indigo-50/30 dark:from-slate-800 dark:to-slate-800/60 border border-blue-100 dark:border-slate-700 space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                                        🚑
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-base sm:text-lg text-slate-900 dark:text-white" x-text="responder.name"></h4>
                                        <p class="text-xs text-slate-500 dark:text-slate-400" x-text="responder.category + ' • ' + (responder.address || 'Dhaka, Bangladesh')"></p>
                                    </div>
                                </div>

                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300" x-text="responder.status || 'EN ROUTE'"></span>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-3 pt-3 border-t border-blue-100/60 dark:border-slate-700">
                                <a :href="'tel:' + responder.phone" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm shadow-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span x-text="'Call Responder: ' + (responder.phone || 'Direct Line')"></span>
                                </a>

                                <span class="text-xs text-slate-500 dark:text-slate-400" x-show="responder.eta_minutes">
                                    Estimated Arrival: <strong class="text-slate-800 dark:text-slate-200" x-text="'~' + Math.round(responder.eta_minutes) + ' minutes'"></strong>
                                </span>
                            </div>
                        </div>
                    </template>

                    <template x-if="!responder">
                        <div class="text-center py-8 px-4 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                            <div class="relative w-16 h-16 mx-auto mb-3">
                                <div class="absolute inset-0 rounded-full bg-red-400/20 animate-ping"></div>
                                <div class="relative w-16 h-16 rounded-full bg-red-100 dark:bg-red-950/60 flex items-center justify-center text-red-600 dark:text-red-400 text-2xl">
                                    📡
                                </div>
                            </div>
                            <h4 class="font-bold text-slate-800 dark:text-white text-base">Searching Nearest Responders</h4>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-1">
                                Our AI dispatch engine and human operator are matching the closest active ambulances, fire units, and trauma facilities. Please keep your phone line open.
                            </p>
                            <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 px-3 py-1.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-400 animate-pulse"></span>
                                Standby for responder confirmation
                            </div>
                        </div>
                    </template>

                    {{-- Community First Responder (CFR) Bystander Support --}}
                    @if(isset($activeCfr) && $activeCfr)
                        <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50/50 dark:from-emerald-950/30 dark:to-slate-800 border border-emerald-200 dark:border-emerald-800 space-y-3 mt-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                                        🦺
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-slate-900 dark:text-white text-sm sm:text-base">{{ $activeCfr->responder?->name }}</h4>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                                {{ $activeCfr->status === 'ON_SCENE' ? 'ON SCENE' : 'RUSHING TO VICTIM' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-600 dark:text-slate-400">
                                            {{ str_replace('_', ' ', $activeCfr->responder?->certification_level ?? 'Certified Responder') }} 
                                            @if($activeCfr->distance_km) • ~{{ $activeCfr->distance_km }} km away @endif
                                        </p>
                                    </div>
                                </div>
                                @if($activeCfr->responder?->phone)
                                    <a href="tel:{{ $activeCfr->responder->phone }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow transition">
                                        Call CFR
                                    </a>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                <span class="text-[11px] font-semibold text-slate-500">Equipped Skills:</span>
                                @foreach((array) ($activeCfr->responder?->skills ?? []) as $sk)
                                    <span class="px-2 py-0.5 rounded bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-[11px] font-medium text-slate-700 dark:text-slate-300">
                                        {{ $sk }}
                                    </span>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-emerald-800 dark:text-emerald-300 bg-emerald-100/60 dark:bg-emerald-950/40 p-2 rounded-lg">
                                ℹ️ A trained volunteer nearby has been dispatched on foot/bicycle to provide immediate CPR / bleeding control before the ambulance arrives.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Pre-Arrival Medical Advisory Box --}}
                @if(isset($advisory))
                    <div class="bg-gradient-to-br from-amber-500/10 via-red-500/5 to-slate-50 dark:from-amber-950/30 dark:via-red-950/20 dark:to-slate-900 rounded-3xl p-6 border border-amber-300/60 dark:border-amber-900/50 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-extrabold text-amber-900 dark:text-amber-300 flex items-center gap-2">
                                <span>🩺</span> Pre-Arrival Medical Advisory (জরুরি প্রাথমিক নির্দেশনা)
                            </h3>
                            <span class="text-[11px] font-black uppercase px-2.5 py-0.5 rounded-full bg-amber-200 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200">
                                Life-Safety First
                            </span>
                        </div>

                        @if(!empty($advisory['critical_warning']))
                            <div class="p-3 rounded-xl bg-red-100 dark:bg-red-950/70 border border-red-200 dark:border-red-900 text-xs font-bold text-red-800 dark:text-red-300 flex items-center gap-2">
                                <span class="text-base">⚠️</span>
                                <span>{{ $advisory['critical_warning'] }}</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            {{-- Bengali Instructions --}}
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                    বাংলা নির্দেশাবলী (立即 করণীয়)
                                </h4>
                                <ul class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5 list-disc pl-4 leading-relaxed">
                                    @foreach($advisory['bn'] as $step)
                                        <li>{{ $step }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- English Instructions --}}
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                    English Guidance (First Aid)
                                </h4>
                                <ul class="text-xs text-slate-700 dark:text-slate-300 space-y-1.5 list-disc pl-4 leading-relaxed">
                                    @foreach($advisory['en'] as $step)
                                        <li>{{ $step }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Recommended Destination Hospitals --}}
                @if(isset($hospitals) && $hospitals->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                    Recommended Receiving Hospitals
                                </h3>
                                <p class="text-xs text-slate-500">
                                    Pre-screened facilities equipped with ICU, emergency operating rooms, and specialists.
                                </p>
                            </div>
                            <span class="text-xs font-bold text-slate-400">Ranked by Proximity & Capability</span>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            @foreach($hospitals as $hosp)
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">
                                                {{ $hosp['name'] }}
                                            </h4>
                                            @if($hosp['has_icu'])
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300">
                                                    ICU Ready
                                                </span>
                                            @endif
                                            @if($hosp['has_oxygen'])
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                                    Oxygen
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $hosp['address'] ?: ($hosp['area'] . ', ' . $hosp['city']) }}
                                            • <strong class="text-slate-700 dark:text-slate-300">{{ $hosp['distance_km'] }} km</strong> (~{{ round($hosp['estimated_eta_minutes']) }} mins ETA)
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        @if($hosp['phone'])
                                            <a href="tel:{{ $hosp['phone'] }}" class="px-3 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                Call Desk
                                            </a>
                                        @endif
                                        <a href="{{ $hosp['directions_url'] }}" target="_blank" class="px-3 py-2 rounded-xl text-xs font-bold border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                                            Route
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Map Container --}}
                @if($incident->latitude && $incident->longitude)
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Emergency Location Map</h3>
                        <div id="incident-map" data-lat="{{ $incident->latitude }}" data-lng="{{ $incident->longitude }}" class="w-full h-72 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700"></div>
                        <p class="text-xs text-slate-400 mt-2">
                            Coordinates: {{ number_format($incident->latitude, 5) }}, {{ number_format($incident->longitude, 5) }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Right Column: Live Timeline of Events --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center justify-between">
                        <span>Incident Timeline</span>
                        <span class="text-xs text-slate-400 font-mono">Live Audit Log</span>
                    </h3>

                    <div class="relative border-l-2 border-slate-200 dark:border-slate-800 ml-3 space-y-6 py-2">
                        <template x-for="(ev, idx) in events" :key="idx">
                            <div class="relative pl-6">
                                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-white dark:border-slate-900 bg-red-600"></div>
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-400" x-text="ev.time"></div>
                                <div class="text-sm font-bold text-slate-900 dark:text-white mt-0.5" x-text="ev.title"></div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed" x-text="ev.description"></div>
                            </div>
                        </template>

                        @if($incident->events->isEmpty())
                            <div class="pl-6 text-sm text-slate-400">
                                Incident registered. Awaiting initial milestone updates.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Citizen Guidance Card --}}
                <div class="p-6 rounded-3xl bg-slate-900 text-white shadow-sm border border-slate-800 space-y-3">
                    <h4 class="font-bold text-base flex items-center gap-2">
                        <span>🛡️</span> What To Do Right Now
                    </h4>
                    <ul class="text-xs text-slate-300 space-y-2 list-disc pl-4 leading-relaxed">
                        <li><strong>Stay in a safe place:</strong> Move away from traffic, smoke, or physical danger.</li>
                        <li><strong>Keep phone line open:</strong> Operators or responders may call to confirm landmark details.</li>
                        <li><strong>Send someone to the main road:</strong> To guide the emergency vehicle when sirens are heard.</li>
                        <li><strong>Prepare patient info:</strong> Age, pre-existing conditions, or allergies if known.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Incident Modal --}}
    <div x-show="cancelModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 dark:border-slate-800" @click.away="cancelModalOpen = false">
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Cancel Emergency Incident?</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                Only cancel if this was an accidental trigger or the emergency situation has completely resolved.
            </p>

            <form action="{{ route('emergency.cancel', $incident->public_reference) }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Reason for Cancellation</label>
                    <select name="reason" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm text-slate-800 dark:text-slate-200">
                        <option value="Accidental SOS trigger / False alarm">Accidental SOS trigger / False alarm</option>
                        <option value="Arranged private help / resolved">Arranged private help / resolved</option>
                        <option value="Patient already transported">Patient already transported</option>
                        <option value="Other">Other reason</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="cancelModalOpen = false" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Keep Active
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-sm font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md">
                        Confirm Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Safe JSON Data Blocks for JS Hydration --}}
    <script id="initial-responder-data" type="application/json">
        {!! json_encode($incident->activeDispatch?->resource ? [
            'name' => $incident->activeDispatch->resource->name,
            'phone' => $incident->activeDispatch->resource->emergency_phone ?: $incident->activeDispatch->resource->phone,
            'category' => $incident->activeDispatch->resource->category?->name ?? 'Emergency Service',
            'address' => $incident->activeDispatch->resource->address,
            'eta_minutes' => $incident->activeDispatch->estimated_eta_minutes,
            'status' => $incident->activeDispatch->status,
        ] : null) !!}
    </script>
    <script id="initial-events-data" type="application/json">
        {!! json_encode($incident->events->map(fn($e) => [
            'title' => $e->title,
            'description' => $e->description,
            'type' => $e->event_type,
            'time' => $e->created_at?->diffForHumans(),
        ])) !!}
    </script>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('emergencyTracker', function (ref, initialStatus) {
            return {
                reference: ref,
                currentStatus: initialStatus,
                currentStatusLabel: initialStatus.replace(/_/g, ' '),
                cancelModalOpen: false,
                etaDisplay: 'Calculating ETA...',
                lastSyncTime: new Date().toLocaleTimeString(),
                responder: (function () {
                    try {
                        var el = document.getElementById('initial-responder-data');
                        return el ? JSON.parse(el.textContent) : null;
                    } catch (e) {
                        return null;
                    }
                })(),
                events: (function () {
                    try {
                        var el = document.getElementById('initial-events-data');
                        return el ? JSON.parse(el.textContent) : [];
                    } catch (e) {
                        return [];
                    }
                })(),
                pollTimer: null,

                init: function () {
                    this.startPolling();
                    var mapEl = document.getElementById('incident-map');
                    if (mapEl && mapEl.dataset.lat && mapEl.dataset.lng) {
                        this.initMap(parseFloat(mapEl.dataset.lat), parseFloat(mapEl.dataset.lng));
                    }
                },

            startPolling() {
                this.pollTimer = setInterval(async () => {
                    if (this.currentStatus === 'RESOLVED' || this.currentStatus === 'CANCELLED') {
                        clearInterval(this.pollTimer);
                        return;
                    }

                    try {
                        const res = await fetch(`/emergency/${this.reference}/status`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            this.currentStatus = data.status;
                            this.currentStatusLabel = data.status_label;
                            this.responder = data.responder;
                            this.events = data.events;
                            this.lastSyncTime = new Date().toLocaleTimeString();

                            if (data.responder && data.responder.eta_minutes) {
                                this.etaDisplay = '~' + Math.round(data.responder.eta_minutes) + ' Mins';
                            }
                        }
                    } catch (e) {
                        console.warn('Status poll failed:', e);
                    }
                }, 5000);
            },

            copyReference() {
                navigator.clipboard.writeText(this.reference);
                alert('Copied Incident Reference: ' + this.reference);
            },

            shareTracking() {
                if (navigator.share) {
                    navigator.share({
                        title: 'Emergency Tracking: ' + this.reference,
                        text: 'Track my emergency response status live on Need Help Bangladesh',
                        url: window.location.href,
                    }).catch(() => {});
                } else {
                    navigator.clipboard.writeText(window.location.href);
                    alert('Tracking link copied to clipboard!');
                }
            },

            initMap(lat, lng) {
                if (typeof L === 'undefined') return;
                const map = L.map('incident-map').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                const icon = L.divIcon({
                    className: 'custom-pin',
                    html: '<div style="background-color: #dc2626; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3); animation: pulse-ring 1.8s infinite;">🚨</div>',
                    iconSize: [34, 34],
                    iconAnchor: [17, 17]
                });

                L.marker([lat, lng], { icon: icon }).addTo(map)
                    .bindPopup('<strong>Emergency Location</strong><br>Reference: ' + this.reference)
                    .openPopup();
            }
        };
    });
});
</script>
@endpush
