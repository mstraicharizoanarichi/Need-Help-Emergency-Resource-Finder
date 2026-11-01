@extends('layouts.provider')

@section('title', 'Provider Dashboard – Need Help')
@section('page_title', 'Provider Portal Overview')

@section('content')
<div class="space-y-8">

    {{-- Provider Welcome & Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Resources</span>
                <p class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_resources'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 flex items-center justify-center font-black">
                🏥
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Verified Live</span>
                <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['verified_resources'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-black">
                ✓
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pending Review</span>
                <p class="text-3xl font-black text-amber-500 mt-1">{{ $stats['pending_resources'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 flex items-center justify-center font-black">
                ⏳
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Issue Reports</span>
                <p class="text-3xl font-black text-rose-600 mt-1">{{ $stats['total_reports'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 flex items-center justify-center font-black">
                ⚠️
            </div>
        </div>

    </div>

    {{-- My Emergency Resources with Quick Availability Toggles --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">My Emergency Facilities</h2>
                <p class="text-xs text-slate-500">Instantly toggle real-time availability status for each facility.</p>
            </div>
            <a href="{{ route('provider.resources.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm flex items-center gap-1">
                + Add Facility
            </a>
        </div>

        @if ($resources->count() > 0)
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($resources as $res)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-base text-slate-900 dark:text-white">
                                    <a href="{{ route('resources.show', $res->slug) }}" target="_blank" class="hover:text-red-600">
                                        {{ $res->name }}
                                    </a>
                                </h3>
                                @if ($res->isVerified())
                                    <x-status-badge type="verified" />
                                @else
                                    <x-status-badge type="pending" />
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">{{ $res->category?->name }} • {{ $res->address }}, {{ $res->city }} • 📞 {{ $res->phone }}</p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            {{-- Quick Availability Toggle Form --}}
                            <form action="{{ route('provider.resources.availability', $res->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $res->availability_status === 'available' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}" title="Click to toggle availability">
                                    <span class="w-2 h-2 rounded-full inline-block mr-1 {{ $res->availability_status === 'available' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $res->availability_status === 'available' ? 'Status: Available' : 'Status: Busy/Closed' }}
                                </button>
                            </form>

                            <a href="{{ route('provider.resources.edit', $res->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold text-slate-700 dark:text-slate-300">
                                Edit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-slate-400 text-xs">
                You have not registered any emergency facilities yet. Tap "+ Add Facility" to list your hospital or service.
            </div>
        @endif
    </div>

</div>
@endsection
