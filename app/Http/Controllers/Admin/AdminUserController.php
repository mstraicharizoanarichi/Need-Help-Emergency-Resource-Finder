<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct(
        protected VerificationService $verificationService
    ) {}

    public function index(Request $request)
    {
        $query = User::query();

        if ($role = $request->query('role')) {
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        }

        if ($status = $request->query('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('organization_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount('resources')->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleStatus(User $user, Request $request)
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot modify the sole administrator account.');
        }

        $status = $request->input('status');
        $this->verificationService->updateUserStatus($user, $status);
        $user->refresh();

        return back()->with('success', "User '{$user->name}' status updated to {$user->status}.");
    }
}
