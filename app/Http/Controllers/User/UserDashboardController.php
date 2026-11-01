<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DonorContactRequest;
use App\Models\Report;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $favorites = $user->favorites()->with(['category', 'details'])->latest()->get();
        $reports = Report::with(['resource', 'bloodDonor'])->where('reporter_user_id', $user->id)->latest()->get();
        $donorRequests = DonorContactRequest::with(['donor'])->where('user_id', $user->id)->latest()->get();
        $bloodRequests = $donorRequests;
        $donorProfile = $user->bloodDonorProfile;

        return view('user.dashboard', compact('user', 'favorites', 'reports', 'donorRequests', 'bloodRequests', 'donorProfile'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return back()->with('success', 'Your profile details have been updated.');
    }
}
