<nav class="sticky top-0 z-50 bg-white/60 backdrop-blur-md border-b border-white/40 shadow-sm dark:bg-slate-900/60 dark:border-slate-700/50 dark:shadow-md transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20">
            
            {{-- Brand Logo --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-red-600 to-rose-500 text-white flex items-center justify-center shadow-md shadow-red-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 6v12M6 12h12"/>
                            <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg sm:text-xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-1.5">
                            Need<span class="text-red-600">Help</span>
                            <span class="pulse-dot bg-red-500"></span>
                        </span>
                        <p class="text-[10px] font-semibold text-slate-400 dark:text-slate-400 uppercase tracking-wider hidden sm:block">Emergency Resource Finder</p>
                    </div>
                </a>
            </div>

            {{-- Desktop Navigation Links --}}
            <div class="hidden md:flex items-center gap-1 lg:gap-2">
                <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors">
                    Home
                </a>
                <a href="{{ route('search') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('search') ? 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    Find Resources
                </a>
                <a href="{{ route('doctors.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('doctors.*') ? 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    Find Doctors
                </a>
                <a href="{{ route('blood-donors.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('blood-donors.*') ? 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Blood Donors
                </a>
                <a href="{{ route('emergency.report') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('emergency.report') ? 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400' : 'text-rose-600 hover:text-rose-700 hover:bg-rose-50/70 dark:text-rose-400 dark:hover:bg-rose-950/40' }} transition-colors flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
                    Report Emergency
                </a>
                <a href="{{ route('emergency-assistant.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('emergency-assistant.*') ? 'bg-red-50 text-red-700 dark:bg-red-950/50 dark:text-red-400' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/60' }} transition-colors flex items-center gap-1.5" title="Emergency Assistant (AI Triage)">
                    <svg class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Assistant
                </a>
            </div>

            {{-- Right Emergency Hotline, Theme Toggle & User Menu --}}
            <div class="flex items-center gap-2 sm:gap-3">
                
                {{-- One-Tap Emergency SOS Trigger Button --}}
                <button type="button" onclick="openSosModal()" class="inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-1.5 rounded-full text-xs font-black bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white shadow-md shadow-red-600/30 transition-all hover:scale-105 active:scale-95 cursor-pointer" title="Trigger Instant One-Tap Emergency SOS">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span>SOS</span>
                </button>

                {{-- National Emergency 999 Direct Call Badge --}}
                <a href="tel:999" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-800 dark:hover:bg-slate-700 shadow-sm transition-all hover:shadow-md hover:scale-105" title="Call Bangladesh National Emergency 999">
                    <svg class="w-3.5 h-3.5 text-red-500 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    999
                </a>

                {{-- Light / Dark / System Theme Toggle Dropdown --}}
                <x-theme-toggle />

                @auth
                    {{-- User Dropdown / Dashboard Button --}}
                    <div class="relative flex items-center gap-2">
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-900 text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 transition-colors flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                                Admin Panel
                            </a>
                        @elseif (Auth::user()->isProvider())
                            <a href="{{ route('provider.dashboard') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-emerald-600 text-white hover:bg-emerald-700 transition-colors flex items-center gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
                                Provider Portal
                            </a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-100 hover:bg-slate-200 text-slate-800 dark:bg-slate-800 dark:text-slate-200 transition-colors flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                                My Dashboard
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 dark:hover:text-white transition-colors" title="Logout">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-lg text-sm font-semibold text-slate-700 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800 transition-colors">
                            Sign In
                        </a>
                        <a href="{{ route('register', ['role' => 'provider']) }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 transition-all shadow-sm">
                            Add Service
                        </a>
                    </div>
                @endauth

                {{-- 3-Line Dot Menu Button (Opens Right-to-Left Slide Drawer) --}}
                <button type="button" 
                    onclick="openEmergencyDrawer()" 
                    class="p-2 sm:p-2.5 rounded-xl text-slate-700 hover:text-red-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:text-red-400 dark:hover:bg-slate-800 border border-slate-200/80 dark:border-slate-800 transition-all flex items-center justify-center group active:scale-95 shadow-xs"
                    title="All Resource Categories & Profile"
                    aria-label="Open emergency services and profile drawer">
                    {{-- 3-Line Dot Icon --}}
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-slate-700 dark:text-slate-200 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="9" y1="6" x2="20" y2="6"></line>
                        <line x1="9" y1="12" x2="20" y2="12"></line>
                        <line x1="9" y1="18" x2="20" y2="18"></line>
                        <circle cx="4" cy="6" r="1.5" fill="currentColor"></circle>
                        <circle cx="4" cy="12" r="1.5" fill="currentColor"></circle>
                        <circle cx="4" cy="18" r="1.5" fill="currentColor"></circle>
                    </svg>
                </button>

                {{-- Mobile Hamburger Button --}}
                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Toggle mobile menu">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile Menu Dropdown --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 pt-3 pb-5 space-y-2">
        <button type="button" onclick="openSosModal(); document.getElementById('mobile-menu').classList.add('hidden');" class="w-full py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 text-white font-black text-sm uppercase tracking-wider flex items-center justify-center gap-2 shadow-md shadow-red-600/30">
            <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
            ONE-TAP EMERGENCY SOS
        </button>
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Home</a>
        <a href="{{ route('emergency-assistant.index') }}" class="block px-3 py-2 rounded-lg text-base font-semibold text-red-600 dark:text-red-400 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Emergency Assistant (AI Triage)
        </a>
        <a href="{{ route('search') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Find Emergency Resources</a>
        <button type="button" onclick="openEmergencyDrawer(); document.getElementById('mobile-menu').classList.add('hidden');" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-base font-medium text-red-600 dark:text-red-400 bg-red-50/70 dark:bg-red-950/40 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="9" y1="6" x2="20" y2="6"></line>
                    <line x1="9" y1="12" x2="20" y2="12"></line>
                    <line x1="9" y1="18" x2="20" y2="18"></line>
                    <circle cx="4" cy="6" r="1.5" fill="currentColor"></circle>
                    <circle cx="4" cy="12" r="1.5" fill="currentColor"></circle>
                    <circle cx="4" cy="18" r="1.5" fill="currentColor"></circle>
                </svg>
                Resource Categories & Profile
            </span>
            <span class="text-xs font-bold uppercase tracking-wider bg-red-600 text-white px-2 py-0.5 rounded-full">Slide</span>
        </button>
        <a href="{{ route('doctors.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/40">Find Doctors & Specialists</a>
        <a href="{{ route('blood-donors.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Blood Donors Directory</a>
        <a href="{{ route('blood-donors.create') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">Register as Blood Donor</a>
        
        {{-- Mobile Theme Switcher Row --}}
        <x-theme-toggle variant="mobile" />

        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-2">
            <a href="tel:999" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-bold bg-red-600 text-white">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Call National Emergency: 999
            </a>
            @guest
                <a href="{{ route('register', ['role' => 'provider']) }}" class="w-full text-center py-2 rounded-lg text-sm font-semibold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    Register as Resource Provider
                </a>
            @endguest
        </div>
    </div>
</nav>

{{-- Slide-Over Drawer (Right-to-Left) --}}
<div id="quick-drawer-backdrop" 
    onclick="closeEmergencyDrawer()" 
    aria-hidden="true">
</div>

<aside id="quick-drawer" 
    aria-label="Emergency Resource & Profile Drawer" 
    class="border-l border-slate-200 dark:border-slate-800 flex flex-col justify-between overflow-hidden">
    
    {{-- Drawer Header --}}
    <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-900/80 backdrop-blur-sm shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-red-600 to-rose-500 text-white flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="9" y1="6" x2="20" y2="6"></line>
                    <line x1="9" y1="12" x2="20" y2="12"></line>
                    <line x1="9" y1="18" x2="20" y2="18"></line>
                    <circle cx="4" cy="6" r="1.5" fill="currentColor"></circle>
                    <circle cx="4" cy="12" r="1.5" fill="currentColor"></circle>
                    <circle cx="4" cy="18" r="1.5" fill="currentColor"></circle>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">Emergency Hub</h2>
                <p class="text-[11px] font-medium text-slate-400">Categories & Account</p>
            </div>
        </div>

        <button type="button" 
            onclick="closeEmergencyDrawer()" 
            class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-slate-200 dark:hover:bg-slate-800 transition-colors"
            title="Close Drawer"
            aria-label="Close Drawer">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    {{-- Drawer Scrollable Content --}}
    <div class="flex-1 overflow-y-auto px-4 sm:px-5 py-4 space-y-6">

        {{-- USER PROFILE & LOGOUT SECTION (IF LOGGED IN) --}}
        @auth
            <div class="bg-gradient-to-br from-slate-50 to-slate-100/80 dark:from-slate-800/60 dark:to-slate-800/30 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80 shadow-xs">
                <div class="flex items-center gap-3 mb-3.5">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-red-600 to-rose-500 text-white flex items-center justify-center font-black text-lg shadow-md shadow-red-500/20 shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                {{ Auth::user()->name }}
                            </h3>
                            @if (Auth::user()->isAdmin())
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">Admin</span>
                            @elseif (Auth::user()->isProvider())
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Provider</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">User</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                {{-- Profile / Dashboard Button & Logout Form --}}
                <div class="space-y-2 pt-2 border-t border-slate-200/80 dark:border-slate-700/60">
                    @php
                        $userDashboardUrl = Auth::user()->isAdmin() 
                            ? route('admin.dashboard') 
                            : (Auth::user()->isProvider() ? route('provider.dashboard') : route('user.dashboard'));
                    @endphp
                    <a href="{{ $userDashboardUrl }}" 
                       class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 hover:bg-red-50 hover:text-red-600 dark:hover:bg-slate-700 dark:hover:text-red-400 transition-colors border border-slate-200/80 dark:border-slate-700 shadow-xs group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                            Go to My Profile & Dashboard
                        </span>
                        <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </a>

                    {{-- Logout Action --}}
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Log Out from Account
                        </button>
                    </form>
                </div>
            </div>
        @else
            {{-- GUEST LOGIN / REGISTER --}}
            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700/80">
                <h3 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-1">Account Access</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Sign in to save emergency favorites, request blood, or manage services.</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-slate-800 dark:text-white bg-white dark:bg-slate-700 hover:bg-slate-100 dark:hover:bg-slate-600 border border-slate-200 dark:border-slate-600 transition-colors shadow-xs">
                        Sign In
                    </a>
                    <a href="{{ route('register', ['role' => 'provider']) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition-colors shadow-xs">
                        Register
                    </a>
                </div>
            </div>
        @endauth

        {{-- ALL RESOURCE CATEGORIES --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    All Resource Categories
                </h3>
                <a href="{{ route('search') }}" class="text-[11px] font-semibold text-red-600 dark:text-red-400 hover:underline">
                    View Map
                </a>
            </div>

            @php
                $drawerCategories = \App\Models\ResourceCategory::active()->withCount('resources')->get();
            @endphp

            <div class="space-y-1.5">
                @foreach ($drawerCategories as $category)
                    <a href="{{ $category->slug === 'blood-donor' ? route('blood-donors.index') : route('search', ['category' => $category->slug]) }}" 
                       class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/70 border border-transparent hover:border-slate-200 dark:hover:border-slate-700/80 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $category->getIconColorClasses() }} shrink-0 group-hover:scale-110 transition-transform">
                                <x-category-icon :icon="$category->icon" class="w-4 h-4" />
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors leading-tight">
                                    {{ $category->name }}
                                </h4>
                                <span class="text-[11px] text-slate-400">
                                    {{ $category->resources_count }} {{ Str::plural('Facility', $category->resources_count) }}
                                </span>
                            </div>
                        </div>

                        <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-red-500 group-hover:translate-x-0.5 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- EMERGENCY DIRECTORIES --}}
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                Emergency Directories
            </h3>
            <div class="space-y-1.5">
                <a href="{{ route('doctors.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50/60 dark:hover:bg-blue-950/30 text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors group">
                    <span class="flex items-center gap-2.5 text-xs font-semibold">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                        </span>
                        Find Doctors & Specialists
                    </span>
                    <svg class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </a>

                <a href="{{ route('blood-donors.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-rose-50/60 dark:hover:bg-rose-950/30 text-slate-700 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 transition-colors group">
                    <span class="flex items-center gap-2.5 text-xs font-semibold">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        </span>
                        Blood Donors Directory
                    </span>
                    <svg class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </a>

                <a href="{{ route('community.responder.register') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 text-slate-700 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors group">
                    <span class="flex items-center gap-2.5 text-xs font-semibold">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm">
                            🦺
                        </span>
                        Volunteer First Responder (CFR)
                    </span>
                    <svg class="w-4 h-4 text-slate-300 group-hover:translate-x-0.5 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
        </div>

    </div>

    {{-- Drawer Footer Hotline --}}
    <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/90 shrink-0">
        <a href="tel:999" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-black bg-red-600 hover:bg-red-700 text-white shadow-md shadow-red-600/20 transition-all hover:scale-[1.01] active:scale-98">
            <svg class="w-4 h-4 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            Call National Emergency: 999
        </a>
    </div>

