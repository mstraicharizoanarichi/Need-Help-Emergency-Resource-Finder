@extends('layouts.admin')

@section('title', 'Issue Reports Moderation – Admin Console')
@section('page_title', 'Citizen Reports Moderation')

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.index', ['status' => 'all']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold {{ request('status', 'all') === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                All Reports
            </a>
            <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold {{ request('status') === 'pending' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300' }}">
                Pending Review
            </a>
            <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold {{ request('status') === 'resolved' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' }}">
                Resolved
            </a>
        </div>
    </div>

    {{-- Reports Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Resource Reported</th>
                        <th class="py-3.5 px-4">Report Type</th>
                        <th class="py-3.5 px-4">Issue Description</th>
                        <th class="py-3.5 px-4">Reporter</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Moderation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($reports as $rep)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="py-4 px-4 sm:px-6">
                                <a href="{{ route('resources.show', $rep->resource?->slug) }}" target="_blank" class="font-bold text-slate-900 dark:text-white hover:text-red-600 block">
                                    {{ $rep->resource?->name }}
                                </a>
                                <span class="text-xs text-slate-400">{{ $rep->resource?->city }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                    {{ str_replace('_', ' ', ucfirst($rep->report_type)) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-600 dark:text-slate-400 max-w-sm">
                                <p class="line-clamp-2">{{ $rep->description }}</p>
                                @if ($rep->admin_notes)
                                    <p class="text-[11px] text-slate-400 mt-1 italic">Resolution note: {{ $rep->admin_notes }}</p>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-500">
                                <span class="font-semibold text-slate-800 dark:text-slate-200 block">{{ $rep->reporter_name }}</span>
                                {{ $rep->reporter_phone ?: $rep->reporter_email }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $rep->status === 'resolved' ? 'bg-emerald-50 text-emerald-700' : ($rep->status === 'dismissed' ? 'bg-slate-100 text-slate-600' : 'bg-amber-50 text-amber-700') }}">
                                    {{ ucfirst($rep->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <button type="button" onclick="openResolveModal({{ $rep->id }}, '{{ addslashes($rep->resource?->name) }}', '{{ $rep->status }}')" class="px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 font-bold text-xs">
                                    Update Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No issue reports in this view.
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

{{-- Resolve Report Modal --}}
<dialog id="resolve-modal" class="backdrop:bg-slate-900/60 rounded-3xl p-0 shadow-2xl border border-slate-200 dark:border-slate-800 max-w-md w-full m-auto">
    <div class="p-6 bg-white dark:bg-slate-900 text-slate-900 dark:text-white space-y-4">
        <h3 class="text-base font-bold">Moderate Issue Report</h3>
        <p id="resolve-modal-subtitle" class="text-xs text-slate-500"></p>

        <form id="resolve-form" action="" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PATCH')

            <div>
                <label class="block font-semibold mb-1">Status *</label>
                <select name="status" id="resolve-status-select" required class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <option value="pending">Pending</option>
                    <option value="in_review">In Review</option>
                    <option value="resolved">Resolved (Corrected data)</option>
                    <option value="dismissed">Dismissed (Invalid report)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-1">Admin Action / Resolution Notes</label>
                <textarea name="admin_notes" rows="3" placeholder="Describe actions taken (e.g. Called hospital to verify hotline, updated phone to +88017...)..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('resolve-modal').close()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700">Save Resolution</button>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
function openResolveModal(reportId, facilityName, currentStatus) {
    document.getElementById('resolve-modal-subtitle').innerText = `Report for: ${facilityName}`;
    document.getElementById('resolve-status-select').value = currentStatus;
    document.getElementById('resolve-form').action = `/admin/reports/${reportId}/resolve`;
    document.getElementById('resolve-modal').showModal();
}
</script>
@endpush
@endsection
