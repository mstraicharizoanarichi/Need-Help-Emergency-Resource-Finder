@extends('layouts.admin')

@section('title', 'Admin Dashboard – Need Help')
@section('page_title', 'System & Verification Overview')

@section('content')
<div class="space-y-8">

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Facilities</span>
                <p class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_resources'] }}</p>
                <span class="text-[11px] text-emerald-600 font-semibold">{{ $stats['verified_resources'] }} Verified</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 flex items-center justify-center font-black text-xl">
                🏥
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending Verification</span>
                <p class="text-3xl font-black text-amber-500 mt-1">{{ $stats['pending_verification'] }}</p>
                <a href="{{ route('admin.resources.pending') }}" class="text-[11px] text-amber-600 font-bold hover:underline">Review Queue &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 flex items-center justify-center font-black text-xl">
                ⏳
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Blood Donors</span>
                <p class="text-3xl font-black text-rose-600 mt-1">{{ $stats['total_donors'] }}</p>
                <span class="text-[11px] text-rose-500 font-semibold">{{ $stats['available_donors'] }} Ready</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 flex items-center justify-center font-black text-xl">
                🩸
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending Reports</span>
                <p class="text-3xl font-black text-red-600 mt-1">{{ $stats['pending_reports'] }}</p>
                <a href="{{ route('admin.reports.index') }}" class="text-[11px] text-red-600 font-bold hover:underline">Moderate &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400 flex items-center justify-center font-black text-xl">
                ⚠️
            </div>
        </div>

    </div>

    {{-- 2-Column Dashboard Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        {{-- Category Breakdown Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
                Resources by Emergency Category
            </h2>

            <div class="space-y-3">
                @foreach ($categoryCounts as $cat)
                    @php
                        $pct = $stats['total_resources'] > 0 ? round(($cat->resources_count / $stats['total_resources']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                <x-category-icon :icon="$cat->icon" class="w-3.5 h-3.5 text-slate-500" />
                                {{ $cat->name }}
                            </span>
                            <span class="text-slate-500">{{ $cat->resources_count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                            <div class="h-full bg-red-600 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pending Verification Quick Action Queue --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    Verification Queue
                </h2>
                <a href="{{ route('admin.resources.pending') }}" class="text-xs font-bold text-red-600 hover:underline">View All &rarr;</a>
            </div>

            @if ($pendingResources->count() > 0)
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($pendingResources as $res)
                        <div class="py-3.5 flex items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <p class="font-bold text-sm text-slate-900 dark:text-white">{{ $res->name }}</p>
                                <p class="text-xs text-slate-500">{{ $res->category?->name }} • {{ $res->city }} • 📞 {{ $res->phone }}</p>
                            </div>
                            <form action="{{ route('admin.resources.verify', $res->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm">
                                    Approve ✓
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 py-6 text-center">No pending resources in queue. All facilities are verified!</p>
            @endif
        </div>

    </div>

</div>
@endsection
