<?php

namespace App\Http\Controllers;

use App\Models\EmergencyIncident;
use App\Models\EmergencyIncidentEvent;
use App\Services\Hospital\HospitalRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmergencyIncidentPublicController extends Controller
{
    public function __construct(
        private HospitalRecommendationService $hospitalService
    ) {}

    /**
     * Handle One-Tap SOS emergency trigger
     */
    public function storeSos(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'emergency_type' => ['nullable', 'string', 'max:50'],
            'reporter_name' => ['nullable', 'string', 'max:150'],
            'reporter_phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = Auth::user();
        $emergencyType = strtoupper($validated['emergency_type'] ?? 'GENERAL_SOS');

        $incident = EmergencyIncident::create([
            'reporter_user_id' => $user?->id,
            'reporter_name' => $user?->name ?? ($validated['reporter_name'] ?? 'Emergency Caller (SOS)'),
            'reporter_phone' => $user?->phone ?? ($validated['reporter_phone'] ?? null),
            'emergency_type' => $emergencyType,
            'description' => $validated['description'] ?? 'Instant One-Tap SOS Emergency Signal triggered by user.',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'address' => $validated['address'] ?? ($validated['latitude'] && $validated['longitude'] ? 'GPS: ' . number_format($validated['latitude'], 5) . ', ' . number_format($validated['longitude'], 5) : 'Location pending confirmation'),
            'severity_level' => 'P1',
            'priority_score' => 95,
            'severity_reasons' => ['Immediate One-Tap SOS Alert Triggered', 'Critical Life-Safety Default Protocol Active'],
            'required_service' => $emergencyType === 'FIRE' ? 'Fire Service' : ($emergencyType === 'POLICE' ? 'Police' : 'Ambulance & Hospital'),
            'status' => 'REPORTED',
            'is_sos' => true,
            'ai_confidence' => 1.0,
            'reported_at' => now(),
        ]);

        $incident->logEvent(
            eventType: 'CREATED',
            title: 'One-Tap Emergency SOS Triggered',
            description: 'Incident flagged as Critical (P1) Priority. Direct hotline 999 response and responder matching initiated.',
            metadata: [
                'is_sos' => true,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'coordinates' => [
                    'latitude' => $incident->latitude,
                    'longitude' => $incident->longitude,
                ],
            ],
            actor: $user,
            fromStatus: null,
            toStatus: 'REPORTED'
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Emergency SOS reported successfully. Stay calm, help is being organized.',
                'reference' => $incident->public_reference,
                'tracking_url' => route('emergency.show', $incident->public_reference),
                'incident' => [
                    'reference' => $incident->public_reference,
                    'severity' => $incident->severity_level,
                    'status' => $incident->status,
                ],
            ], 201);
        }

        return redirect()->route('emergency.show', $incident->public_reference)
            ->with('success', 'Emergency SOS reported. Your public reference is ' . $incident->public_reference);
    }

    /**
     * Show live public incident tracking timeline
     */
    public function show(string $reference): View
    {
        $incident = EmergencyIncident::with([
            'reporter',
            'assignedResource.details',
            'assignedHospital',
            'events.actor',
            'activeDispatch.resource.details',
            'activeCfrAlert.responder',
            'cfrAlerts.responder',
        ])
            ->where('public_reference', $reference)
            ->firstOrFail();

        $hospitals = $this->hospitalService->getRecommendedHospitals($incident);
        $advisory = $this->hospitalService->getPreArrivalAdvisory($incident->emergency_type);
        $activeCfr = $incident->activeCfrAlert;

        return view('emergency.show', compact('incident', 'hospitals', 'advisory', 'activeCfr'));
    }

    /**
     * Polling endpoint for live incident status updates
     */
    public function status(string $reference): JsonResponse
    {
        $incident = EmergencyIncident::with([
            'assignedResource.details',
            'assignedHospital',
            'activeDispatch.resource',
            'activeCfrAlert.responder',
            'events' => fn($q) => $q->latest('created_at')->limit(5),
        ])
            ->where('public_reference', $reference)
            ->firstOrFail();

        $activeDispatch = $incident->activeDispatch;
        $responder = $activeDispatch?->resource ?? $incident->assignedResource;
        $activeCfr = $incident->activeCfrAlert;

        return response()->json([
            'status' => $incident->status,
            'status_label' => str_replace('_', ' ', $incident->status),
            'severity_level' => $incident->severity_level,
            'priority_score' => $incident->priority_score,
            'is_sos' => $incident->is_sos,
            'reported_at' => $incident->reported_at?->diffForHumans(),
            'responder' => $responder ? [
                'name' => $responder->name,
                'phone' => $responder->emergency_phone ?: $responder->phone,
                'category' => $responder->category?->name ?? 'Emergency Service',
                'address' => $responder->address,
                'eta_minutes' => $activeDispatch?->estimated_eta_minutes,
                'status' => $activeDispatch?->status ?? 'DISPATCHED',
            ] : null,
            'active_cfr' => $activeCfr ? [
                'name' => $activeCfr->responder?->name,
                'status' => $activeCfr->status,
                'certification' => $activeCfr->responder?->certification_level,
                'skills' => $activeCfr->responder?->skills,
                'distance_km' => $activeCfr->distance_km,
            ] : null,
            'events' => $incident->events->map(fn($e) => [
                'title' => $e->title,
                'description' => $e->description,
                'type' => $e->event_type,
                'time' => $e->created_at?->diffForHumans(),
                'timestamp' => $e->created_at?->toIso8601String(),
            ]),
        ]);
    }

    /**
     * Citizen can cancel an active incident if false alarm
     */
    public function cancel(Request $request, string $reference): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $incident = EmergencyIncident::where('public_reference', $reference)->firstOrFail();

        if (in_array($incident->status, ['RESOLVED', 'CANCELLED'], true)) {
            return back()->with('info', 'Incident is already concluded.');
        }

        $oldStatus = $incident->status;
        $incident->status = 'CANCELLED';
        $incident->resolved_at = now();
        $incident->save();

        $incident->logEvent(
            eventType: 'CANCELLED',
            title: 'Incident Cancelled by Citizen',
            description: $validated['reason'] ?? 'Citizen marked the emergency as resolved or false alarm.',
            metadata: ['reason' => $validated['reason'] ?? null],
            actor: Auth::user(),
            fromStatus: $oldStatus,
            toStatus: 'CANCELLED'
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Emergency incident marked as cancelled.',
            ]);
        }

        return redirect()->route('emergency.show', $incident->public_reference)
            ->with('info', 'Emergency incident marked as cancelled.');
    }
}
