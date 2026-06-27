<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AiSummary;
use App\Models\Client;

class ReportController extends Controller
{
    public function index()
    {
        $summaries = AiSummary::with('summarizable')
            ->latest()
            ->paginate(20);

        $clients = Client::orderBy('name')->get();

        $stats = [
            'total'          => AiSummary::count(),
            'visible_client' => AiSummary::where('visible_to_client', true)->count(),
        ];

        return view('agency.reports.index', compact('summaries', 'clients', 'stats'));
    }
}
