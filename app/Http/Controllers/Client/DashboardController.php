<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Deliverable;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user   = Auth::user();
        $client = $user->client;

        $stats = [
            'active_projects'     => Project::where('client_id', $client->id)->where('status', 'active')->count(),
            'open_tickets'        => Ticket::where('client_id', $client->id)->whereIn('status', ['open', 'in_progress'])->count(),
            'pending_deliverables'=> Deliverable::where('client_id', $client->id)->where('status', 'in_review')->count(),
            'active_retainer'     => $client->activeRetainer(),
        ];

        $recentTickets = Ticket::where('client_id', $client->id)
            ->with('assignee')
            ->latest()
            ->limit(5)
            ->get();

        $activeProjects = Project::where('client_id', $client->id)
            ->where('status', 'active')
            ->with('owner')
            ->latest()
            ->limit(4)
            ->get();

        $pendingDeliverables = Deliverable::where('client_id', $client->id)
            ->where('status', 'in_review')
            ->with('project')
            ->latest()
            ->limit(5)
            ->get();

        return view('client.dashboard', compact(
            'client', 'stats', 'recentTickets', 'activeProjects', 'pendingDeliverables'
        ));
    }
}
