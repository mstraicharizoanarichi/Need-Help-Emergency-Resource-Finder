<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Resource;
use Illuminate\Http\Request;

class DoctorPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with(['resource.category'])
            ->active();

        // Search term
        if ($search = $request->query('q')) {
            $query->search($search);
        }

        // Specialization filter
        if ($specialization = $request->query('specialization')) {
            $query->specialization($specialization);
        }

        // Emergency available filter
        if ($request->boolean('emergency')) {
            $query->emergency();
        }

        // Hospital filter
        if ($hospitalId = $request->query('hospital_id')) {
            $query->where('resource_id', $hospitalId);
        }

        // City filter
        if ($city = $request->query('city')) {
            $query->whereHas('resource', function ($q) use ($city) {
                $q->where('city', $city);
            });
        }

        // Sorting
        $sort = $request->query('sort', 'popular');
        if ($sort === 'rating') {
            $query->orderBy('rating', 'desc');
        } elseif ($sort === 'emergency') {
            $query->orderBy('is_emergency_available', 'desc')->latest();
        } else {
            $query->orderBy('is_emergency_available', 'desc')->latest();
        }

        $doctors = $query->paginate(12)->withQueryString();

        $specializations = Doctor::active()
            ->select('specialization')
            ->distinct()
            ->pluck('specialization')
            ->filter()
            ->values();

        $hospitals = Resource::verified()
            ->has('doctors')
            ->select('id', 'name', 'city')
            ->orderBy('name')
            ->get();

        return view('doctors.index', compact('doctors', 'specializations', 'hospitals'));
    }

    public function show(string $slug, Request $request)
    {
        $doctor = Doctor::with(['resource.category', 'resource.details', 'resource.operatingHours'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Affiliated hospital
        $hospital = $doctor->resource;

        // Other doctors in same hospital or specialty
        $otherDoctors = Doctor::with(['resource'])
            ->active()
            ->where('id', '!=', $doctor->id)
            ->where(function ($q) use ($doctor) {
                $q->where('resource_id', $doctor->resource_id)
                  ->orWhere('specialization', $doctor->specialization);
            })
            ->limit(4)
            ->get();

        return view('doctors.show', compact('doctor', 'hospital', 'otherDoctors'));
    }
}
