<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmergencyDispatch;
use App\Models\EmergencyIncident;
use App\Models\Resource;
use App\Services\Dispatch\DispatchExecutionService;
use App\Services\Dispatch\ResourceMatchingService;
use App\Services\Hospital\HospitalRecommendationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmergencyCommandCenterController extends Controller
{
    public function __construct(
        private ResourceMatchingService $matchingService,
        private HospitalRecommendationService $hospitalService,
        private DispatchExecutionService $dispatchService
    ) {}

    /**
     * Show real-time Emergency Operations Center (EOC) Command Dashboard
     */
    public function index(Request $request): View
    {
        $kpis = $this->getKpiStats();

        // Active incidents in prioritized queue
        $incidents = EmergencyIncident::with([
            'reporter',
            'assignedResource.category',
            'activeDispatch.resource',
        ])
            ->active()
            ->prioritizedQueue()
            ->paginate(20);

        return view('admin.emergency.index', compact('kpis', 'incidents'));
    }

    /**
     * Real-time JSON feed for live map pins and KPI updates
     */
    public function feed(): JsonResponse
    {
        $kpis = $this->getKpiStats();

        $incidents = EmergencyIncident::with([
            'assignedResource',
            'activeDispatch.resource',
        ])
            ->active()
            ->prioritizedQueue()
            ->limit(40)
            ->get()
            ->map(function ($inc) {
                $dispatch = $inc->activeDispatch;
                return [
                    'id' => $inc->id,
                    'reference' => $inc->public_reference,
                    'type' => $inc->emergency_type,
                    'severity' => $inc->severity_level,
                    'priority' => $inc->priority_score,
                    'status' => $inc->status,
                    'status_label' => str_replace('_', ' ', $inc->status),
                    'is_sos' => (bool) $inc->is_sos,
                    'latitude' => (float) $inc->latitude,
                    'longitude' => (float) $inc->longitude,
                    'address' => $inc->address,
                    'reporter_name' => $inc->reporter_name,
                    'reporter_phone' => $inc->reporter_phone,
                    'reported_time' => $inc->reported_at?->diffForHumans(),
                    'responder' => $dispatch?->resource ? [
                        'name' => $dispatch->resource->name,
                        'phone' => $dispatch->resource->emergency_phone ?: $dispatch->resource->phone,
                        'eta_minutes' => $dispatch->estimated_eta_minutes,
                        'status' => $dispatch->status,
                        'latitude' => (float) $dispatch->resource->latitude,
                        'longitude' => (float) $dispatch->resource->longitude,
                    ] : null,
                ];
            });

        return response()->json([
            'kpis' => $kpis,
            'incidents' => $incidents,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Fetch AI smart dispatch recommendations for a specific incident
     */
    public function recommendations(EmergencyIncident $incident): JsonResponse
    {
        $ambulances = $this->matchingService->findMatches($incident, [
            'category' => 'ambulance',
            'limit' => 3,
        ]);

        $hospitals = $this->hospitalService->getRecommendedHospitals($incident, radiusKm: 40.0, limit: 3);

        return response()->json([
            'status' => 'success',
            'incident' => [
                'reference' => $incident->public_reference,
                'emergency_type' => $incident->emergency_type,
                'severity_level' => $incident->severity_level,
                'priority_score' => $incident->priority_score,
                'required_service' => $incident->required_service,
                'required_equipment' => $incident->required_equipment,
                'summary' => $incident->ai_summary,
            ],
            'recommended_ambulances' => $ambulances->values(),
            'recommended_hospitals' => $hospitals->values(),
        ]);
    }

    /**
     * Operator approves & executes dispatch of a resource to an incident
     */
    public function dispatch(Request $request, EmergencyIncident $incident): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'resource_id' => ['required', 'exists:resources,id'],
            'distance_km' => ['nullable', 'numeric'],
            'estimated_eta_minutes' => ['nullable', 'numeric'],
            'match_score' => ['nullable', 'numeric'],
        ]);

        $resource = Resource::findOrFail($validated['resource_id']);
        $operator = Auth::user();

        $dispatch = $this->dispatchService->executeDispatch(
            incident: $incident,
            resource: $resource,
            operator: $operator,
            matchData: [
                'distance_km' => $validated['distance_km'] ?? 5.0,
                'estimated_eta_minutes' => $validated['estimated_eta_minutes'] ?? 15.0,
                'total_score' => $validated['match_score'] ?? 88.0,
            ]
        );

        $msg = "Emergency responder {$resource->name} successfully dispatched to {$incident->public_reference}.";

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'dispatch_id' => $dispatch->id,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Operator manually updates or escalates incident status
     */
    public function updateStatus(Request $request, EmergencyIncident $incident): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $incident->status;
        $incident->status = $validated['status'];
        if ($validated['status'] === 'RESOLVED') {
            $incident->resolved_at = now();
        }
        $incident->save();

        $incident->logEvent(
            eventType: 'STATUS_CHANGED',
            title: 'Incident Status Manually Updated by Command Operator',
            description: $validated['note'] ?? "Status transitioned from {$oldStatus} to {$incident->status}",
            metadata: ['note' => $validated['note'] ?? null],
            actor: Auth::user(),
            fromStatus: $oldStatus,
            toStatus: $incident->status
        );

        $msg = "Incident {$incident->public_reference} status updated to {$incident->status}.";

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Operator declares Mass Casualty Incident (MCI) and triggers surge bed allocations
     */
    public function declareMci(Request $request, EmergencyIncident $incident): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'number_of_victims' => ['required', 'integer', 'min:3', 'max:500'],
            'mci_level' => ['nullable', 'string', 'in:LEVEL_1,LEVEL_2,LEVEL_3,LEVEL_4'],
            'triage_counts' => ['nullable', 'array'],
            'triage_counts.red' => ['nullable', 'integer', 'min:0'],
            'triage_counts.yellow' => ['nullable', 'integer', 'min:0'],
            'triage_counts.green' => ['nullable', 'integer', 'min:0'],
            'triage_counts.black' => ['nullable', 'integer', 'min:0'],
        ]);

        /** @var \App\Services\Emergency\MassCasualtyProtocolService $mciService */
        $mciService = app(\App\Services\Emergency\MassCasualtyProtocolService::class);
        $incident = $mciService->declareMci($incident, $validated, Auth::user());

        $msg = "Mass Casualty Incident ({$incident->mci_level}) declared for {$incident->public_reference}. Regional hospital surge protocol activated.";

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $msg,
                'incident' => $incident->fresh(),
                'hospital_allocations' => $incident->mci_hospital_allocations,
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Helper to compute real-time EOC statistics
     */
    private function getKpiStats(): array
    {
        $activeIncidents = EmergencyIncident::active();

        return [
            'critical_count' => (clone $activeIncidents)->where('severity_level', 'P1')->count(),
            'awaiting_dispatch_count' => (clone $activeIncidents)->whereIn('status', ['REPORTED', 'ANALYZING', 'PRIORITIZED', 'AWAITING_DISPATCH'])->count(),
            'active_en_route_count' => EmergencyDispatch::whereIn('status', ['ACCEPTED', 'EN_ROUTE', 'ON_SCENE', 'TRANSPORTING'])->count(),
            'resolved_today_count' => EmergencyIncident::whereDate('resolved_at', Carbon::today())->count(),
            'available_ambulances' => Resource::whereHas('category', fn($q) => $q->where('slug', 'ambulance'))->where('availability_status', 'available')->count(),
            'available_icu_hospitals' => Resource::whereHas('category', fn($q) => $q->where('slug', 'hospital'))->whereHas('details', fn($q) => $q->where('icu_available', true))->count(),
        ];
    }
}
