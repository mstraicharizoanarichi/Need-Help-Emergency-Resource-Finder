@extends('layouts.provider')

@section('title', 'Emergency Dispatches – Provider Portal')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                <span>Emergency Dispatch Operations</span>
                <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Real-time incident assignments for your fleet and facilities. Fast responder deployment saves lives.
            </p>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex items-center gap-1.5 p-1 bg-slate-200 dark:bg-slate-800 rounded-xl">
            <a href="{{ route('provider.dispatches.index', ['status' => 'active']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold {{ $filter === 'active' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }} transition">
                Active Assignments
            </a>
            <a href="{{ route('provider.dispatches.index', ['status' => 'completed']) }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold {{ $filter === 'completed' ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }} transition">
                Completed / Archive
            </a>
        </div>
    </div>

    {{-- Dispatch Cards List --}}
    @if($dispatches->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-800">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3 text-2xl">
                🚑
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">No {{ ucfirst($filter) }} Emergency Dispatches</h3>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mt-1">
                {{ $filter === 'active' ? 'Your vehicles and facilities are currently on standby. New incident assignments will appear here automatically.' : 'No concluded dispatches in archive.' }}
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($dispatches as $dispatch)
                @php
                    $incident = $dispatch->incident;
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
                    
                    {{-- Card Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center flex-wrap gap-2.5">
                            <span class="font-mono text-sm font-black text-rose-600 dark:text-rose-400">
                                {{ $incident->public_reference }}
                            </span>

                            <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase {{ $incident->getSeverityColorClasses() }}">
                                Priority {{ $incident->severity_level }}
                            </span>

                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $dispatch->getStatusBadgeClasses() }}">
                                Dispatch: {{ $dispatch->status }}
                            </span>

                            @if($incident->is_sos)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-red-600 text-white">
                                    ONE-TAP SOS
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-slate-400 flex items-center gap-3">
                            <span>Dispatched: {{ $dispatch->dispatched_at?->diffForHumans() }}</span>
                            <a href="{{ route('emergency.show', $incident->public_reference) }}" target="_blank" class="text-blue-600 hover:underline font-semibold flex items-center gap-1">
                                <span>Citizen Live View</span>
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Main Details Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Incident Description & Location --}}
                        <div class="md:col-span-2 space-y-2">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Assigned Unit</span>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $dispatch->resource->name }} ({{ $dispatch->resource->category?->name }})
                                </p>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Incident Situation</span>
                                <p class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed font-medium">
                                    {{ $incident->description ?: 'Instant emergency assistance requested.' }}
                                </p>
                                @if($incident->ai_summary)
                                    <p class="text-xs text-rose-700 dark:text-rose-400 mt-1 italic">
                                        💡 AI Brief: {{ $incident->ai_summary }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center flex-wrap gap-4 pt-1 text-xs text-slate-600 dark:text-slate-400">
                                <div>
                                    <strong class="text-slate-800 dark:text-slate-200">Location:</strong>
                                    {{ $incident->address ?: 'GPS Coordinates reported' }}
                                </div>
                                @if($incident->reporter_phone)
                                    <div>
                                        <strong class="text-slate-800 dark:text-slate-200">Caller:</strong>
                                        <a href="tel:{{ $incident->reporter_phone }}" class="text-blue-600 font-bold hover:underline">
                                            {{ $incident->reporter_name }} ({{ $incident->reporter_phone }})
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Match Metrics Box --}}
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 font-bold uppercase">Match Score</span>
                                <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ $dispatch->match_score }} / 100</span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 font-bold uppercase">Estimated Distance</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $dispatch->estimated_distance_km }} km</span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-400 font-bold uppercase">Traffic ETA</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">~{{ round($dispatch->estimated_eta_minutes) }} Mins</span>
                            </div>

                            @if($dispatch->actual_arrival_minutes)
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-200 dark:border-slate-700">
                                    <span class="text-slate-400 font-bold uppercase">Actual Arrival</span>
                                    <span class="font-black text-indigo-600 dark:text-indigo-400">{{ $dispatch->actual_arrival_minutes }} Mins</span>
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- Actions Bar --}}
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                        
                        {{-- External Map Directions --}}
                        @if($incident->latitude && $incident->longitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $incident->latitude }},{{ $incident->longitude }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-red-600 transition">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                                Navigate on Google Maps
                            </a>
                        @endif

                        {{-- Dispatch Milestone Buttons --}}
                        <div class="flex items-center flex-wrap gap-2 ml-auto">
                            @if($dispatch->status === 'PENDING')
                                <form action="{{ route('provider.dispatches.respond', $dispatch) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                                        ✓ Accept & Deploy
                                    </button>
                                </form>

                                <form action="{{ route('provider.dispatches.respond', $dispatch) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to decline this dispatch? The incident will be re-queued to the next nearest unit.')">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="rejection_reason" value="Vehicle temporarily unavailable">
                                    <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                                        ✕ Decline
                                    </button>
                                </form>
                            @elseif($dispatch->status === 'ACCEPTED')
                                <form action="{{ route('provider.dispatches.status', $dispatch) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="EN_ROUTE">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition flex items-center gap-1.5">
                                        <span>🚑 Departed / En Route (রওনা দিয়েছি)</span>
                                    </button>
                                </form>
                            @elseif($dispatch->status === 'EN_ROUTE')
                                <form action="{{ route('provider.dispatches.status', $dispatch) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="ON_SCENE">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition flex items-center gap-1.5">
                                        <span>📍 Arrived on Scene (পৌঁছেছি)</span>
                                    </button>
                                </form>
                            @elseif($dispatch->status === 'ON_SCENE')
                                <form action="{{ route('provider.dispatches.status', $dispatch) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="TRANSPORTING">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black bg-cyan-600 hover:bg-cyan-700 text-white shadow-sm transition flex items-center gap-1.5">
                                        <span>🏥 Transporting Patient (হাসপাতালে নেওয়া হচ্ছে)</span>
                                    </button>
                                </form>
                            @elseif($dispatch->status === 'TRANSPORTING')
                                <form action="{{ route('provider.dispatches.status', $dispatch) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="COMPLETED">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition flex items-center gap-1.5">
                                        <span>✓ Mission Completed (সেবা সম্পন্ন)</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                    </div>

                </div>
            @endforeach

            <div class="pt-2">
                {{ $dispatches->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
