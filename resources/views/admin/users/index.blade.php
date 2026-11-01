@extends('layouts.admin')

@section('title', 'Users & Providers – Admin Console')
@section('page_title', 'Users & Resource Providers Management')

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by user name, email, phone, organization..." class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
            </div>

            <div class="flex items-center gap-2">
                <select name="role" class="w-full h-10 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                    <option value="all">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admins Only</option>
                    <option value="provider" {{ request('role') === 'provider' ? 'selected' : '' }}>Providers Only</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Citizens Only</option>
                </select>

                <button type="submit" class="px-5 h-10 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold shrink-0">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Name & Contact</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Organization</th>
                        <th class="py-3.5 px-4">Registered Facilities</th>
                        <th class="py-3.5 px-4">Account Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                            <td class="py-4 px-4 sm:px-6">
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $user->name }}</span>
                                <span class="text-xs text-slate-400">{{ $user->email }} • {{ $user->phone ?: 'No phone' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase {{ $user->isAdmin() ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : ($user->isProvider() ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300') }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-slate-600 dark:text-slate-400">
                                {{ $user->organization_name ?: 'Individual User' }}
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-800 dark:text-slate-200">
                                {{ $user->resources_count }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $user->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                @if (Auth::id() !== $user->id)
                                    <form action="{{ route('admin.users.status', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1 rounded-lg text-xs font-bold {{ $user->status === 'active' ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                            {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
