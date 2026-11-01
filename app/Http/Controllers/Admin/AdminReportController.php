<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $query = Report::with(['resource', 'bloodDonor', 'reporterUser', 'resolvedByUser']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->query('type')) {
            $query->where('report_type', $type);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reports.index', compact('reports'));
    }

    public function resolve(Report $report, Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewing,resolved,rejected'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->reportService->resolveReport($report, $validated['status'], $validated['admin_notes'] ?? null, $request->user());

        return back()->with('success', "Report #{$report->id} updated to {$validated['status']}.");
    }
}
