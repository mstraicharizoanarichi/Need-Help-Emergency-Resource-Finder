<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\EmergencyDispatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProviderDispatchController extends Controller
{
    /**
     * Show provider's incoming emergency dispatch list
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $providerResourceIds = $user->isAdmin()
            ? \App\Models\Resource::pluck('id')
            : $user->resources()->pluck('id');

        $query = EmergencyDispatch::with(['incident', 'resource.category'])
            ->whereIn('resource_id', $providerResourceIds);

        $filter = $request->query('status', 'active');
        if ($filter === 'active') {
            $query->whereIn('status', ['PENDING', 'ACCEPTED', 'EN_ROUTE', 'ON_SCENE', 'TRANSPORTING']);
        } elseif ($filter === 'completed') {
            $query->whereIn('status', ['COMPLETED', 'REJECTED', 'CANCELLED']);
        }

        $dispatches = $query->latest('dispatched_at')->paginate(15)->withQueryString();

        return view('provider.dispatches.index', compact('dispatches', 'filter'));
    }

    /**
     * Respond to a dispatch assignment (Accept / Reject)
     */
    public function respond(Request $request, EmergencyDispatch $dispatch): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:accept,reject'],
            'rejection_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $incident = $dispatch->incident;

        if ($validated['action'] === 'accept') {
            $dispatch->status = 'ACCEPTED';
            $dispatch->responded_at = now();
            $dispatch->save();

            $incident->status = 'RESPONDER_ACCEPTED';
            $incident->save();

            $incident->logEvent(
                eventType: 'RESPONDER_ACCEPTED',
                title: 'Responder Accepted Emergency Dispatch',
                description: sprintf('%s confirmed dispatch and is preparing immediate departure.', $dispatch->resource->name),
                metadata: ['dispatch_id' => $dispatch->id],
                actor: $user,
                fromStatus: 'DISPATCHED',
                toStatus: 'RESPONDER_ACCEPTED'
            );

            $msg = 'Dispatch assignment accepted successfully.';
        } else {
            $reason = $validated['rejection_reason'] ?? 'Vehicle temporarily engaged or out of service';
            $dispatch->status = 'REJECTED';
            $dispatch->rejection_reason = $reason;
            $dispatch->responded_at = now();
            $dispatch->save();

            // Unlink incident so it can be re-dispatched
            $incident->assigned_resource_id = null;
            $incident->status = 'AWAITING_DISPATCH';
            $incident->save();

            $incident->logEvent(
                eventType: 'STATUS_CHANGED',
                title: 'Dispatch Declined by Responder',
                description: sprintf('%s declined dispatch: "%s". Re-queuing incident for next closest candidate.', $dispatch->resource->name, $reason),
                metadata: ['dispatch_id' => $dispatch->id, 'reason' => $reason],
                actor: $user,
                fromStatus: 'DISPATCHED',
                toStatus: 'AWAITING_DISPATCH'
            );

            $msg = 'Dispatch assignment declined. Incident re-queued for operator.';
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Update dispatch milestone lifecycle status (EN_ROUTE -> ON_SCENE -> TRANSPORTING -> COMPLETED)
     */
    public function updateStatus(Request $request, EmergencyDispatch $dispatch): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:EN_ROUTE,ON_SCENE,TRANSPORTING,COMPLETED'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $validated['status'];
        $incident = $dispatch->incident;
        $user = Auth::user();

        $dispatch->status = $newStatus;
        if (!empty($validated['note'])) {
            $dispatch->notes = $validated['note'];
        }

        $now = now();
        $incidentStatus = match ($newStatus) {
            'EN_ROUTE' => 'RESPONDER_EN_ROUTE',
            'ON_SCENE' => 'RESPONDER_ARRIVED',
            'TRANSPORTING' => 'PATIENT_PICKED_UP',
            'COMPLETED' => 'RESOLVED',
        };

        if ($newStatus === 'EN_ROUTE') {
            $dispatch->en_route_at = $now;
            $title = 'Responder En Route to Scene';
            $desc = 'Emergency vehicle departed base and is navigating to the reported location.';
        } elseif ($newStatus === 'ON_SCENE') {
            $dispatch->arrived_at = $now;
            if ($dispatch->dispatched_at) {
                $dispatch->actual_arrival_minutes = round($dispatch->dispatched_at->diffInSeconds($now) / 60, 1);
            }
            $incident->arrived_at = $now;
            $title = 'Responder Arrived on Scene';
            $desc = sprintf('Emergency crew arrived on site (%s min from dispatch). Commencing triage.', $dispatch->actual_arrival_minutes ?? '~');
        } elseif ($newStatus === 'TRANSPORTING') {
            $title = 'Patient In Transit to Medical Center';
            $desc = 'Patient stabilized and currently being transported in ambulance to destination facility.';
        } else { // COMPLETED
            $dispatch->completed_at = $now;
            $incident->resolved_at = $now;
            $title = 'Emergency Response Mission Completed';
            $desc = 'Patient transferred to hospital care or situation resolved successfully.';
        }

        $dispatch->save();

        $oldIncidentStatus = $incident->status;
        $incident->status = $incidentStatus;
        $incident->save();

        $incident->logEvent(
            eventType: $newStatus === 'COMPLETED' ? 'RESOLVED' : 'STATUS_CHANGED',
            title: $title,
            description: $desc,
            metadata: [
                'dispatch_status' => $newStatus,
                'actual_arrival_minutes' => $dispatch->actual_arrival_minutes,
                'note' => $validated['note'] ?? null,
            ],
            actor: $user,
            fromStatus: $oldIncidentStatus,
            toStatus: $incidentStatus
        );

        $msg = "Dispatch status updated to {$newStatus}.";

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }
}
