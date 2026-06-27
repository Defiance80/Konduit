<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AiSummary;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $client = auth()->user()->client;

        // Client-visible AI summaries tied to their projects or client record
        $summaries = AiSummary::where(function ($q) use ($client) {
                $q->where(function ($q2) use ($client) {
                    $q2->where('summarizable_type', 'App\\Models\\Client')
                       ->where('summarizable_id', $client->id);
                })->orWhere(function ($q2) use ($client) {
                    $q2->where('summarizable_type', 'App\\Models\\Project')
                       ->whereIn('summarizable_id', $client->projects()->pluck('id'));
                });
            })
            ->where('visible_to_client', true)
            ->latest()
            ->get();

        return view('client.reports.index', compact('summaries'));
    }
}
