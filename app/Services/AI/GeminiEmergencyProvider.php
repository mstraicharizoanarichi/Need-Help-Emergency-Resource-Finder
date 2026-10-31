<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiEmergencyProvider implements EmergencyAIProviderInterface
{
    private DeterministicEmergencyParser $fallbackParser;
    private ?string $apiKey;
    private string $model;

    public function __construct(?DeterministicEmergencyParser $fallbackParser = null)
    {
        $this->fallbackParser = $fallbackParser ?? new DeterministicEmergencyParser();
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
    }

    /**
     * Analyze emergency text description using Gemini API, with automatic fallback
     */
    public function analyzeIncident(string $text, ?string $language = null): array
    {
        if (empty($this->apiKey)) {
            return $this->fallbackParser->analyzeIncident($text, $language);
        }

        try {
            $prompt = <<<PROMPT
You are an expert Emergency Medical Services (EMS) and Disaster Triage AI for Bangladesh.
Analyze the following citizen emergency report and produce a strict JSON response.

Input Report: "{$text}"

Respond ONLY with valid JSON matching this schema:
{
  "emergency_type": "CARDIAC_STROKE|ROAD_ACCIDENT|FIRE|POLICE|OBSTETRICS|PEDIATRIC|TRAUMA|MEDICAL_AMBULANCE|GENERAL_SOS",
  "severity_level": "P1|P2|P3",
  "priority_score": 1-100 (integer: 90-100 for P1 critical, 60-89 for P2 urgent, 1-59 for P3 non-urgent),
  "severity_reasons": ["short rationale in bullet 1", "short rationale in bullet 2"],
  "required_service": "e.g. ALS Cardiac Ambulance & ICU Hospital",
  "required_equipment": ["ICU Bed", "Oxygen Support", "Cardiac Monitor"],
  "required_skills": ["Cardiologist", "Emergency Paramedic"],
  "ai_confidence": 0.0 to 1.0 (float),
  "summary": "1-2 sentence emergency brief for the dispatcher in both English and Bengali if appropriate",
  "language_detected": "bn or en"
}
PROMPT;

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(5)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                        'temperature' => 0.1,
                    ],
                ]);

            if ($response->successful()) {
                $body = $response->json();
                $candidateText = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($candidateText) {
                    $parsed = json_decode(trim($candidateText), true);
                    if (is_array($parsed) && isset($parsed['severity_level'], $parsed['priority_score'])) {
                        return [
                            'emergency_type' => strtoupper($parsed['emergency_type'] ?? 'GENERAL_SOS'),
                            'severity_level' => in_array($parsed['severity_level'], ['P1', 'P2', 'P3'], true) ? $parsed['severity_level'] : 'P1',
                            'priority_score' => (int) ($parsed['priority_score'] ?? 90),
                            'severity_reasons' => (array) ($parsed['severity_reasons'] ?? ['AI analyzed critical symptoms']),
                            'required_service' => (string) ($parsed['required_service'] ?? 'Emergency Response'),
                            'required_equipment' => (array) ($parsed['required_equipment'] ?? []),
                            'required_skills' => (array) ($parsed['required_skills'] ?? []),
                            'ai_confidence' => (float) ($parsed['ai_confidence'] ?? 0.95),
                            'summary' => (string) ($parsed['summary'] ?? 'AI-triaged emergency incident.'),
                            'language_detected' => (string) ($parsed['language_detected'] ?? 'en'),
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini API triage call encountered an issue, falling back to deterministic parser: ' . $e->getMessage());
        }

        // Fallback to deterministic regex parser
        return $this->fallbackParser->analyzeIncident($text, $language);
    }
}
