@extends('layouts.app')

@section('title', 'Emergency Assistant (AI Triage & Guidance) – Need Help Bangladesh')
@section('meta_description', 'Non-diagnostic conversational emergency triage assistant for Bangladesh. Get instant life-safety guidance, stroke/cardiac warning assessment, and 1-Tap SOS dispatch.')

@section('content')
<div class="py-6 sm:py-8 min-h-[calc(100vh-16rem)]" x-data="emergencyAssistantFullscreen()" x-cloak>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumbs & Emergency Fast Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-red-600 dark:hover:text-red-400 transition-colors">Home</a>
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                <span class="text-slate-800 dark:text-slate-200 font-semibold">Emergency Assistant</span>
            </nav>

            <div class="flex items-center gap-3">
                {{-- National Emergency 999 Direct Call --}}
                <a href="tel:999" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white dark:bg-slate-800 dark:hover:bg-slate-700 shadow-md transition-all">
                    <svg class="w-4 h-4 text-red-500 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>Emergency 999</span>
                </a>

                {{-- Direct 1-Tap SOS Trigger --}}
                <button type="button" 
                        @click="triggerEmergencySos()" 
                        :disabled="sosLoading"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white shadow-md shadow-red-600/30 transition-all hover:scale-105 active:scale-95 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    <span x-text="sosLoading ? 'SENDING SOS...' : '1-TAP SOS'"></span>
                </button>
            </div>
        </div>

        {{-- Main Two-Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Left Column: Interactive Conversational Triage Interface --}}
            <div class="lg:col-span-8 flex flex-col h-[750px] max-h-[82vh] bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200/80 dark:border-slate-800 overflow-hidden">
                
                {{-- Assistant Top Bar --}}
                <div class="px-6 py-4 bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white flex items-center justify-between shadow-sm shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20 p-1.5">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                            <div class="flex items-center gap-2">
                                <h1 class="text-base sm:text-lg font-bold tracking-tight">Need Help Emergency Assistant</h1>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-white/20 text-white">AI Health Triage</span>
                            </div>
                            <p class="text-xs text-white/80">Immediate safety advice, stroke/cardiac assessment & hospital routing</p>
                        </div>
                    </div>

                    {{-- Language Switcher & Location Indicator --}}
                    <div class="flex items-center gap-2">
                        <div class="inline-flex rounded-xl bg-black/20 p-1 text-xs font-semibold">
                            <button type="button" 
                                    @click="setLanguage('bn')" 
                                    :class="language === 'bn' ? 'bg-white text-red-700 shadow-xs' : 'text-white/80 hover:text-white'"
                                    class="px-3 py-1 rounded-lg transition-colors cursor-pointer">
                                বাংলা
                            </button>
                            <button type="button" 
                                    @click="setLanguage('en')" 
                                    :class="language === 'en' ? 'bg-white text-red-700 shadow-xs' : 'text-white/80 hover:text-white'"
                                    class="px-3 py-1 rounded-lg transition-colors cursor-pointer">
                                English
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Emergency Red Flag Alert Header --}}
                <div x-show="isEmergencyActive" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-red-500 text-white px-6 py-2.5 flex items-center justify-between gap-3 text-xs font-semibold shadow-inner shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
                        <span x-text="language === 'bn' ? '🚨 অতি-সংকটজনক জরুরি অবস্থা সনাক্ত! কালবিলম্ব না করে সরাসরি ৯৯৯-এ কল করুন বা SOS বাটন চাপুন।' : '🚨 CRITICAL EMERGENCY DETECTED! Call 999 immediately or tap 1-Tap SOS below.'"></span>
                    </div>
                    <button type="button" 
                            @click="triggerEmergencySos()"
                            :disabled="sosLoading"
                            class="px-3 py-1 bg-white text-red-700 hover:bg-red-50 rounded-lg font-black text-xs shadow-xs active:scale-95 transition-transform shrink-0">
                        <span x-show="!sosLoading">SEND SOS NOW</span>
                        <span x-show="sosLoading" class="animate-spin inline-block w-3 h-3 border-2 border-red-600 border-t-transparent rounded-full"></span>
                    </button>
                </div>

                {{-- Messages Stream --}}
                <div x-ref="messagesBox" class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/60 dark:bg-slate-950/60">
                    <template x-for="(item, index) in messages" :key="index">
                        <div :class="item.sender === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="item.sender === 'user' 
                                    ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-2xl rounded-tr-none px-4 py-3 max-w-[80%] text-sm shadow-md' 
                                    : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 rounded-2xl rounded-tl-none px-5 py-4 max-w-[85%] text-sm shadow-sm border border-slate-200 dark:border-slate-700/70'">
                                
                                <div class="whitespace-pre-line leading-relaxed" x-text="item.message"></div>

                                {{-- Question Multiple-Choice Options --}}
                                <template x-if="item.options && item.options.length > 0">
                                    <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-700/60 space-y-2">
                                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                            <span x-text="language === 'bn' ? 'সঠিক বিকল্পটি নির্বাচন করুন:' : 'Select corresponding option:'"></span>
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="opt in item.options" :key="opt.value">
                                                <button type="button" 
                                                        @click="sendOption(opt.value, opt.label)"
                                                        class="px-3.5 py-1.5 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-950/50 dark:hover:bg-red-900/60 dark:text-red-300 rounded-xl border border-red-200 dark:border-red-900 transition-colors cursor-pointer">
                                                    <span x-text="opt.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                {{-- Quick Replies --}}
                                <template x-if="item.quick_replies && item.quick_replies.length > 0 && (!item.options || item.options.length === 0)">
                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        <template x-for="reply in item.quick_replies" :key="reply">
                                            <button type="button" 
                                                    @click="sendMessage(reply)"
                                                    class="px-3 py-1 text-xs font-medium bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg transition-colors cursor-pointer">
                                                <span x-text="reply"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                {{-- Hospital Cards --}}
                                <template x-if="item.hospitals && item.hospitals.length > 0">
                                    <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-700/60 space-y-2">
                                        <h5 class="text-xs font-bold text-red-600 dark:text-red-400 uppercase flex items-center gap-1.5">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                            <span x-text="language === 'bn' ? 'প্রস্তাবিত জরুরি চিকিৎসা কেন্দ্রসমূহ:' : 'Recommended Emergency Centers:'"></span>
                                        </h5>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <template x-for="hosp in item.hospitals" :key="hosp.id">
                                                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex flex-col justify-between">
                                                    <div>
                                                        <h6 class="font-bold text-xs text-slate-900 dark:text-white" x-text="hosp.name"></h6>
                                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5" x-text="hosp.address"></p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <template x-if="hosp.has_icu">
                                                                <span class="text-[9px] px-1.5 py-0.5 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold rounded">ICU Facility</span>
                                                            </template>
                                                            <template x-if="hosp.distance_km">
                                                                <span class="text-[10px] text-slate-400" x-text="hosp.distance_km + ' km away'"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 pt-2 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                                                        <span class="text-[10px] text-slate-400">Emergency Line:</span>
                                                        <a :href="'tel:' + (hosp.emergency_hotline || hosp.phone || '999')" 
                                                           class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                                            <span x-text="hosp.emergency_hotline || hosp.phone || '999'"></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                {{-- Tracking link --}}
                                <template x-if="item.metadata && item.metadata.tracking_url">
                                    <div class="mt-3">
                                        <a :href="item.metadata.tracking_url" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 font-bold text-xs hover:underline">
                                            <span>Open Live Incident Tracking Board</span>
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Typing indicator --}}
                    <div x-show="isLoading" class="flex justify-start">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-tl-none px-4 py-2.5 text-xs shadow-xs border border-slate-200 dark:border-slate-700/60 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-600 animate-bounce"></span>
                            <span class="w-2 h-2 rounded-full bg-red-600 animate-bounce [animation-delay:0.2s]"></span>
                            <span class="w-2 h-2 rounded-full bg-red-600 animate-bounce [animation-delay:0.4s]"></span>
                            <span class="text-xs text-slate-400 ml-1 font-medium" x-text="language === 'bn' ? 'লক্ষণ পর্যালোচনা চলছে...' : 'Analyzing triage indications...'"></span>
                        </div>
                    </div>
                </div>

                {{-- Symptom Quick Chips Bar --}}
                <div class="px-6 py-2.5 bg-slate-100/80 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2 overflow-x-auto text-xs shrink-0">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider shrink-0" x-text="language === 'bn' ? 'লক্ষণসমূহ:' : 'Topics:'"></span>
                    <button type="button" @click="sendMessage(language === 'bn' ? 'স্ট্রোকের লক্ষণ কী কী?' : 'Stroke warning signs')" class="px-3 py-1 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors cursor-pointer">
                        🧠 <span x-text="language === 'bn' ? 'স্ট্রোক লক্ষণ' : 'Stroke Signs'"></span>
                    </button>
                    <button type="button" @click="sendMessage(language === 'bn' ? 'বুকে প্রচণ্ড ব্যথা ও চাপ' : 'Severe crushing chest pain')" class="px-3 py-1 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors cursor-pointer">
                        ❤️ <span x-text="language === 'bn' ? 'হার্ট অ্যাটাক' : 'Heart Attack'"></span>
                    </button>
                    <button type="button" @click="sendMessage(language === 'bn' ? 'দম নিতে পারছি না' : 'Cannot breathe')" class="px-3 py-1 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors cursor-pointer">
                        🫁 <span x-text="language === 'bn' ? 'শ্বাসকষ্ট' : 'Breathing'"></span>
                    </button>
                    <button type="button" @click="sendMessage(language === 'bn' ? 'রক্তপাত বন্ধ হচ্ছে না' : 'Severe bleeding')" class="px-3 py-1 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors cursor-pointer">
                        🩸 <span x-text="language === 'bn' ? 'রক্তপাত' : 'Severe Bleeding'"></span>
                    </button>
                    <button type="button" @click="sendMessage(language === 'bn' ? 'আগুনে পুড়ে গেছে' : 'Burn injury care')" class="px-3 py-1 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 shrink-0 hover:border-red-400 hover:text-red-600 transition-colors cursor-pointer">
                        🔥 <span x-text="language === 'bn' ? 'পোড়া ক্ষত' : 'Burn Care'"></span>
                    </button>
                </div>

                {{-- Message Input Bar --}}
                <div class="p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 shrink-0">
                    <form @submit.prevent="submitForm()" class="flex items-center gap-2">
                        {{-- Location GPS pin toggle --}}
                        <button type="button" 
                                @click="requestLocation()" 
                                :class="hasLocation ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'"
                                class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors cursor-pointer shrink-0" 
                                :title="hasLocation ? 'GPS Location Acquired' : 'Click to share GPS location for nearby routing'">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                        </button>

                        <input type="text" 
                               x-model="inputMessage" 
                               :placeholder="language === 'bn' ? 'লক্ষণ বা পরিস্থিতি বাংলায় বা ইংরেজিতে লিখুন...' : 'Describe emergency signs or situation in English or Bengali...'"
                               class="flex-1 px-4 py-3 text-sm bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-hidden focus:border-red-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400"
                               :disabled="isLoading">

                        <button type="submit" 
                                :disabled="isLoading || !inputMessage.trim()" 
                                class="px-5 py-3 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white rounded-xl font-bold text-sm shadow-md transition-all shrink-0 cursor-pointer flex items-center gap-2">
                            <span>Send</span>
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        </button>
                    </form>
                    
                    <p class="text-[11px] text-slate-400 dark:text-slate-400 text-center mt-2">
                        ⚠️ <span x-text="language === 'bn' ? 'জরুরি স্বাস্থ্য সহকারী কোনো ডায়াগনসিস প্রদান করে না। এটি একটি নির্দেশিকামূলক ট্রায়াজ ব্যবস্থা। জরুরি অবস্থায় সর্বদা ৯৯৯-এ যোগাযোগ করুন।' : 'Emergency Assistant does not provide medical diagnoses. It is a safety triage and advisory guide. Always call 999 in emergencies.'"></span>
                    </p>
                </div>

            </div>

            {{-- Right Column: Information, Guides & Hotline Cards --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Fast-Action Emergency Lifelines Card --}}
                <div class="p-6 rounded-3xl bg-gradient-to-br from-red-600 to-rose-700 text-white shadow-xl">
                    <h3 class="text-base font-black tracking-tight mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>Bangladesh Emergency Lifelines</span>
                    </h3>
                    <p class="text-xs text-white/80 mb-4">Toll-free 24/7 national hotlines for immediate response.</p>

                    <div class="space-y-2.5 text-xs">
                        <a href="tel:999" class="flex items-center justify-between p-3 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-sm transition-colors font-bold">
                            <div class="flex items-center gap-2">
                                <span class="text-base">🚨</span>
                                <span>National Emergency (Police, Fire, Ambulance)</span>
                            </div>
                            <span class="text-sm tracking-wider">999</span>
                        </a>

                        <a href="tel:16263" class="flex items-center justify-between p-3 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-sm transition-colors font-bold">
                            <div class="flex items-center gap-2">
                                <span class="text-base">🏥</span>
                                <span>Shastho Batayon (Govt Health Hotline)</span>
                            </div>
                            <span class="text-sm tracking-wider">16263</span>
                        </a>

                        <a href="tel:109" class="flex items-center justify-between p-3 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-sm transition-colors font-bold">
                            <div class="flex items-center gap-2">
                                <span class="text-base">🛡️</span>
                                <span>Violence Against Women & Children</span>
                            </div>
                            <span class="text-sm tracking-wider">109</span>
                        </a>
                    </div>
                </div>

                {{-- Stroke FAST Quick Reference Card --}}
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-md">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-3">
                        <span class="text-red-600">🧠</span>
                        <span>Stroke FAST Rule (স্ট্রোক সনাক্তকরণ)</span>
                    </h4>
                    <div class="space-y-2 text-xs text-slate-600 dark:text-slate-300">
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/40">
                            <strong class="text-red-600 dark:text-red-400">F - Face (মুখ):</strong> হাসতে গেলে একপাশ বেঁকে যায় কিনা দেখুন।
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/40">
                            <strong class="text-red-600 dark:text-red-400">A - Arms (হাত):</strong> উভয় হাত সমানভাবে উপরে তুলতে পারছে কিনা দেখুন।
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/40">
                            <strong class="text-red-600 dark:text-red-400">S - Speech (কথা):</strong> কথা জড়িয়ে যাচ্ছে বা স্পষ্ট উচ্চারণ করতে পারছে না।
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/40">
                            <strong class="text-red-600 dark:text-red-400">T - Time (সময়):</strong> লক্ষণ দেখা দেওয়ার সাথে সাথে অবিলম্বে ৯৯৯-এ ফোন করুন।
                        </div>
                    </div>
                </div>

                {{-- Heart Attack Emergency Precaution Card --}}
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-md">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-3">
                        <span class="text-rose-600">❤️</span>
                        <span>Heart Attack Actions (হার্ট অ্যাটাক করণীয়)</span>
                    </h4>
                    <ul class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300 list-disc list-inside">
                        <li>রোগীকে হাঁটাচলা করতে দেবেন না, হেলান দিয়ে বসিয়ে রাখুন।</li>
                        <li>গলার বোতাম বা আঁটসাঁট পোশাক দ্রুত আলগা করুন।</li>
                        <li>অচেতন হলে বা পালস না থাকলে অবিলম্বে CPR শুরু করুন।</li>
                        <li>দেরি না করে সরাসরি সিসিইউ সুবিধা সম্পন্ন হাসপাতালে নেওয়ার ব্যবস্থা করুন।</li>
                    </ul>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('emergencyAssistantFullscreen', () => ({
        isLoading: false,
        sosLoading: false,
        language: '{{ $initialLanguage ?? "bn" }}',
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
            this.initConversation();
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
                console.error('Failed to init fullscreen assistant session:', err);
                this.addWelcomeMessage();
            }
        },

        addWelcomeMessage() {
            const welcomeText = this.language === 'bn'
                ? "আসসালামু আলাইকুম। আমি নিড হেল্প জরুরি স্বাস্থ্য সহকারী। জরুরি লক্ষণসমূহ (যেমন: বুকে তীব্র ব্যথা, স্ট্রোকের লক্ষণ, মারাত্মক শ্বাসকষ্ট, পোড়া বা দুর্ঘটনা) পর্যবেক্ষণ করলে নিচে লিখুন। আমি তাৎক্ষণিক জীবনরক্ষাকারী পরামর্শ ও নিকটস্থ হাসপাতালের দিকনির্দেশনা দেব।"
                : "Hello. I am the Need Help Emergency Assistant. Describe any emergency symptoms or signs you observe (e.g. chest pain, stroke signs, difficulty breathing, burns, or accident). I will provide instant life-safety guidance and hospital recommendations.";

            this.messages.push({
                sender: 'assistant',
                message: welcomeText,
                options: [],
                quick_replies: this.language === 'bn' 
                    ? ['স্ট্রোকের লক্ষণ কী?', 'বুকে প্রচণ্ড ব্যথা ও চাপ', 'দম আটকে আসছে', 'নিকটস্থ হাসপাতাল কোথায়?'] 
                    : ['Stroke warning signs', 'Severe crushing chest pain', 'Cannot breathe', 'Nearest hospital?'],
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
                        ? 'নেটওয়ার্ক সংযোগ বিঘ্নিত হয়েছে। জরুরি পরিস্থিতিতে অবিলম্বে ৯৯৯-এ কল করুন।'
                        : 'Connection error. In an emergency, please call 999 directly without delay.',
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
@endsection
