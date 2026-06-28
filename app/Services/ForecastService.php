<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\Retainer;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ForecastService
{
    public function revenueProjection(string $tenantId): array
    {
        $mrr = Retainer::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->sum('monthly_value');

        $pipeline = Invoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['draft', 'sent'])
            ->sum('total');

        $overdue = Invoice::where('tenant_id', $tenantId)
            ->where('status', 'overdue')
            ->sum('total');

        $months = [];
        for ($i = 1; $i <= 3; $i++) {
            $month = Carbon::now()->addMonths($i);
            $months[] = [
                'label'    => $month->format('M Y'),
                'mrr'      => $mrr,
                'total'    => $mrr + ($i === 1 ? $pipeline : 0),
            ];
        }

        return [
            'mrr'                => (float) $mrr,
            'arr'                => (float) $mrr * 12,
            'pipeline'           => (float) $pipeline,
            'overdue'            => (float) $overdue,
            'projected_3m'       => (float) ($mrr * 3) + $pipeline,
            'months'             => $months,
        ];
    }

    public function capacityProjection(string $tenantId): array
    {
        $members = User::where('tenant_id', $tenantId)
            ->where('user_type', 'agency_user')
            ->with(['tasks' => fn ($q) => $q->where('status', '!=', 'done')])
            ->get();

        $weeks = [];
        for ($w = 0; $w < 8; $w++) {
            $weekStart = Carbon::now()->startOfWeek()->addWeeks($w);
            $weekEnd   = $weekStart->copy()->endOfWeek();

            $tasksDue = Task::where('tenant_id', $tenantId)
                ->where('status', '!=', 'done')
                ->whereBetween('due_date', [$weekStart, $weekEnd])
                ->count();

            $capacity = $members->count() > 0
                ? min(100, (int) round(($tasksDue / ($members->count() * 5)) * 100))
                : 0;

            $weeks[] = [
                'label'    => $weekStart->format('M d'),
                'tasks'    => $tasksDue,
                'capacity' => $capacity,
                'status'   => $capacity >= 90 ? 'critical' : ($capacity >= 70 ? 'high' : 'normal'),
            ];
        }

        $teamLoad = $members->map(function (User $m) {
            $taskCount = $m->tasks->count();
            $load = min(100, $taskCount * 12);
            return [
                'name'  => $m->name,
                'tasks' => $taskCount,
                'load'  => $load,
            ];
        })->sortByDesc('load')->values();

        return [
            'weeks'      => $weeks,
            'team_load'  => $teamLoad,
            'avg_load'   => $teamLoad->avg('load') ?? 0,
            'overloaded' => $teamLoad->where('load', '>=', 80)->count(),
        ];
    }

    public function upcomingDeadlines(string $tenantId): Collection
    {
        return Project::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [now(), now()->addDays(45)])
            ->with('client')
            ->orderBy('due_date')
            ->get()
            ->map(fn (Project $p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'client'   => $p->client->name,
                'due_date' => $p->due_date,
                'progress' => $p->progress,
                'days_left'=> (int) now()->diffInDays($p->due_date, false),
                'at_risk'  => $p->progress < 60 && now()->diffInDays($p->due_date, false) < 14,
            ]);
    }
}
