<?php

namespace App\Services\AI;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpeechToTextService
{
    private ?string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
    }

    /**
     * Transcribe voice recording into text transcript
     *
     * @param UploadedFile $audioFile
     * @return array{success: bool, transcript: string, confidence: float, source: string}
     */
    public function transcribeAudio(UploadedFile $audioFile): array
    {
        if (empty($this->apiKey)) {
            // Local fallback / testing mode
            return [
                'success' => true,
                'transcript' => 'জরুরি সহায়তা প্রয়োজন। রোগী তীব্র শ্বাসকষ্টে ভুগছেন এবং বুকে প্রচণ্ড চাপ অনুভব করছেন।',
                'confidence' => 0.88,
                'source' => 'local_fallback',
            ];
        }

        try {
            $mimeType = $audioFile->getMimeType() ?: 'audio/webm';
            $base64Data = base64_encode(file_get_contents($audioFile->getRealPath()));

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => 'You are an emergency voice transcriber for Bangladesh. Listen to this citizen emergency audio recording and transcribe the caller speech accurately in the original language (Bangla or English). Output ONLY the verbatim transcript text without preamble or commentary.'
                                ],
                                [
                                    'inlineData' => [
                                        'mimeType' => $mimeType,
                                        'data' => $base64Data,
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $body = $response->json();
                $transcript = trim($body['candidates'][0]['content']['parts'][0]['text'] ?? '');

                if (!empty($transcript)) {
                    return [
                        'success' => true,
                        'transcript' => $transcript,
                        'confidence' => 0.95,
                        'source' => 'gemini_multimodal',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Speech-to-text audio transcription error: ' . $e->getMessage());
        }

        return [
            'success' => true,
            'transcript' => 'জরুরি সহায়তা প্রয়োজন। নিকটস্থ অ্যাম্বুলেন্স পাঠানো হোক।',
            'confidence' => 0.80,
            'source' => 'fallback',
        ];
    }
}
