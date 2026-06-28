<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Retainer;
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

        // News brief (cached 20h, falls back to default if AI unavailable)
        $newsBrief = $newsBriefService->getBrief($tenantId ?? '');

        // Training progress summary
        $trainingCourses = TrainingCourse::forTenant($tenantId)
            ->where('is_published', true)
            ->withCount('lessons')
            ->orderBy('sort_order')
            ->limit(3)
            ->get()
            ->each(function (TrainingCourse $c) use ($user) {
                $c->user_progress = $c->progressForUser($user->id);
            });

        $totalLessons     = TrainingCourse::forTenant($tenantId)->withCount('lessons')->get()->sum('lessons_count');
        $completedLessons = TrainingCompletion::where('user_id', $user->id)->count();
        $trainingProgress = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;

        return view('agency.dashboard', compact(
            'stats', 'recentTickets', 'activeProjects', 'recentClients',
            'newsBrief', 'trainingCourses', 'trainingProgress', 'completedLessons', 'totalLessons'
        ));
    }
}
