<footer class="bg-slate-900 text-slate-400 pt-12 pb-8 border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Emergency Hotlines Bar --}}
        <div class="bg-slate-800/80 rounded-2xl p-6 mb-10 border border-slate-700/60 shadow-lg">
            <h3 class="text-white font-bold text-base sm:text-lg mb-4 flex items-center gap-2">
                <span class="pulse-dot bg-red-500"></span>
                Bangladesh National Emergency Hotlines (24/7 Toll-Free / Quick Access)
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                <a href="tel:999" class="p-3 rounded-xl bg-slate-900/90 border border-red-500/30 hover:border-red-500 transition-all flex flex-col group">
                    <span class="text-xs text-red-400 font-medium">National Emergency</span>
                    <span class="text-xl font-black text-white group-hover:text-red-400">999</span>
                    <span class="text-[11px] text-slate-400">Police, Fire, Ambulance</span>
                </a>
                <a href="tel:16163" class="p-3 rounded-xl bg-slate-900/90 border border-amber-500/30 hover:border-amber-500 transition-all flex flex-col group">
                    <span class="text-xs text-amber-400 font-medium">Fire Service</span>
                    <span class="text-xl font-black text-white group-hover:text-amber-400">16163</span>
                    <span class="text-[11px] text-slate-400">HQ Control Room</span>
                </a>
                <a href="tel:109" class="p-3 rounded-xl bg-slate-900/90 border border-rose-500/30 hover:border-rose-500 transition-all flex flex-col group">
                    <span class="text-xs text-rose-400 font-medium">Child & Women Helpline</span>
                    <span class="text-xl font-black text-white group-hover:text-rose-400">109</span>
                    <span class="text-[11px] text-slate-400">Ministry of Women Affairs</span>
                </a>
                <a href="tel:333" class="p-3 rounded-xl bg-slate-900/90 border border-blue-500/30 hover:border-blue-500 transition-all flex flex-col group">
                    <span class="text-xs text-blue-400 font-medium">National Helpdesk</span>
                    <span class="text-xl font-black text-white group-hover:text-blue-400">333</span>
                    <span class="text-[11px] text-slate-400">Government Info & Relief</span>
                </a>
                <a href="tel:10655" class="p-3 rounded-xl bg-slate-900/90 border border-emerald-500/30 hover:border-emerald-500 transition-all flex flex-col group">
                    <span class="text-xs text-emerald-400 font-medium">IEDCR Health Line</span>
                    <span class="text-xl font-black text-white group-hover:text-emerald-400">10655</span>
                    <span class="text-[11px] text-slate-400">Health Distress & Diseases</span>
                </a>
            </div>
        </div>

        {{-- Footer Main Columns --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10 text-sm">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-red-600 text-white flex items-center justify-center font-bold">
                        NH
                    </div>
                    <span class="text-white font-bold text-lg">Need Help</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Bangladesh's premier open emergency resource directory. Helping citizens instantly locate verified hospitals, ambulances, blood donors, pharmacies, police, and fire services during critical emergencies.
                </p>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">Emergency Services</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('search', ['category' => 'hospital']) }}" class="hover:text-white transition-colors">Emergency Hospitals & ICU</a></li>
                    <li><a href="{{ route('search', ['category' => 'ambulance']) }}" class="hover:text-white transition-colors">24/7 Ambulance Fleet</a></li>
                    <li><a href="{{ route('search', ['category' => 'pharmacy']) }}" class="hover:text-white transition-colors">24-Hour Pharmacies</a></li>
                    <li><a href="{{ route('blood-donors.index') }}" class="hover:text-white transition-colors">Blood Donor Directory</a></li>
                    <li><a href="{{ route('search', ['category' => 'police-station']) }}" class="hover:text-white transition-colors">Police Stations (Thana)</a></li>
                    <li><a href="{{ route('search', ['category' => 'fire-service']) }}" class="hover:text-white transition-colors">Fire & Civil Defence</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">For Providers & Donors</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('register', ['role' => 'provider']) }}" class="hover:text-white transition-colors">List Your Hospital / Service</a></li>
                    <li><a href="{{ route('blood-donors.create') }}" class="hover:text-white transition-colors">Register as Voluntary Blood Donor</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Provider Portal Sign In</a></li>
                    <li><a href="{{ route('search') }}" class="hover:text-white transition-colors">Nearby Geolocation Finder</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3">Platform Integrity</h4>
                <p class="text-xs text-slate-400 leading-relaxed mb-3">
                    All emergency resources undergo administrative verification. Found incorrect phone numbers or wrong coordinates?
                </p>
                <a href="{{ route('search') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-400 hover:text-red-300">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Report Inaccurate Resource
                </a>
            </div>
        </div>

        {{-- Bottom Copyright --}}
        <div class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
            <p>&copy; {{ date('Y') }} Need Help – Emergency Resource Finder. Academic Project. Built for Bangladesh.</p>
            <p class="text-[11px] text-slate-400">Demo Data for illustrative & emergency preparedness testing.</p>
        </div>

    </div>
</footer>
