@extends('layouts.app')

@section('title', 'Need Help – Find Emergency Hospitals, Ambulances, Blood Donors & Services Near You')

@section('content')
<div class="space-y-12 sm:space-y-16 pb-16">

    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-b from-red-600 via-rose-600 to-red-700 text-white pt-12 pb-20 sm:pt-16 sm:pb-28 overflow-hidden rounded-b-3xl sm:rounded-b-[40px] shadow-xl">
        {{-- Decorative Background Elements --}}
        <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-rose-500/20 blur-3xl"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-red-800/40 blur-3xl"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            
            {{-- Pulse Emergency Tag --}}
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-xs sm:text-sm font-bold uppercase tracking-wider mb-6">
                <span class="pulse-dot bg-amber-400"></span>
                <span>24/7 Verified Emergency Resource Network • Bangladesh</span>
            </div>

            {{-- Main Headline --}}
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight sm:leading-tight mb-5">
                Need Emergency Help?<br class="hidden sm:block">
                <span class="text-amber-300">Find the Right Resource Near You.</span>
            </h1>

            <p class="text-base sm:text-xl text-red-100 max-w-3xl mx-auto font-normal leading-relaxed mb-6 sm:mb-8">
                Instantly locate verified hospitals, ICU beds, 24/7 ambulances, pharmacies, voluntary blood donors, police stations, and fire services within seconds.
            </p>

            {{-- High-Priority Instant SOS Trigger Bar --}}
            <div class="max-w-2xl mx-auto mb-8 p-3 sm:p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-lg">
                <div class="flex items-center gap-3 text-left">
                    <span class="w-10 h-10 rounded-xl bg-white text-red-600 flex items-center justify-center font-black text-xl shadow-md shrink-0">
                        🆘
                    </span>
                    <div>
                        <div class="text-sm font-extrabold text-white leading-tight">Critical Life Emergency?</div>
                        <div class="text-xs text-red-100">Activate One-Tap SOS for immediate P1 responder dispatch.</div>
                    </div>
                </div>
                <button type="button" onclick="openSosModal()" class="w-400px sm:w-auto px-6 py-2.5 rounded-xl bg-white text-red-700 hover:bg-red-50 font-black text-xs uppercase tracking-wider shadow-md hover:scale-105 transition-all shrink-0 flex items-center justify-center gap-2 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
                    Trigger SOS
                </button>
            </div>

            {{-- Main Emergency Search Box --}}
            <div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-3xl p-3 sm:p-4 shadow-2xl border border-white/40 dark:border-slate-800 text-slate-800 dark:text-white">
                <form id="hero-search-form" action="{{ route('search') }}" method="GET" class="flex flex-col md:flex-row items-stretch gap-2.5">
                    
                    {{-- Hidden Lat / Lng for Geolocation --}}
                    <input type="hidden" name="lat" id="hero-lat" value="">
                    <input type="hidden" name="lng" id="hero-lng" value="">

                    {{-- Category Selector --}}
                    <div class="w-full md:w-52">
                        <label for="hero-category" class="sr-only">Emergency Service Type</label>
                        <select name="category" id="hero-category" class="w-full h-12 sm:h-14 px-4 rounded-2xl bg-slate-100 dark:bg-slate-800 border-none font-semibold text-sm focus:ring-2 focus:ring-red-500 cursor-pointer">
                            <option value="all">🚑 All Services</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Input (Location / Name) --}}
                    <div class="flex-1 relative">
                        <label for="hero-q" class="sr-only">Search Location or Name</label>
                        <input type="text" name="q" id="hero-q" placeholder="Enter area, hospital name, or district (e.g. Dhanmondi)..." class="w-full h-12 sm:h-14 pl-11 pr-4 rounded-2xl bg-slate-100 dark:bg-slate-800 border-none font-medium text-sm focus:ring-2 focus:ring-red-500 placeholder-slate-400">
                        <svg class="w-5 h-5 text-slate-400 absolute left-4 top-3.5 sm:top-4.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>

                    {{-- "USE MY LOCATION" Instant GPS Button --}}
                    <button type="button" id="hero-locate-btn" class="h-12 sm:h-14 px-5 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shrink-0 border border-slate-200 dark:border-slate-700" title="Auto-detect current GPS coordinates">
                        <svg class="w-4 h-4 text-red-600 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                        <span>USE MY LOCATION</span>
                    </button>

                    {{-- SEARCH Button --}}
                    <button type="submit" class="h-12 sm:h-14 px-8 rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-extrabold text-sm sm:text-base shadow-md shadow-red-600/30 flex items-center justify-center gap-2 transition-all hover:scale-[1.02] shrink-0">
                        <span>SEARCH</span>
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                    </button>

                </form>
            </div>

            {{-- Quick stats strip --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-3xl mx-auto mt-8 text-center text-xs sm:text-sm font-semibold text-red-100">
                <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm">
                    <span class="block text-xl sm:text-2xl font-black text-white">{{ $stats['total_resources'] }}</span>
                    <span>Verified Facilities</span>
                </div>
                <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm">
                    <span class="block text-xl sm:text-2xl font-black text-white">{{ $stats['total_hospitals'] }}</span>
                    <span>Hospitals & ICU</span>
                </div>
                <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm">
                    <span class="block text-xl sm:text-2xl font-black text-white">{{ $stats['total_ambulances'] }}</span>
                    <span>Ambulances Fleet</span>
                </div>
                <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm">
                    <span class="block text-xl sm:text-2xl font-black text-white">{{ $stats['total_donors'] }}</span>
                    <span>Ready Blood Donors</span>
                </div>
            </div>

        </div>
    </section>

    {{-- Category Cards Grid --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mb-2">Emergency Resource Categories</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Select an emergency service category below to view nearest available providers.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 sm:gap-4">
            @foreach ($categories as $cat)
                <a href="{{ $cat->slug === 'blood-donor' ? route('blood-donors.index') : route('search', ['category' => $cat->slug]) }}" class="group bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-red-500/40 dark:hover:border-red-500/40 transition-all flex flex-col items-center text-center justify-between">
                    
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center {{ $cat->getIconColorClasses() }} mb-3 group-hover:scale-110 transition-transform">
                        <x-category-icon :icon="$cat->icon" class="w-6 h-6 sm:w-7 sm:h-7" />
                    </div>

                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white group-hover:text-red-600 transition-colors leading-tight mb-1">
                            {{ $cat->name }}
                        </h3>
                        <span class="text-[11px] font-medium text-slate-400">
                            {{ $cat->resources_count }} Verified
                        </span>
                    </div>

                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured Verified Emergency Providers --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 mb-6 sm:mb-8">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">Featured 24/7 Emergency Centers</h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">Pre-verified public and private healthcare trauma & emergency centers.</p>
            </div>
            <a href="{{ route('search') }}" class="text-xs sm:text-sm font-bold text-red-600 hover:text-red-700 dark:text-red-400 flex items-center gap-1">
                <span>View All</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($featuredResources as $resource)
                <x-resource-card :resource="$resource" />
            @endforeach
        </div>
    </section>

    {{-- How It Works --}}
    <section class="bg-white dark:bg-slate-900 py-12 sm:py-16 border-y border-slate-200/80 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mb-2">Designed for High-Speed Emergency UX</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Zero registration required to search and call emergency providers.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-center flex flex-col items-center">
                    <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 dark:bg-red-950/60 dark:text-red-400 flex items-center justify-center font-black text-lg mb-4">1</div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2">1. One-Tap Location Detect</h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        Tap "Use My Location" to automatically retrieve your current GPS coordinates or enter your area name.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-center flex flex-col items-center">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400 flex items-center justify-center font-black text-lg mb-4">2</div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2">2. Distance-Ranked Results</h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        The MySQL Haversine engine calculates exact proximity and presents nearest emergency resources with live availability and ICU badges.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 text-center flex flex-col items-center">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center justify-center font-black text-lg mb-4">3</div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white mb-2">3. Direct Call or Navigate</h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        One-click to instantly call the emergency hotline or open turn-by-turn navigation via OpenStreetMap / Google Maps.
                    </p>
                </div>
            </div>
        </div>
    </section>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const locateBtn = document.getElementById('hero-locate-btn');
    if (!locateBtn) return;

    locateBtn.addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        locateBtn.classList.add('opacity-70');
        locateBtn.querySelector('span').innerText = 'Detecting GPS...';

        navigator.geolocation.getCurrentPosition(
            function (position) {
                document.getElementById('hero-lat').value = position.coords.latitude;
                document.getElementById('hero-lng').value = position.coords.longitude;
                document.getElementById('hero-search-form').submit();
            },
            function (err) {
                locateBtn.classList.remove('opacity-70');
                locateBtn.querySelector('span').innerText = 'USE MY LOCATION';
                let msg = 'Could not get your location.';
                if (err.code === 1) msg = 'Location access was denied. Please search by typing your area/city name.';
                alert(msg);
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
});
</script>
@endpush
@endsection
