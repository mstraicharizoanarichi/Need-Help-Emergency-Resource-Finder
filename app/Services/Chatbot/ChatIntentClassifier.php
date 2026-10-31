<?php

namespace App\Services\Chatbot;

class ChatIntentClassifier
{
    /**
     * Normalize conversational input for robust matching
     */
    public function normalizeText(string $text): string
    {
        $normalized = mb_strtolower($text, 'UTF-8');
        // Replace common punctuation with spaces except internal hyphens
        $normalized = preg_replace('/[?,!.:;।\/\\()"\']+/u', ' ', $normalized);
        // Collapse multiple spaces
        $normalized = trim(preg_replace('/\s+/u', ' ', $normalized));

        return $normalized;
    }

    /**
     * Priority 1: Critical current-message red flag override.
     * Evaluates strictly on the CURRENT message, independent of previous conversation state.
     */
    public function detectCriticalCurrentRedFlag(string $text): ?array
    {
        $norm = $this->normalizeText($text);

        // 1. Not breathing / Respiratory cessation
        $notBreathingPatterns = [
            'not breathing', 'stopped breathing', 'cannot breathe at all', 'gasping for life', 'choking cannot breathe',
            'সে শ্বাস নিচ্ছে না', 'শ্বাস নিচ্ছে না', 'শ্বাস বন্ধ', 'শ্বাস বন্ধ হয়ে গেছে', 'দম বন্ধ হয়ে গেছে', 'দম আটকে আসছে কথা বলতে পারছে না',
            'shash nite parchena', 'shash nite parche na', 'shash bondho', 'dom atke geche', 'shash nite pare na', 'breathing stopped',
        ];
        if ($this->matchesAny($norm, $notBreathingPatterns)) {
            return [
                'intent' => 'BREATHING_EMERGENCY',
                'severity' => 'P1',
                'safety_advisory' => 'Critical Respiratory Arrest / Airway Obstruction detected.',
                'immediate_action' => 'Check airway immediately. Call 999 or tap 1-Tap SOS now. If not breathing and unresponsive, begin CPR chest compressions.',
            ];
        }

        // 2. Uncontrolled massive hemorrhage
        $massiveBleedingPatterns = [
            'massive bleeding', 'blood won\'t stop', 'blood will not stop', 'bleeding won\'t stop', 'spurting blood', 'arterial bleed',
            'অনেক রক্ত বের হচ্ছে এবং বন্ধ হচ্ছে না', 'রক্ত বন্ধ হচ্ছে না', 'ফিনকি দিয়ে রক্ত পড়ছে', 'প্রচুর রক্ত বের হচ্ছে এবং থামছে না',
            'rokto thambache na', 'rokto bondho hocche na', 'massive rokto', 'prochur rokto bondho hocche na',
        ];
        if ($this->matchesAny($norm, $norm === 'রক্ত বন্ধ হচ্ছে না' ? ['রক্ত বন্ধ হচ্ছে না'] : $massiveBleedingPatterns)) {
            return [
                'intent' => 'SEVERE_BLEEDING',
                'severity' => 'P1',
                'safety_advisory' => 'Critical Active Hemorrhage detected.',
                'immediate_action' => 'Apply firm, direct, continuous pressure with a clean cloth. Elevate wound if possible. Call 999 immediately.',
            ];
        }

        // 3. Unconscious and unresponsive collapse
        $unconsciousCollapsePatterns = [
            'unconscious and not breathing', 'collapsed and unresponsive', 'fell down unconscious', 'passed out not waking up',
            'জ্ঞান হারিয়েছে এবং শ্বাস নিচ্ছে না', 'অচেতন হয়ে পড়ে আছে', 'সাড়া দিচ্ছে না এবং নিশ্বাস নেই', 'হঠাৎ পড়ে গিয়ে বেহুঁশ',
            'behesh hoye pore ache', 'ogyan hoye geche shash nai', 'unconscious pore ache',
        ];
        if ($this->matchesAny($norm, $unconsciousCollapsePatterns)) {
            return [
                'intent' => 'UNCONSCIOUSNESS',
                'severity' => 'P1',
                'safety_advisory' => 'Sudden Unresponsive Collapse detected.',
                'immediate_action' => 'Check breathing and pulse. Place in recovery position if breathing. If not breathing, start CPR and call 999.',
            ];
        }

        return null;
    }

