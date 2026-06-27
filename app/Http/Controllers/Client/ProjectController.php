<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $client = auth()->user()->client;

        $projects = Project::where('client_id', $client->id)
            ->with(['deliverables', 'tasks'])
            ->orderByRaw("FIELD(status, 'active', 'on_hold', 'draft', 'completed', 'cancelled')")
            ->get();

        $stats = [
            'active'    => $projects->where('status', 'active')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'on_hold'   => $projects->where('status', 'on_hold')->count(),
        ];

        return view('client.projects.index', compact('projects', 'stats'));
    }

    public function show(Project $project): View
    {
        abort_unless($project->client_id === auth()->user()->client_id, 403);

        $project->load(['deliverables', 'tasks', 'retainer', 'owner']);

        // Timeline confidence: based on % complete vs days elapsed
        $timelineConfidence = $this->calculateTimelineConfidence($project);

        return view('client.projects.show', compact('project', 'timelineConfidence'));
    }

    private function calculateTimelineConfidence(Project $project): array
    {
        if (!$project->start_date || !$project->due_date) {
            return ['score' => null, 'label' => 'No timeline set', 'color' => 'gray'];
        }

        $totalDays   = $project->start_date->diffInDays($project->due_date);
        $elapsedDays = $project->start_date->diffInDays(now());
        $expectedPct = $totalDays > 0 ? min(100, round(($elapsedDays / $totalDays) * 100)) : 0;
        $actualPct   = $project->progress ?? 0;
        $delta       = $actualPct - $expectedPct;

        if ($project->status === 'completed') {
            return ['score' => 100, 'label' => 'Completed', 'color' => 'success'];
        }

        if ($delta >= 10) {
            return ['score' => 95, 'label' => 'Ahead of Schedule', 'color' => 'success'];
        } elseif ($delta >= -5) {
            return ['score' => 80, 'label' => 'On Track', 'color' => 'success'];
        } elseif ($delta >= -15) {
            return ['score' => 55, 'label' => 'Slightly Behind', 'color' => 'warning'];
        } elseif ($delta >= -30) {
            return ['score' => 30, 'label' => 'At Risk', 'color' => 'error'];
        } else {
            return ['score' => 10, 'label' => 'Needs Attention', 'color' => 'error'];
        }
    }
}
