@extends('layouts.app')

@section('title', 'My Emergency Dashboard – Need Help')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 space-y-8">

    {{-- Dashboard Profile Header --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-red-600 to-rose-600 text-white font-black text-2xl flex items-center justify-center shadow-md shadow-red-600/20">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">{{ Auth::user()->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-500">{{ Auth::user()->email }} • {{ Auth::user()->phone ?: 'No phone set' }}</p>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 mt-1">
                    {{ ucfirst(Auth::user()->role) }} Account
                </span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('search') }}" class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                Find Resources
            </a>
        </div>
    </div>

    {{-- Main 2-Column Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left 2 Columns: Saved Favorites & Blood Requests --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Saved Emergency Facilities (Favorites) --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.16-1.026c-1.42-1.38-3.08-3.32-3.468-5.376C4.5 7.8 6.1 5.5 8.2 5.5c1.1 0 2.1.6 2.8 1.5.7-.9 1.7-1.5 2.8-1.5 2.1 0 3.7 2.3 3.2 5.0-.388 2.056-2.048 3.996-3.468 5.376a20.76 20.76 0 01-1.16 1.026l-.019.01-.005.003h-.002a.75.75 0 01-.698 0z"/></svg>
                    My Saved Emergency Facilities ({{ $favorites->count() }})
                </h2>

                @if ($favorites->count() > 0)
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($favorites as $fav)
                            @if ($fav->resource)
                                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold text-sm text-slate-900 dark:text-white">
                                            <a href="{{ route('resources.show', $fav->resource->slug) }}" class="hover:text-red-600">
                                                {{ $fav->resource->name }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-slate-500">{{ $fav->resource->address }}, {{ $fav->resource->city }}</p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="tel:{{ $fav->resource->getCleanPhone() }}" class="py-1.5 px-3 rounded-lg bg-red-600 text-white font-bold text-xs">
                                            Call
                                        </a>
                                        <form action="{{ route('favorites.toggle', $fav->resource_id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600">
                                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">You haven't bookmarked any emergency facilities yet. Tap the bookmark icon on any resource card to save it for quick access.</p>
                @endif
            </div>

            {{-- Blood Requests Sent --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <span class="text-rose-500">🩸</span>
                    Emergency Blood Requests Sent ({{ $donorRequests->count() }})
                </h2>

                @if ($donorRequests->count() > 0)
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($donorRequests as $req)
                            <div class="py-3.5 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm text-slate-900 dark:text-white">
                                        Donor: {{ optional($req->bloodDonor)->name }} ({{ optional($req->bloodDonor)->blood_group }})
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $req->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($req->status === 'rejected' ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700') }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">Hospital: {{ $req->hospital_name }} • {{ $req->units_needed }} Unit(s) • {{ $req->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">No emergency blood requests initiated from your account.</p>
                @endif
            </div>

            {{-- My Submitted Reports --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    My Submitted Issue Reports ({{ $reports->count() }})
                </h2>

                @if ($reports->count() > 0)
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($reports as $rep)
                            <div class="py-3.5 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm text-slate-900 dark:text-white">
                                        {{ optional($rep->resource)->name ?: 'Emergency Facility' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $rep->status === 'resolved' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ ucfirst($rep->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">{{ $rep->description }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">You have not submitted any issue reports.</p>
                @endif
            </div>

        </div>

        {{-- Right Column: Profile Update, Theme Preference & Donor Status --}}
        <div class="space-y-8">
            
            {{-- Appearance / Theme Preference Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                        Interface Appearance
                    </h3>
                    <span class="text-[10px] text-slate-400 font-mono">Alt+T</span>
                </div>
                <p class="text-xs text-slate-500">Choose between Light, Dark, or System mode for night emergencies and high contrast.</p>
                <div class="pt-1">
                    <x-theme-toggle variant="segmented" class="w-full justify-center" />
                </div>
            </div>

            {{-- Update Profile Card --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="font-bold text-base text-slate-900 dark:text-white">Update Profile</h3>
                
                <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ Auth::user()->name }}" required class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ Auth::user()->phone }}" placeholder="+88017..." class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">City</label>
                        <input type="text" name="city" value="{{ Auth::user()->city }}" placeholder="e.g. Dhaka" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Address</label>
                        <textarea name="address" rows="2" placeholder="Your residential area..." class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">{{ Auth::user()->address }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs hover:opacity-90">
                        Save Profile Changes
                    </button>
                </form>
            </div>

            {{-- Donor Status Card --}}
            <div class="bg-gradient-to-tr from-rose-50 to-red-50 dark:from-rose-950/40 dark:to-red-950/40 rounded-3xl p-6 border border-rose-200 dark:border-rose-900 shadow-sm space-y-3">
                <h3 class="font-bold text-base text-rose-900 dark:text-rose-200 flex items-center gap-2">
                    🩸 Voluntary Blood Donor
                </h3>
                <p class="text-xs text-rose-800 dark:text-rose-300 leading-relaxed">
                    Save lives in your community during critical blood shortages. You maintain full control over privacy.
                </p>
                <a href="{{ route('blood-donors.create') }}" class="w-full py-2.5 rounded-xl bg-red-600 text-white font-bold text-xs text-center block shadow-sm hover:bg-red-700">
                    Register as Blood Donor
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