    /**
     * Priority 2: Detect negative response or cancellation.
     * Prevents previous emergency state from carrying over into unrelated messages.
     */
    public function isNegativeOrCancellation(string $text): bool
    {
        $norm = $this->normalizeText($text);

        // Exact negative words/phrases
        $exactNegatives = [
            // English
            'no', 'nope', 'nah', 'not now', 'not happening', 'just asking', 'just wanted to know',
            'no just asking', 'dont need it', 'don\'t need it', 'no need', 'nothing', 'not an emergency',
            'cancel', 'stop', 'nevermind', 'never mind', 'false alarm', 'no thanks', 'none',
            // Bengali Unicode
            'না', 'না শুধু জানার জন্য', 'না দরকার নেই', 'দরকার নেই', 'এখন না', 'হচ্ছে না',
            'শুধু জানতে চাচ্ছি', 'শুধু জানার জন্য', 'কোনো সমস্যা নেই', 'কোন সমস্যা নেই', 'জরুরি না',
            'এমার্জেন্সি না', 'জরুরী না', 'লাগবে না', 'না লাগবে না', 'না কিছু না', 'কিছু না',
            'না ধন্যবাদ', 'কিছু হয়নি', 'কিছু হয় নাই',
            // Banglish
            'nah', 'na', 'na dorkar nai', 'dorkar nai', 'na shudhu janar jonno', 'shudhu janar jonno',
            'just janar jonno', 'emergency nai', 'kichu hoy nai', 'problem nai', 'ekhon na',
            'kono problem nai', 'lagbe na', 'na lagbe na', 'nah dorkar nai', 'nah lagbe na',
            'just asking', 'just information', 'shudu jante chai', 'nah shudu jante chai',
        ];

        if (in_array($norm, $exactNegatives, true)) {
            return true;
        }

        // If the text contains clinical/emergency distress keywords, it cannot be a cancellation
        $clinicalIndicators = [
            'কথা বলতে পারছে না', 'শ্বাস', 'রক্ত', 'ব্যথা', 'অবশ', 'দুর্বল', 'অচেতন', 'বেহুঁশ',
            'খিঁচুনি', 'পুড়ে', 'আগুন', 'দুর্ঘটনা', 'অ্যাক্সিডেন্ট', 'বমি', 'পড়ে গিয়ে',
            'breathing', 'bleed', 'pain', 'weakness', 'unconscious', 'seizure', 'accident',
        ];
        foreach ($clinicalIndicators as $ci) {
            if (str_contains($norm, $ci)) {
                return false;
            }
        }

        // Phrases that start with negation prefix and convey cancellation
        $negativePrefixes = [
            'না ', 'nah ', 'na ', 'no ', 'দরকার নেই', 'dorkar nai', 'lagbe na', 'just asking',
            'শুধু জানার জন্য', 'shudhu janar jonno', 'just janar jonno', 'kichu na', 'kono somossa nai',
        ];

        foreach ($negativePrefixes as $p) {
            if (str_starts_with($norm, $p) || $norm === trim($p)) {
                // Short negative statements (up to 8 words)
                if (count(explode(' ', $norm)) <= 8) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Priority 3: Explicit Emergency Confirmation or Direct SOS Request
     */
    public function isExplicitConfirmation(string $text): bool
    {
        $norm = $this->normalizeText($text);

        $confirmationPhrases = [
            // Explicit Yes
            'yes', 'yes right now', 'right now', 'happening now', 'yes happening now', 'it is happening now',
            'হ্যাঁ', 'হ্যাঁ এখন হচ্ছে', 'হ্যাঁ এইমাত্র ঘটেছে', 'হ্যাঁ জরুরি', 'এখনই হচ্ছে', 'এইমাত্র ঘটেছে',
            'ha', 'haa', 'hae', 'ekhon hocche', 'active now', 'yes active', 'hoche',
            // Explicit SOS / Ambulance request
            'help me', 'send ambulance', 'start sos', 'call emergency', 'call 999', 'call ambulance',
            'ambulance দরকার', 'ambulance পাঠান', 'এখনই সাহায্য দরকার', 'বাঁচাও', 'বাচাও', 'জরুরি সাহায্য পাঠান',
            'bachao', 'help me now', 'emergency help', 'send help', 'ambulance lagbe', 'ambulance pathan',
        ];

        if (in_array($norm, $confirmationPhrases, true)) {
            return true;
        }

        foreach (['হ্যাঁ', 'yes', 'help me', 'send ambulance', 'start sos', 'ambulance পাঠান', 'বাঁচাও'] as $kw) {
            if (str_contains($norm, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Priority 4: General Greeting & Polite Chit-Chat
     */
    public function isGeneralGreeting(string $text): bool
    {
        $norm = $this->normalizeText($text);

        $greetings = [
            'hello', 'hi', 'hey', 'hii', 'helo', 'assalamu alaikum', 'assalamualaykum', 'assalam alaikum',
            'assalamualaikum', 'salam', 'slaam', 'good morning', 'good afternoon', 'good evening',
            'how are you', 'kemon acho', 'kemon achen', 'ki khobor',
            'thanks', 'thank you', 'ধন্যবাদ', 'অনেক ধন্যবাদ', 'thx', 'bye', 'goodbye', 'বিদায়',
        ];

        if (in_array($norm, $greetings, true)) {
            return true;
        }

        foreach (['hello', 'hi', 'hey', 'salam', 'assalamu alaikum', 'thank you', 'thanks', 'ধন্যবাদ'] as $g) {
            if ($norm === $g || str_starts_with($norm, $g . ' ') || str_ends_with($norm, ' ' . $g)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Priority 5: Educational / Information Inquiries
     * Distinguishes learning inquiries from acute active emergencies.
     */
    public function isInformationRequest(string $text): ?string
    {
        $norm = $this->normalizeText($text);

        $educationalMarkers = [
            // English
            'what are the symptoms', 'symptoms of', 'signs of', 'what is a', 'what is',
            'how to know', 'how to identify', 'tell me about', 'information about',
            'first aid for', 'what to do if', 'causes of', 'how to treat',
            // Bengali Unicode
            'লক্ষণ কী', 'কী লক্ষণ', 'লক্ষণ কি', 'কী কি লক্ষণ', 'কী কী লক্ষণ', 'কী করতে হয়',
            'কীভাবে বুঝব', 'প্রাথমিক চিকিৎসা', 'কাকে বলে', 'বলতে কি', 'তথ্য', 'উপসর্গ কি',
            'উপসর্গ কী', 'হলে কি করব', 'হলে কী করব', 'জানতে চাই',
            // Banglish
            'symptoms ki', 'symptom ki', 'sign ki', 'signs ki', 'er symptoms ki', 'ki korbo',
            'ki korte hoy', 'ki bhabe bujhbo', 'first aid ki', 'janar jonno', 'janar jonne',
            'jante chai', 'karon ki', 'uposorgo ki',
        ];

        $isInfo = false;
        foreach ($educationalMarkers as $marker) {
            if (str_contains($norm, $marker)) {
                $isInfo = true;
                break;
            }
        }

        if (!$isInfo) {
            // Also check for trailing question marks or questions like "stroke ki?"
            if (preg_match('/^(stroke|heart attack|cardiac|seizure|bleeding|burn)\s+(ki|kake bole|er|signs|symptoms)$/u', $norm)) {
                $isInfo = true;
            }
        }

        if (!$isInfo) {
            return null;
        }

        // Determine the medical topic
        if (str_contains($norm, 'stroke') || str_contains($norm, 'স্ট্রোক') || str_contains($norm, 'fast')) {
            return 'STROKE';
        }
        if (str_contains($norm, 'heart') || str_contains($norm, 'cardiac') || str_contains($norm, 'হার্ট') || str_contains($norm, 'বুকের ব্যথা') || str_contains($norm, 'chest pain')) {
            return 'CARDIAC_EMERGENCY';
        }
        if (str_contains($norm, 'breath') || str_contains($norm, 'শ্বাসকষ্ট') || str_contains($norm, 'দম')) {
            return 'BREATHING_EMERGENCY';
        }
        if (str_contains($norm, 'bleed') || str_contains($norm, 'রক্তপাত') || str_contains($norm, 'rokto')) {
            return 'SEVERE_BLEEDING';
        }
        if (str_contains($norm, 'unconscious') || str_contains($norm, 'অচেতন') || str_contains($norm, 'বেহুঁশ') || str_contains($norm, 'ogyan')) {
            return 'UNCONSCIOUSNESS';
        }
        if (str_contains($norm, 'seizure') || str_contains($norm, 'খিঁচুনি') || str_contains($norm, 'ফিট') || str_contains($norm, 'fits')) {
            return 'SEIZURE';
        }
        if (str_contains($norm, 'burn') || str_contains($norm, 'পোড়া') || str_contains($norm, 'আগুন') || str_contains($norm, 'pura')) {
            return 'BURN';
        }

        return 'GENERAL_HEALTH';
    }

    /**
     * Helper to match any pattern
     */
    private function matchesAny(string $text, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return true;
            }
        }
        return false;
    }
}
