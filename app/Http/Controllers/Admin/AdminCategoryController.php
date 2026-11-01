<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = ResourceCategory::withCount('resources')
            ->orderBy('display_order', 'asc')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:resource_categories,name'],
            'icon' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:500'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['display_order'] = $validated['display_order'] ?? 0;
        $validated['is_active'] = true;

        ResourceCategory::create($validated);

        return back()->with('success', "Emergency category '{$validated['name']}' created successfully.");
    }

    public function update(ResourceCategory $category, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:resource_categories,name,' . $category->id],
            'icon' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:500'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $category->update($validated);

        return back()->with('success', "Category '{$category->name}' updated.");
    }
}
