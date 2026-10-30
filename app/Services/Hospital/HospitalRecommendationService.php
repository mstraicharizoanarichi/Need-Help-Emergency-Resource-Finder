<?php

namespace App\Services\Hospital;

use App\Models\EmergencyIncident;
use App\Models\Resource;
use App\Services\Dispatch\TrafficEstimationService;
use App\Services\LocationSearchService;
use Illuminate\Support\Collection;

class HospitalRecommendationService
{
    public function __construct(
        private LocationSearchService $locationSearchService,
        private TrafficEstimationService $trafficService
    ) {}

    /**
     * Get top recommended destination hospitals with capabilities and ETA
     *
     * @param EmergencyIncident $incident
     * @param float $radiusKm
     * @param int $limit
     * @return Collection<int, array>
     */
    public function getRecommendedHospitals(EmergencyIncident $incident, float $radiusKm = 40.0, int $limit = 3): Collection
    {
        $lat = $incident->latitude ?? 23.7808;
        $lng = $incident->longitude ?? 90.4152;

        $filters = [
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radiusKm,
            'category' => 'hospital',
            'verified_only' => false,
            'per_page' => 30,
        ];

        $paginated = $this->locationSearchService->searchResources($filters);
        $candidates = collect($paginated->items());

        // If no hospitals with category 'hospital' found, fallback to search without category filter
        if ($candidates->isEmpty()) {
            unset($filters['category']);
            $paginated = $this->locationSearchService->searchResources($filters);
            $candidates = collect($paginated->items());
        }

        $emergencyType = $incident->emergency_type;
        $reqEquipment = (array) ($incident->required_equipment ?? []);

        $scored = $candidates->map(function (Resource $hospital) use ($incident, $emergencyType, $reqEquipment, $radiusKm) {
            $distanceKm = isset($hospital->distance) ? (float) $hospital->distance : 6.0;
            $trafficResult = $this->trafficService->calculateETA(
                distanceKm: $distanceKm,
                city: $incident->city ?: $hospital->city
            );

            $score = 0.0;
            $reasons = [];

            // 1. Proximity score (0 - 40 pts)
            $proxScore = max(0, 40 * (1 - ($distanceKm / max(1.0, $radiusKm))));
            $score += $proxScore;
            $reasons[] = sprintf('%.1f km away (~%.0f mins with %s traffic)', $distanceKm, $trafficResult['eta_minutes'], $trafficResult['traffic_condition']);

            // 2. ICU & Critical Equipment (0 - 30 pts)
            $details = $hospital->details;
            if ($details && $details->icu_available) {
                $score += 20.0;
                $reasons[] = 'Verified ICU Trauma Unit Available';
            }
            if ($details && $details->oxygen_available) {
                $score += 10.0;
                $reasons[] = 'Central Medical Oxygen Infrastructure';
            }

            // 3. Specialty Alignment (0 - 20 pts)
            if ($this->hasSpecialtyAlignment($emergencyType, $hospital)) {
                $score += 20.0;
                $reasons[] = 'Specialized in ' . str_replace('_', ' ', $emergencyType);
            }

            // 4. 24/7 Service (0 - 10 pts)
            if ($hospital->is_24_7 || $hospital->isOpenNow()) {
                $score += 10.0;
                $reasons[] = '24/7 Emergency Operation';
            }

            return [
                'hospital' => $hospital,
                'id' => $hospital->id,
                'name' => $hospital->name,
                'address' => $hospital->address,
                'area' => $hospital->area,
                'city' => $hospital->city,
                'phone' => $hospital->emergency_phone ?: $hospital->phone,
                'latitude' => $hospital->latitude,
                'longitude' => $hospital->longitude,
                'distance_km' => round($distanceKm, 1),
                'estimated_eta_minutes' => $trafficResult['eta_minutes'],
                'traffic_condition' => $trafficResult['traffic_condition'],
                'has_icu' => (bool) ($details?->icu_available),
                'has_oxygen' => (bool) ($details?->oxygen_available),
                'is_24_7' => (bool) $hospital->is_24_7,
                'recommendation_score' => round(min(100.0, $score), 1),
                'recommendation_reasons' => $reasons,
                'directions_url' => $hospital->getDirectionsUrl(),
            ];
        });

        return $scored->sortByDesc('recommendation_score')->take($limit)->values();
    }

