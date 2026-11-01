@extends('layouts.provider')

@section('title', 'Add Hospital Doctor – Provider Portal')
@section('page_title', 'Register New Hospital Doctor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Doctor Information</h2>
            <p class="text-xs text-slate-500">Add a consulting specialist or on-duty emergency physician to your facility.</p>
        </div>
        <a href="{{ route('provider.doctors.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white">
            &larr; Back to Doctor List
        </a>
    </div>

    <form action="{{ route('provider.doctors.store') }}" method="POST" class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
        @csrf

        {{-- Hospital Selection --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Hospital Facility *
            </label>
            <select name="resource_id" required class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
                @foreach ($myResources as $res)
                    <option value="{{ $res->id }}" {{ (old('resource_id', $selectedResourceId) == $res->id) ? 'selected' : '' }}>
                        {{ $res->name }} ({{ $res->city }})
                    </option>
                @endforeach
            </select>
            @error('resource_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Basic Doctor Details --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Doctor Full Name *
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Md. Rafiqul Islam" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
                @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Medical Specialization *
                </label>
                <input list="specialization-list" name="specialization" value="{{ old('specialization') }}" required placeholder="Select or type (e.g. Cardiology)" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
                <datalist id="specialization-list">
                    @foreach ($specializations as $specKey => $specLabel)
                        <option value="{{ $specKey }}">{{ $specLabel }}</option>
                    @endforeach
                </datalist>
                @error('specialization') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Qualifications & Designation --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Degrees & Qualifications *
                </label>
                <input type="text" name="qualifications" value="{{ old('qualifications') }}" required placeholder="e.g. MBBS, FCPS (Cardiology), MD, FACC" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
                @error('qualifications') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Designation / Title
                </label>
                <input type="text" name="designation" value="{{ old('designation') }}" placeholder="e.g. Senior Consultant & Professor" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
            </div>
        </div>

        {{-- Department & Experience --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Department
                </label>
                <input type="text" name="department" value="{{ old('department') }}" placeholder="e.g. Department of Interventional Cardiology" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Experience (Years)
                </label>
                <input type="number" name="experience_years" value="{{ old('experience_years', 5) }}" min="0" max="70" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
            </div>
        </div>

        {{-- Visiting Days Checkboxes --}}
        <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Weekly Visiting Days
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
                @php $oldDays = old('visiting_days', ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Sat']); @endphp
                @foreach ($daysOfWeek as $day)
                    <label class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                        <input type="checkbox" name="visiting_days[]" value="{{ $day }}" {{ in_array($day, (array)$oldDays) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        <span>{{ $day }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Visiting Hours, Chamber, Appointment Hotline & Fee --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Visiting Hours
                </label>
                <input type="text" name="visiting_hours" value="{{ old('visiting_hours', '05:00 PM - 09:00 PM') }}" placeholder="e.g. 05:00 PM - 09:00 PM" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Chamber / Room #
                </label>
                <input type="text" name="room_number" value="{{ old('room_number', 'Room 301, 3rd Floor') }}" placeholder="e.g. Room 301, 3rd Floor" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Appointment Phone
                </label>
                <input type="text" name="appointment_phone" value="{{ old('appointment_phone') }}" placeholder="Direct hotline / mobile" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Consultation Fee (৳)
                </label>
                <input type="number" name="consultation_fee" value="{{ old('consultation_fee', 1000) }}" min="0" placeholder="e.g. 1000" class="w-full h-11 px-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">
            </div>
        </div>

        {{-- Emergency On-Duty Status Toggle --}}
        <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 flex items-center justify-between">
            <div class="space-y-0.5">
                <span class="text-xs font-extrabold text-red-900 dark:text-red-200 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🚨</span>
                    <span>Emergency On-Duty Physician</span>
                </span>
                <p class="text-xs text-red-700 dark:text-red-300">Enable this if this specialist is currently assigned to 24/7 Emergency / Trauma duty.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_emergency_available" value="1" {{ old('is_emergency_available') ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
            </label>
        </div>

        {{-- Bio / Notes --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Doctor Biography / Profile Summary
            </label>
            <textarea name="bio" rows="3" placeholder="Brief professional overview, special interests, or appointment instructions..." class="w-full p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm focus:ring-2 focus:ring-emerald-500 outline-none text-slate-900 dark:text-white">{{ old('bio') }}</textarea>
        </div>

        {{-- Submit CTA --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('provider.doctors.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-xs font-bold text-slate-700 dark:text-slate-300">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30">
                Save & Add Doctor
            </button>
        </div>

    </form>

</div>
@endsection
