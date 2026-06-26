<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Retainer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'clients'          => Client::count(),
            'active_projects'  => Project::where('status', 'active')->count(),
            'open_tickets'     => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
            'active_retainers' => Retainer::where('status', 'active')->count(),
        ];

        $recentTickets = Ticket::with(['client', 'assignee'])
            ->whereIn('status', ['open', 'in_progress'])
            ->latest()
            ->limit(5)
            ->get();

        $activeProjects = Project::with(['client', 'owner'])
            ->where('status', 'active')
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        $recentClients = Client::latest()->limit(5)->get();

        return view('agency.dashboard', compact('stats', 'recentTickets', 'activeProjects', 'recentClients'));
    }
}
