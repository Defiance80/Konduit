<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Deliverable;
use App\Models\Project;
use App\Models\Retainer;
use App\Models\Ticket;
use App\Models\AiSummary;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user   = Auth::user();
        $client = $user->client;

        $retainer = Retainer::where('client_id', $client->id)->where('status', 'active')->first();

        $stats = [
            'active_projects'      => Project::where('client_id', $client->id)->where('status', 'active')->count(),
            'open_tickets'         => Ticket::where('client_id', $client->id)->whereIn('status', ['open', 'in_progress'])->count(),
            'pending_deliverables' => Deliverable::where('client_id', $client->id)->where('status', 'in_review')->count(),
            'active_retainer'      => $retainer,
        ];

        $recentTickets = Ticket::where('client_id', $client->id)
            ->latest()->limit(5)->get();

        $activeProjects = Project::where('client_id', $client->id)
            ->where('status', 'active')
            ->withCount(['tasks', 'tasks as completed_tasks_count' => fn ($q) => $q->where('status', 'done')])
            ->latest()->limit(4)->get();

        $pendingDeliverables = Deliverable::where('client_id', $client->id)
            ->where('status', 'in_review')
            ->with('project')
            ->latest()->limit(5)->get();

        $recentReports = AiSummary::where('tenant_id', $client->tenant_id)
            ->where('visible_to_client', true)
            ->where(function ($q) use ($client) {
                $q->where('summarizable_type', 'App\\Models\\Client')->where('summarizable_id', $client->id)
                  ->orWhereIn('summarizable_id', Project::where('client_id', $client->id)->pluck('id'));
            })
            ->latest()->limit(3)->get();

        return view('client.dashboard', compact(
            'client', 'stats', 'recentTickets', 'activeProjects', 'pendingDeliverables', 'retainer', 'recentReports'
        ));
    }
}
