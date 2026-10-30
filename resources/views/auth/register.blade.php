@extends('layouts.app')

@section('title', 'Register – Need Help')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-6">

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
        
        <div class="text-center space-y-1">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">Create an Account</h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Join Need Help as a citizen user or list emergency healthcare facilities as a provider.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4 text-xs sm:text-sm">
            @csrf

            {{-- Role Selector Radio Cards --}}
            <div class="space-y-1.5">
                <label class="block font-bold text-slate-700 dark:text-slate-300">I want to register as: *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col items-center text-center gap-1.5 has-[:checked]:border-red-600 has-[:checked]:bg-red-50/40 dark:has-[:checked]:bg-red-950/30">
                        <input type="radio" name="role" value="user" {{ request('role', 'user') === 'user' ? 'checked' : '' }} onchange="toggleProviderFields(false)" class="sr-only">
                        <span class="text-lg">👤</span>
                        <span class="font-bold text-slate-900 dark:text-white">Citizen User</span>
                        <span class="text-[11px] text-slate-400 leading-tight">Save favorites & request blood</span>
                    </label>

                    <label class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col items-center text-center gap-1.5 has-[:checked]:border-red-600 has-[:checked]:bg-red-50/40 dark:has-[:checked]:bg-red-950/30">
                        <input type="radio" name="role" value="provider" {{ request('role') === 'provider' ? 'checked' : '' }} onchange="toggleProviderFields(true)" class="sr-only">
                        <span class="text-lg">🏥</span>
                        <span class="font-bold text-slate-900 dark:text-white">Service Provider</span>
                        <span class="text-[11px] text-slate-400 leading-tight">Hospital / Ambulance / Clinic</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Full Name *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="e.g. Dr. Rafiqul Islam" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label for="phone" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Phone Number *</label>
                    <input type="text" name="phone" id="phone" required value="{{ old('phone') }}" placeholder="+88017..." class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <div>
                <label for="email" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Email Address *</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="name@domain.com" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
            </div>

            {{-- Dynamic Provider Fields --}}
            <div id="provider-fields" class="{{ request('role') === 'provider' ? '' : 'hidden' }} space-y-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Organization Details</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="organization_name" class="block font-semibold mb-1">Organization / Facility Name</label>
                        <input type="text" name="organization_name" id="organization_name" value="{{ old('organization_name') }}" placeholder="e.g. City General Hospital" class="w-full h-10 px-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label for="organization_type" class="block font-semibold mb-1">Organization Type</label>
                        <select name="organization_type" id="organization_type" class="w-full h-10 px-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <option value="hospital">Hospital / Clinic</option>
                            <option value="ambulance_agency">Ambulance Service Agency</option>
                            <option value="pharmacy">Pharmacy</option>
                            <option value="diagnostic_center">Diagnostic Center</option>
                            <option value="police_station">Police Station</option>
                            <option value="fire_station">Fire Station</option>
                            <option value="other">Other Emergency Provider</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="address" class="block font-semibold mb-1">Official Address</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="e.g. House 12, Road 5, Dhanmondi, Dhaka" class="w-full h-10 px-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Password *</label>
                    <input type="password" name="password" id="password" required placeholder="Min. 8 characters" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Confirm Password *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Re-type password" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <button type="submit" class="w-full h-12 rounded-xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-sm shadow-md shadow-red-600/30 transition-transform hover:scale-[1.01] mt-2">
                Create Account
            </button>
        </form>

        <div class="text-center text-xs text-slate-500 pt-2">
            Already have an account? <a href="{{ route('login') }}" class="font-bold text-red-600 hover:underline">Sign In</a>
        </div>

    </div>

</div>

@push('scripts')
<script>
function toggleProviderFields(show) {
    const fields = document.getElementById('provider-fields');
    if (show) {
        fields.classList.remove('hidden');
    } else {
        fields.classList.add('hidden');
    }
}
</script>
@endpush
@endsection
