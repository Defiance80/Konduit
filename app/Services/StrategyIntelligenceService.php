<?php

namespace App\Services;

use App\Models\ClientHealthScore;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Retainer;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StrategyIntelligenceService
{
    public function getAgencyBrief(string $tenantId, bool $forceRefresh = false): ?array
    {
        $cacheKey = "strategy_brief_{$tenantId}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 3600, fn () => $this->generate($tenantId));
    }

    private function generate(string $tenantId): ?array
    {
        $apiKey = config('ai.anthropic.api_key');
        if (!$apiKey) {
            return null;
        }

        try {
            $mrr          = Retainer::where('tenant_id', $tenantId)->where('status', 'active')->sum('monthly_value');
            $activeProjects = Project::where('tenant_id', $tenantId)->where('status', 'active')->count();
            $overdueInvoices = Invoice::where('tenant_id', $tenantId)->where('status', 'overdue')->count();
            $teamCount    = User::where('tenant_id', $tenantId)->where('user_type', 'agency_user')->count();

            $atRisk = Project::where('tenant_id', $tenantId)
                ->where('status', 'active')
                ->with('tasks')
                ->get()
                ->filter(fn ($p) => $p->tasks->where('status', '!=', 'done')
                    ->filter(fn ($t) => $t->due_date && $t->due_date->isPast())->count() > 0
                )->count();

            $churnRisk = ClientHealthScore::where('tenant_id', $tenantId)
                ->whereIn('churn_risk_level', ['high', 'critical'])
                ->count();

            $prompt = "Generate an executive strategy brief for a digital agency with the following metrics:
- MRR: \${$mrr}
- Active Projects: {$activeProjects} ({$atRisk} at risk)
- Team Size: {$teamCount}
- Overdue Invoices: {$overdueInvoices}
- Clients with High/Critical Churn Risk: {$churnRisk}

Today is " . now()->format('F j, Y') . ".

Respond ONLY with valid JSON:
{
  \"headline\": \"One sentence describing the agency's current state.\",
  \"health\": \"good|caution|critical\",
  \"priorities\": [\"Highest priority action\", \"Second priority\", \"Third priority\"],
  \"revenue_note\": \"One sentence on revenue health.\",
  \"risk_note\": \"One sentence on the biggest operational risk right now.\",
  \"opportunity\": \"One sentence on the best growth or efficiency opportunity this week.\",
  \"confidence\": 0.85
}";

            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => config('ai.anthropic.version'),
                'content-type'      => 'application/json',
            ])->timeout(30)->post(config('ai.anthropic.base_url') . '/messages', [
                'model'      => config('ai.anthropic.model'),
                'max_tokens' => 500,
                'system'     => 'You are a senior agency strategist. Give concise, actionable executive intelligence. Respond only with valid JSON.',
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

            $text = preg_replace('/^```(?:json)?\s*/m', '', $response->json('content.0.text', ''));
            $text = preg_replace('/\s*```$/m', '', $text);
            $data = json_decode(trim($text), true);

            if (json_last_error() === JSON_ERROR_NONE && isset($data['headline'])) {
                $data['generated_at'] = now()->toISOString();
                return $data;
            }
        } catch (\Throwable $e) {
            Log::error("Strategy brief generation failed: {$e->getMessage()}");
        }

        return null;
    }
}
