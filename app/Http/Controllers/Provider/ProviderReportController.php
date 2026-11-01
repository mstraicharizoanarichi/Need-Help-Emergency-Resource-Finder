<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Resource;
use Illuminate\Http\Request;

class ProviderReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $myResourceIds = Resource::where('user_id', $user->id)->pluck('id');

        $reports = Report::with(['resource'])
            ->whereIn('resource_id', $myResourceIds)
            ->latest()
            ->paginate(12);

        return view('provider.reports.index', compact('reports'));
    }
}
