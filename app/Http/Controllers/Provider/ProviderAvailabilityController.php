<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Services\ResourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProviderAvailabilityController extends Controller
{
    public function __construct(
        protected ResourceService $resourceService
    ) {}

    public function toggle(Resource $resource, Request $request)
    {
        Gate::authorize('update', $resource);

        $status = $request->input('status');

        $this->resourceService->toggleAvailability($resource, $status);
        $resource->refresh();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'resource_id' => $resource->id,
                'availability_status' => $resource->availability_status,
                'message' => "Availability updated to {$resource->availability_status}.",
            ]);
        }

        return back()->with('success', "Availability for '{$resource->name}' updated to {$resource->availability_status}.");
    }
}
