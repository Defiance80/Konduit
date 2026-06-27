<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IntakeAiService
{
    public function classify(string $name, string $issueType, string $description, ?string $company = null, ?string $url = null): array
    {
        $apiKey = config('ai.anthropic.api_key');
        if (!$apiKey) {
            return $this->fallback($issueType, $description);
        }

        $prompt = "A {$issueType} request was submitted by {$name}"
            . ($company ? " from {$company}" : '')
            . ".\n\n"
            . "Description: {$description}\n"
            . ($url ? "Affected URL: {$url}\n" : '')
            . "\nClassify this submission and generate response messages. Respond ONLY with valid JSON:
{
  \"issue_type\": \"bug|feature|content|question|billing|emergency\",
  \"priority\": \"low|medium|high|urgent\",
  \"department\": \"development|design|content|seo|general\",
  \"tags\": [\"tag1\", \"tag2\"],
  \"internal_summary\": \"2-3 sentence frank assessment for the agency team. What is this really asking for? Any red flags?\",
  \"client_message\": \"2-3 sentence professional acknowledgement for the client. Warm, specific to their issue, states next steps.\",
  \"estimated_effort\": \"quick|moderate|significant\",
  \"confidence\": 0.9
}";

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => config('ai.anthropic.version'),
                'content-type'      => 'application/json',
            ])->timeout(30)->post(config('ai.anthropic.base_url') . '/messages', [
                'model'      => config('ai.anthropic.model'),
                'max_tokens' => 600,
                'system'     => 'You are Konduit\'s Intake AI. You classify client support requests for digital agencies and generate professional, warm client acknowledgements. Always respond with valid JSON only.',
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

            $text = $response->json('content.0.text', '');
            $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
            $text = preg_replace('/\s*```$/m', '', $text);
            $data = json_decode(trim($text), true);

            if (json_last_error() === JSON_ERROR_NONE && isset($data['priority'])) {
                return $data;
            }
        } catch (\Throwable $e) {
            Log::error('IntakeAI classification failed: ' . $e->getMessage());
        }

        return $this->fallback($issueType, $description);
    }

    private function fallback(string $issueType, string $description): array
    {
        $priority = str_contains(strtolower($description), 'urgent')
            || str_contains(strtolower($description), 'down')
            || str_contains(strtolower($description), 'emergency')
            ? 'high' : 'medium';

        return [
            'issue_type'       => $issueType,
            'priority'         => $priority,
            'department'       => 'general',
            'tags'             => [$issueType],
            'internal_summary' => "New {$issueType} submission received. Needs manual review.",
            'client_message'   => "Thank you for reaching out. We've received your request and our team will be in touch shortly.",
            'estimated_effort' => 'moderate',
            'confidence'       => 0.5,
        ];
    }
}
