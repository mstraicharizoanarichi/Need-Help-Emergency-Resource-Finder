@extends('layouts.provider')

@section('title', 'Manage Hospital Doctors – Provider Portal')
@section('page_title', 'Hospital Doctors & Specialists')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Doctor Directory</h2>
            <p class="text-xs text-slate-500">Manage specialist physicians, visiting hours, chambers, and emergency on-duty status.</p>
        </div>
        <a href="{{ route('provider.doctors.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-sm flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
            <span>+ Add New Doctor</span>
        </a>
    </div>

    {{-- Doctor Filter & Search Bar --}}
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center gap-3">
        <form action="{{ route('provider.doctors.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search doctor by name or specialty..." class="w-full h-10 pl-9 pr-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>

            <select name="resource_id" onchange="this.form.submit()" class="h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs outline-none focus:ring-2 focus:ring-emerald-500 text-slate-700 dark:text-slate-200">
                <option value="">All My Facilities</option>
                @foreach ($myResources as $facility)
                    <option value="{{ $facility->id }}" {{ request('resource_id') == $facility->id ? 'selected' : '' }}>{{ $facility->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="h-10 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 font-bold text-xs">
                Filter
            </button>
        </form>
    </div>

    {{-- Doctors Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Doctor Name & Qualifications</th>
                        <th class="py-3.5 px-4">Hospital Facility</th>
                        <th class="py-3.5 px-4">Specialization</th>
                        <th class="py-3.5 px-4">Visiting Schedule</th>
                        <th class="py-3.5 px-4">Emergency Duty</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($doctors as $doc)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4 sm:px-6">
                                <a href="{{ route('doctors.show', $doc->slug) }}" target="_blank" class="font-bold text-slate-900 dark:text-white hover:text-blue-600 block">
                                    Dr. {{ $doc->name }}
                                </a>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $doc->qualifications }}</p>
                                @if ($doc->room_number)
                                    <span class="text-[11px] text-slate-500">Chamber: {{ $doc->room_number }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-medium text-slate-700 dark:text-slate-300">
                                {{ optional($doc->resource)->name }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $doc->getSpecialtyBadgeColor() }}">
                                    {{ $doc->specialization }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-600 dark:text-slate-400">
                                <span class="font-bold block text-slate-800 dark:text-slate-200">
                                    {{ !empty($doc->visiting_days) ? implode(', ', (array)$doc->visiting_days) : 'Daily' }}
                                </span>
                                <span>{{ $doc->visiting_hours ?: 'Standard Hours' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                {{-- Quick Emergency Status Toggle Form --}}
                                <form action="{{ route('provider.doctors.emergency', $doc->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ $doc->is_emergency_available ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 border border-red-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}" title="Click to toggle emergency on-duty status">
                                        <span class="w-1.5 h-1.5 rounded-full inline-block mr-1 {{ $doc->is_emergency_available ? 'bg-red-600' : 'bg-slate-400' }}"></span>
                                        {{ $doc->is_emergency_available ? '🚨 ON DUTY' : 'Standard' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('doctors.show', $doc->slug) }}" target="_blank" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white" title="View Public Profile">
                                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    <a href="{{ route('provider.doctors.edit', $doc->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white" title="Edit Doctor">
                                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z"/><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z"/></svg>
                                    </a>
                                    <form action="{{ route('provider.doctors.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove Dr. {{ addslashes($doc->name) }} from the directory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700" title="Delete">
                                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold">No doctors registered yet under your hospital facilities.</p>
                                <a href="{{ route('provider.doctors.create') }}" class="mt-2 inline-block text-xs font-bold text-emerald-600 hover:underline">+ Add First Doctor</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($doctors->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $doctors->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
