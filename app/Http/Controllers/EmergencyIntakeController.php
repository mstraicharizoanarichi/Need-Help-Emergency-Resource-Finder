<?php

namespace App\Http\Controllers;

use App\Models\EmergencyIncident;
use App\Services\AI\EmergencyIntakeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmergencyIntakeController extends Controller
{
    public function __construct(
        private EmergencyIntakeService $intakeService
    ) {}

    /**
     * Show citizen emergency intake reporting view
     */
    public function create(): View
    {
        return view('emergency.report');
    }

    /**
     * Store new emergency incident and trigger AI triage
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:2000'],
            'voice_audio' => ['nullable', 'file', 'mimes:webm,wav,ogg,mp3,mp4,m4a', 'max:15360'], // max 15MB
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'number_of_victims' => ['nullable', 'integer', 'min:1', 'max:500'],
            'reporter_name' => ['nullable', 'string', 'max:150'],
            'reporter_phone' => ['nullable', 'string', 'max:50'],
            'emergency_type' => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $description = $validated['description'] ?? 'Emergency assistance requested via citizen intake.';

        $incident = EmergencyIncident::create([
            'reporter_user_id' => $user?->id,
            'reporter_name' => $user?->name ?? ($validated['reporter_name'] ?? 'Citizen Reporter'),
            'reporter_phone' => $user?->phone ?? ($validated['reporter_phone'] ?? null),
            'emergency_type' => strtoupper($validated['emergency_type'] ?? 'GENERAL_SOS'),
            'description' => $description,
            'number_of_victims' => $validated['number_of_victims'] ?? 1,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'address' => $validated['address'] ?? ($validated['latitude'] && $validated['longitude'] ? 'GPS: ' . number_format($validated['latitude'], 5) . ', ' . number_format($validated['longitude'], 5) : 'Location pending confirmation'),
            'severity_level' => 'P2',
            'priority_score' => 60,
            'status' => 'ANALYZING',
            'reported_at' => now(),
        ]);

        $incident->logEvent(
            eventType: 'CREATED',
            title: 'Emergency Incident Reported by Citizen',
            description: 'Intake details received. AI Triage engine initiating symptom analysis and resource recommendation.',
            metadata: [
                'has_voice' => $request->hasFile('voice_audio'),
                'victims' => $incident->number_of_victims,
            ],
            actor: $user,
            fromStatus: null,
            toStatus: 'ANALYZING'
        );

        // Run AI Triage
        $this->intakeService->processIncidentIntake(
            incident: $incident,
            rawText: $validated['description'] ?? null,
            audio: $request->file('voice_audio')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Emergency report submitted and prioritized.',
                'reference' => $incident->public_reference,
                'tracking_url' => route('emergency.show', $incident->public_reference),
                'incident' => [
                    'reference' => $incident->public_reference,
                    'severity' => $incident->severity_level,
                    'priority_score' => $incident->priority_score,
                    'required_service' => $incident->required_service,
                    'status' => $incident->status,
                ],
            ], 201);
        }

        return redirect()->route('emergency.show', $incident->public_reference)
            ->with('success', 'Emergency reported successfully. Public Reference: ' . $incident->public_reference);
    }

    /**
     * Real-time triage preview endpoint for interactive UI
     */
    public function previewTriage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['nullable', 'string', 'max:2000'],
            'voice_audio' => ['nullable', 'file', 'mimes:webm,wav,ogg,mp3,mp4,m4a', 'max:15360'],
        ]);

        if ($request->hasFile('voice_audio')) {
            $triage = $this->intakeService->triageAudio($request->file('voice_audio'));
        } elseif (!empty($validated['text'])) {
            $triage = $this->intakeService->triageText($validated['text']);
        } else {
            return response()->json(['error' => 'Please provide text or voice audio to analyze.'], 422);
        }

        return response()->json([
            'status' => 'success',
            'triage' => $triage,
        ]);
    }
}
