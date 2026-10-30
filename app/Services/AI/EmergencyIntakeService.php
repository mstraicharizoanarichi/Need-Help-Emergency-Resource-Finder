<?php

namespace App\Services\AI;

use App\Models\EmergencyIncident;
use Illuminate\Http\UploadedFile;

class EmergencyIntakeService
{
    private EmergencyAIProviderInterface $aiProvider;
    private SpeechToTextService $speechToTextService;

    public function __construct(
        ?EmergencyAIProviderInterface $aiProvider = null,
        ?SpeechToTextService $speechToTextService = null
    ) {
        $this->aiProvider = $aiProvider ?? new GeminiEmergencyProvider();
        $this->speechToTextService = $speechToTextService ?? new SpeechToTextService();
    }

    /**
     * Triage plain text description
     */
    public function triageText(string $text, ?string $language = null): array
    {
        return $this->aiProvider->analyzeIncident($text, $language);
    }

    /**
     * Transcribe audio and triage the resulting transcript
     */
    public function triageAudio(UploadedFile $audio): array
    {
        $sttResult = $this->speechToTextService->transcribeAudio($audio);
        $transcript = $sttResult['transcript'];

        $triage = $this->triageText($transcript);
        $triage['voice_transcript'] = $transcript;
        $triage['voice_confidence'] = $sttResult['confidence'];

        return $triage;
    }

    /**
     * Process and enrich an emergency incident with AI Triage metrics and audit timeline event
     */
    public function processIncidentIntake(
        EmergencyIncident $incident,
        ?string $rawText = null,
        ?UploadedFile $audio = null
    ): EmergencyIncident {
        $textToAnalyze = $rawText ?? ($incident->description ?? '');
        $voiceTranscript = null;

        if ($audio) {
            $sttResult = $this->speechToTextService->transcribeAudio($audio);
            $voiceTranscript = $sttResult['transcript'];
            $incident->voice_transcript = $voiceTranscript;
            $textToAnalyze = trim($textToAnalyze . ' ' . $voiceTranscript);
        }

        if (!empty($textToAnalyze)) {
            $triage = $this->triageText($textToAnalyze);

            $oldStatus = $incident->status;
            $incident->emergency_type = $triage['emergency_type'];
            $incident->severity_level = $triage['severity_level'];
            $incident->priority_score = $triage['priority_score'];
            $incident->severity_reasons = $triage['severity_reasons'];
            $incident->required_service = $triage['required_service'];
            $incident->required_equipment = $triage['required_equipment'];
            $incident->required_skills = $triage['required_skills'];
            $incident->ai_confidence = $triage['ai_confidence'];
            $incident->ai_summary = $triage['summary'];
            $incident->status = 'PRIORITIZED';
            $incident->save();

            // Log AI Triage event to the timeline
            $incident->logEvent(
                eventType: 'AI_TRIAGED',
                title: 'AI Structured Emergency Triage Complete',
                description: sprintf(
                    'Classified as %s (Priority %d/100). Required: %s. %s',
                    $incident->severity_level,
                    $incident->priority_score,
                    $incident->required_service,
                    $incident->ai_summary
                ),
                metadata: [
                    'triage' => $triage,
                    'has_voice' => !is_null($voiceTranscript),
                ],
                actor: null,
                fromStatus: $oldStatus,
                toStatus: 'PRIORITIZED'
            );
        }

        return $incident;
    }
}
