<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Services\ForecastService;
use Illuminate\Support\Facades\Auth;

class ForecastController extends Controller
{
    public function index(ForecastService $service): \Illuminate\View\View
    {
        $tenantId  = Auth::user()->tenant_id;
        $revenue   = $service->revenueProjection($tenantId);
        $capacity  = $service->capacityProjection($tenantId);
        $deadlines = $service->upcomingDeadlines($tenantId);

        return view('agency.forecast.index', compact('revenue', 'capacity', 'deadlines'));
    }

    public function simulator(): \Illuminate\View\View
    {
        return view('agency.forecast.simulator');
    }

    public function simulate(\Illuminate\Http\Request $request, ForecastService $service): \Illuminate\View\View
    {
        $tenantId = Auth::user()->tenant_id;

        $retainerValue   = (float) $request->input('retainer_value', 0);
        $weeklyHours     = (float) $request->input('weekly_hours', 0);
        $projectDuration = (int)   $request->input('project_duration_weeks', 12);

        $current  = $service->revenueProjection($tenantId);
        $capacity = $service->capacityProjection($tenantId);

        $simulation = [
            'revenue_impact' => $retainerValue,
            'new_mrr'        => $current['mrr'] + $retainerValue,
            'new_arr'        => ($current['mrr'] + $retainerValue) * 12,
            'new_3m'         => $current['projected_3m'] + ($retainerValue * 3),
            'team_hours_per_week' => $weeklyHours,
            'duration_weeks' => $projectDuration,
            'total_hours'    => $weeklyHours * $projectDuration,
            'avg_load_now'   => $capacity['avg_load'],
            'avg_load_after' => min(100, $capacity['avg_load'] + ($weeklyHours / max(1, count($capacity['team_load'])) * 2)),
            'recommendation' => $this->simulationRecommendation($capacity['avg_load'], $weeklyHours, $capacity['team_load']),
        ];

        return view('agency.forecast.simulator', compact('simulation', 'current', 'capacity', 'retainerValue', 'weeklyHours', 'projectDuration'));
    }

    private function simulationRecommendation(float $currentLoad, float $weeklyHours, $team): string
    {
        $teamSize = count($team);
        if ($teamSize === 0) {
            return 'No team members found. Add agency team members before running simulations.';
        }
        $addedLoadPerPerson = ($weeklyHours / $teamSize) * 2;
        $projected = $currentLoad + $addedLoadPerPerson;

        if ($projected >= 90) {
            return 'This engagement would push the team over capacity. Consider hiring or pushing the start date.';
        } elseif ($projected >= 70) {
            return 'Team will be near full capacity. Assign a dedicated project lead and monitor weekly.';
        } else {
            return 'Team has sufficient capacity to absorb this engagement without impact to existing work.';
        }
    }
}
