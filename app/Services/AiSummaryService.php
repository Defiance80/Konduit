<?php

namespace App\Services;

use App\Models\AiSummary;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiSummaryService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;
    private string $version;

    public function __construct()
    {
        $this->apiKey  = config('ai.anthropic.api_key', '');
        $this->model   = config('ai.anthropic.model', 'claude-sonnet-4-6');
        $this->baseUrl = config('ai.anthropic.base_url', 'https://api.anthropic.com/v1');
        $this->version = config('ai.anthropic.version', '2023-06-01');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    // ─── Project Summary ──────────────────────────────────────────────────────

    public function generateProjectSummary(Project $project): AiSummary
    {
        $project->load(['client', 'owner', 'tasks', 'deliverables', 'tickets']);

        $context = $this->buildProjectContext($project);
        $prompt  = $this->buildProjectPrompt($project, $context);

        $response = $this->callClaude($prompt, $this->projectSystemPrompt());

        return AiSummary::updateOrCreate(
            [
                'tenant_id'          => $project->tenant_id,
                'summarizable_type'  => Project::class,
                'summarizable_id'    => $project->id,
                'type'               => 'project_status',
            ],
            [
                'subject'           => "Project Status: {$project->name}",
                'content'           => $response['content'],
                'client_content'    => $response['client_content'],
                'what_happened'     => $response['what_happened'],
                'why'               => $response['why'],
                'what_next'         => $response['what_next'],
                'confidence'        => $response['confidence'],
                'visible_to_client' => false,
                'generated_by'      => $this->model,
                'metadata'          => $context,
            ]
        );
    }

    // ─── Client Summary ───────────────────────────────────────────────────────

    public function generateClientSummary(Client $client): AiSummary
    {
        $client->load(['projects.tasks', 'projects.deliverables', 'retainers', 'tickets']);

        $context = $this->buildClientContext($client);
        $prompt  = $this->buildClientPrompt($client, $context);

        $response = $this->callClaude($prompt, $this->clientSystemPrompt());

        return AiSummary::updateOrCreate(
            [
                'tenant_id'         => $client->tenant_id,
                'summarizable_type' => Client::class,
                'summarizable_id'   => $client->id,
                'type'              => 'client_health',
            ],
            [
                'subject'           => "Client Health: {$client->name}",
                'content'           => $response['content'],
                'client_content'    => $response['client_content'],
                'what_happened'     => $response['what_happened'],
                'why'               => $response['why'],
                'what_next'         => $response['what_next'],
                'confidence'        => $response['confidence'],
                'visible_to_client' => false,
                'generated_by'      => $this->model,
                'metadata'          => $context,
            ]
        );
    }

    // ─── Context builders ─────────────────────────────────────────────────────

    private function buildProjectContext(Project $project): array
    {
        $tasks       = $project->tasks;
        $deliverables= $project->deliverables;
        $tickets     = $project->tickets;

        $tasksByStatus = $tasks->groupBy('status');
        $overdueTaskCount = $tasks->filter(fn ($t) => $t->isOverdue())->count();

        $daysTotal    = $project->start_date && $project->due_date
            ? $project->start_date->diffInDays($project->due_date) : null;
        $daysElapsed  = $project->start_date
            ? $project->start_date->diffInDays(now()) : null;
        $daysRemaining= $project->due_date
            ? max(0, now()->diffInDays($project->due_date, false)) : null;

        $budgetPct = $project->budget > 0
            ? round(($project->budget_spent / $project->budget) * 100) : null;

        return [
            'name'             => $project->name,
            'status'           => $project->status,
            'progress'         => $project->progress,
            'client'           => $project->client?->name,
            'owner'            => $project->owner?->name,
            'start_date'       => $project->start_date?->format('Y-m-d'),
            'due_date'         => $project->due_date?->format('Y-m-d'),
            'days_remaining'   => $daysRemaining,
            'days_elapsed'     => $daysElapsed,
            'days_total'       => $daysTotal,
            'budget'           => $project->budget,
            'budget_spent'     => $project->budget_spent,
            'budget_pct'       => $budgetPct,
            'task_counts'      => [
                'total'       => $tasks->count(),
                'todo'        => ($tasksByStatus['todo'] ?? collect())->count(),
                'in_progress' => ($tasksByStatus['in_progress'] ?? collect())->count(),
                'in_review'   => ($tasksByStatus['in_review'] ?? collect())->count(),
                'done'        => ($tasksByStatus['done'] ?? collect())->count(),
                'overdue'     => $overdueTaskCount,
            ],
            'deliverable_counts' => [
                'total'    => $deliverables->count(),
                'approved' => $deliverables->where('status', 'approved')->count(),
                'pending'  => $deliverables->whereIn('status', ['pending','in_review'])->count(),
                'rejected' => $deliverables->where('status', 'rejected')->count(),
            ],
            'ticket_counts' => [
                'total'  => $tickets->count(),
                'open'   => $tickets->whereIn('status', ['open','in_progress'])->count(),
                'urgent' => $tickets->where('priority', 'urgent')->count(),
            ],
        ];
    }

    private function buildClientContext(Client $client): array
    {
        $projects   = $client->projects;
        $retainer   = $client->retainers->where('status', 'active')->first();
        $tickets    = $client->tickets;

        $projectHealth = $projects->map(fn ($p) => [
            'name'     => $p->name,
            'status'   => $p->status,
            'progress' => $p->progress,
            'overdue_tasks' => $p->tasks->filter(fn ($t) => $t->isOverdue())->count(),
            'pending_approvals' => $p->deliverables->whereIn('status', ['pending','in_review'])->count(),
        ]);

        return [
            'client_name'     => $client->name,
            'industry'        => $client->industry,
            'health_score'    => $client->health_score,
            'active_projects' => $projects->where('status', 'active')->count(),
            'total_projects'  => $projects->count(),
            'projects'        => $projectHealth->values()->all(),
            'retainer'        => $retainer ? [
                'monthly_value' => $retainer->monthly_value,
                'status'        => $retainer->status,
                'renewal_date'  => $retainer->renewal_date?->format('Y-m-d'),
            ] : null,
            'open_tickets'    => $tickets->whereIn('status', ['open','in_progress'])->count(),
            'urgent_tickets'  => $tickets->where('priority', 'urgent')->count(),
        ];
    }

    // ─── Prompt builders ──────────────────────────────────────────────────────

    private function projectSystemPrompt(): string
    {
        return <<<PROMPT
You are Konduit's Agency Intelligence AI. Your role is to translate raw project data into
meaningful operational intelligence for agency teams and their clients.

You produce two versions of every summary:
1. Internal (frank, honest, highlights risks and blockers without sugarcoating)
2. Client-facing (professional, confident, focuses on progress and next steps — never mentions internal team issues)

Clients never see internal notes, team conflicts, or risk escalations.

Always respond with valid JSON only. No markdown, no prose outside the JSON object.
PROMPT;
    }

    private function clientSystemPrompt(): string
    {
        return <<<PROMPT
You are Konduit's Agency Intelligence AI. Your role is to assess the overall health of an
agency-client relationship based on operational data across all active projects.

You produce two versions:
1. Internal (honest assessment of relationship risk, budget burn, engagement health)
2. Client-facing (professional relationship summary focused on what's been delivered and what's coming)

Always respond with valid JSON only. No markdown, no prose outside the JSON object.
PROMPT;
    }

    private function buildProjectPrompt(Project $project, array $ctx): string
    {
        $daysInfo = $ctx['days_remaining'] !== null
            ? "{$ctx['days_remaining']} days remaining (started {$ctx['days_elapsed']} days ago of {$ctx['days_total']} total)"
            : 'No timeline set';

        $budgetInfo = $ctx['budget']
            ? "\${$ctx['budget_spent']} spent of \${$ctx['budget']} budget ({$ctx['budget_pct']}%)"
            : 'No budget set';

        $tasks = $ctx['task_counts'];
        $dels  = $ctx['deliverable_counts'];
        $tix   = $ctx['ticket_counts'];

        return <<<PROMPT
Generate a project intelligence summary for the following project data.

PROJECT: {$ctx['name']}
Client: {$ctx['client']}
Status: {$ctx['status']} | Progress: {$ctx['progress']}%
Timeline: {$daysInfo}
Budget: {$budgetInfo}

TASKS: {$tasks['total']} total — {$tasks['todo']} todo, {$tasks['in_progress']} in progress, {$tasks['in_review']} in review, {$tasks['done']} done, {$tasks['overdue']} overdue

DELIVERABLES: {$dels['total']} total — {$dels['approved']} approved, {$dels['pending']} awaiting approval, {$dels['rejected']} with change requests

SUPPORT TICKETS: {$tix['total']} total — {$tix['open']} open, {$tix['urgent']} urgent

Respond ONLY with this JSON structure:
{
  "content": "2-3 sentence internal summary. Frank assessment of where the project stands, any risks or blockers worth noting.",
  "client_content": "2-3 sentence client-facing summary. Professional, positive-leaning. Highlight progress and next milestone. No internal team issues.",
  "what_happened": "One sentence: what significant things occurred or changed recently.",
  "why": "One sentence: why this matters to the client and the project outcome.",
  "what_next": "One sentence: the most important next action or upcoming milestone.",
  "confidence": 0.85
}

The confidence score (0.0–1.0) reflects how much data was available to generate a meaningful summary. Lower if timeline or tasks are missing.
PROMPT;
    }

    private function buildClientPrompt(Client $client, array $ctx): string
    {
        $projectLines = collect($ctx['projects'])->map(fn ($p) =>
            "  - {$p['name']}: {$p['status']}, {$p['progress']}% complete, {$p['overdue_tasks']} overdue tasks, {$p['pending_approvals']} pending approvals"
        )->join("\n");

        $retainerInfo = $ctx['retainer']
            ? "\${$ctx['retainer']['monthly_value']}/mo retainer, renews {$ctx['retainer']['renewal_date']}"
            : 'No active retainer';

        $industryLabel = $ctx['industry'] ?? 'Unknown';

        return <<<PROMPT
Generate a client relationship health summary for the following client data.

CLIENT: {$ctx['client_name']}
Industry: {$industryLabel}
Active Projects: {$ctx['active_projects']} of {$ctx['total_projects']} total
Retainer: {$retainerInfo}
Open Support Tickets: {$ctx['open_tickets']} ({$ctx['urgent_tickets']} urgent)

PROJECTS:
{$projectLines}

Respond ONLY with this JSON structure:
{
  "content": "2-3 sentence internal assessment. Honest view of relationship health, engagement risk, or budget concerns.",
  "client_content": "2-3 sentence client-facing relationship summary. What's been achieved together, what's ahead.",
  "what_happened": "One sentence: the most significant recent development across all this client's work.",
  "why": "One sentence: what this means for the relationship and outcomes.",
  "what_next": "One sentence: the highest priority action to move the relationship forward.",
  "confidence": 0.82
}

The confidence score (0.0–1.0) reflects data completeness. Lower if client has no projects or minimal data.
PROMPT;
    }

    // ─── HTTP call ────────────────────────────────────────────────────────────

    private function callClaude(string $userPrompt, string $systemPrompt): array
    {
        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => $this->version,
            'content-type'      => 'application/json',
        ])
        ->timeout(config('ai.anthropic.timeout', 60))
        ->post("{$this->baseUrl}/messages", [
            'model'      => $this->model,
            'max_tokens' => 1024,
            'system'     => $systemPrompt,
            'messages'   => [
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException("Anthropic API error: {$response->status()} — {$response->body()}");
        }

        $text = $response->json('content.0.text', '');

        // Strip any markdown fences Claude might add despite instructions
        $text = preg_replace('/^```json\s*/m', '', $text);
        $text = preg_replace('/^```\s*$/m', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);

        if (!$data || !isset($data['content'])) {
            Log::error('Anthropic returned invalid JSON', ['text' => $text]);
            throw new \RuntimeException('AI returned an unexpected response format.');
        }

        return [
            'content'        => $data['content'] ?? '',
            'client_content' => $data['client_content'] ?? '',
            'what_happened'  => $data['what_happened'] ?? '',
            'why'            => $data['why'] ?? '',
            'what_next'      => $data['what_next'] ?? '',
            'confidence'     => (float) ($data['confidence'] ?? 0.7),
        ];
    }
}
