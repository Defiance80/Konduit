<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\TrainingCourse;
use App\Models\TrainingCompletion;
use App\Services\NewsBriefService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(NewsBriefService $newsBriefService): View
    {
        $user     = Auth::user();
        $tenantId = $user->tenant_id;

        $stats = [
            'clients'         => Client::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'open_tickets'    => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
            'tasks_due_today' => Task::whereDate('due_date', today())->whereNull('completed_at')->count(),
        ];

        $recentTickets = Ticket::with(['client', 'assignee'])
            ->whereIn('status', ['open', 'in_progress'])
            ->latest()
            ->limit(5)
            ->get();

        $recentProjects = Project::with(['client', 'owner'])
            ->where('status', 'active')
            ->orderBy('due_date')
            ->limit(6)
            ->get();

        $recentClients = Client::with('retainers')->latest()->limit(5)->get();

        // News brief (cached 20h, falls back to default if AI unavailable)
        $newsBrief = $newsBriefService->getBrief($tenantId ?? '');

        // Featured courses for dashboard widget (first 3)
        $featuredCourses = TrainingCourse::forTenant($tenantId)
            ->where('is_published', true)
            ->withCount('lessons')
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        // Build progress map: course_id => progress%
        $trainingProgress = [];
        foreach ($featuredCourses as $course) {
            $trainingProgress[$course->id] = $course->progressForUser($user->id);
        }

        return view('agency.dashboard', compact(
            'stats', 'recentTickets', 'recentProjects', 'recentClients',
            'newsBrief', 'featuredCourses', 'trainingProgress'
        ));
    }
}
