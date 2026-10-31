<?php

namespace App\Services\Chatbot;

class EmergencyKnowledgeBase
{
    /**
     * Curated, concise, non-diagnostic guidance for emergency inquiries
     */
    private const TOPICS = [
        'STROKE' => [
            'en' => [
                'summary' => 'Stroke warning signs require immediate emergency action (FAST):',
                'points' => [
                    'Face: One side of the face droops or feels numb when smiling.',
                    'Arms: Weakness or numbness in one arm; cannot raise both arms evenly.',
                    'Speech: Slurred speech, inability to speak, or difficulty repeating simple sentences.',
                    'Time: Emergency brain-saving clot-busting treatments must be started within 3–4.5 hours of first symptom.',
                ],
                'safety_note' => 'These symptoms can be warning signs of an acute stroke and may require immediate emergency medical care. Do not give food, water, or aspirin.',
                'question_prompt' => 'Are these symptoms happening to someone right now?',
            ],
            'bn' => [
                'summary' => 'স্ট্রোকের সতর্কতামূলক প্রধান লক্ষণসমূহ (FAST পদ্ধতি):',
                'points' => [
                    'মুখ (Face): হাসতে গেলে মুখের একপাশ বেঁকে যাওয়া বা অসাড় লাগা।',
                    'হাত (Arm): এক হাত দুর্বল হয়ে যাওয়া বা দুই হাত সমানভাবে তুলতে না পারা।',
                    'কথা (Speech): কথা জড়িয়ে যাওয়া, স্পষ্ট উচ্চারণ করতে না পারা বা কথা হারিয়ে ফেলা।',
                    'সময় (Time): স্ট্রোকের প্রথম ৩ থেকে ৪.৫ ঘণ্টার মধ্যে জরুরি হাসপাতালে নেওয়া অত্যন্ত জরুরি।',
                ],
                'safety_note' => 'এই লক্ষণগুলো তীব্র স্ট্রোকের পূর্বাভাস হতে পারে এবং অবিলম্বে জরুরি চিকিৎসার প্রয়োজন হতে পারে। রোগীকে কোনো খাবার বা পানি খাওয়াবেন না।',
                'question_prompt' => 'এই লক্ষণগুলো কি এই মুহূর্তে কারো ক্ষেত্রে ঘটছে?',
            ],
        ],

        'CARDIAC_EMERGENCY' => [
            'en' => [
                'summary' => 'Warning signs of a possible Heart Attack / Acute Cardiac Emergency:',
                'points' => [
                    'Heavy crushing pressure, fullness, squeezing, or pain in the center of the chest.',
                    'Pain or discomfort radiating to the left shoulder, arm, neck, back, or jaw.',
                    'Shortness of breath with or without chest discomfort.',
                    'Cold sweats, unexplained nausea, dizziness, or lightheadedness.',
                ],
                'safety_note' => 'These symptoms can indicate acute cardiac ischemia and require immediate emergency care. Keep the patient seated upright and call for an ambulance.',
                'question_prompt' => 'Is the person experiencing this chest pain right now?',
            ],
            'bn' => [
                'summary' => 'হার্ট অ্যাটাকের সম্ভাব্য প্রধান লক্ষণসমূহ:',
                'points' => [
                    'বুকের মাঝখানে প্রচণ্ড ভারী চাপ, সংকোচন বা ব্যথার অনুভূতি।',
                    'ব্যথা বাঁ কাঁধ, বাঁ হাত, ঘাড়, পিঠ বা চোয়ালে ছড়িয়ে পড়া।',
                    'তীব্র শ্বাসকষ্ট, দম আটকে আসা এবং অতিরিক্ত ঠান্ডা ঘাম হওয়া।',
                    'মাথা ঘোরা, বমি বমি ভাব বা হঠাৎ দুর্বল হয়ে পড়া।',
                ],
                'safety_note' => 'এই লক্ষণগুলো মারাত্মক হৃদরোগের ইঙ্গিত হতে পারে এবং অবিলম্বে জরুরি চিকিৎসার দাবি রাখে। রোগীকে বসিয়ে রাখুন এবং দ্রুত অ্যাম্বুলেন্স ডাকুন।',
                'question_prompt' => 'রোগী কি এই মুহূর্তে বুকে এই ব্যথা অনুভব করছেন?',
            ],
        ],

        'BREATHING_EMERGENCY' => [
            'en' => [
                'summary' => 'Warning signs of an acute Respiratory Emergency:',
                'points' => [
                    'Gasping for air or unable to complete a full sentence in one breath.',
                    'Fingertips, tongue, or lips turning pale, bluish, or grey (cyanosis).',
                    'Stridor, high-pitched wheezing, or choking sensation in the throat.',
                    'Chest and ribs visibly pulling inward with every breath (retractions).',
                ],
                'safety_note' => 'Acute breathing difficulty is a life-safety emergency. Assist the person into an upright seated position and loosen tight collars.',
                'question_prompt' => 'Is the person struggling to breathe right now?',
            ],
            'bn' => [
                'summary' => 'তীব্র শ্বাসকষ্টজনিত জরুরি অবস্থার লক্ষণসমূহ:',
                'points' => [
                    'শ্বাস নিতে প্রচণ্ড কষ্ট হওয়া বা এক শ্বাসে একটি পুরো বাক্য বলতে না পারা।',
                    'ঠোঁট, জিহ্বা বা আঙুলের ডগা নীল বা ছাইবর্ণ ধারণ করা (অক্সিজেনের ঘাটতি)।',
                    'গলা বা বুক থেকে তীব্র সাঁই সাঁই বা ঘড়ঘড় শব্দ হওয়া।',
                    'প্রতি শ্বাসের সাথে বুকের খাঁচা ভেতরে দেবে যাওয়া।',
                ],
                'safety_note' => 'তীব্র শ্বাসকষ্ট একটি জীবনঘাতী জরুরি অবস্থা। রোগীকে সোজা করে বসিয়ে রাখুন এবং গলার বোতাম ও পোশাক আলগা করে দিন।',
                'question_prompt' => 'ব্যক্তি কি এই মুহূর্তে শ্বাস নিতে কষ্ট পাচ্ছেন?',
            ],
        ],

        'SEVERE_BLEEDING' => [
            'en' => [
                'summary' => 'Emergency steps for Severe or Continuous Bleeding:',
                'points' => [
                    'Apply firm, continuous direct pressure with a clean cloth or sterile dressing.',
                    'Do not remove blood-soaked cloths; add new cloths on top and keep pressing firmly.',
                    'Keep the injured area still and elevated above heart level if no fracture is suspected.',
                    'Do NOT remove deeply embedded objects (knives, glass) — pack around them.',
                ],
                'safety_note' => 'Uncontrolled heavy bleeding can cause rapid hypovolemic shock. Apply continuous pressure while summoning emergency transport.',
                'question_prompt' => 'Is the bleeding actively happening right now?',
            ],
            'bn' => [
                'summary' => 'অতিরিক্ত রক্তক্ষরণে তাৎক্ষণিক জরুরি করণীয়:',
                'points' => [
                    'ক্ষতস্থানের ওপর পরিষ্কার কাপড় বা গজ দিয়ে শক্তভাবে একটানা চাপ দিয়ে ধরে রাখুন।',
                    'রক্তে কাপড় ভিজে গেলে তা সরাবেন না; তার ওপরেই আরও কাপড় দিয়ে চাপ অব্যাহত রাখুন।',
                    'ভাঙা না থাকলে রক্তপাতের অঙ্গটি সম্ভব হলে হৃৎপিণ্ডের উচ্চতার ওপরে রাখুন।',
                    'কোনো বস্তু শরীরে বিঁধে থাকলে তা টেনে বের করবেন না; চারপাশ দিয়ে কাপড় দিয়ে আটকে রাখুন।',
                ],
                'safety_note' => 'অতিরিক্ত রক্তক্ষরণ দ্রুত রোগীকে শকে নিয়ে যেতে পারে। শক্তভাবে চেপে ধরে রাখুন এবং দ্রুত হাসপাতালে নেওয়ার ব্যবস্থা করুন।',
                'question_prompt' => 'রক্তপাত কি এই মুহূর্তে চলছে?',
            ],
        ],

        'UNCONSCIOUSNESS' => [
            'en' => [
                'summary' => 'Emergency steps for an Unresponsive or Collapsed Person:',
                'points' => [
                    'Check for normal breathing immediately (look at chest rise, listen for breath).',
                    'If NOT breathing normally: Call 999 immediately and begin chest compressions (CPR).',
                    'If breathing normally: Place the person gently onto their side in the Recovery Position to keep airway clear.',
                    'Do not shake the person, do not throw water on their face, and do not give anything by mouth.',
                ],
                'safety_note' => 'Loss of consciousness requires immediate clinical assessment. If breathing stops, CPR must be initiated without delay.',
                'question_prompt' => 'Is the person currently unresponsive?',
            ],
            'bn' => [
                'summary' => 'অচেতন বা বেহুঁশ ব্যক্তির ক্ষেত্রে জরুরি পদক্ষেপ:',
                'points' => [
                    'রোগী স্বাভাবিকভাবে শ্বাস নিচ্ছে কি না তা দ্রুত খেয়াল করুন (বুকের ওঠানামা দেখুন)।',
                    'শ্বাস না থাকলে: অবিলম্বে ৯৯৯ এ কল করুন এবং বুকে দুই হাত দিয়ে একটানা চাপ (CPR) শুরু করুন।',
                    'শ্বাস থাকলে: রোগীকে একপাশে কাত করে (Recovery Position) রাখুন যাতে জিহ্বা বা লালা শ্বাসনালী বন্ধ না করে।',
                    'রোগীর মুখে পানি ছিঁটাবেন না, ঝাঁকাবেন না এবং মুখে কোনো খাবার বা ওষুধ দেবেন না।',
                ],
                'safety_note' => 'অচেতন হওয়া একটি বিপজ্জনক অবস্থা। শ্বাস বন্ধ থাকলে কালক্ষেপণ না করে সিপিআর শুরু করা উচিত।',
                'question_prompt' => 'ব্যক্তি কি এই মুহূর্তে অচেতন অবস্থায় আছেন?',
            ],
        ],

        'SEIZURE' => [
            'en' => [
                'summary' => 'First-Aid steps during an active Seizure / Convulsion:',
                'points' => [
                    'Cushion the head with a folded jacket or soft pillow to prevent injury.',
                    'Loosen tight clothing around the neck and remove glasses.',
                    'Do NOT hold the person down or try to stop their movements.',
                    'Do NOT place any objects, spoons, or fingers into the person\'s mouth.',
                    'Once shaking stops, gently turn the person onto their side.',
                ],
                'safety_note' => 'Call emergency services immediately if the seizure lasts over 5 minutes, if breathing remains difficult, or if another seizure starts.',
                'question_prompt' => 'Is the seizure actively happening right now?',
            ],
            'bn' => [
                'summary' => 'খিঁচুনির সময় জরুরি সতর্কতা ও করণীয়:',
                'points' => [
                    'মাথায় যাতে আঘাত না লাগে সেজন্য নরম কাপড় বা বালিশ দিয়ে মাথা আগলে রাখুন।',
                    'গলার বোতাম ও আঁটসাঁট পোশাক আলগা করে দিন। চশমা থাকলে সাবধানে খুলে ফেলুন।',
                    'রোগীকে জোর করে চেপে ধরে ঝাঁকুনি থামানোর চেষ্টা করবেন না।',
                    'মুখের ভেতর কখনই চামচ, আঙুল বা কোনো বস্তু ঢোকাবেন না।',
                    'খিঁচুনি থামার পর রোগীকে আলতো করে একপাশে কাত করে দিন।',
                ],
                'safety_note' => 'খিঁচুনি ৫ মিনিটের বেশি স্থায়ী হলে, শ্বাসকষ্ট থাকলে বা বারবার খিঁচুনি হলে কালবিলম্ব না করে জরুরি হাসপাতালে নিন।',
                'question_prompt' => 'খিঁচুনি কি এই মুহূর্তে চলছে?',
            ],
        ],

        'BURN' => [
            'en' => [
                'summary' => 'Immediate First-Aid for Thermal and Fire Burns:',
                'points' => [
                    'Immediately cool the burn under clean, cool running tap water for 10 to 15 minutes.',
                    'Do NOT use ice, freezing water, oil, butter, or toothpaste on burn wounds.',
                    'Gently remove tight rings, watches, or clothing before the area begins to swell.',
                    'Cover the burned area loosely with a clean, dry, non-stick sterile dressing or plastic wrap.',
                ],
                'safety_note' => 'Burns to the face, airway, hands, or extensive skin surfaces require urgent burn center care and IV fluid resuscitation.',
                'question_prompt' => 'Did the burn incident happen recently?',
            ],
            'bn' => [
                'summary' => 'আগুনে পোড়া বা গরম তরলে দগ্ধ হলে তাৎক্ষণিক করণীয়:',
                'points' => [
                    'পোড়া জায়গায় অবিলম্বে পরিষ্কার ঠান্ডা পানির ধারা ১০ থেকে ১৫ মিনিট ঢালুন।',
                    'পোড়া ক্ষতস্থানে কখনও বরফ, তেল, মাখন বা টুথপেস্ট লাগাবেন না।',
                    'ক্ষত ফুলে ওঠার আগেই আংটি, ঘড়ি বা আঁটসাঁট পোশাক সাবধানে খুলে ফেলুন।',
                    'পোড়া জায়গা পরিষ্কার শুকনো পাতলা কাপড় দিয়ে আলতো করে ঢেকে রাখুন; ফোসকা গলাবেন না।',
                ],
                'safety_note' => 'মুখমণ্ডল, শ্বাসনালী বা শরীরের বড় অংশ পুড়ে গেলে রোগীকে অবিলম্বে বিশেষায়িত বার্ন অ্যান্ড প্লাস্টিক সার্জারি ইউনিটে নিতে হবে।',
                'question_prompt' => 'পোড়া দুর্ঘটনাটি কি এইমাত্র বা সম্প্রতি ঘটেছে?',
            ],
        ],
    ];

    /**
     * Get concise knowledge response for an emergency intent
     */
    public function getTopic(string $intent, string $lang = 'en'): ?array
    {
        if (!isset(self::TOPICS[$intent])) {
            return null;
        }

        $data = self::TOPICS[$intent];
        return $data[$lang] ?? $data['en'];
    }
}
