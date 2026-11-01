<?php

namespace App\Http\Controllers;

use App\Models\ResourceCategory;
use App\Services\LocationSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmergencySearchController extends Controller
{
    public function __construct(
        protected LocationSearchService $searchService
    ) {}

    public function index(Request $request)
    {
        $categories = ResourceCategory::active()->get();

        $filters = [
            'category' => $request->query('category', 'all'),
            'lat' => $request->query('lat'),
            'lng' => $request->query('lng'),
            'radius' => $request->query('radius', 25),
            'query' => $request->query('q'),
            'availability' => $request->query('availability', 'all'),
            'verified_only' => $request->boolean('verified_only', false),
            'district' => $request->query('district'),
            'city' => $request->query('city'),
            'per_page' => 12,
            'user_id' => $request->user()?->id,
            'ip' => $request->ip(),
        ];

        $resources = $this->searchService->searchResources($filters);

        $selectedCategory = $filters['category'] !== 'all' 
            ? $categories->firstWhere('slug', $filters['category']) ?? $categories->firstWhere('id', $filters['category'])
            : null;

        return view('search.index', compact('resources', 'categories', 'filters', 'selectedCategory'));
    }

    public function mapData(Request $request): JsonResponse
    {
        $filters = [
            'category' => $request->query('category', 'all'),
            'lat' => $request->query('lat'),
            'lng' => $request->query('lng'),
            'radius' => $request->query('radius', 50),
            'query' => $request->query('q'),
        ];

        $mapResources = $this->searchService->getMapResources($filters);

        return response()->json([
            'status' => 'success',
            'count' => $mapResources->count(),
            'data' => $mapResources,
        ]);
    }
}
