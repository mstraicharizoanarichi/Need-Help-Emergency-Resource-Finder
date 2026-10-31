<?php

namespace App\Services\Chatbot;

class EmergencyRedFlagDetector
{
    /**
     * Detect red flags and provide advisory
     */
    public function detect(string $text): array
    {
        $analysis = $this->analyze($text);
        $advisoryMap = [
            'STROKE' => 'Possible Acute Stroke signs detected (FAST: Facial droop, Arm weakness, Slurred speech, Time is critical).',
            'CARDIAC_EMERGENCY' => 'Possible Heart Attack / Acute Coronary Syndrome indicators detected.',
            'BREATHING_EMERGENCY' => 'Critical Respiratory Distress detected. Airway and oxygenation compromised.',
            'UNCONSCIOUSNESS' => 'Unresponsive / Collapsed individual detected. Immediate emergency response required.',
            'SEVERE_BLEEDING' => 'Severe arterial or heavy hemorrhage detected. Direct pressure required.',
            'SEIZURE' => 'Active convulsions or prolonged seizure episode detected.',
            'BURN' => 'Significant thermal burn or inhalation injury detected.',
            'ROAD_ACCIDENT' => 'High-velocity road collision or vehicular trauma reported.',
            'SOS_REQUEST' => 'Emergency SOS alert manually triggered by user.',
        ];

        $analysis['safety_advisory'] = $advisoryMap[$analysis['intent']] ?? ($analysis['reasons'][0] ?? 'Potential emergency condition.');
        return $analysis;
    }