</aside>

<style>
    #quick-drawer-backdrop {
        position: fixed !important;
        inset: 0 !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background-color: rgba(15, 23, 42, 0.6) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 999998 !important;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease !important;
        display: none;
    }
    #quick-drawer-backdrop.is-open {
        opacity: 1 !important;
        pointer-events: auto !important;
        display: block !important;
    }
    #quick-drawer {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        height: 100vh !important;
        height: 100dvh !important;
        width: 380px !important;
        max-width: 90vw !important;
        z-index: 999999 !important;
        transform: translateX(100%) !important;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-shadow: -12px 0 35px -5px rgba(0, 0, 0, 0.35) !important;
        background-color: #ffffff;
    }
    .dark #quick-drawer {
        background-color: #0f172a !important;
    }
    #quick-drawer.is-open {
        transform: translateX(0) !important;
    }
</style>

<script>
    function openEmergencyDrawer() {
        const drawer = document.getElementById('quick-drawer');
        const backdrop = document.getElementById('quick-drawer-backdrop');
        if (drawer && backdrop) {
            backdrop.style.display = 'block';
            // Force browser reflow so transition executes cleanly
            void backdrop.offsetWidth;
            backdrop.classList.add('is-open');
            drawer.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeEmergencyDrawer() {
        const drawer = document.getElementById('quick-drawer');
        const backdrop = document.getElementById('quick-drawer-backdrop');
        if (drawer && backdrop) {
            backdrop.classList.remove('is-open');
            drawer.classList.remove('is-open');
            document.body.style.overflow = '';
            setTimeout(() => {
                if (!backdrop.classList.contains('is-open')) {
                    backdrop.style.display = 'none';
                }
            }, 350);
        }
    }

    // Close on Escape key press
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeEmergencyDrawer();
        }
    });
</script>
