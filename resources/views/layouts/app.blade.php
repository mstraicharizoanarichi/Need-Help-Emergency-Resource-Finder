<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Need Help – Bangladesh Emergency Resource Finder')</title>
    <meta name="description" content="@yield('meta_description', 'Locate nearest verified hospitals, 24/7 ambulances, blood donors, pharmacies, police stations, and fire services in Bangladesh.')">

    {{-- Instant Anti-FOUC Theme Script --}}
    <script>
        (function() {
            try {
                var stored = localStorage.getItem('nh_theme') || 'system';
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var effective = stored === 'system' ? (prefersDark ? 'dark' : 'light') : stored;
                if (effective === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.style.colorScheme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.style.colorScheme = 'light';
                }
                document.documentElement.setAttribute('data-theme', stored);
                document.documentElement.setAttribute('data-effective-theme', effective);
            } catch (e) {}
        })();
    </script>

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class'
            };
        </script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <!-- Alpine Core + Intersect for Fallback -->
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            .pulse-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; position: relative; }
            .pulse-dot::after { content: ''; position: absolute; width: 100%; height: 100%; top: 0; left: 0; border-radius: 50%; background: inherit; animation: pulse-ring 1.8s infinite; }
            @keyframes pulse-ring { 0% { transform: scale(0.95); opacity: 0.8; } 50% { transform: scale(2.4); opacity: 0; } 100% { transform: scale(2.4); opacity: 0; } }
            .custom-pin { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; color: #fff; font-weight: 700; box-shadow: 0 4px 10px rgba(0,0,0,0.25); border: 2px solid #fff; }
            .custom-pin.hospital { background: #dc2626; }
            .custom-pin.ambulance { background: #ea580c; }
            .custom-pin.pharmacy { background: #059669; }
            .custom-pin.blood-donor { background: #e11d48; }
            .custom-pin.police-station { background: #2563eb; }
            .custom-pin.fire-service { background: #d97706; }
            .custom-pin.diagnostic-center { background: #9333ea; }
            .custom-pin.user-location { background: #0284c7; }
        </style>
    @endif

    @stack('styles')
</head>
<body class="flex flex-col min-h-full bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 font-sans antialiased selection:bg-red-500 selection:text-white">

    {{-- Accessible Skip to Main Content Link --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-red-600 focus:text-white focus:rounded-lg focus:shadow-lg">
        Skip to main content
    </a>

    {{-- Top Navbar Component --}}
    <x-navbar />

    {{-- Flash Notifications / Alerts --}}
    <main id="main-content" class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <x-alerts />
        </div>
        
        @yield('content')
    </main>

    {{-- Footer Component --}}
    <x-footer />

    {{-- One-Tap Emergency SOS Modal --}}
    <x-sos-modal />

    {{-- Floating Emergency Assistant Chat Widget --}}
    @if(!request()->routeIs('emergency-assistant.*'))
        <x-emergency-assistant />
    @endif

    @stack('scripts')
</body>
</html>
