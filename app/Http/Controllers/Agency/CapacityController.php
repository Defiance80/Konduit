<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class CapacityController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $teamMembers = User::where('tenant_id', $tenantId)
            ->where('user_type', 'agency_user')
            ->orderBy('name')
            ->get();

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        // Per-member workload this week
        $capacityData = $teamMembers->map(function (User $member) use ($weekEnd) {
            $assignedTasks = Task::where('assignee_id', $member->id)
                ->whereNull('parent_task_id')
                ->where('status', '!=', 'done')
                ->with(['project'])
                ->get();

            $overdueTasks   = $assignedTasks->filter(fn ($t) => $t->isOverdue());
            $dueSoon        = $assignedTasks->filter(fn ($t) => $t->due_date && $t->due_date->lte($weekEnd) && !$t->isOverdue());
            $estimatedHours = $assignedTasks->sum('estimated_hours');

            // Simple load score: 0-100 based on task count + hours
            $loadScore = min(100, ($assignedTasks->count() * 8) + ($estimatedHours * 2));

            return [
                'member'          => $member,
                'total_tasks'     => $assignedTasks->count(),
                'overdue_tasks'   => $overdueTasks->count(),
                'due_soon'        => $dueSoon->count(),
                'estimated_hours' => round($estimatedHours, 1),
                'load_score'      => $loadScore,
                'load_label'      => match (true) {
                    $loadScore >= 80 => 'Overloaded',
                    $loadScore >= 50 => 'Busy',
                    $loadScore >= 20 => 'Available',
                    default          => 'Light',
                },
                'load_color'      => match (true) {
                    $loadScore >= 80 => 'error',
                    $loadScore >= 50 => 'warning',
                    $loadScore >= 20 => 'success',
                    default          => 'gray',
                },
                'tasks'           => $assignedTasks->take(5),
            ];
        });

        // Agency-wide stats
        $agencyStats = [
            'total_open_tasks'  => Task::whereNull('parent_task_id')->where('status', '!=', 'done')->count(),
            'overdue_tasks'     => Task::whereNull('parent_task_id')->where('status', '!=', 'done')
                ->whereNotNull('due_date')->where('due_date', '<', now()->toDateString())->count(),
            'unassigned_tasks'  => Task::whereNull('parent_task_id')->whereNull('assignee_id')->where('status', '!=', 'done')->count(),
            'total_est_hours'   => round(Task::whereNull('parent_task_id')->where('status', '!=', 'done')->sum('estimated_hours'), 1),
        ];

        return view('agency.capacity.index', compact('capacityData', 'agencyStats', 'weekStart', 'weekEnd'));
    }
}
