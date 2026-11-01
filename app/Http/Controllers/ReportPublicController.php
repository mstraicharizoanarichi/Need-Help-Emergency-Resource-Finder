<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportStoreRequest;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportPublicController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function store(ReportStoreRequest $request)
    {
        $this->reportService->createReport($request->validated(), $request->user());

        return back()->with('success', 'Thank you for reporting this issue. Our administration team will review and update the information promptly.');
    }
}
