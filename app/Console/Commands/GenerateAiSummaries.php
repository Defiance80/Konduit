<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAiSummaryJob;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Console\Command;

class GenerateAiSummaries extends Command
{
    protected $signature = 'konduit:ai-summaries
                            {--type=all : project, client, or all}
                            {--id= : Specific model ID to summarise}
                            {--sync : Run synchronously instead of queuing}';

    protected $description = 'Generate AI summaries for projects and/or clients';

    public function handle(): int
    {
        $type = $this->option('type');
        $id   = $this->option('id');
        $sync = $this->option('sync');

        if (empty(config('ai.anthropic.api_key'))) {
            $this->error('ANTHROPIC_API_KEY is not set. Add it to your .env file.');
            return self::FAILURE;
        }

        $dispatch = fn (string $t, int $modelId) => $sync
            ? (new GenerateAiSummaryJob($t, $modelId))->handle(app(\App\Services\AiSummaryService::class))
            : GenerateAiSummaryJob::dispatch($t, $modelId);

        if ($id) {
            $dispatch($type === 'client' ? 'client' : 'project', (int) $id);
            $this->info("Dispatched {$type} summary for ID {$id}.");
            return self::SUCCESS;
        }

        if (in_array($type, ['all', 'project'])) {
            $projects = Project::whereIn('status', ['active', 'on_hold'])->get();
            foreach ($projects as $p) {
                $dispatch('project', $p->id);
                $this->line("  Queued project: {$p->name}");
            }
            $this->info("Queued {$projects->count()} project summaries.");
        }

        if (in_array($type, ['all', 'client'])) {
            $clients = Client::where('status', 'active')->get();
            foreach ($clients as $c) {
                $dispatch('client', $c->id);
                $this->line("  Queued client: {$c->name}");
            }
            $this->info("Queued {$clients->count()} client summaries.");
        }

        return self::SUCCESS;
    }
}
