<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Analytics\EmergencyAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmergencyAnalyticsController extends Controller
{
    public function __construct(
        private EmergencyAnalyticsService $analyticsService
    ) {}

    /**
     * Display Emergency Operations Center (EOC) Analytics & Quality Audit Dashboard
     */
    public function index(Request $request): View
    {
        $days = (int) $request->input('days', 30);
        if (!in_array($days, [7, 14, 30, 90, 365], true)) {
            $days = 30;
        }

        $summary = $this->analyticsService->getAnalyticsSummary($days);
        $heatmapPoints = $this->analyticsService->getHeatmapPoints($days);

        return view('admin.emergency.analytics', compact('summary', 'heatmapPoints', 'days'));
    }

    /**
     * API endpoint returning GIS coordinates for live map layer
     */
    public function heatmapData(Request $request): JsonResponse
    {
        $days = (int) $request->input('days', 30);
        $points = $this->analyticsService->getHeatmapPoints($days);

        return response()->json([
            'status' => 'success',
            'points' => $points,
            'count' => count($points),
        ]);
    }
}
