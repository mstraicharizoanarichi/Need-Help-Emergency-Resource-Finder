<?php

namespace App\Http\Controllers\Emergency;

use App\Http\Controllers\Controller;
use App\Models\CfrAlert;
use App\Models\CommunityFirstResponder;
use App\Services\Emergency\CommunityResponderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityResponderController extends Controller
{
    public function __construct(
        private CommunityResponderService $responderService
    ) {}

    /**
     * Show CFR volunteer registration view
     */
    public function create(): View
    {
        return view('community.responder_register');
    }

    /**
     * Handle registration of volunteer first responder
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'skills' => ['required', 'array', 'min:1'],
            'skills.*' => ['string'],
            'certification_level' => ['required', 'string'],
            'certification_details' => ['nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $responder = $this->responderService->registerResponder($validated, $request->user());

        return redirect()->route('community.responder.registered', ['id' => $responder->id])
            ->with('success', 'Thank you for registering as a Community First Responder! Your readiness will save lives in your neighborhood.');
    }

    /**
     * Registration confirmation view
     */
    public function registered(int $id): View
    {
        $responder = CommunityFirstResponder::findOrFail($id);
        return view('community.responder_success', compact('responder'));
    }

    /**
     * Toggle availability status
     */
    public function toggleAvailability(Request $request, CommunityFirstResponder $responder): JsonResponse
    {
        $this->authorizeResponder($request, $responder);

        $newAvailable = !$responder->is_available;
        $responder->update([
            'is_available' => $newAvailable,
            'status' => $newAvailable ? 'AVAILABLE' : 'OFF_DUTY',
            'last_active_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'is_available' => $responder->is_available,
            'status' => $responder->status,
            'message' => $newAvailable ? 'You are now ON-DUTY and ready to receive nearby emergency alerts.' : 'Status set to OFF-DUTY.',
        ]);
    }

    /**
     * Respond to an alert (ACCEPT or DECLINE)
     */
    public function respondToAlert(Request $request, CfrAlert $alert): JsonResponse
    {
        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:ACCEPT,DECLINE'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['decision'] === 'ACCEPT') {
            $this->responderService->acceptAlert($alert, $validated['notes'] ?? null);
            return response()->json([
                'success' => true,
                'status' => 'ACCEPTED',
                'message' => 'Alert accepted. Please proceed immediately to provide life-saving assistance.',
            ]);
        }

        $alert->update([
            'status' => 'DECLINED',
            'responded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'status' => 'DECLINED',
            'message' => 'Alert declined.',
        ]);
    }

    /**
     * Mark CFR arrived on scene
     */
    public function markArrived(Request $request, CfrAlert $alert): JsonResponse
    {
        $this->responderService->markArrivedOnScene($alert);

        return response()->json([
            'success' => true,
            'status' => 'ON_SCENE',
            'message' => 'Status updated: You are recorded as arrived on scene.',
        ]);
    }

    private function authorizeResponder(Request $request, CommunityFirstResponder $responder): void
    {
        if ($request->user() && $responder->user_id && $responder->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized access to responder profile.');
        }
    }
}
