@extends('layouts.app')

@section('title', 'Emergency Blood Donors Directory – Need Help')
@section('meta_description', 'Find voluntary blood donors across Bangladesh (A+, A-, B+, B-, O+, O-, AB+, AB-) with privacy protection.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8">

    {{-- Blood Donors Header Banner --}}
    <div class="bg-gradient-to-r from-rose-600 to-red-600 rounded-3xl p-6 sm:p-10 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-3 max-w-2xl text-center md:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-xs font-bold uppercase tracking-wider">
                <span class="pulse-dot bg-white"></span>
                Emergency Blood Donor Network
            </div>
            <h1 class="text-2xl sm:text-4xl font-black">
                Find Compatible Blood Donors Instantly
            </h1>
            <p class="text-xs sm:text-sm text-rose-100 leading-relaxed">
                Connect with active, voluntary blood donors in your city. Exact numbers and addresses are protected by privacy settings until an emergency request is initiated.
            </p>
        </div>

        <div class="shrink-0 flex flex-col sm:flex-row items-center gap-3">
            <a href="{{ route('blood-donors.create') }}" class="py-3 px-6 rounded-2xl bg-white text-rose-700 hover:bg-rose-50 font-black text-sm shadow-md transition-transform hover:scale-105">
                + Register as Voluntary Donor
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        
        {{-- Blood Group Pills --}}
        <div>
            <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Filter by Blood Group:</span>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('blood-donors.index', array_merge(request()->except('blood_group'), ['blood_group' => 'all'])) }}" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all {{ empty($selectedBloodGroup) || $selectedBloodGroup === 'all' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}">
                    All Groups
                </a>
                @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                    <a href="{{ route('blood-donors.index', array_merge(request()->except('blood_group'), ['blood_group' => $bg])) }}" class="px-4 py-2 rounded-xl text-xs font-extrabold transition-all {{ $selectedBloodGroup === $bg ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200' }}">
                        🩸 {{ $bg }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Search & District Form --}}
        <form action="{{ route('blood-donors.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
            @if ($selectedBloodGroup && $selectedBloodGroup !== 'all')
                <input type="hidden" name="blood_group" value="{{ $selectedBloodGroup }}">
            @endif

            <div class="relative sm:col-span-2">
                <input type="text" name="city" value="{{ request('city') }}" placeholder="Search by city or area (e.g. Dhaka, Dhanmondi, Chattogram)..." class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium focus:ring-2 focus:ring-red-500">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full h-11 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs sm:text-sm hover:opacity-90 transition-opacity">
                    Filter Donors
                </button>
                @if (request()->hasAny(['city', 'blood_group']))
                    <a href="{{ route('blood-donors.index') }}" class="h-11 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs font-semibold hover:text-red-600">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Donors Grid --}}
    @if ($donors->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($donors as $donor)
                <x-scroll-reveal :delay="$loop->index * 100" class="h-full">
                    <div class="h-full bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between space-y-4">
                        
                        {{-- Header with Blood Drop Pill --}}
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 flex items-center justify-center font-black text-base border border-rose-200 dark:border-rose-800">
                                        {{ $donor->blood_group }}
                                    </span>
                                    <div>
                                        <h3 class="font-bold text-base text-slate-900 dark:text-white">
                                            {{ $donor->name }}
                                        </h3>
                                        <span class="text-xs text-slate-500">
                                            {{ $donor->city }}, {{ $donor->district }}
                                        </span>
                                    </div>
                                </div>

                                @if ($donor->is_available && $donor->isEligibleToDonate())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        Ready
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Cooldown
                                    </span>
                                @endif
                            </div>

                            {{-- Stats & Medical Info --}}
                            <div class="space-y-2 text-xs text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/40 p-3.5 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <span>Area / Thana:</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $donor->area ?: 'Central Area' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Total Donations:</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $donor->total_donations }} times</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Last Donated:</span>
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $donor->last_donation_date ? $donor->last_donation_date->diffForHumans() : 'First time donor' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button: Direct Call (if public) or Request Emergency Contact Modal --}}
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                            @if ($donor->canShowPhonePublicly())
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $donor->phone) }}" class="w-full py-3 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-sm">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    <span>CALL: {{ $donor->phone }}</span>
                                </a>
                            @else
                                <button type="button" onclick="openContactRequestModal({{ $donor->id }}, '{{ addslashes($donor->name) }}', '{{ $donor->blood_group }}', '{{ $donor->city }}')" class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-sm transition-transform hover:scale-[1.02]">
                                    <svg class="w-4 h-4 text-rose-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 4.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm0 8.5a.75.75 0 10-1.5 0 .75.75 0 001.5 0z" clip-rule="evenodd"/></svg>
                                    <span>Request Emergency Contact</span>
                                </button>
                                <p class="text-[10px] text-center text-slate-400 mt-1">🔒 Donor phone protected by privacy setting</p>
                            @endif
                        </div>

                    </div>
                </x-scroll-reveal>
            @endforeach
        </div>

        <div class="pt-6">
            {{ $donors->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 text-center border border-slate-200/80 dark:border-slate-800 shadow-sm max-w-xl mx-auto space-y-4">
            <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto text-2xl font-bold">
                🩸
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">No blood donors found for this filter</h3>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                Be the hero in your community by registering as a voluntary blood donor today.
            </p>
            <div class="pt-2">
                <a href="{{ route('blood-donors.create') }}" class="px-5 py-2.5 rounded-xl bg-red-600 text-white font-bold text-xs sm:text-sm inline-block">
                    Register as Blood Donor
                </a>
            </div>
        </div>
    @endif

</div>

{{-- Donor Emergency Contact Request Modal --}}
<dialog id="donor-contact-modal" class="backdrop:bg-slate-900/60 rounded-3xl p-0 shadow-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full m-auto">
    <div class="p-6 sm:p-8 bg-white dark:bg-slate-900 text-slate-900 dark:text-white space-y-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-sm">🩸</span>
                <div>
                    <h3 class="text-base font-bold">Request Donor Contact</h3>
                    <p id="modal-donor-subtitle" class="text-xs text-slate-400"></p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('donor-contact-modal').close()" class="p-1 text-slate-400 hover:text-slate-700">✕</button>
        </div>

        <p class="text-xs text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl leading-relaxed">
            Please provide accurate patient & hospital details. An instant SMS/Notification will alert the donor with your emergency request.
        </p>

        <form action="{{ route('blood-donors.request') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <input type="hidden" name="blood_donor_id" id="modal-donor-id" value="">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold mb-1">Requester Name *</label>
                    <input type="text" name="requester_name" required value="{{ optional(Auth::user())->name }}" placeholder="Your name" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Requester Phone *</label>
                    <input type="text" name="requester_phone" required value="{{ optional(Auth::user())->phone }}" placeholder="+88017..." class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold mb-1">Patient Name</label>
                    <input type="text" name="patient_name" placeholder="Patient's name" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Hospital / Clinic *</label>
                    <input type="text" name="hospital_name" required placeholder="e.g. Dhaka Medical Hospital" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold mb-1">Units (Bags) *</label>
                    <input type="number" name="units_needed" min="1" max="10" value="1" required class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Urgency Level *</label>
                    <select name="urgency_level" required class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <option value="critical">🚨 Critical / Immediate (within 2h)</option>
                        <option value="urgent" selected>Urgent (Today)</option>
                        <option value="standard">Standard (Tomorrow)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-semibold mb-1">Emergency Message / Medical Case</label>
                <textarea name="message" rows="2" placeholder="e.g. Surgery scheduled, platelets needed..." class="w-full p-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('donor-contact-modal').close()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">Cancel</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700">Send Emergency Request</button>
            </div>
        </form>
    </div>
</dialog>

@push('scripts')
<script>
function openContactRequestModal(donorId, donorName, bloodGroup, city) {
    document.getElementById('modal-donor-id').value = donorId;
    document.getElementById('modal-donor-subtitle').innerText = `Donor: ${donorName} (${bloodGroup}) • ${city}`;
    document.getElementById('donor-contact-modal').showModal();
}
</script>
@endpush
@endsection
