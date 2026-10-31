<?php

namespace App\Services\Chatbot;

use App\Models\ChatAssessment;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\EmergencyIncident;
use App\Services\AI\GeminiEmergencyProvider;
use App\Services\Hospital\HospitalRecommendationService;
use App\Services\LocationSearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmergencyChatbotService
{
    public function __construct(
        private LanguageDetectionService $languageDetector,
        private EmergencyRedFlagDetector $redFlagDetector,
        private EmergencyQuestionEngine $questionEngine,
        private EmergencyKnowledgeBase $knowledgeBase,
        private GeminiEmergencyProvider $geminiProvider,
        private LocationSearchService $locationService,
        private HospitalRecommendationService $hospitalRecommendationService,
        private ChatIntentClassifier $intentClassifier
    ) {}

    /**
     * Start a new conversation or retrieve existing
     */
    public function startConversation(?string $sessionId = null, ?int $userId = null, string $language = 'bn'): ChatConversation
    {
        $sessionId = $sessionId ?: bin2hex(random_bytes(16));

        return ChatConversation::create([
            'session_id' => $sessionId,
            'user_id' => $userId ?? Auth::id(),
            'language' => $language,
            'severity_level' => 'P3',
            'is_emergency_active' => false,
            'status' => ChatConversation::STATE_IDLE,
        ]);
    }

    /**
     * Process an incoming message within a conversation
     */
    public function processMessage(
        ChatConversation $conversation,
        string $messageText,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $requestedLang = null
    ): array {
        $trimmedMessage = trim($messageText);

        // Language resolution: requested -> detected -> conversation language -> default 'bn'
        $langCode = $requestedLang ?: $this->languageDetector->detect($trimmedMessage);
        $lang = ($langCode === 'en') ? 'en' : 'bn';

        if ($conversation->language !== $lang) {
            $conversation->update(['language' => $lang]);
        }

        // Record User Message
        $userMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'user',
            'message' => $trimmedMessage,
            'metadata' => [
                'language' => $lang,
                'state_before' => $conversation->getState(),
            ],
        ]);

        $botResponseText = '';
        $responseType = 'GENERAL_GREETING';
        $quickReplies = [];
        $options = [];
        $hospitals = [];
        $isRedFlag = false;
        $requiresSos = false;
        $requiresConfirmation = false;
        $severity = 'P3';

        // -------------------------------------------------------------
        // STEP 1: CRITICAL CURRENT-MESSAGE RED FLAG OVERRIDE
        // (Evaluated strictly on the current message text, bypassing any history)
        // -------------------------------------------------------------
        $criticalOverride = $this->intentClassifier->detectCriticalCurrentRedFlag($trimmedMessage);
        if ($criticalOverride !== null) {
            $conversation->transitionTo(ChatConversation::STATE_EMERGENCY_CONFIRMED, [
                'active_intent' => $criticalOverride['intent'],
                'severity' => 'P1',
            ]);
            $isRedFlag = true;
            $requiresSos = true;
            $severity = 'P1';
            $responseType = 'EMERGENCY_ACTION';

            $botResponseText = ($lang === 'bn')
                ? "🚨 অতি জরুরি সতর্কতা: {$criticalOverride['safety_advisory']}\n\nতাৎক্ষণিকভাবে করণীয়:\n• {$criticalOverride['immediate_action']}\n• অবিলম্বে নিচের 'জরুরি SOS পাঠান' বাটনে চাপুন অথবা ৯৯৯-এ ডায়াল করুন।"
                : "🚨 CRITICAL EMERGENCY: {$criticalOverride['safety_advisory']}\n\nImmediate Actions:\n• {$criticalOverride['immediate_action']}\n• Call 999 immediately or tap the Emergency SOS button below.";

            $quickReplies = ($lang === 'bn')
                ? ['🚨 জরুরি SOS পাঠান', '📞 ৯৯৯-এ কল করুন', '🏥 নিকটস্থ হাসপাতাল']
                : ['🚨 Send Emergency SOS', '📞 Call 999', '🏥 Nearest Hospital'];
        }

        // -------------------------------------------------------------
        // STEP 2: NEGATIVE / CANCELLATION RESPONSE
        // ("nah", "না", "না, শুধু জানার জন্য", "nah dokar nai", "দরকার নেই", "just asking")
        // Cancels active assessment and resets emergency state!
        // -------------------------------------------------------------
        if (empty($botResponseText) && $this->intentClassifier->isNegativeOrCancellation($trimmedMessage)) {
            $conversation->resetEmergencyState(ChatConversation::STATE_EMERGENCY_CANCELLED);
            $responseType = 'CANCELLATION';
            $isRedFlag = false;
            $requiresSos = false;
            $severity = 'P3';

            $botResponseText = ($lang === 'bn')
                ? "জরুরি মূল্যায়ন বাতিল করা হয়েছে। কোনো সক্রিয় জরুরি পরিস্থিতি রেকর্ড করা হয়নি। আপনার আর কোনো সাধারণ স্বাস্থ্য তথ্য বা লক্ষণ সম্পর্কে জানার থাকলে জিজ্ঞাসা করতে পারেন।"
                : "Emergency assessment cancelled. No active emergency incident has been recorded. Let me know if you would like general health information or symptom guidance.";

            $quickReplies = ($lang === 'bn')
                ? ['স্ট্রোকের লক্ষণ কী?', 'হার্ট অ্যাটাকের লক্ষণ', 'শ্বাসকষ্ট হলে করণীয়', 'নিকটস্থ হাসপাতাল']
                : ['Stroke warning signs', 'Heart attack symptoms', 'Breathing difficulty', 'Nearest hospital'];
        }

        // -------------------------------------------------------------
        // STEP 3: GENERAL GREETING & CHIT-CHAT ("hello", "hi", "salam")
        // Clears stale emergency context and resets state to IDLE
        // -------------------------------------------------------------
        if (empty($botResponseText) && $this->intentClassifier->isGeneralGreeting($trimmedMessage)) {
            $conversation->resetEmergencyState(ChatConversation::STATE_IDLE);
            $responseType = 'GENERAL_GREETING';
            $isRedFlag = false;
            $requiresSos = false;
            $severity = 'P3';

            $botResponseText = ($lang === 'bn')
                ? "আসসালামু আলাইকুম! 👋 আমি Need Help জরুরি স্বাস্থ্য সহকারী। আপনি কোনো জরুরি স্বাস্থ্য সমস্যা সম্পর্কে জানতে চান, নাকি এখনই কারও সাহায্য প্রয়োজন?"
                : "Hello! 👋 I am the Need Help Emergency Health Assistant. Are you looking for health symptom guidance, or do you require immediate emergency assistance?";

            $quickReplies = ($lang === 'bn')
                ? ['🧠 স্ট্রোকের লক্ষণ', '❤️ বুকে ব্যথা', '🫁 শ্বাসকষ্ট', '🩸 প্রচণ্ড রক্তপাত', '🏥 নিকটস্থ হাসপাতাল', '🚨 জরুরি SOS']
                : ['🧠 Stroke symptoms', '❤️ Chest pain', '🫁 Breathing difficulty', '🩸 Severe bleeding', '🏥 Find nearby hospital', '🚨 Emergency SOS'];
        }

        // -------------------------------------------------------------
        // STEP 4: EXPLICIT EMERGENCY CONFIRMATION OR SOS REQUEST
        // ("yes, right now", "হ্যাঁ, এখন হচ্ছে", "send ambulance", "help me")
        // -------------------------------------------------------------
        if (empty($botResponseText) && $this->intentClassifier->isExplicitConfirmation($trimmedMessage)) {
            $currentIntent = $conversation->active_intent ?: 'SOS_REQUEST';
            $conversation->transitionTo(ChatConversation::STATE_EMERGENCY_CONFIRMED, [
                'active_intent' => $currentIntent,
                'severity' => 'P1',
            ]);
            $isRedFlag = true;
            $requiresSos = true;
            $severity = 'P1';
            $responseType = 'EMERGENCY_ACTION';

            $botResponseText = ($lang === 'bn')
                ? "🚨 জরুরি পরিস্থিতি সক্রিয় করা হয়েছে! আপনি যদি তাৎক্ষণিক বিপদে থাকেন, অবিলম্বে নিচের 'জরুরি SOS পাঠান' বাটনে চাপুন অথবা ৯৯৯-এ সরাসরি ফোন করুন। রেসপন্ডার ও অ্যাম্বুলেন্স সহায়তার জন্য প্রস্তুত।"
                : "🚨 Emergency mode activated! If in immediate danger, tap 'Send Emergency SOS' below or dial 999 immediately. Responders and emergency dispatch are standing by.";

            $quickReplies = ($lang === 'bn')
                ? ['🚨 জরুরি SOS পাঠান', '📞 ৯৯৯-এ কল করুন', '🏥 নিকটস্থ হাসপাতাল']
                : ['🚨 Send Emergency SOS', '📞 Call 999', '🏥 Nearest Hospital'];
        }

        // -------------------------------------------------------------
        // STEP 5: EDUCATIONAL / INFORMATIONAL INQUIRIES
        // ("What are symptoms of stroke?", "প্রচণ্ড রক্তপাত হলে কী করতে হয়?")
        // -------------------------------------------------------------
        if (empty($botResponseText)) {
            $infoTopic = $this->intentClassifier->isInformationRequest($trimmedMessage);
            if ($infoTopic) {
                $conversation->transitionTo(ChatConversation::STATE_INFORMATION, [
                    'active_intent' => $infoTopic === 'GENERAL_HEALTH' ? null : $infoTopic,
                    'severity' => 'P3',
                ]);
                $responseType = 'INFORMATION';
                $isRedFlag = false;
                $requiresSos = false;
                $severity = 'P3';

                $topic = $this->knowledgeBase->getTopic($infoTopic, $lang);
                if ($topic) {
                    $pointsList = implode("\n• ", $topic['points']);
                    $botResponseText = "{$topic['summary']}\n• {$pointsList}\n\n⚠️ {$topic['safety_note']}\n\n{$topic['question_prompt']}";
                    $options = ($lang === 'bn')
                        ? [
                            ['value' => 'active_now', 'label' => 'হ্যাঁ, এখন হচ্ছে'],
                            ['value' => 'just_asking', 'label' => 'না, শুধু জানতে চাচ্ছি'],
                        ]
                        : [
                            ['value' => 'active_now', 'label' => 'Yes, happening right now'],
                            ['value' => 'just_asking', 'label' => 'No, just learning'],
                        ];
                    $quickReplies = array_map(fn($o) => $o['label'], $options);
                    $requiresConfirmation = true;
                } else {
                    $botResponseText = ($lang === 'bn')
                        ? "জরুরি স্বাস্থ্য তথ্য: যে কোনো তীব্র বা হঠাৎ লক্ষণ দেখা দিলে রোগীকে নিরাপদ অবস্থানে রাখুন এবং পরিস্থিতি পর্যবেক্ষণ করুন। এই লক্ষণগুলো কি এই মুহূর্তে কারও হচ্ছে?"
                        : "Health Information: For any sudden or severe symptoms, keep the patient safe and observe closely. Is this happening right now?";
                    $options = ($lang === 'bn')
                        ? [
                            ['value' => 'active_now', 'label' => 'হ্যাঁ, এখন হচ্ছে'],
                            ['value' => 'just_asking', 'label' => 'না, শুধু জানতে চাচ্ছি'],
                        ]
                        : [
                            ['value' => 'active_now', 'label' => 'Yes, happening right now'],
                            ['value' => 'just_asking', 'label' => 'No, just asking'],
                        ];
                    $quickReplies = array_map(fn($o) => $o['label'], $options);
                    $requiresConfirmation = true;
                }
            }
        }

        // -------------------------------------------------------------
        // STEP 6: IN-ASSESSMENT QUESTION ANSWERING (Multi-turn Triage)
        // -------------------------------------------------------------
        if (empty($botResponseText) && $conversation->active_intent && !empty($conversation->assessment_data['current_question_key'])) {
            $activeIntent = $conversation->active_intent;
            $currentKey = $conversation->assessment_data['current_question_key'];

            $normalizedAnswer = $this->matchOptionValue($activeIntent, $currentKey, $trimmedMessage);

            if ($normalizedAnswer !== null) {
                $conversation->recordAnswer($currentKey, $normalizedAnswer);

                // Check if this specific answer triggers a red flag
                if ($this->questionEngine->isAnswerRedFlag($activeIntent, $currentKey, $normalizedAnswer)) {
                    $conversation->transitionTo(ChatConversation::STATE_POSSIBLE_EMERGENCY, [
                        'active_intent' => $activeIntent,
                        'severity' => 'P1',
                    ]);
                    $severity = 'P1';
                    $isRedFlag = true;
                }

                // Check for next question
                $nextQuestion = $this->questionEngine->getNextQuestion(
                    $activeIntent,
                    $conversation->assessment_data,
                    $lang
                );

                if ($nextQuestion) {
                    $assessmentData = $conversation->assessment_data;
                    $assessmentData['current_question_key'] = $nextQuestion['key'];
                    $conversation->update(['assessment_data' => $assessmentData]);

                    $responseType = 'FOLLOW_UP_QUESTION';
                    $botResponseText = $nextQuestion['text'];
                    $options = $nextQuestion['options'];
                    $quickReplies = array_map(fn($o) => $o['label'], $options);
                } else {
                    // Assessment completed
                    $conversation->clearActiveQuestion();
                    $conversation->transitionTo(ChatConversation::STATE_COMPLETED);
                    $this->finalizeAssessment($conversation, $activeIntent, $lang);

                    $responseType = 'EMERGENCY_WARNING';
                    $botResponseText = ($lang === 'bn')
                        ? 'আপনার দেওয়া তথ্যের ভিত্তিতে জরুরি মূল্যায়ন সম্পন্ন হয়েছে। কোনো ঝুঁকি থাকলে দ্রুত ৯৯৯-এ কল করুন অথবা নিচের SOS বাটনে চাপুন।'
                        : 'Assessment completed based on the provided answers. If any life-safety risk is present, call 999 immediately or tap Emergency SOS below.';

                    if ($conversation->is_emergency_active) {
                        $isRedFlag = true;
                        $requiresSos = true;
                        $severity = 'P1';
                    }
                }
            }
        }

        // -------------------------------------------------------------
        // STEP 7: CURRENT-MESSAGE ACUTE RED-FLAG EVALUATION
        // -------------------------------------------------------------
        if (empty($botResponseText)) {
            $redFlag = $this->redFlagDetector->detect($trimmedMessage);

            if ($redFlag['is_red_flag']) {
                $isRedFlag = true;
                $severity = $redFlag['severity'];
                $intent = $redFlag['intent'];

                if ($intent === 'SOS_REQUEST') {
                    $conversation->transitionTo(ChatConversation::STATE_EMERGENCY_CONFIRMED, [
                        'active_intent' => 'SOS_REQUEST',
                        'severity' => 'P1',
                    ]);
                    $requiresSos = true;
                    $responseType = 'EMERGENCY_ACTION';
                    $botResponseText = ($lang === 'bn')
                        ? "🚨 জরুরি সতর্কতা! আপনি যদি তাৎক্ষণিক বিপদে থাকেন, অবিলম্বে নিচের 'জরুরি SOS পাঠান' বাটনে চাপুন অথবা ৯৯৯-এ কল করুন। আমরা নিকটস্থ জরুরি সহায়তা ও হাসপাতাল প্রস্তুত করছি।"
                        : "🚨 EMERGENCY ALERT! If you are in immediate danger, tap 'Send Emergency SOS' below or call 999 right away. We are locating nearest emergency responders.";
                } else {
                    $conversation->transitionTo(ChatConversation::STATE_ASSESSMENT, [
                        'active_intent' => $intent,
                        'severity' => $severity,
                    ]);

                    if ($severity === 'P1') {
                        $requiresSos = true;
                    }
                    $responseType = 'EMERGENCY_WARNING';

                    $prefix = ($lang === 'bn')
                        ? "🚨 জরুরি সতর্কতা: {$redFlag['safety_advisory']}\n\nতাৎক্ষণিকভাবে করণীয়:\n• অবিলম্বে ৯৯৯-এ ফোন করে অ্যাম্বুলেন্স ডাকুন অথবা নিচের SOS বোতামে ট্যাপ করুন।\n• রোগীকে একা রাখবেন না এবং কোনো খাবার বা ওষুধ খাওয়াবেন না।"
                        : "🚨 EMERGENCY ALERT: {$redFlag['safety_advisory']}\n\nImmediate Actions:\n• Call 999 for an ambulance immediately or tap the SOS button below.\n• Do not leave the person alone; do not give food, water, or medication.";

                    // Check if structured triage flow is available
                    $nextQuestion = $this->questionEngine->getNextQuestion($intent, $conversation->assessment_data ?? [], $lang);

                    if ($nextQuestion) {
                        $assessmentData = $conversation->assessment_data ?? [];
                        $assessmentData['current_question_key'] = $nextQuestion['key'];
                        $conversation->update([
                            'active_intent' => $intent,
                            'assessment_data' => $assessmentData,
                        ]);

                        $botResponseText = $prefix . "\n\n" . $nextQuestion['text'];
                        $options = $nextQuestion['options'];
                        $quickReplies = array_map(fn($o) => $o['label'], $options);
                    } else {
                        $botResponseText = $prefix;
                    }
                }
            } elseif (in_array($redFlag['intent'], ['NEAREST_HOSPITAL', 'NEAREST_AMBULANCE'], true)) {
                $responseType = 'HOSPITAL_SEARCH';
                $botResponseText = ($lang === 'bn')
                    ? 'আমরা আপনার নিকটস্থ জরুরি হাসপাতাল ও অ্যাম্বুলেন্স তালিকা তৈরি করছি। সংকটজনক অবস্থায় কালবিলম্ব না করে সরাসরি ৯৯৯-এ ফোন করুন।'
                    : 'Finding nearest emergency medical centers and ambulances. In critical situations, call 999 immediately.';
                $hospitals = $this->findNearbyHospitals($latitude, $longitude);
            }
        }

        // -------------------------------------------------------------
        // STEP 8: SAFE CONVERSATIONAL FALLBACK
        // (Never accidentally invoke incident intake or claim incident registered)
        // -------------------------------------------------------------
        if (empty($botResponseText)) {
            $conversation->transitionTo(ChatConversation::STATE_IDLE);
            $responseType = 'GENERAL_GREETING';
            $isRedFlag = false;
            $requiresSos = false;
            $severity = 'P3';

            $botResponseText = ($lang === 'bn')
                ? "আমি আপনার বার্তাটি পেয়েছি। আপনি কি কোনো স্বাস্থ্য সমস্যা বা লক্ষণ সম্পর্কে জানতে চান, নাকি এখনই কারও জরুরি চিকিৎসা সাহায্য প্রয়োজন? নিচের যে কোনো বিষয় বেছে নিতে পারেন:"
                : "I received your message. Are you inquiring about medical symptoms, or do you require immediate emergency assistance? Please select a topic below:";

            $quickReplies = ($lang === 'bn')
                ? ['🧠 স্ট্রোকের লক্ষণ', '❤️ বুকে ব্যথা', '🫁 শ্বাসকষ্ট', '🩸 প্রচণ্ড রক্তপাত', '🏥 নিকটস্থ হাসপাতাল', '🚨 জরুরি SOS']
                : ['🧠 Stroke symptoms', '❤️ Chest pain', '🫁 Breathing difficulty', '🩸 Severe bleeding', '🏥 Find nearby hospital', '🚨 Emergency SOS'];
        }

        // Attach nearby hospitals if red-flag, emergency mode, or hospital search
        if ($isRedFlag || $conversation->is_emergency_active || $responseType === 'HOSPITAL_SEARCH') {
            if (empty($hospitals)) {
                $hospitals = $this->findNearbyHospitals($latitude, $longitude);
            }
        }

        // Store Assistant Message
        $botMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'assistant',
            'message' => $botResponseText,
            'metadata' => [
                'language' => $lang,
                'type' => $responseType,
                'conversation_state' => $conversation->getState(),
                'is_red_flag' => $isRedFlag,
                'severity_level' => $severity,
                'intent' => $conversation->active_intent,
                'options' => $options,
                'quick_replies' => $quickReplies,
                'hospitals' => $hospitals,
                'requires_sos' => $requiresSos,
                'requires_confirmation' => $requiresConfirmation,
            ],
        ]);

        return [
            'conversation_id' => $conversation->id,
            'message' => $botResponseText,
            'sender' => 'assistant',
            'language' => $lang,
            'type' => $responseType,
            'conversation_state' => $conversation->getState(),
            'is_emergency' => $isRedFlag || (bool) $conversation->is_emergency_active,
            'emergency_detected' => $isRedFlag || (bool) $conversation->emergency_detected,
            'severity_level' => $severity,
            'active_intent' => $conversation->active_intent,
            'requires_confirmation' => $requiresConfirmation,
            'requires_sos' => $requiresSos,
            'options' => $options,
            'quick_replies' => $quickReplies,
            'hospitals' => $hospitals,
            'created_at' => $botMessage->created_at->toIso8601String(),
        ];
    }

    /**
     * Match user input against quick reply options for a question
     */
    private function matchOptionValue(string $intent, string $questionKey, string $userText): ?string
    {
        $flow = [
            'STROKE' => [
                'consciousness' => ['unconscious' => ['unconscious', 'অচেতন', 'বেহুঁশ'], 'drowsy' => ['drowsy', 'confused', 'ঝিমুনি'], 'conscious' => ['conscious', 'yes', 'সচেতন', 'হ্যাঁ']],
                'onset_time' => ['under_30m' => ['under_30m', 'just now', '< 30', 'এইমাত্র'], '1_to_3h' => ['1_to_3h', '1 to 3', '১ – ৩'], 'over_3h' => ['over_3h', '> 3', '৩ ঘণ্টার বেশি']],
                'fast_symptoms' => ['yes_multiple' => ['yes_multiple', 'multiple', 'একাধিক'], 'yes_one' => ['yes_one', 'one', 'একটি'], 'none' => ['none', 'না', 'কোনোটিই নয়']],
            ],
            'CARDIAC_EMERGENCY' => [
                'consciousness' => ['unconscious' => ['unconscious', 'collapsed', 'অচেতন'], 'conscious' => ['conscious', 'yes', 'সচেতন']],
                'breathing_status' => ['struggling' => ['struggling', 'gasping', 'দম আটকে আসছে'], 'normal' => ['normal', 'স্বাভাবিক']],
                'pain_radiation' => ['radiating' => ['radiating', 'spread', 'ছড়িয়ে পড়ছে'], 'chest_only' => ['chest_only', 'শুধুমাত্র বুকে']],
            ],
            'BREATHING_EMERGENCY' => [
                'speech_ability' => ['cant_speak' => ['cant_speak', 'no words', 'কথা বলতে পারছে না'], 'short_words' => ['short_words', 'few words', 'ভাঙা ভাঙা শব্দ'], 'full_sentences' => ['full_sentences', 'পুরো বাক্য']],
                'blue_lips' => ['yes_blue' => ['yes_blue', 'blue', 'নীল', 'ফ্যাকাশে'], 'normal_color' => ['normal_color', 'normal', 'স্বাভাবিক']],
            ],
            'SEVERE_BLEEDING' => [
                'bleeding_type' => ['spurting_gushing' => ['spurting', 'gushing', 'ফিনকি দিয়ে'], 'steady_soaking' => ['steady', 'soaking', 'টানা রক্তপাত'], 'minor_oozing' => ['minor', 'oozing', 'সামান্য']],
                'shock_signs' => ['shock_present' => ['shock', 'pale', 'cold sweat', 'ঠান্ডা ঘাম'], 'alert_warm' => ['alert', 'warm', 'সচেতন']],
            ],
            'UNCONSCIOUSNESS' => [
                'breathing_present' => ['not_breathing' => ['not breathing', 'gasping', 'শ্বাস নিচ্ছে না'], 'breathing_normally' => ['breathing normally', 'yes', 'শ্বাস নিচ্ছে']],
                'cause_suspected' => ['trauma_fall' => ['trauma', 'fall', 'আঘাত', 'পড়ে যাওয়া'], 'medical_collapse' => ['medical', 'collapse', 'অসুস্থ হয়ে']],
            ],
            'SEIZURE' => [
                'currently_seizing' => ['active_now' => ['active_now', 'active', 'shaking now', 'এখনই হচ্ছে'], 'stopped_now' => ['stopped_now', 'stopped', 'থেমেছে']],
                'duration' => ['over_5m' => ['over_5m', '> 5', '৫ মিনিটের বেশি'], 'under_5m' => ['under_5m', '< 5', '৫ মিনিটের কম']],
            ],
            'BURN' => [
                'airway_involvement' => ['face_airway' => ['face', 'airway', 'মুখ', 'শ্বাসনালী'], 'body_limbs' => ['body', 'limbs', 'হাত-পা']],
                'burn_cause' => ['flame_explosion' => ['flame', 'explosion', 'আগুন', 'বিস্ফোরণ'], 'hot_liquid' => ['liquid', 'scald', 'গরম পানি'], 'chemical_electric' => ['chemical', 'electric', 'বিদ্যুৎ']],
            ],
        ];

        $lower = trim(mb_strtolower($userText, 'UTF-8'));

        if (isset($flow[$intent][$questionKey])) {
            // First pass: exact match against value or aliases
            foreach ($flow[$intent][$questionKey] as $val => $aliases) {
                if ($lower === mb_strtolower($val, 'UTF-8')) {
                    return $val;
                }
                foreach ($aliases as $alias) {
                    if ($lower === mb_strtolower($alias, 'UTF-8')) {
                        return $val;
                    }
                }
            }

            // Second pass: substring match checking longer aliases first
            foreach ($flow[$intent][$questionKey] as $val => $aliases) {
                foreach ($aliases as $alias) {
                    $aliasLower = mb_strtolower($alias, 'UTF-8');
                    if (str_contains($lower, $aliasLower)) {
                        return $val;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Detect general knowledge topic intent from text
     */
    private function detectKnowledgeTopic(string $text): ?string
    {
        $lower = mb_strtolower($text, 'UTF-8');

        if (str_contains($lower, 'stroke') || str_contains($lower, 'স্ট্রোক') || str_contains($lower, 'fast')) {
            return 'STROKE';
        }
        if (str_contains($lower, 'heart attack') || str_contains($lower, 'cardiac') || str_contains($lower, 'হার্ট অ্যাটাক') || str_contains($lower, 'বুকের ব্যথা')) {
            return 'CARDIAC_EMERGENCY';
        }
        if (str_contains($lower, 'breath') || str_contains($lower, 'শ্বাসকষ্ট') || str_contains($lower, 'দম')) {
            return 'BREATHING_EMERGENCY';
        }
        if (str_contains($lower, 'bleed') || str_contains($lower, 'রক্তপাত')) {
            return 'SEVERE_BLEEDING';
        }
        if (str_contains($lower, 'unconscious') || str_contains($lower, 'অচেতন') || str_contains($lower, 'বেহুঁশ')) {
            return 'UNCONSCIOUSNESS';
        }
        if (str_contains($lower, 'seizure') || str_contains($lower, 'খিঁচুনি') || str_contains($lower, 'ফিট')) {
            return 'SEIZURE';
        }
        if (str_contains($lower, 'burn') || str_contains($lower, 'পোড়া') || str_contains($lower, 'আগুনে')) {
            return 'BURN';
        }

        return null;
    }

    /**
     * Finalize and store structured assessment record
     */
    private function finalizeAssessment(ChatConversation $conversation, string $intent, string $lang): ChatAssessment
    {
        $isEmergency = $conversation->is_emergency_active;
        $severity = $isEmergency ? 'P1' : 'P2';

        $summary = ($lang === 'bn')
            ? "{$intent} লক্ষণসমূহের প্রাথমিক জরুরি পর্যালোচনা সম্পন্ন।"
            : "Preliminary emergency assessment completed for {$intent} symptoms.";

        $recommendations = ($isEmergency)
            ? [
                ($lang === 'bn' ? 'অবিলম্বে ৯৯৯-এ কল করুন এবং জরুরি অ্যাম্বুলেন্স চান' : 'Call 999 immediately and request an emergency ambulance'),
                ($lang === 'bn' ? 'রোগীকে নিরাপদ স্থানে বিশ্রামে রাখুন' : 'Keep patient in a safe, resting posture and monitor vital signs'),
            ]
            : [
                ($lang === 'bn' ? 'নিকটস্থ রেজিস্টার্ড চিকিৎসকের সাথে পরামর্শ করুন' : 'Consult with a registered healthcare provider promptly'),
            ];

        return ChatAssessment::updateOrCreate(
            ['conversation_id' => $conversation->id],
            [
                'incident_type' => $intent,
                'severity' => $severity,
                'answers' => $conversation->assessment_data ?? [],
                'red_flags' => $isEmergency ? [$intent] : [],
                'emergency_triggered' => $isEmergency,
            ]
        );
    }

    /**
     * Trigger direct SOS from Chat Assistant
     */
    public function triggerSosFromChat(ChatConversation $conversation, array $data = []): array
    {
        $user = Auth::user();
        $emergencyType = $conversation->active_intent ?: 'GENERAL_SOS';
        $lat = $data['latitude'] ?? null;
        $lng = $data['longitude'] ?? null;
        $address = $data['address'] ?? ($lat && $lng ? "GPS: {$lat}, {$lng}" : 'Location captured via Emergency Assistant');

        $incident = EmergencyIncident::create([
            'reporter_user_id' => $user?->id ?? $conversation->user_id,
            'reporter_name' => $user?->name ?? ($data['reporter_name'] ?? 'Emergency Assistant Caller'),
            'reporter_phone' => $user?->phone ?? ($data['reporter_phone'] ?? null),
            'emergency_type' => $emergencyType,
            'description' => 'Direct SOS triggered via Need Help Emergency Assistant. Conversation ID: ' . $conversation->id,
            'latitude' => $lat,
            'longitude' => $lng,
            'address' => $address,
            'severity_level' => 'P1',
            'priority_score' => 99,
            'severity_reasons' => ['Emergency Assistant Triggered SOS', 'Critical Life-Safety Escalation'],
            'required_service' => 'Ambulance & Hospital Dispatch',
            'status' => 'REPORTED',
            'is_sos' => true,
            'ai_confidence' => 1.0,
            'reported_at' => now(),
        ]);

        $incident->logEvent(
            eventType: 'CREATED',
            title: 'Chat Assistant SOS Dispatched',
            description: 'Emergency SOS activated directly through conversational assistant triage.',
            metadata: ['conversation_id' => $conversation->id],
            actor: $user,
            fromStatus: null,
            toStatus: 'REPORTED'
        );

        $conversation->linkIncident($incident);

        $lang = $conversation->language;
        $confirmationText = ($lang === 'bn')
            ? "🚨 জরুরি SOS সফলভাবে রেকর্ড করা হয়েছে! রেফারেন্স কোড: {$incident->public_reference}। কন্ট্রোল রুম ও জরুরি রেসপন্ডারদের নিকট তথ্য পাঠানো হয়েছে। আপনি লাইভ ট্র্যাকিং পেজে অবস্থান দেখতে পারেন।"
            : "🚨 Emergency SOS successfully recorded! Reference Code: {$incident->public_reference}. Dispatchers have been alerted. You can track status on the live incident page.";

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender' => 'assistant',
            'message' => $confirmationText,
            'metadata' => [
                'language' => $lang,
                'sos_reference' => $incident->public_reference,
                'tracking_url' => route('emergency.show', $incident->public_reference),
            ],
        ]);

        return [
            'status' => 'success',
            'message' => $confirmationText,
            'reference' => $incident->public_reference,
            'tracking_url' => route('emergency.show', $incident->public_reference),
            'incident' => [
                'reference' => $incident->public_reference,
                'severity' => $incident->severity_level,
                'status' => $incident->status,
            ],
        ];
    }

    /**
     * Find nearby hospitals based on coordinates
     */
    public function findNearbyHospitals(?float $lat, ?float $lng, int $limit = 3): array
    {
        $lat = $lat ?? 23.7808; // Default Dhaka Center
        $lng = $lng ?? 90.4152;

        try {
            $paginated = $this->locationService->searchResources([
                'lat' => $lat,
                'lng' => $lng,
                'radius' => 25.0,
                'category' => 'hospital',
                'per_page' => $limit,
            ]);

            $hospitals = [];
            foreach ($paginated->items() as $item) {
                $hospitals[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'address' => $item->address,
                    'phone' => $item->phone,
                    'emergency_hotline' => $item->details?->emergency_hotline ?? $item->phone ?? '999',
                    'distance_km' => isset($item->distance) ? round((float) $item->distance, 1) : null,
                    'has_icu' => (bool) ($item->details?->has_icu ?? false),
                    'has_emergency_ward' => (bool) ($item->details?->has_emergency_ward ?? true),
                ];
            }

            return $hospitals;
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch nearby hospitals for chatbot: ' . $e->getMessage());
            return [];
        }
    }
}
