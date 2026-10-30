<?php

namespace App\Services\AI;

interface EmergencyAIProviderInterface
{
    /**
     * Analyze emergency text description and extract structured triage metrics.
     *
     * @param string $text
     * @param string|null $language
     * @return array{
     *     emergency_type: string,
     *     severity_level: string,
     *     priority_score: int,
     *     severity_reasons: array<string>,
     *     required_service: string,
     *     required_equipment: array<string>,
     *     required_skills: array<string>,
     *     ai_confidence: float,
     *     summary: string,
     *     language_detected: string
     * }
     */
    public function analyzeIncident(string $text, ?string $language = null): array;
}
