@extends('layouts.provider')

@section('title', 'My Emergency Resources – Provider Portal')
@section('page_title', 'My Registered Emergency Resources')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Facility Directory</h2>
            <p class="text-xs text-slate-500">Manage your emergency healthcare centers, ambulances, or pharmacies.</p>
        </div>
        <a href="{{ route('provider.resources.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-sm flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/></svg>
            <span>+ Add Emergency Resource</span>
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Resource Name</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Location</th>
                        <th class="py-3.5 px-4">Verification</th>
                        <th class="py-3.5 px-4">Availability</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($resources as $res)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-4 sm:px-6">
                                <a href="{{ route('resources.show', $res->slug) }}" target="_blank" class="font-bold text-slate-900 dark:text-white hover:text-red-600 block">
                                    {{ $res->name }}
                                </a>
                                <span class="text-xs text-slate-400">📞 {{ $res->phone }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $res->category?->getIconColorClasses() }} border">
                                    {{ $res->category?->name }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-600 dark:text-slate-400">
                                {{ $res->address }}, {{ $res->city }}
                            </td>
                            <td class="py-4 px-4">
                                @if ($res->isVerified())
                                    <x-status-badge type="verified" />
                                @elseif ($res->verification_status === 'rejected')
                                    <x-status-badge type="rejected" />
                                @else
                                    <x-status-badge type="pending" />
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <form action="{{ route('provider.resources.availability', $res->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ $res->availability_status === 'available' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800' }}">
                                        {{ $res->availability_status === 'available' ? '● Available' : '○ Busy/Closed' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('resources.show', $res->slug) }}" target="_blank" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white" title="View Public Page">
                                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                    </a>
                                    <a href="{{ route('provider.resources.edit', $res->id) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 dark:hover:text-white" title="Edit Resource">
                                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z"/><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z"/></svg>
                                    </a>
                                    <form action="{{ route('provider.resources.destroy', $res->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this resource?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700" title="Delete">
                                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No emergency facilities found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($resources->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $resources->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