    /**
     * Rules mapping indicators across English, Bengali and Banglish to critical intents & severity
     */
    public function analyze(string $text): array
    {
        $lower = mb_strtolower($text, 'UTF-8');

        // If purely an informational or symptom guide inquiry, do not trigger acute emergency red-flag
        if ($this->isEducationalQuery($lower)) {
            return [
                'intent' => 'GENERAL_HEALTH',
                'severity' => 'P3',
                'is_red_flag' => false,
                'emergency_mode' => false,
                'reasons' => ['General health / informational knowledge query'],
            ];
        }

        // 1. STROKE / ACUTE NEUROLOGICAL (P1 Critical)
        if ($this->matches($lower, [
            'stroke', 'facial droop', 'face drooping', 'face droop', 'slurred speech', 'cannot speak', 'sudden weakness',
            'paralysis', 'one side weak', 'loss of speech', 'speech difficulty',
            'স্ট্রোক', 'মুখ বাঁকা', 'হাত অবশ', 'অসাড়', 'কথা বলতে পারছে না', 'কথা জড়িয়ে',
            'kotha bolte partese na', 'kotha bolte parchen na', 'mukh baka', 'hat obosh', 'side obosh', 'kotha ashe na',
        ])) {
            return [
                'intent' => 'STROKE',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['Acute stroke warning indicators (facial droop, speech impairment, or unilateral weakness)'],
            ];
        }

        // 2. CARDIAC EMERGENCY / CHEST PAIN (P1 Critical)
        if ($this->matches($lower, [
            'chest pain', 'heart attack', 'cardiac', 'crushing chest', 'chest pressure', 'radiating pain', 'angina',
            'বুকে ব্যথা', 'হার্ট অ্যাটাক', 'বুকে চাপ', 'বুকের ব্যথা', 'হার্ট এটাক',
            'buke betha', 'bukey betha', 'buke chap', 'heart attack', 'buke prachando betha',
        ])) {
            return [
                'intent' => 'CARDIAC_EMERGENCY',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['Severe chest pain or cardiac emergency warning indicators'],
            ];
        }

        // 3. RESPIRATORY EMERGENCY / BREATHING DISTRESS (P1 Critical)
        if ($this->matches($lower, [
            'difficulty breathing', 'cannot breathe', 'shortness of breath', 'choking', 'cyanosis', 'turning blue', 'gasping', 'stopped breathing', 'not breathing',
            'শ্বাসকষ্ট', 'শ্বাস নিতে পারছি না', 'দম আটকে', 'নীল হয়ে', 'শ্বাস বন্ধ',
            'shash nite parchi na', 'shash koshto', 'shash bondho', 'dom atke', 'nil hoye', 'breathing problem',
        ])) {
            return [
                'intent' => 'BREATHING_EMERGENCY',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['Acute respiratory distress, severe airway obstruction, or breathing cessation'],
            ];
        }

        // 4. UNCONSCIOUSNESS / SUDDEN COLLAPSE (P1 Critical)
        if ($this->matches($lower, [
            'unconscious', 'collapsed', 'unresponsive', 'passed out', 'fainted', 'loss of consciousness', 'not waking up',
            'অচেতন', 'বেহুঁশ', 'জ্ঞান হারিয়েছে', 'অচৈতন্য', 'সাড়া দিচ্ছে না',
            'behesh', 'ogyan', 'gyan nai', 'gyan harieche', 'unresponsive', 'pore geche',
        ])) {
            return [
                'intent' => 'UNCONSCIOUSNESS',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['Sudden unresponsiveness, collapse, or loss of consciousness'],
            ];
        }

        // 5. SEVERE BLEEDING / HEMORRHAGE (P1 Critical)
        if ($this->matches($lower, [
            'severe bleeding', 'heavy bleeding', 'bleeding profusely', 'spurting blood', 'blood won\'t stop', 'massive blood loss', 'deep cut bleeding', 'bleeding',
            'প্রচুর রক্ত', 'রক্তক্ষরণ', 'রক্ত বন্ধ হচ্ছে না', 'রক্ত ঝরছে', 'অতিরিক্ত রক্ত', 'রক্তপাত', 'প্রচণ্ড রক্তপাত', 'রক্তপাত হচ্ছে', 'রক্ত বের হচ্ছে',
            'prochur rokto', 'rokto porche', 'rokto thambache na', 'heavy rokto', 'rokto pora', 'rokto jhorche',
        ])) {
            return [
                'intent' => 'SEVERE_BLEEDING',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['Severe active hemorrhage or uncontrolled arterial/venous bleeding'],
            ];
        }

        // 6. SEIZURE / CONVULSION (P1/P2 Urgent)
        if ($this->matches($lower, [
            'seizure', 'convulsion', 'fits', 'epilepsy', 'shaking violently',
            'খিঁচুনি', 'চোখ উল্টে', 'ফিট হয়েছে',
            'khichuni', 'chokh ulte', 'fits', 'shorir kapche',
        ])) {
            return [
                'intent' => 'SEIZURE',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['Active acute seizure or severe convulsions'],
            ];
        }

        // 7. SEVERE BURN / FIRE HAZARD (P1 Critical)
        if ($this->matches($lower, [
            'burn', 'fire', 'explosion', 'acid burn', 'chemical burn', 'smoke inhalation', 'scald',
            'আগুন', 'দগ্ধ', 'পুড়ে গেছে', 'বিস্ফোরণ', 'গ্যাস সিলিন্ডার',
            'pure geche', 'agune', 'pure gache', 'bisforon', 'burn hoyeche',
        ])) {
            return [
                'intent' => 'BURN',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['Thermal, chemical, or explosive burn trauma requiring burn center care'],
            ];
        }

        // 8. ROAD ACCIDENT / MAJOR TRAUMA (P1 Critical)
        if ($this->matches($lower, [
            'road accident', 'car crash', 'bus accident', 'collision', 'hit by truck', 'crushed', 'compound fracture', 'head trauma',
            'সড়ক দুর্ঘটনা', 'অ্যাক্সিডেন্ট', 'বাস দুর্ঘটনা', 'ট্রাক ধাক্কা', 'মাথায় মারাত্মক আঘাত',
            'road accident', 'accident hoyeche', 'truck dhakka', 'bus accident',
        ])) {
            return [
                'intent' => 'ROAD_ACCIDENT',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['High-impact vehicular collision or major physical trauma'],
            ];
        }

        // 9. DIRECT SOS / LIFE THREAT TRIGGER (P1 Critical)
        if ($this->matches($lower, [
            'sos', 'save me', 'save us', 'dying', 'urgent emergency', 'immediate help',
            'বাঁচাও', 'বাচাও', 'জরুরি সাহায্য', 'প্রাণ সংশয়',
            'bachao', 'help me now', 'emergency help', 'khub emergency',
        ])) {
            return [
                'intent' => 'SOS_REQUEST',
                'severity' => 'P1',
                'is_red_flag' => true,
                'emergency_mode' => true,
                'reasons' => ['Direct emergency SOS help requested by citizen'],
            ];
        }

        // 10. NEAREST FACILITY (HOSPITAL / AMBULANCE)
        if ($this->matches($lower, [
            'find hospital', 'nearest hospital', 'hospital near me', 'nearby hospital', 'hospital', 'icu bed',
            'নিকটস্থ হাসপাতাল', 'হাসপাতাল কোথায়', 'হাসপাতাল দরকার', 'কাছের হাসপাতাল',
            'kaser hospital', 'hospital koi', 'hospital dorkar',
        ])) {
            return [
                'intent' => 'NEAREST_HOSPITAL',
                'severity' => 'P2',
                'is_red_flag' => false,
                'emergency_mode' => false,
                'reasons' => ['Inquiry for nearest emergency hospital facilities'],
            ];
        }

        if ($this->matches($lower, [
            'ambulance', 'nearest ambulance', 'icu ambulance', 'call ambulance',
            'অ্যাম্বুলেন্স', 'অ্যাম্বুলেন্স চাই', 'অ্যাম্বুলেন্স দরকার',
            'ambulance dorkar', 'ambulance lagbe', 'kaser ambulance',
        ])) {
            return [
                'intent' => 'NEAREST_AMBULANCE',
                'severity' => 'P2',
                'is_red_flag' => false,
                'emergency_mode' => false,
                'reasons' => ['Inquiry for emergency ambulance dispatch services'],
            ];
        }

        return [
            'intent' => 'GENERAL_HEALTH',
            'severity' => 'P3',
            'is_red_flag' => false,
            'emergency_mode' => false,
            'reasons' => [],
        ];
    }

