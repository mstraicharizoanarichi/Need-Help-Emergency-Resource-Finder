@extends('layouts.app')

@section('title', 'AI Emergency Intake & Triage – Need Help Bangladesh')

@section('content')
<div class="py-8 bg-slate-50 dark:bg-slate-950 min-h-[85vh]" x-data="emergencyIntake()">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Urgent 999 Life-Safety Alert Banner --}}
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white shadow-xl shadow-red-500/20 flex flex-col md:flex-row items-center justify-between gap-4 border border-red-400/30">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/30 text-2xl font-black">
                    🚨
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold flex items-center gap-2">
                        Life in Imminent Danger?
                        <span class="text-[11px] bg-white text-red-700 font-extrabold uppercase px-2 py-0.5 rounded-full">National Hotline</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-red-100">
                        Please call <strong>999</strong> immediately for national police, fire, and ambulance service.
                    </p>
                </div>
            </div>
            <a href="tel:999" class="w-500px md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-red-700 hover:bg-red-50 rounded-xl font-bold shadow-lg transition-all transform active:scale-95 text-base shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Call 999 Now
            </a>
        </div>

        {{-- Intake Main Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 dark:border-slate-800">
            
            <div class="border-b border-slate-100 dark:border-slate-800 pb-6 mb-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                    AI-Assisted Emergency Dispatch
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    Report Emergency & Request Dispatch
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Describe what happened in Bengali or English, or use voice input. Our AI Triage engine instantly assesses severity, predicts required trauma/ICU resources, and prepares dispatch.
                </p>
            </div>

            <form id="emergency-report-form" action="{{ route('emergency.report.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="latitude" id="intake-lat" x-model="lat">
                <input type="hidden" name="longitude" id="intake-lng" x-model="lng">
                <input type="file" name="voice_audio" id="voice-audio-input" class="hidden" accept="audio/*">

                {{-- Quick Emergency Symptom Chips (Bilingual) --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                        Quick Symptom / Incident Select (দ্রুত লক্ষণ নির্বাচন করুন)
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="appendSymptom('বুকে প্রচণ্ড ব্যথা ও শ্বাসকষ্ট (Severe chest pain and difficulty breathing)')" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-red-50 hover:text-red-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition">
                            ❤️ বুকে ব্যথা (Chest Pain)
                        </button>
                        <button type="button" @click="appendSymptom('সড়ক দুর্ঘটনায় মারাত্মক রক্তক্ষরণ (Major road accident with severe bleeding)')" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-red-50 hover:text-red-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition">
                            ⚠️ অ্যাক্সিডেন্ট (Accident)
                        </button>
                        <button type="button" @click="appendSymptom('রোগী সম্পূর্ণ অচেতন ও সাড়াহীন (Patient unconscious and unresponsive)')" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-red-50 hover:text-red-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition">
                            🧠 অচেতন (Unconscious)
                        </button>
                        <button type="button" @click="appendSymptom('ভবনে আগুন লেগেছে ও ধোঁয়া ছড়িয়ে পড়েছে (Building fire hazard with smoke)')" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-red-50 hover:text-red-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition">
                            🔥 আগুন (Fire)
                        </button>
                        <button type="button" @click="appendSymptom('প্রসূতি মায়ের জরুরি প্রসব বেদনা (Maternal emergency labor pain)')" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-red-50 hover:text-red-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition">
                            🤰 প্রসব বেদনা (Labor)
                        </button>
                        <button type="button" @click="appendSymptom('শিশুর তীব্র খিঁচুনি ও শ্বাসকষ্ট (Child acute convulsion and cyanosis)')" class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-red-50 hover:text-red-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 transition">
                            👶 শিশু সংকট (Pediatric)
                        </button>
                    </div>
                </div>

                {{-- Description & Voice Recording Area --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="intake-description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Incident Details (ঘটনার বিবরণ) *
                        </label>

                        {{-- Voice Record Trigger Button --}}
                        <div class="flex items-center gap-2">
                            <template x-if="!isRecording && !recordedAudioBlob">
                                <button type="button" @click="startRecording()" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-900 transition">
                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/><path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/></svg>
                                    Record Voice Note (ভয়েস রেকর্ড)
                                </button>
                            </template>

                            <template x-if="isRecording">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-xs font-bold bg-red-600 text-white animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                    <span x-text="'Recording... ' + recordingTimer + 's'"></span>
                                    <button type="button" @click="stopRecording()" class="ml-2 underline font-black">STOP</button>
                                </div>
                            </template>

                            <template x-if="recordedAudioBlob && !isRecording">
                                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200">
                                    <span>✓ Voice Attached</span>
                                    <button type="button" @click="clearAudio()" class="text-rose-600 hover:underline">Remove</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <textarea id="intake-description" name="description" rows="4" x-model="description" @input.debounce.500ms="triggerAiTriagePreview()" required placeholder="Describe the situation in Bangla or English (e.g. রোগী হঠাৎ বুকে তীব্র ব্যথা অনুভব করছেন এবং ঘামছেন, বাধানালী মোড়, ঢাকা)..." class="w-full rounded-2xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-4 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent transition leading-relaxed"></textarea>
                </div>

                {{-- Live Real-time AI Triage Preview Assessment Box --}}
                <div x-show="triagePreview" style="display: none;" class="p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-red-50/40 dark:from-slate-800/80 dark:to-slate-800/40 border border-red-200/80 dark:border-red-900/50 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-red-600 animate-ping"></span>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                                Live AI Triage Analysis (তাৎক্ষণিক এআই মূল্যায়ন)
                            </h3>
                        </div>
                        <span class="text-xs font-bold font-mono text-slate-500" x-text="'Confidence: ' + Math.round((triagePreview?.ai_confidence || 0.9) * 100) + '%'"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Assessed Severity</span>
                            <span class="text-base font-black text-red-600 dark:text-red-400" x-text="triagePreview?.severity_level + ' (' + (triagePreview?.severity_level === 'P1' ? 'Critical' : (triagePreview?.severity_level === 'P2' ? 'Urgent' : 'Non-Urgent')) + ')'"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Priority Score</span>
                            <span class="text-base font-black text-slate-900 dark:text-white" x-text="(triagePreview?.priority_score || 0) + ' / 100'"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Recommended Service</span>
                            <span class="text-xs font-bold text-slate-900 dark:text-white" x-text="triagePreview?.required_service || 'Emergency Ambulance'"></span>
                        </div>
                    </div>

                    {{-- Reasons and summary --}}
                    <div class="text-xs text-slate-600 dark:text-slate-300 space-y-1">
                        <p class="font-medium italic" x-text="triagePreview?.summary"></p>
                    </div>

                    {{-- Required Equipment badges --}}
                    <template x-if="triagePreview?.required_equipment && triagePreview.required_equipment.length > 0">
                        <div class="flex items-center gap-1.5 flex-wrap pt-1">
                            <span class="text-[11px] font-bold text-slate-500 mr-1">Required Equipment:</span>
                            <template x-for="eq in triagePreview.required_equipment" :key="eq">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300" x-text="eq"></span>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Location Section --}}
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Location & Address (অবস্থান ও ঠিকানা)
                        </label>
                        <button type="button" @click="detectGps()" class="text-xs font-bold text-red-600 dark:text-red-400 hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                            <span x-text="gpsStatusText">Auto-Detect GPS</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <input type="text" name="address" x-model="address" placeholder="Road, House No, Landmark, Area (e.g. House 14, Road 7, Dhanmondi)" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-red-500">
                        </div>
                        <div>
                            <input type="number" name="number_of_victims" value="1" min="1" max="100" placeholder="Victims / Patients" title="Number of Patients or Affected People" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>

                    <div class="text-[11px] text-slate-400" x-show="lat && lng">
                        Coordinates Locked: <span class="font-mono text-slate-600 dark:text-slate-300" x-text="lat.toFixed(5) + ', ' + lng.toFixed(5)"></span>
                    </div>
                </div>

                {{-- Contact Info (Guest only) --}}
                @guest
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Your Name</label>
                            <input type="text" name="reporter_name" placeholder="Full Name" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">Contact Phone *</label>
                            <input type="tel" name="reporter_phone" required placeholder="01XXXXXXXXX" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-slate-900 dark:text-white">
                        </div>
                    </div>
                @endguest

                {{-- Submit Button --}}
                <div class="pt-2">
                    <button type="submit" id="intake-submit-btn" class="w-full py-4 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-rose-800 text-white font-black text-base uppercase tracking-wider shadow-xl shadow-red-600/30 flex items-center justify-center gap-3 transition-all transform active:scale-98">
                        <span class="w-3 h-3 rounded-full bg-white animate-ping"></span>
                        <span>Submit Emergency Report & Request Responders</span>
                    </button>
                    <p class="text-center text-[11px] text-slate-400 mt-2">
                        By submitting, your emergency location and triage metrics are instantly queued for matching available ambulances and hospitals.
                    </p>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('emergencyIntake', function () {
            return {
                description: '',
                lat: null,
                lng: null,
                address: '',
                gpsStatusText: 'Auto-Detect GPS',
                isRecording: false,
                mediaRecorder: null,
                audioChunks: [],
                recordedAudioBlob: null,
                recordingTimer: 0,
                timerInterval: null,
                triagePreview: null,

                init: function () {
                    this.detectGps();
                },

                appendSymptom: function (text) {
                    if (this.description.trim().length > 0) {
                        this.description += '\n' + text;
                    } else {
                        this.description = text;
                    }
                    this.triggerAiTriagePreview();
                },

                detectGps: function () {
                    var self = this;
                    if (!navigator.geolocation) {
                        self.gpsStatusText = 'GPS Not Supported';
                        return;
                    }
                    self.gpsStatusText = 'Detecting GPS...';
                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            self.lat = pos.coords.latitude;
                            self.lng = pos.coords.longitude;
                            self.gpsStatusText = 'GPS Locked ✓';
                        },
                        function () {
                            self.gpsStatusText = 'Location Denied';
                        },
                        { enableHighAccuracy: true, timeout: 8000 }
                    );
                },

                triggerAiTriagePreview: async function () {
                    if (!this.description || this.description.trim().length < 5) {
                        this.triagePreview = null;
                        return;
                    }

                    try {
                        var res = await fetch('{{ route("emergency.preview-triage") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ text: this.description })
                        });

                        if (res.ok) {
                            var data = await res.json();
                            this.triagePreview = data.triage;
                        }
                    } catch (e) {
                        console.warn('AI Triage preview failed:', e);
                    }
                },

                startRecording: async function () {
                    var self = this;
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Audio recording is not supported in your browser.');
                        return;
                    }

                    try {
                        var stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        self.mediaRecorder = new MediaRecorder(stream);
                        self.audioChunks = [];
                        self.isRecording = true;
                        self.recordingTimer = 0;

                        self.timerInterval = setInterval(function () {
                            self.recordingTimer++;
                            if (self.recordingTimer >= 60) {
                                self.stopRecording();
                            }
                        }, 1000);

                        self.mediaRecorder.ondataavailable = function (e) {
                            if (e.data.size > 0) {
                                self.audioChunks.push(e.data);
                            }
                        };

                        self.mediaRecorder.onstop = function () {
                            clearInterval(self.timerInterval);
                            self.isRecording = false;
                            self.recordedAudioBlob = new Blob(self.audioChunks, { type: 'audio/webm' });

                            // Attach to file input
                            var file = new File([self.recordedAudioBlob], 'voice-emergency.webm', { type: 'audio/webm' });
                            var dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            document.getElementById('voice-audio-input').files = dataTransfer.files;

                            // Append note to text area
                            if (!self.description) {
                                self.description = 'ভয়েস রেকর্ডিং সংযুক্ত করা হয়েছে (Voice note attached for transcription)';
                            }
                            self.triggerAiTriagePreview();
                        };

                        self.mediaRecorder.start();
                    } catch (err) {
                        alert('Could not access microphone: ' + err.message);
                        self.isRecording = false;
                    }
                },

                stopRecording: function () {
                    if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                        this.mediaRecorder.stop();
                    }
                },

                clearAudio: function () {
                    this.recordedAudioBlob = null;
                    document.getElementById('voice-audio-input').value = '';
                }
            };
        });
    });
</script>
@endpush
