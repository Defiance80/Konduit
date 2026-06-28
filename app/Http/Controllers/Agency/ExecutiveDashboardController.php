<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\AiSummary;
use App\Models\Client;
use App\Models\ClientHealthScore;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Retainer;
use App\Models\User;
use App\Services\ForecastService;
use App\Services\RelationshipIntelligenceService;
use App\Services\StrategyIntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExecutiveDashboardController extends Controller
{
    public function generateBrief(StrategyIntelligenceService $strategy): \Illuminate\Http\RedirectResponse
    {
        $tenantId = Auth::user()->tenant_id;
        if (!$tenantId) return back()->with('error', 'No agency tenant on this account.');
        $strategy->getAgencyBrief($tenantId, forceRefresh: true);
        return back()->with('success', 'Agency brief regenerated.');
    }

    public function index(ForecastService $forecast, RelationshipIntelligenceService $rel, StrategyIntelligenceService $strategy): \Illuminate\View\View
    {
        $tenantId = Auth::user()->tenant_id;
        if (!$tenantId) abort(403, 'No agency tenant associated with this account.');

        // Recalculate relationship scores on each load (fast enough for dashboard)
        $rel->calculateForTenant($tenantId);

        // Financial pulse
        $revenue = $forecast->revenueProjection($tenantId);

        // Project health
        $projects = Project::where('tenant_id', $tenantId)
            ->with(['tasks', 'client'])
            ->get();

        $activeProjects = $projects->where('status', 'active');

        $atRiskProjects = $activeProjects->filter(function (Project $p) {
            $overdueTasks = $p->tasks->filter(
                fn ($t) => $t->status !== 'done' && $t->due_date !== null && $t->due_date->isPast()
            )->count();
            $daysLeft = $p->due_date ? now()->diffInDays($p->due_date, false) : null;
            return $overdueTasks > 0
                || ($daysLeft !== null && $daysLeft < 14 && $p->progress < 60)
                || ($p->due_date && $p->due_date->isPast() && $p->progress < 100);
        });

        // Client health
        $clientScores = ClientHealthScore::where('tenant_id', $tenantId)
            ->with('client')
            ->orderBy('churn_risk_score', 'desc')
            ->get();

        // Capacity + upcoming deadlines
        $capacity  = $forecast->capacityProjection($tenantId);
        $deadlines = $forecast->upcomingDeadlines($tenantId);

        // Strategy brief (cached 1 hour)
        $strategyBrief = $strategy->getAgencyBrief($tenantId);

        // Recent AI summaries for intelligence feed
        $intelligenceFeed = AiSummary::where('tenant_id', $tenantId)
            ->latest()
            ->take(5)
            ->get();

        // Team member count
        $teamCount = User::where('tenant_id', $tenantId)
            ->where('user_type', 'agency_user')
            ->count();

        // Outstanding invoices
        $overdueInvoices = Invoice::where('tenant_id', $tenantId)
            ->where('status', 'overdue')
            ->count();

        return view('agency.executive.index', compact(
            'revenue', 'activeProjects', 'atRiskProjects', 'clientScores',
            'capacity', 'deadlines', 'intelligenceFeed', 'teamCount', 'overdueInvoices',
            'strategyBrief'
        ));
    }
}
