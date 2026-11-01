@extends('layouts.app')

@section('title', 'Search Emergency Resources – Need Help')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">

    {{-- Search Filter Bar Card --}}
    <div class="bg-white/60 backdrop-blur-md dark:bg-slate-900/60 dark:backdrop-blur-md rounded-3xl p-5 sm:p-6 border border-white/40 dark:border-slate-700/50 shadow-sm dark:shadow-md transition-all">
        <form id="emergency-search-form" action="{{ route('search') }}" method="GET" class="space-y-4">
            
            {{-- Lat / Lng inputs --}}
            <input type="hidden" name="lat" id="search-lat" value="{{ $filters['lat'] ?? '' }}">
            <input type="hidden" name="lng" id="search-lng" value="{{ $filters['lng'] ?? '' }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                
                {{-- Search Query --}}
                <div class="relative lg:col-span-2">
                    <label for="q" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Keywords / Area / Name</label>
                    <div class="relative">
                        <input type="text" name="q" id="q" value="{{ $filters['query'] ?? '' }}" placeholder="Hospital name, area (e.g. Dhanmondi, Shahbagh)..." class="w-full h-11 pl-10 pr-4 rounded-xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200/50 dark:border-slate-700/50 text-sm font-medium focus:ring-2 focus:ring-red-500 transition-colors">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5 pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                </div>

                {{-- Category Dropdown --}}
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Service Type</label>
                    <select name="category" id="category" class="w-full h-11 px-3 rounded-xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200/50 dark:border-slate-700/50 text-sm font-medium focus:ring-2 focus:ring-red-500 cursor-pointer transition-colors">
                        <option value="all" {{ ($filters['category'] ?? 'all') === 'all' ? 'selected' : '' }}>🚑 All Emergency Services</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ ($filters['category'] ?? '') === $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Radius (km) --}}
                <div>
                    <label for="radius" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Search Radius</label>
                    <select name="radius" id="radius" class="w-full h-11 px-3 rounded-xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-slate-200/50 dark:border-slate-700/50 text-sm font-medium focus:ring-2 focus:ring-red-500 cursor-pointer transition-colors">
                        <option value="5" {{ ($filters['radius'] ?? 25) == 5 ? 'selected' : '' }}>Within 5 km</option>
                        <option value="10" {{ ($filters['radius'] ?? 25) == 10 ? 'selected' : '' }}>Within 10 km</option>
                        <option value="25" {{ ($filters['radius'] ?? 25) == 25 ? 'selected' : '' }}>Within 25 km</option>
                        <option value="50" {{ ($filters['radius'] ?? 25) == 50 ? 'selected' : '' }}>Within 50 km</option>
                        <option value="100" {{ ($filters['radius'] ?? 25) == 100 ? 'selected' : '' }}>Within 100 km (Wide)</option>
                    </select>
                </div>

            </div>

            {{-- Availability & Filter Buttons Bar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Filter:</span>
                    
                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer transition-colors {{ ($filters['availability'] ?? 'all') === 'available' ? 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
                        <input type="radio" name="availability" value="available" onchange="this.form.submit()" {{ ($filters['availability'] ?? 'all') === 'available' ? 'checked' : '' }} class="sr-only">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Available Now
                    </label>

                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer transition-colors {{ ($filters['availability'] ?? 'all') === '24_7' ? 'bg-blue-50 text-blue-800 border-blue-300 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
                        <input type="radio" name="availability" value="24_7" onchange="this.form.submit()" {{ ($filters['availability'] ?? 'all') === '24_7' ? 'checked' : '' }} class="sr-only">
                        <span class="pulse-dot bg-blue-500"></span>
                        24/7 Open
                    </label>

                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold cursor-pointer transition-colors {{ ($filters['availability'] ?? 'all') === 'all' ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 border-slate-900 dark:border-white' : 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
                        <input type="radio" name="availability" value="all" onchange="this.form.submit()" {{ ($filters['availability'] ?? 'all') === 'all' ? 'checked' : '' }} class="sr-only">
                        All Statuses
                    </label>
                </div>

                <div class="flex items-center gap-2">
                    {{-- USE GPS Location button --}}
                    <button type="button" id="search-locate-btn" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-white text-xs font-bold flex items-center gap-1.5 border border-slate-200 dark:border-slate-700">
                        <svg class="w-3.5 h-3.5 text-red-600 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                        <span>USE MY LOCATION</span>
                    </button>

                    <button type="submit" class="px-6 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm shadow-sm transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <span>Apply Search</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- Interactive Leaflet Map View for Search Results --}}
    <div class="bg-white/60 backdrop-blur-md dark:bg-slate-900/60 dark:backdrop-blur-md rounded-3xl p-4 border border-white/40 dark:border-slate-700/50 shadow-sm dark:shadow-md transition-all">
        <div class="flex items-center justify-between gap-3 mb-3 px-2">
            <h3 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                Live Emergency Interactive Map
            </h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Click pins for 1-click call & directions</span>
        </div>

        @php
            $mapMarkers = $resources->map(function ($r) {
                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'category_slug' => $r->category?->slug,
                    'category_name' => $r->category?->name,
                    'address' => $r->address,
                    'area' => $r->area,
                    'latitude' => (float) $r->latitude,
                    'longitude' => (float) $r->longitude,
                    'phone' => $r->phone,
                    'clean_phone' => $r->getCleanPhone(),
                    'is_verified' => $r->isVerified(),
                    'is_24_7' => (bool) $r->is_24_7,
                    'formatted_distance' => $r->formatted_distance,
                    'directions_url' => $r->getDirectionsUrl(),
                ];
            })->toArray();

            $mapLat = !empty($filters['lat']) ? (float) $filters['lat'] : 23.7500;
            $mapLng = !empty($filters['lng']) ? (float) $filters['lng'] : 90.3800;
            $mapRadius = !empty($filters['radius']) ? (float) $filters['radius'] : null;
        @endphp

        <x-map-view 
            :lat="$mapLat" 
            :lng="$mapLng" 
            :radius="$mapRadius" 
            :markers="$mapMarkers" 
            height="380px" 
            mapId="search-results-map" 
        />
    </div>

    {{-- Results Header & Sort Info --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>Search Results</span>
                <span class="text-sm font-bold px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300">
                    {{ $resources->total() }} {{ Str::plural('Facility', $resources->total()) }}
                </span>
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                @if (!empty($filters['lat']) && !empty($filters['lng']))
                    Showing resources sorted by closest distance to your location (within {{ $filters['radius'] ?? 25 }} km).
                @else
                    Showing all emergency resources in Bangladesh. Tap "USE MY LOCATION" to sort by nearest.
                @endif
            </p>
        </div>

        @if (!empty($filters['lat']) || !empty($filters['query']) || ($filters['category'] ?? 'all') !== 'all')
            <a href="{{ route('search') }}" class="text-xs font-semibold text-slate-500 hover:text-red-600 dark:hover:text-red-400 self-start sm:self-auto">
                ✕ Reset Filters
            </a>
        @endif
    </div>

    {{-- Resource Cards Grid or Empty State --}}
    @if ($resources->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($resources as $resource)
                <x-resource-card :resource="$resource" />
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pt-6">
            {{ $resources->links() }}
        </div>
    @else
        {{-- Friendly Empty State --}}
        <div class="bg-white/60 backdrop-blur-md dark:bg-slate-900/60 dark:backdrop-blur-md rounded-3xl p-10 text-center border border-white/40 dark:border-slate-700/50 shadow-sm dark:shadow-md max-w-xl mx-auto space-y-4 transition-all">
            <div class="w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto text-2xl">
                📍
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">No emergency resources found within this distance</h3>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                No facilities matched your exact location and radius. Try increasing the search radius to 50 km or clearing specific keyword filters.
            </p>
            <div class="pt-2 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('search', ['radius' => 50, 'category' => $filters['category'] ?? 'all']) }}" class="px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700">
                    Expand Radius to 50 km
                </a>
                <a href="{{ route('search') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-200">
                    View All Resources
                </a>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const locateBtn = document.getElementById('search-locate-btn');
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
                document.getElementById('search-lat').value = position.coords.latitude;
                document.getElementById('search-lng').value = position.coords.longitude;
                document.getElementById('emergency-search-form').submit();
            },
            function (err) {
                locateBtn.classList.remove('opacity-70');
                locateBtn.querySelector('span').innerText = 'USE MY LOCATION';
                let msg = 'Could not get your location.';
                if (err.code === 1) msg = 'Location access was denied. Please enable location access or type your area name.';
                alert(msg);
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });
});
</script>
@endpush
@endsection
