<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Provider Portal – Need Help')</title>

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
    @endif

    @stack('styles')
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased h-full flex flex-col md:flex-row">

    {{-- Provider Sidebar Navigation --}}
    <aside class="w-full md:w-64 bg-slate-900 text-slate-300 flex flex-col shrink-0 border-r border-slate-800">
        <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
            <a href="{{ route('provider.dashboard') }}" class="flex items-center gap-2 font-black text-white text-lg tracking-tight">
                <span class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center text-xs font-bold">PR</span>
                <span>Provider<span class="text-emerald-400">Portal</span></span>
            </a>
            <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-white" title="Go to Public Site">
                ↗ Web
            </a>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <a href="{{ route('provider.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('provider.dashboard') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                Overview
            </a>

            <a href="{{ route('provider.dispatches.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('provider.dispatches.*') ? 'bg-rose-600 text-white' : 'hover:bg-slate-800 hover:text-white' }} transition-colors">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polygon points="12 6 12 12 16 14"/></svg>
                    <span>Emergency Dispatches</span>
                </span>
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            </a>

            <a href="{{ route('provider.resources.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('provider.resources.index', 'provider.resources.edit') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                My Emergency Resources
            </a>

            <a href="{{ route('provider.resources.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('provider.resources.create') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Add New Resource
            </a>

            <a href="{{ route('provider.doctors.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('provider.doctors.*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Hospital Doctors
            </a>

            <a href="{{ route('provider.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('provider.reports.*') ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white' }} transition-colors">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0z"/><path d="M12 8v4M12 16h.01"/></svg>
                Received Reports
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2.5 overflow-hidden">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                    PR
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->organization_name ?: 'Resource Provider' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800" title="Logout">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Provider Content --}}
    <main class="flex-1 flex flex-col overflow-y-auto min-h-screen">
        <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between sticky top-0 z-20">
            <h1 class="text-lg font-bold text-slate-900 dark:text-white">@yield('page_title', 'Provider Portal')</h1>
            <div class="flex items-center gap-3">
                <x-theme-toggle />
                <a href="{{ route('provider.resources.create') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white flex items-center gap-1 shadow-sm">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                    + Add Emergency Service
                </a>
            </div>
        </header>

        <div class="p-6 md:p-8 flex-1">
            <x-alerts />
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
