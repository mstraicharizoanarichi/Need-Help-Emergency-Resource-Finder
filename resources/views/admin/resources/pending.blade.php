@extends('layouts.admin')

@section('title', 'Pending Verifications – Admin Console')
@section('page_title', 'Pending Facility Verification Queue')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Awaiting Administrator Review</h2>
            <p class="text-xs text-slate-500">Carefully verify provider credentials, phone hotlines, and coordinates before approving for public display.</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-300">
            {{ $resources->total() }} In Queue
        </span>
    </div>

    @if ($resources->count() > 0)
        <div class="space-y-4">
            @foreach ($resources as $res)
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold {{ $res->category?->getIconColorClasses() }} border">
                                    {{ $res->category?->name }}
                                </span>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">
                                    {{ $res->name }}
                                </h3>
                            </div>
                            <p class="text-xs text-slate-500">
                                📍 {{ $res->address }}, {{ $res->area }}, {{ $res->city }} (Coordinates: {{ $res->latitude }}, {{ $res->longitude }})
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('resources.show', $res->slug) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                                Preview Page ↗
                            </a>

                            <form action="{{ route('admin.resources.verify', $res->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm flex items-center gap-1.5">
                                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    <span>Approve & Publish</span>
                                </button>
                            </form>

                            <button type="button" onclick="openRejectModal({{ $res->id }}, '{{ addslashes($res->name) }}')" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs">
                                Reject with Reason
                            </button>
                        </div>
                    </div>

                    {{-- Specs & Provider Info --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-slate-50 dark:bg-slate-800/40 p-4 rounded-2xl">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">Registered By</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $res->user?->name ?: 'Admin' }} ({{ $res->user?->email }})</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">Hotline Number</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">📞 {{ $res->phone }}</span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">ICU & Ambulance</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">
                                ICU: {{ $res->details?->icu_available ? ($res->details->icu_beds_count ?: 'Yes') : 'No' }} | Amb: {{ $res->details?->ambulance_available ? 'Yes' : 'No' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase">Submission Date</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $res->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                </div>
            @endforeach

            <div class="pt-4">
                {{ $resources->links() }}
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200/80 dark:border-slate-800 shadow-sm max-w-lg mx-auto space-y-3">
            <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-2xl mx-auto">
                ✓
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Verification Queue is Empty</h3>
            <p class="text-xs text-slate-500">All submitted emergency resources have been reviewed and approved.</p>
        </div>
    @endif

</div>

{{-- Reject Reason Modal --}}
<dialog id="reject-modal" class="backdrop:bg-slate-900/60 rounded-3xl p-0 shadow-2xl border border-slate-200 dark:border-slate-800 max-w-md w-full m-auto">
    <div class="p-6 bg-white dark:bg-slate-900 text-slate-900 dark:text-white space-y-4">
        <h3 class="text-base font-bold text-rose-600">Reject Emergency Facility</h3>
        <p id="reject-modal-title" class="text-xs text-slate-500"></p>

        <form id="reject-form" action="" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PATCH')

            <div>
                <label class="block font-semibold mb-1">Rejection Reason *</label>
                <textarea name="rejection_reason" required rows="3" placeholder="Explain why this resource was rejected (e.g. Unreachable phone number, invalid address, lack of emergency credentials)..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('reject-modal').close()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 text-white font-bold hover:bg-rose-700">Confirm Rejection</button>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
function openRejectModal(resourceId, resourceName) {
    document.getElementById('reject-modal-title').innerText = `Resource: ${resourceName}`;
    document.getElementById('reject-form').action = `/admin/resources/${resourceId}/reject`;
    document.getElementById('reject-modal').showModal();
}
</script>
@endpush
@endsection
