@extends('layouts.admin')

@section('title', 'Emergency Categories – Admin Console')
@section('page_title', 'Emergency Resource Categories')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Categories Table (Left 2 cols) --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Active Categories</h2>
            <p class="text-xs text-slate-500">Emergency service classifications displayed across the public portal.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Category</th>
                        <th class="py-3.5 px-4">Slug</th>
                        <th class="py-3.5 px-4">Total Resources</th>
                        <th class="py-3.5 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($categories as $cat)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center {{ $cat->getIconColorClasses() }}">
                                        <x-category-icon :icon="$cat->icon" class="w-4 h-4" />
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-white block">{{ $cat->name }}</span>
                                        <span class="text-xs text-slate-400">{{ $cat->description }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono text-xs text-slate-500">
                                {{ $cat->slug }}
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $cat->resources_count }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create New Category Form (Right col) --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        <h3 class="font-bold text-base text-slate-900 dark:text-white">Add Emergency Category</h3>
        
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-semibold mb-1">Category Name *</label>
                <input type="text" name="name" required placeholder="e.g. Oxygen Cylinder Supplier" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            </div>

            <div>
                <label class="block font-semibold mb-1">Icon Key *</label>
                <select name="icon" required class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <option value="hospital">Hospital (Red Cross)</option>
                    <option value="ambulance">Ambulance</option>
                    <option value="pharmacy">Pharmacy (Pill)</option>
                    <option value="blood-donor">Blood Donor (Droplet)</option>
                    <option value="police-station">Police (Shield)</option>
                    <option value="fire-service">Fire Service (Flame)</option>
                    <option value="diagnostic-center">Diagnostic (Microscope)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Brief description of this service category..." class="w-full p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700"></textarea>
            </div>

            <div>
                <label class="block font-semibold mb-1">Display Order</label>
                <input type="number" name="display_order" value="10" min="0" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            </div>

            <button type="submit" class="w-full py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-sm">
                Save Category
            </button>
        </form>
    </div>

</div>
@endsection
