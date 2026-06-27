<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientHealthScore;
use Illuminate\Support\Facades\Log;

class RelationshipIntelligenceService
{
    public function calculateForClient(Client $client): ClientHealthScore
    {
        $client->loadMissing([
            'projects.tasks', 'projects.deliverables',
            'retainers', 'tickets',
        ]);

        $factors  = [];
        $score    = 0;
        $maxScore = 0;

        // Active retainer (strong indicator of commitment)
        $hasActiveRetainer = $client->retainers->where('status', 'active')->isNotEmpty();
        $maxScore += 20;
        if ($hasActiveRetainer) {
            $score += 20;
            $factors['retainer'] = ['label' => 'Active retainer', 'weight' => 20, 'earned' => 20];
        } else {
            $factors['retainer'] = ['label' => 'No active retainer', 'weight' => 20, 'earned' => 0];
        }

        // Active projects
        $activeProjects = $client->projects->where('status', 'active');
        $maxScore += 20;
        $projectScore = min(20, $activeProjects->count() * 10);
        $score += $projectScore;
        $factors['projects'] = [
            'label'   => "{$activeProjects->count()} active project(s)",
            'weight'  => 20,
            'earned'  => $projectScore,
        ];

        // Deliverable approval rate
        $allDeliverables = $client->projects->flatMap(fn ($p) => $p->deliverables);
        $maxScore += 20;
        if ($allDeliverables->count() > 0) {
            $approved = $allDeliverables->where('status', 'approved')->count();
            $approvalRate = $approved / $allDeliverables->count();
            $deliverableScore = (int) round($approvalRate * 20);
            $score += $deliverableScore;
            $factors['approvals'] = [
                'label'  => round($approvalRate * 100) . '% deliverable approval rate',
                'weight' => 20,
                'earned' => $deliverableScore,
            ];
        } else {
            $factors['approvals'] = ['label' => 'No deliverables tracked', 'weight' => 20, 'earned' => 10];
            $score += 10;
        }

        // Ticket activity (recent engagement signal)
        $recentTickets = $client->tickets->filter(
            fn ($t) => $t->created_at->greaterThan(now()->subDays(30))
        );
        $urgentOpen = $client->tickets->where('status', 'open')->where('priority', 'urgent');
        $maxScore += 20;
        $ticketScore = 20;
        if ($urgentOpen->count() > 0) {
            $ticketScore -= min(20, $urgentOpen->count() * 5);
        }
        if ($recentTickets->count() === 0 && $client->projects->isNotEmpty()) {
            $ticketScore -= 5; // silent client on active work
        }
        $ticketScore = max(0, $ticketScore);
        $score += $ticketScore;
        $factors['tickets'] = [
            'label'  => "{$urgentOpen->count()} urgent open ticket(s)",
            'weight' => 20,
            'earned' => $ticketScore,
        ];

        // Project health (overdue tasks)
        $maxScore += 20;
        $overdueTasks = $client->projects->flatMap(fn ($p) => $p->tasks)
            ->filter(fn ($t) => $t->status !== 'done'
                && $t->due_date !== null
                && $t->due_date->isPast()
            );
        $healthScore = max(0, 20 - ($overdueTasks->count() * 4));
        $score += $healthScore;
        $factors['overdue'] = [
            'label'  => "{$overdueTasks->count()} overdue task(s)",
            'weight' => 20,
            'earned' => $healthScore,
        ];

        $engagementScore = $maxScore > 0 ? (int) round(($score / $maxScore) * 100) : 50;
        $churnRisk       = 100 - $engagementScore;

        $riskLevel = match(true) {
            $churnRisk >= 75 => 'critical',
            $churnRisk >= 50 => 'high',
            $churnRisk >= 25 => 'medium',
            default          => 'low',
        };

        return ClientHealthScore::updateOrCreate(
            ['tenant_id' => $client->tenant_id, 'client_id' => $client->id],
            [
                'engagement_score'  => $engagementScore,
                'churn_risk_score'  => $churnRisk,
                'churn_risk_level'  => $riskLevel,
                'factors'           => $factors,
                'calculated_at'     => now(),
            ]
        );
    }

    public function calculateForTenant(string $tenantId): int
    {
        $clients = Client::where('tenant_id', $tenantId)->get();
        $count = 0;
        foreach ($clients as $client) {
            try {
                $this->calculateForClient($client);
                $count++;
            } catch (\Throwable $e) {
                Log::error("Relationship score failed for client {$client->id}: {$e->getMessage()}");
            }
        }
        return $count;
    }
}
