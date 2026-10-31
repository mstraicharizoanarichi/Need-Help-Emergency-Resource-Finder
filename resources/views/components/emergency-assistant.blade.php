<div x-data="emergencyAssistant()" 
     x-cloak
     class="fixed bottom-5 right-5 z-40 print:hidden font-sans"
     @keydown.escape.window="isOpen = false">

    {{-- Floating Standalone AI Chatbot Launcher Icon --}}
    <div x-show="!isOpen" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         class="relative flex items-center justify-center group">
        
        {{-- Soft Outer Ambient Glow / Pulse Ring --}}
        <div class="absolute -inset-1.5 rounded-full bg-gradient-to-br from-red-500 to-rose-700 opacity-30 blur-md group-hover:opacity-60 transition-opacity duration-300 animate-pulse"></div>

        <button type="button" 
                @click="openChat()" 
                id="emergency-chatbot-launcher-btn"
                aria-label="Open Emergency AI Assistant"
                title="Need Help Emergency AI Assistant"
                class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-full flex items-center justify-center bg-gradient-to-br from-red-500 via-red-600 to-rose-900 text-white shadow-xl shadow-red-600/40 hover:shadow-red-600/60 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer border border-white/30 backdrop-blur-md">
            
            {{-- Top-Right Active Status Indicator Dot --}}
            <span class="absolute top-0.5 right-0.5 sm:top-1 sm:right-1 flex h-3 w-3 sm:h-3.5 sm:w-3.5 z-10" title="AI Assistant Online">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 sm:h-3.5 sm:w-3.5 bg-emerald-500 border-2 border-white/80 dark:border-slate-900 shadow-sm shadow-emerald-500/50"></span>
            </span>

            {{-- Centered Standalone Robot Chat Bubble Character --}}
            <svg class="w-8 h-8 sm:w-9 sm:h-9 text-white transition-transform duration-200 group-hover:scale-105" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Top Antenna Knob -->
                <circle cx="32" cy="13.5" r="3.25" fill="#FFFFFF" />
                
                <!-- Antenna Stem -->
                <rect x="30.5" y="16.5" width="3" height="4.5" rx="1.5" fill="#FFFFFF" />

                <!-- Left Ear Pad -->
                <rect x="9.5" y="27" width="4.5" height="11.5" rx="2.25" fill="#FFFFFF" />

                <!-- Right Ear Pad -->
                <rect x="50" y="27" width="4.5" height="11.5" rx="2.25" fill="#FFFFFF" />

                <!-- Robot Head / Speech Bubble Body (White fill) -->
                <path d="M 21 20.5 H 43 C 48 20.5 51.5 24 51.5 29 V 39 C 51.5 44 48 47.5 43 47.5 H 28 L 20 54.5 L 21.5 47.5 H 21 C 16 47.5 12.5 44 12.5 39 V 29 C 12.5 24 16 20.5 21 20.5 Z" 
                      fill="#FFFFFF" />

                <!-- Friendly Eyes (Cutout in Emergency Red) -->
                <circle cx="24" cy="31.5" r="3.4" fill="#DC2626" />
                <circle cx="40" cy="31.5" r="3.4" fill="#DC2626" />

                <!-- Pleasant Smile (Curved in Emergency Red) -->
                <path d="M 26.5 38 Q 32 42.5 37.5 38" 
                      stroke="#DC2626" 
                      stroke-width="2.75" 
                      stroke-linecap="round" 
                      fill="none" />
            </svg>
        </button>
    </div>

    {{-- Chat Drawer / Dialog --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         class="w-[92vw] sm:w-[420px] h-[580px] max-h-[85vh] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden backdrop-blur-xl">

        {{-- Header --}}
        <div class="px-4 py-3.5 bg-gradient-to-r from-red-600 to-rose-700 text-white flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border border-white/20 p-1">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32" cy="13.5" r="3.25" fill="#FFFFFF" />
                        <rect x="30.5" y="16.5" width="3" height="4.5" rx="1.5" fill="#FFFFFF" />
                        <rect x="9.5" y="27" width="4.5" height="11.5" rx="2.25" fill="#FFFFFF" />
                        <rect x="50" y="27" width="4.5" height="11.5" rx="2.25" fill="#FFFFFF" />
                        <path d="M 21 20.5 H 43 C 48 20.5 51.5 24 51.5 29 V 39 C 51.5 44 48 47.5 43 47.5 H 28 L 20 54.5 L 21.5 47.5 H 21 C 16 47.5 12.5 44 12.5 39 V 29 C 12.5 24 16 20.5 21 20.5 Z" fill="#FFFFFF" />
                        <circle cx="24" cy="31.5" r="3.4" fill="#DC2626" />
                        <circle cx="40" cy="31.5" r="3.4" fill="#DC2626" />
                        <path d="M 26.5 38 Q 32 42.5 37.5 38" stroke="#DC2626" stroke-width="2.75" stroke-linecap="round" fill="none" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h3 class="text-sm font-bold tracking-tight">Need Help Assistant</h3>
                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold uppercase bg-white/20 text-white">Live</span>
                    </div>
                    <p class="text-[10px] text-white/80 leading-tight">Instant Triage & Bangladesh Lifelines</p>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                {{-- Language Toggle --}}
                <div class="inline-flex rounded-lg bg-black/20 p-0.5 text-[11px] font-semibold">
                    <button type="button" 
                            @click="setLanguage('bn')" 
                            :class="language === 'bn' ? 'bg-white text-red-700 shadow-xs' : 'text-white/80 hover:text-white'"
                            class="px-2 py-0.5 rounded transition-colors">
                        বাং
                    </button>
                    <button type="button" 
                            @click="setLanguage('en')" 
                            :class="language === 'en' ? 'bg-white text-red-700 shadow-xs' : 'text-white/80 hover:text-white'"
                            class="px-2 py-0.5 rounded transition-colors">
                        EN
                    </button>
                </div>

                {{-- Fullscreen External Link --}}
                <a href="{{ route('emergency-assistant.index') }}" target="_blank" class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Open Fullscreen Assistant">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
                    </svg>
                </a>

                {{-- Close Button --}}
                <button type="button" 
                        @click="isOpen = false" 
                        class="p-1.5 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors" 
                        title="Close Assistant">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Pinned Emergency Red-Flag Banner --}}
        <div x-show="isEmergencyActive" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-red-50 dark:bg-red-950/70 border-b border-red-200 dark:border-red-900/60 p-2.5 flex items-center justify-between gap-2 text-xs">
            <div class="flex items-center gap-2 text-red-800 dark:text-red-200">
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
                </span>
                <span class="font-bold leading-tight" x-text="language === 'bn' ? 'জরুরি অবস্থা সনাক্ত! অবিলম্বে ৯৯৯-এ কল করুন।' : 'Emergency detected! Dial 999 immediately.'"></span>
            </div>
            <button type="button" 
                    @click="triggerEmergencySos()"
                    :disabled="sosLoading"
                    class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md font-bold text-[11px] shadow-xs active:scale-95 transition-all flex items-center gap-1 shrink-0">
                <span x-show="!sosLoading">🚨 SOS</span>
                <span x-show="sosLoading" class="animate-spin inline-block w-3 h-3 border-2 border-white border-t-transparent rounded-full"></span>
            </button>
        </div>

        {{-- Messages Scroll Area --}}
        <div x-ref="messagesBox" class="flex-1 overflow-y-auto p-4 space-y-3.5 bg-slate-50/50 dark:bg-slate-950/50">
            
            {{-- Initial Welcome Assistant Bubble --}}
            <template x-for="(item, index) in messages" :key="index">
                <div :class="item.sender === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="item.sender === 'user' 
                            ? 'bg-red-600 text-white rounded-2xl rounded-tr-none px-3.5 py-2.5 max-w-[85%] text-xs shadow-xs' 
                            : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-2xl rounded-tl-none px-3.5 py-2.5 max-w-[88%] text-xs shadow-sm border border-slate-200 dark:border-slate-700/60'">
                        
                        {{-- Message Text formatted --}}
                        <div class="whitespace-pre-line leading-relaxed" x-text="item.message"></div>

                        {{-- Structured Question Options --}}
                        <template x-if="item.options && item.options.length > 0">
                            <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-700/60 space-y-1.5">
                                <p class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    <span x-text="language === 'bn' ? 'দ্রুত উত্তর দিন:' : 'Select Option:'"></span>
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="opt in item.options" :key="opt.value">
                                        <button type="button" 
                                                @click="sendOption(opt.value, opt.label)"
                                                class="px-2.5 py-1 text-[11px] font-medium bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-950/50 dark:hover:bg-red-900/60 dark:text-red-300 rounded-lg border border-red-200 dark:border-red-900 transition-colors">
                                            <span x-text="opt.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Quick Reply Pills --}}
                        <template x-if="item.quick_replies && item.quick_replies.length > 0 && (!item.options || item.options.length === 0)">
                            <div class="mt-2 flex flex-wrap gap-1">
                                <template x-for="reply in item.quick_replies" :key="reply">
                                    <button type="button" 
                                            @click="sendMessage(reply)"
                                            class="px-2 py-0.5 text-[10px] font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-md transition-colors">
                                        <span x-text="reply"></span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        {{-- Hospital Recommendations Cards --}}
                        <template x-if="item.hospitals && item.hospitals.length > 0">
                            <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-700/60 space-y-2">
                                <p class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase flex items-center gap-1">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                    <span x-text="language === 'bn' ? 'নিকটস্থ জরুরি হাসপাতালসমূহ:' : 'Recommended Hospitals:'"></span>
                                </p>
                                <div class="space-y-1.5">
                                    <template x-for="hosp in item.hospitals" :key="hosp.id">
                                        <div class="p-2 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <h5 class="font-bold text-[11px] truncate text-slate-900 dark:text-white" x-text="hosp.name"></h5>
                                                <p class="text-[9px] text-slate-500 dark:text-slate-400 truncate" x-text="hosp.address"></p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <template x-if="hosp.has_icu">
                                                        <span class="text-[8px] px-1 py-0.2 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold rounded">ICU Available</span>
                                                    </template>
                                                    <template x-if="hosp.distance_km">
                                                        <span class="text-[9px] text-slate-400" x-text="hosp.distance_km + ' km'"></span>
                                                    </template>
                                                </div>
                                            </div>
                                            <a :href="'tel:' + (hosp.emergency_hotline || hosp.phone || '999')" 
                                               class="p-1.5 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white shrink-0" 
                                               title="Call Emergency Hotline">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Incident Tracking Link --}}
                        <template x-if="item.metadata && item.metadata.tracking_url">
                            <div class="mt-2">
                                <a :href="item.metadata.tracking_url" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 dark:text-red-400 hover:underline">
                                    <span>Track Live Dispatch Status</span>
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Loading Indicator Bubble --}}
            <div x-show="isLoading" class="flex justify-start">
                <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs shadow-xs border border-slate-200 dark:border-slate-700/60 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-bounce"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-bounce [animation-delay:0.4s]"></span>
                    <span class="text-[10px] text-slate-400 ml-1 font-medium" x-text="language === 'bn' ? 'বিশ্লেষণ চলছে...' : 'Evaluating triage...'"></span>
                </div>
            </div>
        </div>

        {{-- Quick Symptom Chips --}}
        <div class="px-3 py-1.5 bg-slate-100/80 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center gap-1.5 overflow-x-auto no-scrollbar text-[10px]">
            <button type="button" @click="sendMessage(language === 'bn' ? 'স্ট্রোকের লক্ষণ' : 'Stroke signs')" class="px-2 py-0.5 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors">
                🧠 <span x-text="language === 'bn' ? 'স্ট্রোক' : 'Stroke'"></span>
            </button>
            <button type="button" @click="sendMessage(language === 'bn' ? 'বুকে প্রচণ্ড ব্যথা' : 'Crushing chest pain')" class="px-2 py-0.5 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors">
                ❤️ <span x-text="language === 'bn' ? 'বুকে ব্যথা' : 'Chest Pain'"></span>
            </button>
            <button type="button" @click="sendMessage(language === 'bn' ? 'শ্বাস নিতে পারছি না' : 'Cannot breathe')" class="px-2 py-0.5 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors">
                🫁 <span x-text="language === 'bn' ? 'শ্বাসকষ্ট' : 'Breathing'"></span>
            </button>
            <button type="button" @click="sendMessage(language === 'bn' ? 'প্রচণ্ড রক্তপাত হচ্ছে' : 'Severe bleeding')" class="px-2 py-0.5 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors">
                🩸 <span x-text="language === 'bn' ? 'রক্তপাত' : 'Bleeding'"></span>
            </button>
            <button type="button" @click="sendMessage(language === 'bn' ? 'নিকটস্থ হাসপাতাল কোথায়?' : 'Nearest hospital?')" class="px-2 py-0.5 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors">
                🏥 <span x-text="language === 'bn' ? 'হাসপাতাল' : 'Hospital'"></span>
            </button>
        </div>

        {{-- Input Form --}}
        <div class="p-2.5 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
            <form @submit.prevent="submitForm()" class="flex items-center gap-1.5">
                {{-- GPS Pin status --}}
                <button type="button" 
                        @click="requestLocation()" 
                        :class="hasLocation ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'"
                        class="p-2 rounded-xl transition-colors cursor-pointer shrink-0" 
                        :title="hasLocation ? 'Location Active' : 'Share GPS Location'">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                </button>

                <input type="text" 
                       x-model="inputMessage" 
                       :placeholder="language === 'bn' ? 'কী সমস্যা হচ্ছে সংক্ষেপে লিখুন...' : 'Describe symptoms or ask guidance...'"
                       class="flex-1 px-3 py-2 text-xs bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-hidden focus:border-red-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400"
                       :disabled="isLoading">

                <button type="submit" 
                        :disabled="isLoading || !inputMessage.trim()" 
                        class="p-2 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white rounded-xl shadow-xs transition-colors shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                </button>
            </form>
            
            <p class="text-[9px] text-slate-400 dark:text-slate-400 text-center mt-1.5 leading-tight">
                ⚠️ <span x-text="language === 'bn' ? 'অ-নির্ণয়কারী জরুরি ট্রায়াজ। প্রাণঘাতী অবস্থায় সরাসরি ৯৯৯-এ কল করুন।' : 'Non-diagnostic assistant. For life-threatening emergencies, dial 999 directly.'"></span>
            </p>
        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('emergencyAssistant', () => ({
        isOpen: false,
        isLoading: false,
        sosLoading: false,
        language: '{{ app()->getLocale() === "bn" ? "bn" : "bn" }}',
        conversationId: null,
        inputMessage: '',
        isEmergencyActive: false,
        severityLevel: 'P3',
        hasLocation: false,
        latitude: null,
        longitude: null,
        messages: [],

        init() {
            this.requestLocation();
        },

        openChat() {
            this.isOpen = true;
            if (this.messages.length === 0) {
                this.initConversation();
            }
            this.$nextTick(() => this.scrollToBottom());
        },

        async initConversation() {
            try {
                const res = await fetch('{{ route("emergency-assistant.conversations.init") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ language: this.language })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    this.conversationId = data.conversation.id;
                    this.addWelcomeMessage();
                }
            } catch (err) {
                console.error('Failed to init assistant session:', err);
                this.addWelcomeMessage();
            }
        },

        addWelcomeMessage() {
            const welcomeText = this.language === 'bn'
                ? "আসসালামু আলাইকুম। আমি নিড হেল্প জরুরি স্বাস্থ্য সহকারী। আপনার কী ধরনের জরুরি লক্ষণ বা সমস্যা হচ্ছে লিখুন (যেমন: বুকে ব্যথা, স্ট্রোকের লক্ষণ, শ্বাসকষ্ট, পোড়া বা দুর্ঘটনা)।"
                : "Hello. I am the Need Help Emergency Assistant. Describe any emergency symptoms or signs you observe (e.g. chest pain, stroke signs, difficulty breathing, burns, or road accident).";

            this.messages.push({
                sender: 'assistant',
                message: welcomeText,
                options: [],
                quick_replies: this.language === 'bn' 
                    ? ['স্ট্রোকের লক্ষণ কী?', 'বুকে প্রচণ্ড চাপ ও ব্যথা', 'দম আটকে আসছে', 'নিকটস্থ হাসপাতাল'] 
                    : ['Stroke warning signs', 'Severe crushing chest pain', 'Cannot breathe', 'Nearest hospital'],
                hospitals: []
            });
        },

        async setLanguage(lang) {
            this.language = lang;
            if (this.conversationId) {
                try {
                    await fetch(`/emergency-assistant/${this.conversationId}/language`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ language: lang })
                    });
                } catch (e) {}
            }
        },

        requestLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.latitude = pos.coords.latitude;
                        this.longitude = pos.coords.longitude;
                        this.hasLocation = true;
                    },
                    (err) => {
                        this.hasLocation = false;
                    },
                    { enableHighAccuracy: true, timeout: 5000 }
                );
            }
        },

        async submitForm() {
            if (!this.inputMessage.trim() || this.isLoading) return;
            const text = this.inputMessage.trim();
            this.inputMessage = '';
            await this.sendMessage(text);
        },

        async sendOption(value, label) {
            await this.sendMessage(value, label);
        },

        async sendMessage(textToSend, displayLabel = null) {
            this.messages.push({
                sender: 'user',
                message: displayLabel || textToSend
            });
            this.isLoading = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                const res = await fetch('{{ route("emergency-assistant.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        conversation_id: this.conversationId,
                        message: textToSend,
                        language: this.language,
                        latitude: this.latitude,
                        longitude: this.longitude
                    })
                });

                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    const d = json.data;
                    this.conversationId = d.conversation_id;
                    this.isEmergencyActive = Boolean(d.is_emergency);
                    this.severityLevel = d.severity_level || this.severityLevel;

                    this.messages.push({
                        sender: 'assistant',
                        message: d.message,
                        options: d.options || [],
                        quick_replies: d.quick_replies || [],
                        hospitals: d.hospitals || [],
                        metadata: {
                            requires_sos: d.requires_sos,
                        }
                    });
                }
            } catch (err) {
                console.error('Chat error:', err);
                this.messages.push({
                    sender: 'assistant',
                    message: this.language === 'bn' 
                        ? 'নেটওয়ার্ক সংযোগ বিঘ্নিত হয়েছে। জরুরি পরিস্থিতিতে কালবিলম্ব না করে অবিলম্বে ৯৯৯-এ ডায়াল করুন।'
                        : 'Connection error. In an emergency, please dial 999 directly without delay.',
                    options: [],
                    quick_replies: []
                });
            } finally {
                this.isLoading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        async triggerEmergencySos() {
            if (!this.conversationId || this.sosLoading) return;
            this.sosLoading = true;

            try {
                const res = await fetch(`/emergency-assistant/${this.conversationId}/sos`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        latitude: this.latitude,
                        longitude: this.longitude
                    })
                });

                const data = await res.json();
                if (data.status === 'success') {
                    this.messages.push({
                        sender: 'assistant',
                        message: data.message,
                        metadata: {
                            tracking_url: data.tracking_url
                        }
                    });
                }
            } catch (err) {
                console.error('SOS failed:', err);
            } finally {
                this.sosLoading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        scrollToBottom() {
            const box = this.$refs.messagesBox;
            if (box) {
                box.scrollTop = box.scrollHeight;
            }
        }
    }));
});
</script>
