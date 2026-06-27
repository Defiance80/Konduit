<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\Project;
use App\Services\AiSummaryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAiSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 90;

    public function __construct(
        private readonly string $type,
        private readonly int $modelId
    ) {}

    public function handle(AiSummaryService $service): void
    {
        if (!$service->isConfigured()) {
            Log::warning('AiSummaryService: ANTHROPIC_API_KEY not set. Skipping job.');
            return;
        }

        match ($this->type) {
            'project' => $service->generateProjectSummary(
                Project::withoutGlobalScopes()->findOrFail($this->modelId)
            ),
            'client'  => $service->generateClientSummary(
                Client::withoutGlobalScopes()->findOrFail($this->modelId)
            ),
            default   => throw new \InvalidArgumentException("Unknown summary type: {$this->type}"),
        };
    }

    public function failed(\Throwable $e): void
    {
        Log::error('GenerateAiSummaryJob failed', [
            'type'  => $this->type,
            'id'    => $this->modelId,
            'error' => $e->getMessage(),
        ]);
    }
}
