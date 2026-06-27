<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateAiSummaryJob;
use App\Models\AiSummary;
use App\Models\Client;
use App\Models\Project;
use App\Services\AiSummaryService;
use Illuminate\Http\Request;

class AiSummaryController extends Controller
{
    public function generateProject(Project $project, AiSummaryService $service)
    {
        if (!$service->isConfigured()) {
            return back()->with('error', 'AI key not configured. Add ANTHROPIC_API_KEY to your environment.');
        }

        GenerateAiSummaryJob::dispatch('project', $project->id);

        return back()->with('success', 'AI summary is being generated. Refresh in a moment.');
    }

    public function generateClient(Client $client, AiSummaryService $service)
    {
        if (!$service->isConfigured()) {
            return back()->with('error', 'AI key not configured. Add ANTHROPIC_API_KEY to your environment.');
        }

        GenerateAiSummaryJob::dispatch('client', $client->id);

        return back()->with('success', 'AI client health summary is being generated. Refresh in a moment.');
    }

    public function toggleClientVisible(AiSummary $summary)
    {
        $summary->update(['visible_to_client' => !$summary->visible_to_client]);

        $state = $summary->visible_to_client ? 'visible to client' : 'hidden from client';

        return back()->with('success', "Summary is now {$state}.");
    }

    public function destroy(AiSummary $summary)
    {
        $summary->delete();
        return back()->with('success', 'Summary removed.');
    }
}