    private function isEducationalQuery(string $lower): bool
    {
        $educationalPhrases = [
            'লক্ষণ কী', 'কী লক্ষণ', 'লক্ষণ কি', 'কী কি লক্ষণ', 'কী কী লক্ষণ', 'কাকে বলে', 'বলতে কি', 'তথ্য',
            'কী করতে হয়', 'কীভাবে বুঝব', 'প্রাথমিক চিকিৎসা', 'উপসর্গ কি', 'উপসর্গ কী', 'হলে কি করব', 'হলে কী করব', 'জানতে চাই',
            'what are the signs', 'signs of', 'symptoms of', 'what is a', 'what is', 'what are the symptoms',
            'how to identify', 'how to know', 'tell me about', 'information about', 'first aid for', 'what to do if',
            'symptoms ki', 'symptom ki', 'sign ki', 'signs ki', 'er symptoms ki', 'ki korbo', 'ki korte hoy',
            'ki bhabe bujhbo', 'first aid ki', 'janar jonno', 'janar jonne', 'jante chai', 'karon ki',
        ];

        foreach ($educationalPhrases as $phrase) {
            if (str_contains($lower, $phrase)) {
                return true;
            }
        }

        return false;
    }

    private function matches(string $text, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (mb_strpos($text, mb_strtolower($pattern, 'UTF-8')) !== false) {
                return true;
            }
        }
        return false;
    }
}
