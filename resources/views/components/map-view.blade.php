@props([
    'lat' => 23.7500,
    'lng' => 90.3800,
    'zoom' => 12,
    'height' => '500px',
    'interactive' => true,
    'showLocateButton' => true,
    'radius' => null,
    'markers' => [],
    'mapId' => 'leaflet-emergency-map',
])

<div class="relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm" style="height: {{ $height }};">
    
    {{-- Map Canvas --}}
    <div id="{{ $mapId }}" class="w-full h-full z-0 bg-slate-100 dark:bg-slate-800"></div>

    {{-- Map Overlay Controls (Locate Me & Radius) --}}
    @if ($showLocateButton)
        <div class="absolute top-4 right-4 z-10 flex flex-col gap-2">
            <button type="button" id="{{ $mapId }}-locate-btn" class="p-3 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-white shadow-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-2 text-xs font-bold" title="Use My Current GPS Location">
                <svg class="w-4 h-4 text-red-600 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                <span>Locate Me</span>
            </button>
        </div>
    @endif

</div>

{{-- Leaflet CSS & JS (CDN) --}}
@once
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endonce

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('{{ $mapId }}');
    if (!mapElement) return;

    const initialLat = {{ (float) $lat }};
    const initialLng = {{ (float) $lng }};
    const initialZoom = {{ (int) $zoom }};
    const radiusKm = {{ $radius ? (float) $radius : 'null' }};
    const initialMarkers = @json($markers);

    // Initialize map
    const map = L.map('{{ $mapId }}', {
        center: [initialLat, initialLng],
        zoom: initialZoom,
        zoomControl: true,
    });

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    let markersLayer = L.layerGroup().addTo(map);
    let radiusCircle = null;
    let userMarker = null;

    // Helper for category SVG pins
    function createCategoryIcon(categorySlug) {
        const pinClass = categorySlug || 'hospital';
        return L.divIcon({
            className: 'custom-leaflet-pin',
            html: `<div class="custom-pin ${pinClass}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v8M8 12h8"/>
                </svg>
            </div>`,
            iconSize: [38, 38],
            iconAnchor: [19, 19],
            popupAnchor: [0, -20]
        });
    }

    function createUserIcon() {
        return L.divIcon({
            className: 'custom-leaflet-user-pin',
            html: `<div class="custom-pin user-location ring-4 ring-blue-400/40">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
            </div>`,
            iconSize: [38, 38],
            iconAnchor: [19, 19],
            popupAnchor: [0, -20]
        });
    }

    // Render Markers
    function renderMarkers(items) {
        markersLayer.clearLayers();

        if (!items || items.length === 0) return;

        const bounds = L.latLngBounds();

        items.forEach(item => {
            if (!item.latitude || !item.longitude) return;

            const icon = createCategoryIcon(item.category_slug);
            const marker = L.marker([item.latitude, item.longitude], { icon: icon });

            const distanceHtml = item.formatted_distance 
                ? `<span class="inline-block px-2 py-0.5 rounded text-[11px] font-bold bg-slate-900 text-white">${item.formatted_distance}</span>` 
                : '';

            const verifiedHtml = item.is_verified
                ? `<span class="text-[11px] font-bold text-blue-600 dark:text-blue-400">✓ Verified</span>`
                : `<span class="text-[11px] font-medium text-amber-600 dark:text-amber-400">Pending</span>`;

            const popupContent = `
                <div class="p-3.5 max-w-[260px] font-sans text-slate-900 dark:text-slate-100">
                    <div class="flex items-center justify-between gap-1 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">${item.category_name || 'Emergency'}</span>
                        ${verifiedHtml}
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-1 leading-snug">${item.name}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-2.5 leading-tight">${item.address || ''}${item.area ? ', ' + item.area : ''}</p>
                    <div class="flex items-center gap-1.5 mb-2.5">
                        ${distanceHtml}
                        ${item.is_24_7 ? '<span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">● 24/7</span>' : ''}
                    </div>
                    <div class="grid grid-cols-2 gap-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <a href="tel:${item.clean_phone || item.phone}" class="py-1.5 px-2 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-xs text-center flex items-center justify-center gap-1">
                            📞 CALL
                        </a>
                        <a href="${item.directions_url}" target="_blank" class="py-1.5 px-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs text-center">
                            Route
                        </a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent);
            markersLayer.addLayer(marker);
            bounds.extend([item.latitude, item.longitude]);
        });

        if (items.length > 1) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
        }
    }

    // Initial render from passed markers
    if (initialMarkers && initialMarkers.length > 0) {
        renderMarkers(initialMarkers);
    }

    // Radius Circle
    if (radiusKm && radiusKm > 0) {
        radiusCircle = L.circle([initialLat, initialLng], {
            radius: radiusKm * 1000,
            color: '#ef4444',
            fillColor: '#ef4444',
            fillOpacity: 0.08,
            weight: 1.5,
            dashArray: '5, 5'
        }).addTo(map);
    }

    // Geolocation Locate Me Button
    const locateBtn = document.getElementById('{{ $mapId }}-locate-btn');
    if (locateBtn) {
        locateBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            locateBtn.classList.add('opacity-75');
            locateBtn.querySelector('span').innerText = 'Locating...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    if (userMarker) {
                        userMarker.setLatLng([userLat, userLng]);
                    } else {
                        userMarker = L.marker([userLat, userLng], { icon: createUserIcon() })
                            .bindPopup('<div class="p-2 text-xs font-bold">📍 Your Current GPS Location</div>')
                            .addTo(map);
                    }

                    map.setView([userLat, userLng], 14);

                    locateBtn.classList.remove('opacity-75');
                    locateBtn.querySelector('span').innerText = 'Located';

                    // If on search page, redirect or update form inputs
                    const latInput = document.getElementById('search-lat');
                    const lngInput = document.getElementById('search-lng');
                    if (latInput && lngInput) {
                        latInput.value = userLat;
                        lngInput.value = userLng;
                        const searchForm = document.getElementById('emergency-search-form');
                        if (searchForm) {
                            searchForm.submit();
                        }
                    }
                },
                function (error) {
                    locateBtn.classList.remove('opacity-75');
                    locateBtn.querySelector('span').innerText = 'Locate Me';
                    let errorMsg = 'Unable to retrieve location.';
                    if (error.code === error.PERMISSION_DENIED) {
                        errorMsg = 'Location permission was denied. Please allow location access in your browser settings.';
                    } else if (error.code === error.TIMEOUT) {
                        errorMsg = 'Location request timed out. Please try again.';
                    }
                    alert(errorMsg);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        });
    }

    // Expose map instance globally for coordinate pickers
    window['map_{{ $mapId }}'] = map;
});
</script>
