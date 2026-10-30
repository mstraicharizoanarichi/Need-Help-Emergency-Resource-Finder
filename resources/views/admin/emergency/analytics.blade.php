@extends('layouts.admin')

@section('title', 'Emergency Response Analytics & Heatmaps – Need Help Admin')

@section('content')
<div class="space-y-6 pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-indigo-500 animate-ping"></span>
                Emergency Response Analytics & Quality Audit
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Real-time incident density, dispatch latency metrics, and SLA compliance across Bangladesh.
            </p>
        </div>

        <!-- Period Selector Filter -->
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 uppercase">Period:</span>
            <div class="inline-flex rounded-xl bg-slate-200 dark:bg-slate-800 p-1 border border-slate-300 dark:border-slate-700">
                @foreach ([7 => '7D', 14 => '14D', 30 => '30D', 90 => '90D'] as $d => $label)
                    <a href="{{ route('admin.emergency.analytics', ['days' => $d]) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $days === $d ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 4 Primary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Total Incidents -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-indigo-500"></div>
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Total Incidents</span>
                <span class="text-indigo-500 text-base">📊</span>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white mt-2">
                {{ number_format($summary['total_incidents']) }}
            </div>
            <div class="mt-2 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $summary['resolved_incidents'] }} Resolved</span>
                <span>•</span>
                <span>{{ $summary['sos_incidents'] }} One-Tap SOS</span>
            </div>
        </div>

        <!-- Average Dispatch Latency -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Avg Dispatch Latency</span>
                <span class="text-amber-500 text-base">⏱️</span>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white mt-2">
                {{ $summary['avg_dispatch_latency_minutes'] }} <span class="text-base font-medium text-slate-500">min</span>
            </div>
            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                AI triage intake to operator dispatch
            </div>
        </div>

        <!-- Average Response Arrival Time -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>Avg Unit Arrival Time</span>
                <span class="text-blue-500 text-base">🚑</span>
            </div>
            <div class="text-3xl font-black text-slate-900 dark:text-white mt-2">
                {{ $summary['avg_response_time_minutes'] }} <span class="text-base font-medium text-slate-500">min</span>
            </div>
            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                Dhaka city & regional travel average
            </div>
        </div>

        <!-- SLA Compliance Rate -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                <span>SLA Target Compliance</span>
                <span class="text-emerald-500 text-base">🎯</span>
            </div>
            <div class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2">
                {{ $summary['sla_compliance_rate'] }}%
            </div>
            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                P1 &le; 18 min, P2 &le; 28 min target
            </div>
        </div>
    </div>

    <!-- GIS Incident Heatmap & Regional Distribution Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Spatial GIS Heatmap (Left 2 cols) -->
        <div class="lg:col-span-2 p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🗺️</span> Bangladesh Emergency Density Radar
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Spatial clustering of reported incidents across metropolitan zones</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">
                    {{ count($heatmapPoints) }} Incidents Plotted
                </span>
            </div>

            <!-- Leaflet Heatmap Container -->
            <div id="analytics-heatmap" class="w-full h-96 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 relative z-0"></div>

            <div class="mt-4 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-600 inline-block"></span> P1 Critical</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span> P2 Serious</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> P3 Standard</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-purple-600 inline-block"></span> MCI Mass Casualty</span>
                </div>
                <span>Default center: Dhaka Metropolitan Area</span>
            </div>
        </div>

        <!-- Hotspots by District / Division (Right 1 col) -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 flex items-center gap-2">
                <span>📍</span> Hotspot Districts
            </h3>
            <p class="text-xs text-slate-500 mb-4">Incidents aggregated by administrative district</p>

            <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                @forelse ($summary['by_district'] as $item)
                    @php
                        $districtPct = round(($item['count'] / max(1, $summary['total_incidents'])) * 100);
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $item['district'] }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-20 bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                <div class="bg-indigo-600 h-full rounded-full" @style(["width: {$districtPct}%"])></div>
                            </div>
                            <span class="text-xs font-black text-slate-900 dark:text-white w-8 text-right">{{ $item['count'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500">
                        No district incident records for this period.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Triage Breakdown & Incident Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Breakdown by Incident Type -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 flex items-center gap-2">
                <span>🚨</span> Incident Type Distribution
            </h3>
            <p class="text-xs text-slate-500 mb-4">AI-classified emergency categories</p>

            <div class="space-y-3">
                @forelse ($summary['by_type'] as $type => $count)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">
                                @switch($type)
                                    @case('CARDIAC_STROKE') 🫀 @break
                                    @case('ROAD_ACCIDENT') 🚗 @break
                                    @case('TRAUMA') 🩸 @break
                                    @case('FIRE') 🔥 @break
                                    @case('POLICE') 👮 @break
                                    @case('RESPIRATORY') 🫁 @break
                                    @default ⚠️
                                @endswitch
                            </span>
                            <span class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ str_replace('_', ' ', $type) }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-slate-200 dark:bg-slate-700 text-slate-900 dark:text-white">
                            {{ $count }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500">
                        No category records available for this period.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Severity Triage Level Distribution -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 flex items-center gap-2">
                <span>🩺</span> Triage Severity Ratio
            </h3>
            <p class="text-xs text-slate-500 mb-4">Priority classification across active dispatches</p>

            <div class="grid grid-cols-2 gap-4">
                @php
                    $p1 = $summary['by_severity']['P1'] ?? 0;
                    $p2 = $summary['by_severity']['P2'] ?? 0;
                    $p3 = $summary['by_severity']['P3'] ?? 0;
                    $p4 = $summary['by_severity']['P4'] ?? 0;
                @endphp

                <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50">
                    <span class="text-xs font-bold uppercase tracking-wider text-red-700 dark:text-red-400">P1 Critical</span>
                    <div class="text-2xl font-black text-red-600 dark:text-red-400 mt-1">{{ $p1 }}</div>
                    <p class="text-[11px] text-red-600/80 dark:text-red-400/80 mt-1">Life-Threatening (&le; 18 min SLA)</p>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">P2 Serious</span>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $p2 }}</div>
                    <p class="text-[11px] text-amber-600/80 dark:text-amber-400/80 mt-1">Urgent Care (&le; 28 min SLA)</p>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">P3 Standard</span>
                    <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ $p3 }}</div>
                    <p class="text-[11px] text-blue-600/80 dark:text-blue-400/80 mt-1">Semi-Urgent Transfer</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">P4 Minor / Other</span>
                    <div class="text-2xl font-black text-slate-800 dark:text-slate-200 mt-1">{{ $p4 }}</div>
                    <p class="text-[11px] text-slate-500 mt-1">Routine Non-Emergency</p>
                </div>
            </div>

            <!-- MCI Protocol Badge -->
            <div class="mt-4 p-3 rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-900/50 flex items-center justify-between text-xs">
                <span class="font-bold text-purple-900 dark:text-purple-300">MCI Major Incidents Handled:</span>
                <span class="px-2.5 py-0.5 rounded-full font-black bg-purple-200 dark:bg-purple-900 text-purple-900 dark:text-purple-200">
                    {{ $summary['mci_incidents'] }} Events
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Raw Heatmap Data JSON Island -->
<script type="application/json" id="heatmap-points-data">
    {!! json_encode($heatmapPoints) !!}
</script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var mapContainer = document.getElementById('analytics-heatmap');
        if (!mapContainer || typeof L === 'undefined') return;

        var points = [];
        try {
            var dataEl = document.getElementById('heatmap-points-data');
            if (dataEl) {
                points = JSON.parse(dataEl.textContent);
            }
        } catch (e) {
            console.error('Failed to parse heatmap points:', e);
        }

        // Center on Dhaka by default (23.8103, 90.4125)
        var map = L.map('analytics-heatmap').setView([23.8103, 90.4125], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Plot pins/clusters
        points.forEach(function (pt) {
            var color = '#3b82f6';
            if (pt.severity === 'P1') color = '#dc2626';
            else if (pt.severity === 'P2') color = '#f59e0b';
            if (pt.weight >= 1.2) color = '#7c3aed'; // MCI

            var marker = L.circleMarker([pt.lat, pt.lng], {
                radius: pt.severity === 'P1' ? 9 : 7,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            marker.bindPopup(
                '<strong>' + pt.reference + '</strong><br>' +
                'Type: ' + pt.type + '<br>' +
                'Severity: ' + pt.severity
            );
        });
    });
</script>
@endpush
