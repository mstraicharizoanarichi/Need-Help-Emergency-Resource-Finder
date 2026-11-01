<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResourceStoreRequest;
use App\Http\Requests\ResourceUpdateRequest;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\ResourceOperatingHour;
use App\Services\ResourceService;
use App\Services\VerificationService;
use Illuminate\Http\Request;

class AdminResourceController extends Controller
{
    public function __construct(
        protected ResourceService $resourceService,
        protected VerificationService $verificationService
    ) {}

    public function index(Request $request)
    {
        $query = Resource::with(['category', 'user']);

        if ($status = $request->query('status')) {
            $query->where('verification_status', $status);
        }

        if ($category = $request->query('category')) {
            $query->where('category_id', $category);
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $resources = $query->latest()->paginate(15)->withQueryString();
        $categories = ResourceCategory::active()->get();

        return view('admin.resources.index', compact('resources', 'categories'));
    }

    public function pending()
    {
        $resources = Resource::with(['category', 'user'])
            ->where('verification_status', 'pending')
            ->latest()
            ->paginate(15);

        return view('admin.resources.pending', compact('resources'));
    }

    public function create()
    {
        $categories = ResourceCategory::active()->get();
        $days = ResourceOperatingHour::$days;

        return view('admin.resources.create', compact('categories', 'days'));
    }

    public function store(ResourceStoreRequest $request)
    {
        $resource = $this->resourceService->createResource($request->validated(), $request->user());

        return redirect()->route('admin.resources.index')
            ->with('success', "Resource '{$resource->name}' created and verified.");
    }

    public function edit(Resource $resource)
    {
        $resource->load(['category', 'details', 'operatingHours']);
        $categories = ResourceCategory::active()->get();
        $days = ResourceOperatingHour::$days;

        return view('admin.resources.edit', compact('resource', 'categories', 'days'));
    }

    public function update(ResourceUpdateRequest $request, Resource $resource)
    {
        $this->resourceService->updateResource($resource, $request->validated(), $request->user());

        return redirect()->route('admin.resources.index')
            ->with('success', "Resource '{$resource->name}' updated successfully.");
    }

    public function verify(Resource $resource, Request $request)
    {
        $this->verificationService->verifyResource($resource, $request->user());

        return back()->with('success', "Resource '{$resource->name}' has been verified and marked as trustworthy.");
    }

    public function reject(Resource $resource, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->verificationService->rejectResource($resource, $validated['rejection_reason'], $request->user());

        return back()->with('success', "Resource '{$resource->name}' has been marked as rejected.");
    }

    public function destroy(Resource $resource)
    {
        $name = $resource->name;
        $resource->delete();

        return redirect()->route('admin.resources.index')
            ->with('success', "Resource '{$name}' deleted successfully.");
    }
}
