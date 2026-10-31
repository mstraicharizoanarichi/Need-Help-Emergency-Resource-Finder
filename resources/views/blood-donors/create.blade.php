@extends('layouts.app')

@section('title', 'Register as Voluntary Blood Donor – Need Help')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-10 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-8">
        
        {{-- Form Header --}}
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-2xl mx-auto mb-3">
                🩸
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
                Register as a Voluntary Blood Donor
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-lg mx-auto">
                Join our emergency blood donor network across Bangladesh. You can choose to keep your phone number private or make it public.
            </p>
        </div>

        <form action="{{ route('blood-donors.store') }}" method="POST" class="space-y-6 text-sm">
            @csrf

            {{-- Donor Name & Phone --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Full Name *</label>
                    <input type="text" name="name" required value="{{ old('name', Auth::user()?->name) }}" placeholder="e.g. Tanvir Ahmed" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Phone Number *</label>
                    <input type="text" name="phone" required value="{{ old('phone', Auth::user()?->phone) }}" placeholder="+88017..." class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            {{-- Blood Group & Availability --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Blood Group *</label>
                    <select name="blood_group" required class="w-full h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500 font-bold text-rose-600">
                        <option value="">Select Group</option>
                        @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Gender</label>
                    <select name="gender" class="w-full h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            {{-- Location --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">City *</label>
                    <input type="text" name="city" required value="{{ old('city', 'Dhaka') }}" placeholder="e.g. Dhaka" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">District *</label>
                    <input type="text" name="district" required value="{{ old('district', 'Dhaka') }}" placeholder="e.g. Dhaka" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Area / Thana</label>
                    <input type="text" name="area" value="{{ old('area') }}" placeholder="e.g. Dhanmondi, Mirpur" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            {{-- Last Donation & Total Count --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Last Donation Date (if any)</label>
                    <input type="date" name="last_donation_date" value="{{ old('last_donation_date') }}" class="w-full h-11 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                    <p class="text-[11px] text-slate-400 mt-1">Recommended cooldown is 90-120 days between whole blood donations.</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">Total Previous Donations</label>
                    <input type="number" name="total_donations" min="0" value="{{ old('total_donations', 0) }}" class="w-full h-11 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            {{-- Privacy Settings Box --}}
            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3">
                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    🔒 Privacy & Contact Mode
                </h3>
                <div class="space-y-2">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="radio" name="privacy_mode" value="private" checked class="mt-1 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="font-semibold text-slate-900 dark:text-white block">Protected Emergency Request (Recommended)</span>
                            <span class="text-xs text-slate-500">Hide my phone number from public lists. Requesters must submit an emergency verification form to reach me.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                        <input type="radio" name="privacy_mode" value="public" class="mt-1 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="font-semibold text-slate-900 dark:text-white block">Public Phone Direct Call</span>
                            <span class="text-xs text-slate-500">Allow patients and family members in urgent emergencies to call my phone number directly.</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('blood-donors.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:text-slate-900 font-semibold text-xs sm:text-sm">Cancel</a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-extrabold text-sm shadow-md shadow-red-600/30 transition-transform hover:scale-[1.02]">
                    Register as Blood Donor
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
