<?php

namespace App\Http\Controllers;

use App\Http\Requests\BloodDonorRequest;
use App\Http\Requests\DonorContactRequestFormRequest;
use App\Models\BloodDonor;
use App\Services\BloodDonorService;
use App\Services\LocationSearchService;
use Illuminate\Http\Request;

class BloodDonorPublicController extends Controller
{
    public function __construct(
        protected LocationSearchService $searchService,
        protected BloodDonorService $donorService
    ) {}

    public function index(Request $request)
    {
        $selectedBloodGroup = $request->query('blood_group', 'all');

        $filters = [
            'blood_group' => $selectedBloodGroup,
            'lat' => $request->query('lat'),
            'lng' => $request->query('lng'),
            'radius' => $request->query('radius', 50),
            'city' => $request->query('city'),
            'area' => $request->query('area'),
            'available_only' => $request->boolean('available_only', false),
            'per_page' => 12,
        ];

        $donors = $this->searchService->searchBloodDonors($filters);

        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        $stats = [
            'total_registered' => BloodDonor::count(),
            'available_now' => BloodDonor::where('is_available', true)->count(),
            'verified_donors' => BloodDonor::where('verification_status', 'verified')->count(),
        ];

        return view('blood-donors.index', compact('donors', 'bloodGroups', 'filters', 'stats', 'selectedBloodGroup'));
    }

    public function create()
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        return view('blood-donors.create', compact('bloodGroups'));
    }

    public function store(BloodDonorRequest $request)
    {
        $donor = $this->donorService->saveDonorProfile($request->validated(), $request->user());

        return redirect()->route('blood-donors.index')
            ->with('success', 'Thank you! Your blood donor profile has been registered successfully and is ready to save lives.');
    }

    public function requestContact(DonorContactRequestFormRequest $request)
    {
        $donor = BloodDonor::findOrFail($request->validated('blood_donor_id'));

        $this->donorService->createContactRequest($donor, $request->validated(), $request->user());

        return back()->with('success', "Emergency blood request submitted for {$donor->full_name}. The donor has been notified with your urgent patient details.");
    }
}
