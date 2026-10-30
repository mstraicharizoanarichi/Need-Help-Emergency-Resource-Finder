@extends('layouts.admin')

@section('title', 'Emergency Command Center (EOC) – Need Help')

@section('content')
<div class="space-y-6" x-data="emergencyCommandCenter()">

    {{-- Top Bar with Live Radar Sync Status --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    Emergency Operations Command Center (EOC)
                </h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                National triage orchestration, GIS dispatch map, and human-in-the-loop responder matching.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Sync: <span x-text="lastSync">Just now</span>
            </span>

            <a href="{{ route('emergency.report') }}" target="_blank" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm flex items-center gap-1.5 transition">
                <span>+ Log Emergency</span>
            </a>
        </div>
    </div>

    {{-- Real-Time KPI Stats Metric Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        
        {{-- Critical P1 --}}
        <div class="p-4 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 text-white shadow-md shadow-red-600/20 border border-red-500/40">
            <span class="text-[10px] font-black uppercase tracking-wider text-red-100 block">Critical P1</span>
            <span class="text-2xl sm:text-3xl font-black mt-1 block" x-text="kpis.critical_count">{{ $kpis['critical_count'] }}</span>
            <span class="text-[10px] text-red-100">Life-Threat Active</span>
        </div>

        {{-- Awaiting Dispatch --}}
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">Awaiting Dispatch</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 block" x-text="kpis.awaiting_dispatch_count">{{ $kpis['awaiting_dispatch_count'] }}</span>
            <span class="text-[10px] text-slate-400">Needs Unit Match</span>
        </div>

        {{-- En Route --}}
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block">Units En Route</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 block" x-text="kpis.active_en_route_count">{{ $kpis['active_en_route_count'] }}</span>
            <span class="text-[10px] text-slate-400">Navigating Scene</span>
        </div>

        {{-- Resolved Today --}}
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">Resolved Today</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 block" x-text="kpis.resolved_today_count">{{ $kpis['resolved_today_count'] }}</span>
            <span class="text-[10px] text-slate-400">Concluded Safe</span>
        </div>

        {{-- Available Ambulances --}}
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 block">Ambulances</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 block" x-text="kpis.available_ambulances">{{ $kpis['available_ambulances'] }}</span>
            <span class="text-[10px] text-slate-400">Fleet Ready</span>
        </div>

        {{-- Available ICU --}}
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 block">ICU Facilities</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 block" x-text="kpis.available_icu_hospitals">{{ $kpis['available_icu_hospitals'] }}</span>
            <span class="text-[10px] text-slate-400">Trauma Centers</span>
        </div>

    </div>

    {{-- Main Command Split Screen: Map (Left 55%) + Priority Queue (Right 45%) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Left: Live Interactive Emergency Incident GIS Map --}}
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                        Live Situational GIS Radar
                    </h2>
                </div>
                <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-400">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-red-600 inline-block"></span> P1 Critical</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span> P2 Urgent</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> Responders</span>
                </div>
            </div>

            <div id="command-radar-map" class="w-full h-[520px] rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-950"></div>
        </div>

        {{-- Right: Prioritized Queue & Dispatch Console --}}
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-1.5">
                        <span>Prioritized Emergency Queue</span>
                    </h2>
                    <p class="text-[11px] text-slate-400">Ordered by Priority Score (1-100) & Arrival Timestamp</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300" x-text="incidentsList.length + ' Active'">
                    {{ $incidents->total() }} Active
                </span>
            </div>

            {{-- Queue List with Smooth Scroll --}}
            <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                <template x-for="inc in incidentsList" :key="inc.id">
                    <div class="p-4 rounded-2xl border transition-all cursor-pointer hover:shadow-md"
                         :class="selectedIncidentId === inc.id ? 'border-red-500 bg-red-50/30 dark:bg-red-950/30' : 'border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 hover:border-slate-300'"
                         @click="selectIncident(inc)">
                        
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-black" :class="inc.severity === 'P1' ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400'" x-text="inc.reference"></span>
                                <span class="px-2 py-0.2 rounded-full text-[10px] font-black uppercase"
                                      :class="inc.severity === 'P1' ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                                      x-text="inc.severity + ' • ' + inc.priority + '/100'"></span>
                            </div>

                            <span class="text-[11px] font-bold uppercase px-2 py-0.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300" x-text="inc.status_label"></span>
                        </div>

                        <div class="text-xs font-bold text-slate-900 dark:text-white" x-text="inc.type.replace(/_/g, ' ')"></div>
                        
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5" x-text="inc.address || 'Dhaka coordinates locked'"></p>

                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-[11px]">
                            <span class="text-slate-400" x-text="inc.reported_time"></span>
                            <button type="button" @click.stop="openDispatchModal(inc)" class="font-bold text-red-600 dark:text-red-400 hover:underline flex items-center gap-1">
                                <span>Triage & Dispatch</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>
                </template>

                @if($incidents->isEmpty())
                    <div class="text-center py-12 text-xs text-slate-400">
                        No active emergencies in queue. Systems all clear.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Human-in-the-Loop Smart Dispatch Modal --}}
    <div x-show="dispatchModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 my-auto" @click.away="dispatchModalOpen = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Human-in-the-Loop Operator Dispatch</span>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white" x-text="'Incident: ' + (activeIncident?.reference || '')"></h3>
                </div>
                <button type="button" @click="dispatchModalOpen = false" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white">✕</button>
            </div>

            {{-- Incident Triage Brief --}}
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-1.5">
                <div class="flex items-center justify-between font-bold">
                    <span class="text-slate-800 dark:text-slate-200" x-text="activeIncident?.type"></span>
                    <span class="text-rose-600" x-text="'Priority Score: ' + activeIncident?.priority + ' / 100'"></span>
                </div>
                <p class="text-slate-600 dark:text-slate-300" x-text="recommendationsData?.incident?.summary || 'Triage summary loading...'"></p>
                <div class="text-[11px] text-slate-400" x-text="'Caller: ' + (activeIncident?.reporter_name || 'Citizen') + ' (' + (activeIncident?.reporter_phone || 'No phone') + ')'"></div>
            </div>

            {{-- Top 3 AI-Recommended Ambulances --}}
            <div class="space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center justify-between">
                    <span>Top Recommended Ambulances</span>
                    <span class="text-[10px] text-slate-400">Ranked by Haversine & Traffic</span>
                </h4>

                <template x-if="loadingRecs">
                    <div class="p-4 text-center text-xs text-slate-400 animate-pulse">
                        Calculating multi-factor score and real-time traffic ETA...
                    </div>
                </template>

                <template x-if="!loadingRecs && recommendationsData?.recommended_ambulances">
                    <div class="space-y-2">
                        <template x-for="amb in recommendationsData.recommended_ambulances" :key="amb.resource_id">
                            <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/80 flex items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white" x-text="amb.name"></span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300" x-text="amb.total_score + ' pts'"></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="amb.distance_km + ' km • ~' + Math.round(amb.estimated_eta_minutes) + ' mins ETA (' + amb.traffic_condition + ' traffic)'"></p>
                                </div>

                                <button type="button" @click="confirmDispatch(amb)" class="px-4 py-2 rounded-xl text-xs font-black bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition shrink-0">
                                    Approve & Dispatch
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Top Recommended Receiving Hospitals --}}
            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Recommended Receiving Hospitals
                </h4>

                <template x-if="recommendationsData?.recommended_hospitals">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <template x-for="hosp in recommendationsData.recommended_hospitals" :key="hosp.id">
                            <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-xs space-y-1">
                                <span class="font-bold text-slate-900 dark:text-white block truncate" x-text="hosp.name"></span>
                                <span class="text-[10px] text-slate-400 block" x-text="hosp.distance_km + ' km • ~' + Math.round(hosp.estimated_eta_minutes) + 'm'"></span>
                                <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold" :class="hosp.has_icu ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' : 'bg-slate-200 text-slate-700'" x-text="hosp.has_icu ? 'ICU Available' : 'General Bed'"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('emergencyCommandCenter', function () {
            return {
                lastSync: 'Syncing...',
                kpis: @json($kpis),
                incidentsList: @json($incidents->items()),
                selectedIncidentId: null,
                activeIncident: null,
                dispatchModalOpen: false,
                loadingRecs: false,
                recommendationsData: null,
                map: null,
                markerGroup: null,
                pollTimer: null,

                init: function () {
                    this.initMap();
                    this.startLiveFeed();
                },

                initMap: function () {
                    if (typeof L === 'undefined') return;
                    this.map = L.map('command-radar-map').setView([23.7808, 90.4152], 12);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(this.map);

                    this.markerGroup = L.layerGroup().addTo(this.map);
                    this.renderMapMarkers();
                },

                renderMapMarkers: function () {
                    if (!this.markerGroup || typeof L === 'undefined') return;
                    this.markerGroup.clearLayers();

                    var self = this;
                    this.incidentsList.forEach(function (inc) {
                        if (!inc.latitude || !inc.longitude) return;

                        var isP1 = (inc.severity === 'P1' || inc.severity_level === 'P1');
                        var color = isP1 ? '#dc2626' : '#d97706';

                        var icon = L.divIcon({
                            className: 'custom-incident-pin',
                            html: '<div style="background-color:' + color + '; width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:14px; border:2px solid white; box-shadow:0 3px 8px rgba(0,0,0,0.4);' + (isP1 ? 'animation:pulse-ring 1.5s infinite;' : '') + '">🚨</div>',
                            iconSize: [28, 28],
                            iconAnchor: [14, 14]
                        });

                        var m = L.marker([inc.latitude, inc.longitude], { icon: icon }).addTo(self.markerGroup);
                        m.bindPopup('<strong>' + (inc.reference || inc.public_reference) + '</strong><br>' + (inc.type || inc.emergency_type) + '<br>Priority: ' + (inc.priority || inc.priority_score));
                    });
                },

                startLiveFeed: function () {
                    var self = this;
                    this.pollTimer = setInterval(async function () {
                        try {
                            var res = await fetch('{{ route("admin.emergency.feed") }}', {
                                headers: { 'Accept': 'application/json' }
                            });
                            if (res.ok) {
                                var data = await res.json();
                                self.kpis = data.kpis;
                                self.incidentsList = data.incidents;
                                self.lastSync = new Date().toLocaleTimeString();
                                self.renderMapMarkers();
                            }
                        } catch (e) {
                            console.warn('Feed poll failed:', e);
                        }
                    }, 5000);
                },

                selectIncident: function (inc) {
                    this.selectedIncidentId = inc.id;
                    if (this.map && inc.latitude && inc.longitude) {
                        this.map.setView([inc.latitude, inc.longitude], 14);
                    }
                },

                openDispatchModal: async function (inc) {
                    this.activeIncident = inc;
                    this.dispatchModalOpen = true;
                    this.loadingRecs = true;
                    this.recommendationsData = null;

                    try {
                        var res = await fetch('/admin/emergency-command/' + inc.id + '/recommendations', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            this.recommendationsData = await res.json();
                        }
                    } catch (e) {
                        console.warn('Recs failed:', e);
                    } finally {
                        this.loadingRecs = false;
                    }
                },

                confirmDispatch: async function (amb) {
                    if (!confirm('Approve dispatch of ' + amb.name + ' to incident ' + this.activeIncident.reference + '?')) {
                        return;
                    }

                    try {
                        var res = await fetch('/admin/emergency-command/' + this.activeIncident.id + '/dispatch', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                resource_id: amb.resource_id,
                                distance_km: amb.distance_km,
                                estimated_eta_minutes: amb.estimated_eta_minutes,
                                match_score: amb.total_score
                            })
                        });

                        if (res.ok) {
                            alert('Dispatcher assignment confirmed! Responder unit alerted.');
                            this.dispatchModalOpen = false;
                        }
                    } catch (e) {
                        alert('Dispatch action failed.');
                    }
                }
            };
        });
    });
</script>
@endpush
