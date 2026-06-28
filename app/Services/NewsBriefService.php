<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsBriefService
{
    private string $model = 'claude-sonnet-4-6';

    public function getBrief(string $tenantId): array
    {
        $key = 'news_brief_' . $tenantId . '_' . now()->format('Y-m-d');

        return cache()->remember($key, now()->addHours(20), fn () => $this->generate($tenantId));
    }

    public function refresh(string $tenantId): array
    {
        $key = 'news_brief_' . $tenantId . '_' . now()->format('Y-m-d');
        cache()->forget($key);

        return $this->getBrief($tenantId);
    }

    private function generate(string $tenantId): array
    {
        try {
            $tenant = Tenant::find($tenantId);
            $dashSettings = $tenant?->data['dashboard'] ?? [];

            $industries = Client::where('tenant_id', $tenantId)
                ->whereNotNull('industry')
                ->pluck('industry')
                ->filter()
                ->unique()
                ->join(', ');

            $context = $industries
                ? "Their clients are primarily in: {$industries}."
                : 'They serve various businesses across industries.';

            $focusKeywords = trim($dashSettings['news_focus_keywords'] ?? '');
            if ($focusKeywords) {
                $context .= " Place extra emphasis on topics related to: {$focusKeywords}.";
            }

            $allowedTypes = $dashSettings['news_categories'] ?? ['trend', 'platform', 'industry', 'tip'];
            $typeList = implode(', ', $allowedTypes);

            $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->model,
                'max_tokens' => 1200,
                'messages'   => [[
                    'role'    => 'user',
                    'content' => "You are a marketing intelligence analyst for a digital marketing agency. {$context}

Generate a morning intelligence brief with 6 curated items. Return ONLY a valid JSON object:
{
  \"generated_at\": \"" . now()->format('F j, Y') . "\",
  \"items\": [
    {
      \"type\": \"{$typeList}\",
      \"category\": \"SEO|Social Media|Paid Ads|Content|Email|Analytics|Agency\",
      \"headline\": \"short compelling headline under 80 characters\",
      \"summary\": \"2 sentence explanation of what happened and why it matters\",
      \"relevance\": \"one sentence: what action the agency should take\"
    }
  ]
}

Include exactly:
- 2 current marketing/digital industry trends or news for 2026
- 2 platform or algorithm updates (Google, Meta, TikTok, LinkedIn, etc.)
- 1 agency operations or client retention tip
- 1 content or campaign strategy insight

Be specific, actionable, and timely. Return only the JSON object, no other text.",
                ]],
            ]);

            $text = $response->json('content.0.text', '');
            $data = json_decode($text, true);

            if (isset($data['items']) && is_array($data['items']) && count($data['items']) > 0) {
                return $data;
            }
        } catch (\Throwable $e) {
            Log::warning('NewsBriefService generate failed: ' . $e->getMessage());
        }

        return $this->defaultBrief();
    }

    private function defaultBrief(): array
    {
        return [
            'generated_at' => now()->format('F j, Y'),
            'items' => [
                [
                    'type'      => 'platform',
                    'category'  => 'SEO',
                    'headline'  => 'Google AI Overviews Expands to More Query Types',
                    'summary'   => 'Google\'s AI Overviews now appear in over 35% of searches, with a focus on informational and how-to queries. Content that answers questions directly in the first 40–60 words is receiving featured placement.',
                    'relevance' => 'Audit client pages to ensure opening paragraphs answer the core question before elaborating — this is now table stakes for organic visibility.',
                ],
                [
                    'type'      => 'trend',
                    'category'  => 'Social Media',
                    'headline'  => 'Short-Form Video Delivers Highest ROI for B2C Brands',
                    'summary'   => 'Brands posting 5+ Reels or TikToks per week see 3x organic reach compared to those posting fewer than 2. Authenticity and native-feeling content outperform polished production.',
                    'relevance' => 'Recommend a short-form video calendar as a core retainer deliverable for any B2C client — it\'s the highest-leverage activity per hour spent.',
                ],
                [
                    'type'      => 'platform',
                    'category'  => 'Paid Ads',
                    'headline'  => 'Meta Advantage+ AI Campaigns Outperform Manual Setups',
                    'summary'   => 'Meta\'s automated Advantage+ shopping campaigns are outperforming manually configured campaigns by 28% average ROAS. Smart bidding is becoming the new baseline for e-commerce.',
                    'relevance' => 'Review client ad accounts and migrate eligible campaigns to Advantage+ to improve performance without increasing budget.',
                ],
                [
                    'type'      => 'industry',
                    'category'  => 'Content',
                    'headline'  => 'E-E-A-T Signals Now Critical for AI-Assisted Content',
                    'summary'   => 'Google continues to prioritise Experience, Expertise, Authority, and Trust signals. Pure AI-generated content without human expertise markers is showing measurable ranking decline.',
                    'relevance' => 'All AI-drafted content should include client quotes, proprietary data, or subject matter expert review before publishing.',
                ],
                [
                    'type'      => 'tip',
                    'category'  => 'Agency',
                    'headline'  => 'Weekly Async Updates Reduce Client Churn by 40%',
                    'summary'   => 'Agencies that deliver a structured weekly async video or written update — covering wins, blockers, and next steps — report significantly lower churn and fewer scope creep requests.',
                    'relevance' => 'Standardise a weekly 2-minute update as part of every retainer to increase perceived value without adding meeting time.',
                ],
                [
                    'type'      => 'trend',
                    'category'  => 'Analytics',
                    'headline'  => 'GA4 Predictive Audiences Now Available for All Properties',
                    'summary'   => 'Google Analytics 4 has rolled out purchase probability and churn probability models to all properties with sufficient data. These audiences can now be synced directly to Google Ads.',
                    'relevance' => 'Connect client GA4 accounts to Google Ads to unlock predictive targeting — a meaningful performance lift at no additional cost.',
                ],
            ],
        ];
    }
}
