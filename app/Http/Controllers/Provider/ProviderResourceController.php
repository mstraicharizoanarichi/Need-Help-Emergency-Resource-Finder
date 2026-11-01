<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceStoreRequest;
use App\Http\Requests\ResourceUpdateRequest;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\ResourceOperatingHour;
use App\Services\ResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProviderResourceController extends Controller
{
    public function __construct(
        protected ResourceService $resourceService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $query = Resource::with(['category', 'details'])
            ->where('user_id', $user->id);

        if ($status = $request->query('status')) {
            $query->where('verification_status', $status);
        }

        if ($category = $request->query('category')) {
            $query->where('category_id', $category);
        }

        $resources = $query->latest()->paginate(12)->withQueryString();
        $categories = ResourceCategory::active()->get();

        return view('provider.resources.index', compact('resources', 'categories'));
    }

    public function create()
    {
        $categories = ResourceCategory::active()->get();
        $days = ResourceOperatingHour::$days;

        return view('provider.resources.create', compact('categories', 'days'));
    }

    public function store(ResourceStoreRequest $request)
    {
        $resource = $this->resourceService->createResource($request->validated(), $request->user());

        return redirect()->route('provider.resources.index')
            ->with('success', "Emergency resource '{$resource->name}' created successfully. It will be reviewed by administration for verification.");
    }

    public function edit(Resource $resource, Request $request)
    {
        Gate::authorize('update', $resource);

        $resource->load(['category', 'details', 'operatingHours']);
        $categories = ResourceCategory::active()->get();
        $days = ResourceOperatingHour::$days;

        return view('provider.resources.edit', compact('resource', 'categories', 'days'));
    }

    public function update(ResourceUpdateRequest $request, Resource $resource)
    {
        Gate::authorize('update', $resource);

        $this->resourceService->updateResource($resource, $request->validated(), $request->user());

        return redirect()->route('provider.resources.index')
            ->with('success', "Resource '{$resource->name}' updated successfully.");
    }

    public function destroy(Resource $resource, Request $request)
    {
        Gate::authorize('delete', $resource);

        $name = $resource->name;
        $resource->delete();

        return redirect()->route('provider.resources.index')
            ->with('success', "Resource '{$name}' has been deleted.");
    }
}