    /**
     * Generate bilingual pre-arrival first aid and stabilization advisory
     *
     * @param string $emergencyType
     * @return array{en: array<string>, bn: array<string>, critical_warning: string}
     */
    public function getPreArrivalAdvisory(string $emergencyType): array
    {
        return match ($emergencyType) {
            'CARDIAC_STROKE' => [
                'critical_warning' => 'Do NOT exert the patient or allow them to walk. Keep calm.',
                'en' => [
                    'Position the patient comfortably, seated upright to ease breathing.',
                    'Loosen all tight clothing, collars, and belts immediately.',
                    'If conscious, ask about prescribed nitroglycerin or aspirin (only if previously recommended by doctor).',
                    'Do not administer water or solid food if the patient is drowsy or confused.',
                    'Keep phone line clear; receiving hospital Cath Lab and CCU team have been alerted.',
                ],
                'bn' => [
                    'রোগীকে শোয়াবেন না; আরামদায়কভাবে পিঠে ভর দিয়ে বসিয়ে রাখুন যাতে শ্বাস নিতে সুবিধা হয়।',
                    'গলার বোতাম, টাই, বেল্ট এবং আঁটসাঁট পোশাক দ্রুত আলগা করে দিন।',
                    'রোগী জ্ঞান হারালে বা ঝিমুনি আসলে মুখে কোনো পানি বা খাবার দেবেন না।',
                    'জরুরি অ্যাম্বুলেন্স না আসা পর্যন্ত রোগীকে শান্ত রাখুন এবং ফ্যান বা বাতাস দিন।',
                    'হাসপাতালের সিসিইউ ও কার্ডিওলজিস্টদের পূর্ব-সতর্কবার্তা পাঠানো হয়েছে।',
                ],
            ],

            'ROAD_ACCIDENT', 'TRAUMA' => [
                'critical_warning' => 'Do NOT move the patient if neck or spinal injury is suspected, unless in immediate fire/traffic danger.',
                'en' => [
                    'Control bleeding: Apply firm, continuous direct pressure with a clean cloth or bandage.',
                    'Do not attempt to push protruding bones back in or remove deeply embedded objects.',
                    'Stabilize the neck and head in a neutral position if vehicular collision occurred.',
                    'Cover the patient with a blanket or jacket to prevent shock and hypothermia.',
                    'Trauma team and blood bank on standby at receiving center.',
                ],
                'bn' => [
                    'রক্তক্ষরণ বন্ধ করতে পরিষ্কার কাপড় দিয়ে ক্ষতস্থানে শক্তভাবে চাপ দিয়ে ধরে রাখুন।',
                    'ঘাড় বা মেরুদণ্ডে আঘাতের সম্ভাবনা থাকলে রোগীকে টানাটানি বা স্থানান্তর করবেন না।',
                    'ভেঙে যাওয়া হাড় বা কোনো বস্তু শরীরে বিঁধে থাকলে তা বের করার চেষ্টা করবেন না।',
                    'রোগীকে চাদর বা কাপড় দিয়ে জড়িয়ে উষ্ণ রাখুন যাতে শকে না চলে যায়।',
                    'নিকটস্থ ট্রমা সেন্টারে ইমার্জেন্সি অপারেশন থিয়েটার প্রস্তুত রাখা হচ্ছে।',
                ],
            ],

            'FIRE' => [
                'critical_warning' => 'Do NOT apply ice, oil, butter, or toothpaste to burn wounds.',
                'en' => [
                    'Immediately cool burn areas under clean running cool tap water for 10-15 minutes.',
                    'Carefully remove rings, watches, and loose belts before swelling begins.',
                    'Cover burns loosely with a clean, dry, non-adhesive sterile cloth.',
                    'Move to open air if smoke was inhaled; stay low under smoke if escaping.',
                    'Burn Unit trauma doctors notified for immediate fluid resuscitation.',
                ],
                'bn' => [
                    'পোড়া স্থানে ১০-১৫ মিনিট পরিষ্কার ঠাণ্ডা পানির ধারা ঢালুন (কখনই বরফ বা তেল ব্যবহার করবেন না)।',
                    'আক্রান্ত স্থান ফুলে যাওয়ার আগেই আংটি, ঘড়ি বা আঁটসাঁট পোশাক সাবধানে খুলে ফেলুন।',
                    'পোড়া ক্ষত পরিষ্কার সুতি কাপড় দিয়ে আলতো করে ঢেকে রাখুন; ফোসকা গলাবেন না।',
                    'ধোঁয়া শ্বাসে গেলে রোগীকে দ্রুত খোলা বাতাসে নিয়ে আসুন।',
                    'নিকটস্থ বার্ন অ্যান্ড প্লাস্টিক সার্জারি ইউনিটকে সতর্ক করা হয়েছে।',
                ],
            ],

            'OBSTETRICS' => [
                'critical_warning' => 'Do NOT force the patient to walk or hold back natural delivery reflexes.',
                'en' => [
                    'Help the expectant mother lie comfortably on her left side to maximize fetal oxygen supply.',
                    'Prepare clean warm towels, blankets, and sanitary pads.',
                    'Encourage slow, rhythmic deep breathing during contraction peaks.',
                    'If crowning or imminent birth, support the baby gently; do not pull the umbilical cord.',
                    'Maternity labor ward and neonatal incubator pre-arranged.',
                ],
                'bn' => [
                    'প্রসূতি মাকে বাঁ কাত হয়ে শুতে সাহায্য করুন যাতে গর্ভস্থ শিশুর রক্ত সঞ্চালন ভালো থাকে।',
                    'পরিষ্কার উষ্ণ তোয়ালে, চাদর ও প্যাড হাতের কাছে প্রস্তুত রাখুন।',
                    'ব্যথা বাড়লে মাকে লম্বা শ্বাস নিতে ও শান্ত থাকতে উৎসাহিত করুন।',
                    'বাচ্চা প্রসব হয়ে গেলে উষ্ণ কাপড়ে জড়িয়ে রাখুন এবং নাড়ি টানবেন না।',
                    'প্রসূতি বিভাগ ও নবজাতক এনআইসিইউ প্রস্তুত রাখা হয়েছে।',
                ],
            ],

            'PEDIATRIC' => [
                'critical_warning' => 'Do NOT put spoons, fingers, or objects into a seizing child’s mouth.',
                'en' => [
                    'If experiencing a febrile convulsion, gently turn the child onto their side to prevent choking.',
                    'Loosen clothing and sponge forehead/neck with lukewarm (not cold) water if high fever.',
                    'Do not shake or forcefully hold the child down during convulsions.',
                    'Keep airway clear and note how many minutes the convulsion lasts.',
                    'Pediatric ICU and child specialist notified.',
                ],
                'bn' => [
                    'শিশুর খিঁচুনি হলে মুখে আঙুল, চামচ বা কোনো কিছু ঢোকাবেন না।',
                    'শিশুকে আলতো করে একপাশে কাত করে দিন যাতে লালা বা বমিতে শ্বাস বন্ধ না হয়।',
                    'তীব্র জ্বর থাকলে কুসুম গরম পানিতে কাপড় ভিজিয়ে শরীর মুছে দিন।',
                    'খিঁচুনি কতক্ষণ স্থায়ী হচ্ছে তা ঘড়ি দেখে লক্ষ্য রাখুন।',
                    'শিশু আইসিইউ (PICU) ও শিশু বিশেষজ্ঞ ডাক্তারকে প্রস্তুত থাকতে বলা হয়েছে।',
                ],
            ],

            default => [
                'critical_warning' => 'Keep phone line open for responder or operator verification call.',
                'en' => [
                    'Remain in a safe, visible location and observe the patient continuously.',
                    'If patient becomes unresponsive, check breathing and pulse immediately.',
                    'Have patient National ID / health history ready for arriving responders.',
                    'Direct a bystander to the main road to guide the emergency vehicle when sirens sound.',
                ],
                'bn' => [
                    'রোগীকে নিরাপদ স্থানে রাখুন এবং সার্বক্ষণিক পাশে থাকুন।',
                    'রোগী জ্ঞান হারালে শ্বাস-প্রশ্বাস চলছে কি না খেয়াল করুন।',
                    'রোগীর পূর্বের প্রেসক্রিপশন বা প্রয়োজনীয় কাগজপত্র হাতের কাছে রাখুন।',
                    'অ্যাম্বুলেন্সের সাইরেন শুনলে রাস্তায় কাউকে পাঠিয়ে পথ দেখিয়ে আনুন।',
                ],
            ],
        };
    }

    private function hasSpecialtyAlignment(string $emergencyType, Resource $hospital): bool
    {
        $nameLower = strtolower($hospital->name . ' ' . $hospital->description);

        return match ($emergencyType) {
            'CARDIAC_STROKE' => str_contains($nameLower, 'heart') || str_contains($nameLower, 'cardiac') || str_contains($nameLower, 'national institute of cardiovascular') || str_contains($nameLower, 'neuro'),
            'ROAD_ACCIDENT', 'TRAUMA' => str_contains($nameLower, 'trauma') || str_contains($nameLower, 'orthopedic') || str_contains($nameLower, 'nitor') || str_contains($nameLower, 'medical college'),
            'FIRE' => str_contains($nameLower, 'burn') || str_contains($nameLower, 'plastic surgery'),
            'OBSTETRICS' => str_contains($nameLower, 'maternity') || str_contains($nameLower, 'shishu') || str_contains($nameLower, 'mother'),
            'PEDIATRIC' => str_contains($nameLower, 'child') || str_contains($nameLower, 'shishu') || str_contains($nameLower, 'pediatric'),
            default => false,
        };
    }
}
