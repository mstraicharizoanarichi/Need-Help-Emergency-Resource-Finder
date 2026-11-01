{{-- One-Tap Emergency SOS Modal Component --}}
<div x-data="{ open: false }" 
     @open-sos-modal.window="open = true; document.body.classList.add('overflow-y-hidden'); setTimeout(() => detectSosGps(), 100)" 
     @keydown.escape.window="open = false; document.body.classList.remove('overflow-y-hidden')"
     x-show="open" 
     style="display: none; z-index: 9999999;" 
     class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto">
     
    {{-- Backdrop --}}
    <div x-show="open" 
         @click="open = false; document.body.classList.remove('overflow-y-hidden')"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-sm"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 backdrop-blur-sm"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         class="fixed inset-0 bg-slate-900/75 backdrop-blur-[6px]"></div>

    {{-- Modal Content --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95 blur-sm"
         x-transition:enter-end="opacity-100 scale-100 blur-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100 blur-0"
         x-transition:leave-end="opacity-0 scale-95 blur-sm"
         class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-red-500/30 overflow-hidden my-auto" 
         @click.stop>
        
        {{-- Header with Pulsing Siren & Close --}}
        <div class="bg-gradient-to-r from-red-600 via-rose-600 to-red-700 px-6 py-5 text-white flex items-center justify-between relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/10 blur-xl"></div>
            <div class="flex items-center gap-3.5 relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl font-black border border-white/30 shadow-md">
                    🚨
                </div>
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight">One-Tap Emergency SOS</h2>
                    <p class="text-xs text-red-100 font-medium">Immediate Priority (P1) Incident Dispatch</p>
                </div>
            </div>
            <button type="button" @click="open = false; document.body.classList.remove('overflow-y-hidden')" class="relative z-10 p-2 rounded-xl text-white/80 hover:text-white hover:bg-white/15 transition" title="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- 999 Prompt Alert --}}
        <div class="bg-red-50 dark:bg-red-950/50 border-b border-red-100 dark:border-red-900/50 px-6 py-3.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-xs text-red-800 dark:text-red-200">
                <span class="font-bold">Immediate Life Threat?</span>
                <span class="text-red-600 dark:text-red-400">Call national hotline:</span>
            </div>
            <a href="tel:999" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-red-600 hover:bg-red-700 text-white shadow-sm transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                Call 999
            </a>
        </div>

        {{-- SOS Dispatch Form --}}
        <form id="sos-dispatch-form" action="{{ route('emergency.sos') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="latitude" id="sos-lat" value="">
            <input type="hidden" name="longitude" id="sos-lng" value="">

            {{-- Emergency Type Selector --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    Emergency Nature
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="emergency_type" value="MEDICAL_AMBULANCE" checked class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-800 peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-950/40 text-center transition-all">
                            <span class="text-xl block mb-1">🚑</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Ambulance</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="emergency_type" value="CARDIAC_STROKE" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-800 peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-950/40 text-center transition-all">
                            <span class="text-xl block mb-1">❤️</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Heart / Stroke</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="emergency_type" value="ROAD_ACCIDENT" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-800 peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-950/40 text-center transition-all">
                            <span class="text-xl block mb-1">⚠️</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Accident</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="emergency_type" value="FIRE" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-800 peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-950/40 text-center transition-all">
                            <span class="text-xl block mb-1">🔥</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Fire Hazard</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="emergency_type" value="POLICE" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-800 peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-950/40 text-center transition-all">
                            <span class="text-xl block mb-1">👮</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Police / Safety</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="emergency_type" value="GENERAL_SOS" class="peer sr-only">
                        <div class="p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-800 peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-950/40 text-center transition-all">
                            <span class="text-xl block mb-1">🆘</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Urgent SOS</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Location Status Badge & Override Input --}}
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <span id="sos-gps-dot" class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                        <span id="sos-gps-status">Detecting high-precision GPS...</span>
                    </span>
                    <button type="button" onclick="detectSosGps()" class="text-red-600 dark:text-red-400 font-bold hover:underline">
                        Retry GPS
                    </button>
                </div>
                <input type="text" name="address" id="sos-address" placeholder="Address or Landmark (e.g. House 12, Road 4, Banani)..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-red-500">
            </div>

            @guest
                {{-- Caller Contact Phone (If not logged in) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Your Name</label>
                        <input type="text" name="reporter_name" placeholder="Full Name" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Phone Number *</label>
                        <input type="tel" name="reporter_phone" required placeholder="017XXXXXXXX" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-200">
                    </div>
                </div>
            @endguest

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Brief Situation (Optional)</label>
                <textarea name="description" rows="2" placeholder="e.g. Unconscious elder patient, severe breathing distress, 2nd floor..." class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-200"></textarea>
            </div>

            {{-- Instant SOS Trigger Button --}}
            <button type="submit" id="sos-submit-btn" class="w-full py-4 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-rose-800 text-white font-black text-base uppercase tracking-wider shadow-xl shadow-red-600/30 flex items-center justify-center gap-3 transition-all transform active:scale-98">
                <span class="w-3 h-3 rounded-full bg-white animate-ping"></span>
                <span id="sos-btn-text">CONFIRM & BROADCAST EMERGENCY SOS</span>
            </button>
        </form>

    </div>
</div>

<script>
    function openSosModal() {
        window.dispatchEvent(new CustomEvent('open-sos-modal'));
    }

    function closeSosModal() {
        // Now handled by Alpine.js via x-data open = false; but keeping function for global usage
        const evt = new KeyboardEvent('keydown', { 'key': 'Escape' });
        window.dispatchEvent(evt);
    }

    function detectSosGps() {
        const dot = document.getElementById('sos-gps-dot');
        const status = document.getElementById('sos-gps-status');

        if (!navigator.geolocation) {
            if (status) status.innerText = 'GPS not supported by browser. Enter address manually.';
            if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
            return;
        }

        if (status) status.innerText = 'Locking onto high-accuracy GPS...';
        if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                document.getElementById('sos-lat').value = lat;
                document.getElementById('sos-lng').value = lng;
                if (status) status.innerText = 'GPS Locked (' + lat.toFixed(4) + ', ' + lng.toFixed(4) + ')';
                if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500';
            },
            (err) => {
                if (status) status.innerText = 'Location permission denied. Please enter address manually.';
                if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('sos-dispatch-form');
        if (form) {
            form.addEventListener('submit', () => {
                const btn = document.getElementById('sos-submit-btn');
                const txt = document.getElementById('sos-btn-text');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-80');
                    if (txt) txt.innerText = 'BROADCASTING SOS ALERT...';
                }
            });
        }
    });

    // Backdrop click is now handled by Alpine.js @click

</script>
