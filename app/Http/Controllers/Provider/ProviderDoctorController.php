<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProviderDoctorController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $myResources = Resource::where('user_id', $user->id)->get();
        $myResourceIds = $myResources->pluck('id');

        $query = Doctor::with('resource')
            ->whereIn('resource_id', $myResourceIds);

        if ($resourceId = $request->query('resource_id')) {
            $query->where('resource_id', $resourceId);
        }

        if ($search = $request->query('q')) {
            $query->search($search);
        }

        $doctors = $query->latest()->paginate(12)->withQueryString();

        return view('provider.doctors.index', compact('doctors', 'myResources'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $myResources = Resource::where('user_id', $user->id)->get();

        if ($myResources->isEmpty()) {
            return redirect()->route('provider.resources.create')
                ->with('error', 'Please create an emergency facility/hospital first before adding doctors.');
        }

        $selectedResourceId = $request->query('resource_id', $myResources->first()->id);
        $specializations = Doctor::$popularSpecializations;
        $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        return view('provider.doctors.create', compact('myResources', 'selectedResourceId', 'specializations', 'daysOfWeek'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $myResourceIds = Resource::where('user_id', $user->id)->pluck('id')->toArray();

        $validated = $request->validate([
            'resource_id' => ['required', 'integer', 'in:' . implode(',', $myResourceIds)],
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'qualifications' => ['required', 'string', 'max:500'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:70'],
            'visiting_days' => ['nullable', 'array'],
            'visiting_hours' => ['nullable', 'string', 'max:255'],
            'appointment_phone' => ['nullable', 'string', 'max:50'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'is_emergency_available' => ['nullable', 'boolean'],
            'is_available_today' => ['nullable', 'boolean'],
            'room_number' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['is_emergency_available'] = $request->boolean('is_emergency_available');
        $validated['is_available_today'] = $request->boolean('is_available_today', true);
        $validated['visiting_days'] = $request->input('visiting_days', []);

        $doctor = Doctor::create($validated);

        return redirect()->route('provider.doctors.index')
            ->with('success', "Doctor '{$doctor->name}' has been added successfully to the hospital directory.");
    }

    public function edit(Doctor $doctor, Request $request)
    {
        $user = $request->user();
        $myResources = Resource::where('user_id', $user->id)->get();
        $myResourceIds = $myResources->pluck('id')->toArray();

        if (!in_array($doctor->resource_id, $myResourceIds) && $user->role !== 'admin') {
            abort(403, 'Unauthorized access to this doctor record.');
        }

        $specializations = Doctor::$popularSpecializations;
        $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        return view('provider.doctors.edit', compact('doctor', 'myResources', 'specializations', 'daysOfWeek'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $user = $request->user();
        $myResourceIds = Resource::where('user_id', $user->id)->pluck('id')->toArray();

        if (!in_array($doctor->resource_id, $myResourceIds) && $user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'resource_id' => ['required', 'integer', 'in:' . implode(',', $myResourceIds)],
            'name' => ['required', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'qualifications' => ['required', 'string', 'max:500'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:70'],
            'visiting_days' => ['nullable', 'array'],
            'visiting_hours' => ['nullable', 'string', 'max:255'],
            'appointment_phone' => ['nullable', 'string', 'max:50'],
            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'is_emergency_available' => ['nullable', 'boolean'],
            'is_available_today' => ['nullable', 'boolean'],
            'room_number' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['is_emergency_available'] = $request->boolean('is_emergency_available');
        $validated['is_available_today'] = $request->boolean('is_available_today');
        $validated['visiting_days'] = $request->input('visiting_days', []);

        $doctor->update($validated);

        return redirect()->route('provider.doctors.index')
            ->with('success', "Doctor '{$doctor->name}' updated successfully.");
    }

    public function destroy(Doctor $doctor, Request $request)
    {
        $user = $request->user();
        $myResourceIds = Resource::where('user_id', $user->id)->pluck('id')->toArray();

        if (!in_array($doctor->resource_id, $myResourceIds) && $user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $name = $doctor->name;
        $doctor->delete();

        return redirect()->route('provider.doctors.index')
            ->with('success', "Doctor '{$name}' has been removed from directory.");
    }

    public function toggleEmergency(Doctor $doctor, Request $request)
    {
        $user = $request->user();
        $myResourceIds = Resource::where('user_id', $user->id)->pluck('id')->toArray();

        if (!in_array($doctor->resource_id, $myResourceIds) && $user->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $doctor->is_emergency_available = !$doctor->is_emergency_available;
        $doctor->save();

        $status = $doctor->is_emergency_available ? 'marked ON-DUTY for Emergency' : 'set to Standard Schedule';

        return back()->with('success', "Dr. {$doctor->name} is now {$status}.");
    }
}
