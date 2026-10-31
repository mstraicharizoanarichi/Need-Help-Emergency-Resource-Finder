<?php

namespace App\Services\Chatbot;

class EmergencyQuestionEngine
{
    /**
     * Question definitions per emergency intent with bilingual support and quick-reply pills
     */
    private const FLOWS = [
        'STROKE' => [
            'consciousness' => [
                'key' => 'consciousness',
                'en' => 'Is the person currently conscious and responsive?',
                'bn' => 'ব্যক্তি কি বর্তমানে সচেতন এবং সাড়া দিচ্ছেন?',
                'options' => [
                    ['value' => 'conscious', 'label_en' => 'Yes, Conscious', 'label_bn' => 'হ্যাঁ, সচেতন আছেন'],
                    ['value' => 'drowsy', 'label_en' => 'Drowsy / Confused', 'label_bn' => 'ঝিমুনি / বিভ্রান্ত'],
                    ['value' => 'unconscious', 'label_en' => 'Unconscious', 'label_bn' => 'অচেতন / বেহুঁশ'],
                ],
                'red_flag_values' => ['unconscious'],
            ],
            'onset_time' => [
                'key' => 'onset_time',
                'en' => 'When did the symptoms begin?',
                'bn' => 'লক্ষণগুলো কখন শুরু হয়েছে?',
                'options' => [
                    ['value' => 'under_30m', 'label_en' => 'Just now (< 30 mins)', 'label_bn' => 'এইমাত্র (< ৩০ মিনিট)'],
                    ['value' => '1_to_3h', 'label_en' => '1 – 3 hours ago', 'label_bn' => '১ – ৩ ঘণ্টা আগে'],
                    ['value' => 'over_3h', 'label_en' => 'More than 3 hours ago', 'label_bn' => '৩ ঘণ্টার বেশি আগে'],
                ],
                'red_flag_values' => ['under_30m', '1_to_3h'],
            ],
            'fast_symptoms' => [
                'key' => 'fast_symptoms',
                'en' => 'Do you notice facial drooping, one-sided arm weakness, or difficulty speaking?',
                'bn' => 'মুখ একপাশে বেঁকে যাওয়া, এক হাত অবশ হওয়া বা কথা জড়িয়ে যাওয়া লক্ষ্য করছেন?',
                'options' => [
                    ['value' => 'yes_multiple', 'label_en' => 'Yes, Multiple signs', 'label_bn' => 'হ্যাঁ, একাধিক লক্ষণ রয়েছে'],
                    ['value' => 'yes_one', 'label_en' => 'Yes, One sign present', 'label_bn' => 'হ্যাঁ, একটি লক্ষণ রয়েছে'],
                    ['value' => 'none', 'label_en' => 'None of these', 'label_bn' => 'এর কোনোটিই নয়'],
                ],
                'red_flag_values' => ['yes_multiple', 'yes_one'],
            ],
        ],

        'CARDIAC_EMERGENCY' => [
            'consciousness' => [
                'key' => 'consciousness',
                'en' => 'Is the patient conscious?',
                'bn' => 'রোগী কি সচেতন আছেন?',
                'options' => [
                    ['value' => 'conscious', 'label_en' => 'Yes, Conscious', 'label_bn' => 'হ্যাঁ, সচেতন আছেন'],
                    ['value' => 'unconscious', 'label_en' => 'Unconscious / Collapsed', 'label_bn' => 'অচেতন / ঢলে পড়েছেন'],
                ],
                'red_flag_values' => ['unconscious'],
            ],
            'breathing_status' => [
                'key' => 'breathing_status',
                'en' => 'Are they breathing normally, or gasping / struggling for air?',
                'bn' => 'শ্বাস-প্রশ্বাস কি স্বাভাবিক, নাকি শ্বাস নিতে প্রচণ্ড কষ্ট হচ্ছে?',
                'options' => [
                    ['value' => 'struggling', 'label_en' => 'Severe Shortness of Breath', 'label_bn' => 'তীব্র শ্বাসকষ্ট / দম বন্ধ'],
                    ['value' => 'normal', 'label_en' => 'Breathing normally', 'label_bn' => 'শ্বাস স্বাভাবিক'],
                    ['value' => 'not_breathing', 'label_en' => 'Not breathing / Gasping', 'label_bn' => 'শ্বাস নিচ্ছে না / গোঙানি'],
                ],
                'red_flag_values' => ['struggling', 'not_breathing'],
            ],
            'radiating_pain' => [
                'key' => 'radiating_pain',
                'en' => 'Is the chest pain crushing or spreading to the left arm, neck, back, or jaw?',
                'bn' => 'বুকে কি ভারী চাপ এবং ব্যথা কি বাঁ হাত, ঘাড় বা চোয়ালে ছড়াচ্ছে?',
                'options' => [
                    ['value' => 'spreading', 'label_en' => 'Yes, Spreading Pain', 'label_bn' => 'হ্যাঁ, ব্যথা ছড়িয়ে পড়ছে'],
                    ['value' => 'pressure_only', 'label_en' => 'Heavy Pressure Only', 'label_bn' => 'বুকে ভারী চাপ রয়েছে'],
                    ['value' => 'mild_sharp', 'label_en' => 'Mild / Sharp Pain', 'label_bn' => 'হালকা / সূঁচ ফোটানোর মতো'],
                ],
                'red_flag_values' => ['spreading', 'pressure_only'],
            ],
        ],

        'BREATHING_EMERGENCY' => [
            'consciousness' => [
                'key' => 'consciousness',
                'en' => 'Is the person conscious?',
                'bn' => 'ব্যক্তি কি সচেতন আছেন?',
                'options' => [
                    ['value' => 'conscious', 'label_en' => 'Yes', 'label_bn' => 'হ্যাঁ'],
                    ['value' => 'unresponsive', 'label_en' => 'Unresponsive / Collapsed', 'label_bn' => 'অচেতন / পড়ে গেছেন'],
                ],
                'red_flag_values' => ['unresponsive'],
            ],
            'cyanosis_or_choking' => [
                'key' => 'cyanosis_or_choking',
                'en' => 'Are lips or fingertips turning blue, or is the person choking on an object?',
                'bn' => 'ঠোঁট বা আঙুল কি নীল হয়ে আসছে, অথবা গলায় কিছু আটকে গেছে?',
                'options' => [
                    ['value' => 'blue_or_choking', 'label_en' => 'Yes, Turning Blue / Choking', 'label_bn' => 'হ্যাঁ, নীল হচ্ছে / গলায় আটকেছে'],
                    ['value' => 'cannot_speak', 'label_en' => 'Struggling to speak full words', 'label_bn' => 'কথা বলতে পারছে না'],
                    ['value' => 'wheezing', 'label_en' => 'Wheezing / Coughing only', 'label_bn' => 'শুধু সাঁই সাঁই শব্দ / কাশি'],
                ],
                'red_flag_values' => ['blue_or_choking', 'cannot_speak'],
            ],
        ],

        'SEVERE_BLEEDING' => [
            'bleeding_flow' => [
                'key' => 'bleeding_flow',
                'en' => 'Is the blood spurting, pumping, or soaking through cloths continuously?',
                'bn' => 'রক্ত কি ফিনকি দিয়ে বের হচ্ছে বা কাপড় ভিজে উপচে পড়ছে?',
                'options' => [
                    ['value' => 'spurting', 'label_en' => 'Spurting / Heavy Continuous Flow', 'label_bn' => 'হ্যাঁ, ফিনকি দিয়ে রক্ত ঝরছে'],
                    ['value' => 'steady', 'label_en' => 'Steady Slow Bleeding', 'label_bn' => 'ধীরে ধীরে রক্ত বের হচ্ছে'],
                    ['value' => 'controlled', 'label_en' => 'Bleeding Stopped with Pressure', 'label_bn' => 'চাপ দেওয়ার পর বন্ধ হয়েছে'],
                ],
                'red_flag_values' => ['spurting'],
            ],
            'direct_pressure' => [
                'key' => 'direct_pressure',
                'en' => 'Have you applied firm, continuous direct pressure with a clean cloth or bandage?',
                'bn' => 'পরিষ্কার কাপড় দিয়ে ক্ষতস্থানে কি শক্তভাবে একটানা চাপ দিয়ে রেখেছেন?',
                'options' => [
                    ['value' => 'yes_pressing', 'label_en' => 'Yes, Firm Pressure Applied', 'label_bn' => 'হ্যাঁ, শক্তভাবে চেপে রেখেছি'],
                    ['value' => 'no_not_yet', 'label_en' => 'No, Not Yet', 'label_bn' => 'না, এখনো দেওয়া হয়নি'],
                ],
                'red_flag_values' => ['no_not_yet'],
            ],
            'shock_signs' => [
                'key' => 'shock_signs',
                'en' => 'Is the person unusually pale, cold, dizzy, or losing consciousness?',
                'bn' => 'ব্যক্তি কি অতিরিক্ত ফ্যাকাশে, শরীর ঠান্ডা, মাথা ঘোরা বা জ্ঞান হারাচ্ছেন?',
                'options' => [
                    ['value' => 'shock_present', 'label_en' => 'Yes, Pale / Cold / Dizzy', 'label_bn' => 'হ্যাঁ, শরীর ঠান্ডা ও ফ্যাকাশে'],
                    ['value' => 'alert', 'label_en' => 'No, Alert and Talking', 'label_bn' => 'না, কথা বলতে পারছেন'],
                ],
                'red_flag_values' => ['shock_present'],
            ],
        ],

        'UNCONSCIOUSNESS' => [
            'breathing' => [
                'key' => 'breathing',
                'en' => 'Is the person breathing normally right now?',
                'bn' => 'ব্যক্তি কি এই মুহূর্তে স্বাভাবিকভাবে শ্বাস নিচ্ছেন?',
                'options' => [
                    ['value' => 'breathing_normal', 'label_en' => 'Yes, Breathing Normally', 'label_bn' => 'হ্যাঁ, স্বাভাবিক শ্বাস নিচ্ছে'],
                    ['value' => 'not_breathing', 'label_en' => 'Not Breathing / Gasping', 'label_bn' => 'শ্বাস নিচ্ছে না / গোঙাচ্ছে'],
                    ['value' => 'unsure', 'label_en' => 'Unsure / Cannot Tell', 'label_bn' => 'নিশ্চিত নই'],
                ],
                'red_flag_values' => ['not_breathing', 'unsure'],
            ],
            'trauma_history' => [
                'key' => 'trauma_history',
                'en' => 'Was there a physical fall, head blow, or vehicular collision?',
                'bn' => 'পড়ে গিয়ে মাথায় আঘাত বা কোনো সড়ক দুর্ঘটনা কি ঘটেছিল?',
                'options' => [
                    ['value' => 'head_injury', 'label_en' => 'Yes, Head / Neck Trauma', 'label_bn' => 'হ্যাঁ, মাথায় বা ঘাড়ে আঘাত লেগেছে'],
                    ['value' => 'sudden_collapse', 'label_en' => 'No injury, Sudden Collapse', 'label_bn' => 'আঘাত নেই, হঠাৎ ঢলে পড়েছে'],
                ],
                'red_flag_values' => ['head_injury', 'sudden_collapse'],
            ],
        ],

        'SEIZURE' => [
            'currently_seizing' => [
                'key' => 'currently_seizing',
                'en' => 'Is the seizure / violent shaking happening right now?',
                'bn' => 'খিঁচুনি বা শরীর ঝাঁকানো কি এই মুহূর্তে চলছে?',
                'options' => [
                    ['value' => 'active_now', 'label_en' => 'Yes, Shaking Right Now', 'label_bn' => 'হ্যাঁ, এখনই খিঁচুনি হচ্ছে'],
                    ['value' => 'stopped_now', 'label_en' => 'Seizure Just Stopped', 'label_bn' => 'মাত্র খিঁচুনি থেমেছে'],
                ],
                'red_flag_values' => ['active_now'],
            ],
            'duration' => [
                'key' => 'duration',
                'en' => 'Has the seizure lasted longer than 5 minutes, or have multiple seizures occurred without waking?',
                'bn' => 'খিঁচুনি কি ৫ মিনিটের বেশি সময় ধরে চলছে, বা বারবার হচ্ছে?',
                'options' => [
                    ['value' => 'over_5m', 'label_en' => 'Yes (> 5 minutes / Repeated)', 'label_bn' => 'হ্যাঁ (৫ মিনিটের বেশি / বারবার)'],
                    ['value' => 'under_5m', 'label_en' => 'No (< 5 minutes)', 'label_bn' => 'না (৫ মিনিটের কম)'],
                ],
                'red_flag_values' => ['over_5m'],
            ],
        ],

        'BURN' => [
            'airway_involvement' => [
                'key' => 'airway_involvement',
                'en' => 'Did the person inhale smoke, or is the face, mouth, or neck burned?',
                'bn' => 'ধোঁয়া কি শ্বাসে গেছে, অথবা মুখমণ্ডল, গলা বা বুকে কি আগুন লেগেছে?',
                'options' => [
                    ['value' => 'face_airway', 'label_en' => 'Yes, Face / Airway Affected', 'label_bn' => 'হ্যাঁ, মুখ বা শ্বাসতন্ত্র আক্রান্ত'],
                    ['value' => 'body_limbs', 'label_en' => 'Body / Limbs only', 'label_bn' => 'হাত-পা বা শরীরে পুড়েছে'],
                ],
                'red_flag_values' => ['face_airway'],
            ],
            'burn_cause' => [
                'key' => 'burn_cause',
                'en' => 'What was the source of the burn?',
                'bn' => 'পোড়ার উৎস কী ছিল?',
                'options' => [
                    ['value' => 'flame_explosion', 'label_en' => 'Open Flame / Gas Explosion', 'label_bn' => 'আগুন / গ্যাস বিস্ফোরণ'],
                    ['value' => 'hot_liquid', 'label_en' => 'Hot Liquid / Scald', 'label_bn' => 'গরম পানি বা তরল'],
                    ['value' => 'chemical_electric', 'label_en' => 'Chemical / Electrical Wire', 'label_bn' => 'কেমিক্যাল / বিদ্যুৎস্পৃষ্ট'],
                ],
                'red_flag_values' => ['flame_explosion', 'chemical_electric'],
            ],
        ],
    ];

    /**
     * Get next unanswered question for a given intent and existing answers
     */
    public function getNextQuestion(string $intent, array $answeredData = [], string $lang = 'en'): ?array
    {
        if (!isset(self::FLOWS[$intent])) {
            return null;
        }

        $flow = self::FLOWS[$intent];

        foreach ($flow as $key => $question) {
            if (!array_key_exists($key, $answeredData)) {
                return [
                    'key' => $key,
                    'text' => $lang === 'bn' ? $question['bn'] : $question['en'],
                    'options' => array_map(function ($opt) use ($lang) {
                        return [
                            'value' => $opt['value'],
                            'label' => $lang === 'bn' ? $opt['label_bn'] : $opt['label_en'],
                        ];
                    }, $question['options']),
                ];
            }
        }

        return null;
    }

    /**
     * Check if a specific answered value triggered a critical red-flag
     */
    public function isAnswerRedFlag(string $intent, string $key, string $value): bool
    {
        if (!isset(self::FLOWS[$intent][$key])) {
            return false;
        }

        $redFlags = self::FLOWS[$intent][$key]['red_flag_values'] ?? [];
        return in_array($value, $redFlags, true);
    }
}
