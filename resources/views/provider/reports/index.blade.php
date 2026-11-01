@extends('layouts.provider')

@section('title', 'Received Issue Reports – Provider Portal')
@section('page_title', 'Citizen Reports for My Facilities')

@section('content')
<div class="space-y-6">

    <div>
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Facility Issue Reports</h2>
        <p class="text-xs text-slate-500">Citizen feedback and corrections reported for your healthcare facilities.</p>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Reported Facility</th>
                        <th class="py-3.5 px-4">Issue Type</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4">Reporter</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($reports as $rep)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="py-4 px-4 sm:px-6 font-bold text-slate-900 dark:text-white">
                                <a href="{{ route('resources.show', $rep->resource?->slug) }}" target="_blank" class="hover:text-red-600">
                                    {{ $rep->resource?->name }}
                                </a>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                    {{ str_replace('_', ' ', ucfirst($rep->report_type)) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-600 dark:text-slate-400 max-w-xs">
                                {{ $rep->description }}
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-500">
                                <span class="font-semibold block text-slate-800 dark:text-slate-200">{{ $rep->reporter_name }}</span>
                                {{ $rep->reporter_phone ?: $rep->reporter_email }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $rep->status === 'resolved' ? 'bg-emerald-50 text-emerald-700' : ($rep->status === 'dismissed' ? 'bg-slate-100 text-slate-600' : 'bg-amber-50 text-amber-700') }}">
                                    {{ ucfirst($rep->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-400">
                                {{ $rep->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No issue reports received for your facilities. Everything looks great!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
