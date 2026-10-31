<?php

namespace App\Services\AI;

class DeterministicEmergencyParser implements EmergencyAIProviderInterface
{
    /**
     * Triage incident description using rule-based Bangla and English medical keywords
     */
    public function analyzeIncident(string $text, ?string $language = null): array
    {
        $detectedLang = $language ?? $this->detectLanguage($text);
        $lowerText = mb_strtolower($text, 'UTF-8');

        // Check for Cardiac / Stroke emergencies (P1)
        if ($this->matchesKeywords($lowerText, [
            'বুকে ব্যথা', 'হার্ট অ্যাটাক', 'শ্বাসকষ্ট', 'শ্বাস নিতে পারছি না', 'স্ট্রোক', 'মুখ বাঁকা', 'হাত অবশ', 'অসাড়', 'বুকে চাপ',
            'chest pain', 'heart attack', 'cardiac', 'difficulty breathing', 'shortness of breath', 'stroke', 'facial droop', 'unresponsive',
        ])) {
            return [
                'emergency_type' => 'CARDIAC_STROKE',
                'severity_level' => 'P1',
                'priority_score' => 98,
                'severity_reasons' => [
                    $detectedLang === 'bn' ? 'তীব্র হৃদরোগ অথবা স্ট্রোকের লক্ষণ সনাক্ত' : 'Severe cardiac or acute stroke symptoms detected',
                    $detectedLang === 'bn' ? 'জরুরি অক্সিজেন ও সিসিইউ সাপোর্ট প্রয়োজন' : 'Immediate oxygen, cardiac monitoring and CCU/ICU required',
                ],
                'required_service' => 'Cardiac Care & ICU Ambulance',
                'required_equipment' => ['ICU Bed', 'Oxygen Support', 'Cardiac Monitor', 'Defibrillator'],
                'required_skills' => ['Cardiologist', 'Critical Care Paramedic'],
                'ai_confidence' => 0.94,
                'summary' => $detectedLang === 'bn' 
                    ? 'তীব্র কার্ডিয়াক/স্ট্রোক জরুরি অবস্থা। রোগী অবিলম্বে সিসিইউ/আইসিইউ সুবিধা সম্পন্ন হাসপাতালে স্থানান্তরযোগ্য।'
                    : 'Critical cardiac/stroke emergency. Immediate ALS ambulance dispatch with oxygen and CCU/ICU bed allocation required.',
                'language_detected' => $detectedLang,
            ];
        }

        // Check for Fire / Explosion / Burn hazards (P1)
        if ($this->matchesKeywords($lowerText, [
            'আগুন', 'অগ্নি', 'ধোঁয়া', 'আটকে আছে', 'দগ্ধ', 'পুড়ে গেছে', 'সিলিন্ডার বিস্ফোরণ', 'বিস্ফোরণ',
            'fire', 'burn', 'explosion', 'smoke inhalation', 'trapped in fire', 'gas explosion', 'gas leak',
        ])) {
            return [
                'emergency_type' => 'FIRE',
                'severity_level' => 'P1',
                'priority_score' => 93,
                'severity_reasons' => [
                    $detectedLang === 'bn' ? 'অগ্নি দুর্ঘটনা অথবা বিস্ফোরণের ঝুঁকি সনাক্ত' : 'Active fire incident or combustion explosion danger',
                    $detectedLang === 'bn' ? 'ফায়ার সার্ভিস উদ্ধার ও বার্ন ইউনিট প্রয়োজন' : 'Fire service rescue and burn trauma management required',
                ],
                'required_service' => 'Fire Service & Burn Unit Hospital',
                'required_equipment' => ['Fire Rescue Unit', 'Burn Unit Bed', 'Oxygen Resuscitator'],
                'required_skills' => ['Firefighter Rescue Specialist', 'Burn Specialist'],
                'ai_confidence' => 0.95,
                'summary' => $detectedLang === 'bn'
                    ? 'অগ্নিকাণ্ড বা বিস্ফোরণ পরিস্থিতি। ফায়ার সার্ভিস এবং নিকটস্থ বার্ন সেন্টারে দ্রুত সতর্কবার্তা প্রেরিত।'
                    : 'Active fire hazard. Direct dispatch notification to Fire Service & Civil Defence and regional burn trauma center.',
                'language_detected' => $detectedLang,
            ];
        }

        // Check for Road Accident / Major Trauma (P1)
        if ($this->matchesKeywords($lowerText, [
            'অ্যাক্সিডেন্ট', 'দুর্ঘটনা', 'রক্তক্ষরণ', 'প্রচুর রক্ত', 'মাথায় আঘাত', 'অচেতন', 'বেহুঁশ', 'জ্ঞান হারিয়েছে', 'হাত ভেঙে', 'পা ভেঙে', 'ট্রাক ধাক্কা', 'বাস দুর্ঘটনা',
            'accident', 'collision', 'heavy bleeding', 'severe bleeding', 'head injury', 'unconscious', 'fracture', 'crash', 'crushed',
        ])) {
            return [
                'emergency_type' => 'ROAD_ACCIDENT',
                'severity_level' => 'P1',
                'priority_score' => 95,
                'severity_reasons' => [
                    $detectedLang === 'bn' ? 'মারাত্মক শারীরিক আঘাত অথবা অতিরিক্ত রক্তক্ষরণ' : 'Critical physical trauma or severe hemorrhage detected',
                    $detectedLang === 'bn' ? 'ট্রমা সার্জন এবং রক্ত সঞ্চালন সাপোর্ট জরুরি' : 'Immediate trauma surgery, blood transfusion, and immobilization required',
                ],
                'required_service' => 'Trauma Center & ALS Ambulance',
                'required_equipment' => ['Trauma Stretcher', 'C-Spine Collar', 'Oxygen Support', 'Blood Transfusion Unit'],
                'required_skills' => ['Trauma Surgeon', 'Orthopedic Surgeon', 'Emergency Paramedic'],
                'ai_confidence' => 0.92,
                'summary' => $detectedLang === 'bn'
                    ? 'মারাত্মক সড়ক দুর্ঘটনা/ট্রমা কেস। প্যারামেডিক সহ লাইফ-সাপোর্ট অ্যাম্বুলেন্স দ্রুত প্রেরণ করা প্রয়োজন।'
                    : 'Major trauma/collision case. Priority dispatch of ALS ambulance with trauma immobilization gear.',
                'language_detected' => $detectedLang,
            ];
        }

        // Check for Crime / Police / Threat (P1/P2)
        if ($this->matchesKeywords($lowerText, [
            'ডাকাতি', 'ছিনতাই', 'খুন', 'হামলা', 'মারামারি', 'বন্দুক', 'ছুরিকাঘাত', 'অপহরণ', 'হুমকি',
            'robbery', 'mugging', 'assault', 'stab', 'gunshot', 'kidnap', 'violence', 'attack', 'police',
        ])) {
            return [
                'emergency_type' => 'POLICE',
                'severity_level' => 'P1',
                'priority_score' => 90,
                'severity_reasons' => [
                    $detectedLang === 'bn' ? 'সরাসরি অপরাধ বা শারীরিক নিরাপত্তার হুমকি' : 'Direct crime or violent physical safety threat detected',
                    $detectedLang === 'bn' ? 'আইনশৃঙ্খলা রক্ষাকারী বাহিনীর তাৎক্ষণিক হস্তক্ষেপ প্রয়োজন' : 'Immediate law enforcement intervention required',
                ],
                'required_service' => 'Police Rapid Action & Hospital Escort',
                'required_equipment' => ['Police Patrol Unit', 'First Aid Trauma Kit'],
                'required_skills' => ['Police Officer', 'First Responder'],
                'ai_confidence' => 0.91,
                'summary' => $detectedLang === 'bn'
                    ? 'জরুরি আইনশৃঙ্খলা পরিস্থিতি। নিকটস্থ থানা ও পুলিশ পেট্রোল টিমকে সক্রিয় করা প্রয়োজন।'
                    : 'Active public safety/police emergency. Immediate police patrol dispatch and hospital standby.',
                'language_detected' => $detectedLang,
            ];
        }

        // Check for Obstetrics / Labor (P2 or P1)
        if ($this->matchesKeywords($lowerText, [
            'প্রসব বেদনা', 'ডেলিভারি', 'গর্ভবতী', 'পানি ভেঙে', 'বাচ্চা হবে', 'লেবার পেইন',
            'labor pain', 'labour pain', 'delivery', 'pregnant', 'pregnancy', 'water broke', 'childbirth',
        ])) {
            return [
                'emergency_type' => 'OBSTETRICS',
                'severity_level' => 'P2',
                'priority_score' => 84,
                'severity_reasons' => [
                    $detectedLang === 'bn' ? 'প্রসূতি জরুরি অবস্থা ও সম্ভাব্য প্রসবকালীন জটিলতা' : 'Active maternal labor or delivery complication',
                ],
                'required_service' => 'Maternity Hospital & Patient Transport Ambulance',
                'required_equipment' => ['Labor Bed', 'Neonatal Incubator', 'Fetal Heart Monitor'],
                'required_skills' => ['Obstetrician', 'Gynecologist', 'Midwife'],
                'ai_confidence' => 0.90,
                'summary' => $detectedLang === 'bn'
                    ? 'প্রসূতি মায়ের জরুরি সেবা। নিকটস্থ প্রসূতি ও শিশু হাসপাতালে স্থানান্তরের নির্দেশিত ব্যবস্থা।'
                    : 'Obstetric labor emergency. Coordinated transport to nearest specialized maternity and neonatal hospital.',
                'language_detected' => $detectedLang,
            ];
        }

        // Check for Pediatric / Child Emergency (P1/P2)
        if ($this->matchesKeywords($lowerText, [
            'বাচ্চা', 'শিশু', 'খিঁচুনি', 'শ্বাস বন্ধ', 'নীল হয়ে', 'দম আটকে',
            'baby', 'infant', 'child', 'convulsion', 'pediatric', 'choking', 'cyanosis',
        ])) {
            return [
                'emergency_type' => 'PEDIATRIC',
                'severity_level' => 'P1',
                'priority_score' => 89,
                'severity_reasons' => [
                    $detectedLang === 'bn' ? 'শিশুর আকস্মিক খিঁচুনি বা শ্বাসকষ্টজনিত সংকট' : 'Acute pediatric convulsion or respiratory distress',
                ],
                'required_service' => 'Pediatric Emergency & NICU/PICU Hospital',
                'required_equipment' => ['Pediatric ICU (PICU)', 'Pediatric Oxygen Mask', 'Pediatric Ventilator'],
                'required_skills' => ['Pediatrician', 'PICU Nurse'],
                'ai_confidence' => 0.88,
                'summary' => $detectedLang === 'bn'
                    ? 'শিশুর জরুরি শারীরিক সংকট। শিশু আইসিইউ (PICU) সুবিধা সম্পন্ন হাসপাতালে দ্রুত প্রেরণের সুপারিশ।'
                    : 'Pediatric crisis. Route immediately to medical center equipped with PICU and pediatric life support.',
                'language_detected' => $detectedLang,
            ];
        }

        // Check for Moderate Urgency (P2)
        if ($this->matchesKeywords($lowerText, [
            'উচ্চ জ্বর', 'তীব্র পেট ব্যথা', 'ফুড পয়জনিং', 'ঘন ঘন বমি', 'ডায়রিয়া', 'স্যালাইন',
            'high fever', 'severe stomach pain', 'food poisoning', 'vomiting', 'dehydration', 'acute abdominal pain',
        ])) {
            return [
                'emergency_type' => 'MEDICAL_AMBULANCE',
                'severity_level' => 'P2',
                'priority_score' => 68,
                'severity_reasons' => [
                    $detectedLang === 'bn' ? 'তীব্র অসুস্থতা যা পর্যবেক্ষণ ও চিকিৎসার দাবি রাখে' : 'Acute illness requiring medical observation and stabilization',
                ],
                'required_service' => 'Urgent Care Center & General Hospital',
                'required_equipment' => ['IV Fluid Infusion Stand', 'Observation Bed'],
                'required_skills' => ['Duty Medical Officer', 'General Physician'],
                'ai_confidence' => 0.85,
                'summary' => $detectedLang === 'bn'
                    ? 'মধ্যম মাত্রার স্বাস্থ্য সমস্যা (P2)। জরুরি বিভাগে ডাক্তার দেখানো প্রয়োজন।'
                    : 'Moderate urgency case (P2). Clinical assessment at nearest urgent care or hospital emergency room.',
                'language_detected' => $detectedLang,
            ];
        }

        // Check for Low Severity (P3)
        if ($this->matchesKeywords($lowerText, [
            'হালকা জ্বর', 'কাশি', 'সর্দি', 'গলা ব্যথা', 'মাথাব্যথা', 'প্রেসারের ওষুধ',
            'mild fever', 'cough', 'cold', 'sore throat', 'headache', 'checkup', 'prescription',
        ])) {
            return [
                'emergency_type' => 'GENERAL_MEDICAL',
                'severity_level' => 'P3',
                'priority_score' => 35,
                'severity_reasons' => [
                    $detectedLang === 'bn' ? 'সাধারণ অসুস্থতা, তাৎক্ষণিক জীবন সংশয় নেই' : 'Non-life-threatening standard medical consultation',
                ],
                'required_service' => 'Outpatient Clinic & Pharmacy',
                'required_equipment' => ['Consultation Desk', 'Diagnostic Lab'],
                'required_skills' => ['General Physician'],
                'ai_confidence' => 0.85,
                'summary' => $detectedLang === 'bn'
                    ? 'সাধারণ লক্ষণ (P3)। নিয়মিত ডাক্তার বা ওপিডি ক্লিনিকে পরামর্শ নেওয়ার পরামর্শ।'
                    : 'Low urgency inquiry (P3). Recommend outpatient clinic visit or tele-consultation.',
                'language_detected' => $detectedLang,
            ];
        }

        // Default Fallback
        return [
            'emergency_type' => 'GENERAL_SOS',
            'severity_level' => 'P2',
            'priority_score' => 60,
            'severity_reasons' => [
                $detectedLang === 'bn' ? 'প্রাথমিক বর্ণনা অনুযায়ী স্ট্যান্ডার্ড ইমার্জেন্সি প্রটোকল সক্রিয়' : 'Standard emergency intake triage applied based on text',
            ],
            'required_service' => 'Emergency Response & Hospital Assessment',
            'required_equipment' => ['General Emergency Ward Bed'],
            'required_skills' => ['Emergency Medical Officer'],
            'ai_confidence' => 0.70,
            'summary' => $detectedLang === 'bn'
                ? 'জরুরি পরিস্থিতি গৃহীত হয়েছে। নিকটস্থ উদ্ধারকারী ও স্বাস্থ্যসেবা টিমকে সতর্ক করা হচ্ছে।'
                : 'Emergency incident registered. Dispatched to regional responders and hospital emergency teams for verification.',
            'language_detected' => $detectedLang,
        ];
    }

    /**
     * Detect if text is predominantly Bengali
     */
    public function detectLanguage(string $text): string
    {
        // Check for Bengali Unicode range \x{0980}-\x{09FF}
        return preg_match('/[\x{0980}-\x{09FF}]/u', $text) ? 'bn' : 'en';
    }

    /**
     * Helper to match multiple keywords
     */
    private function matchesKeywords(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (mb_strpos($text, mb_strtolower($kw, 'UTF-8')) !== false) {
                return true;
            }
        }
        return false;
    }
}
