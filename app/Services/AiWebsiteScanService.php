<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiWebsiteScanService
{
    private string $model = 'claude-sonnet-4-6';

    public function scan(string $url): array
    {
        $extracted = $this->extractFromUrl($url);

        if (isset($extracted['error'])) {
            return $this->fallbackResult($extracted);
        }

        return $this->analyzeWithAi($url, $extracted);
    }

    private function extractFromUrl(string $url): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; KonduitAuditBot/1.0)'])
                ->get($url);

            if (!$response->successful()) {
                return ['error' => 'HTTP ' . $response->status() . ' — could not fetch URL.'];
            }

            return $this->parseHtml($response->body(), $url);
        } catch (\Exception $e) {
            Log::warning('AiWebsiteScanService fetch failed', ['url' => $url, 'error' => $e->getMessage()]);
            return ['error' => 'Connection failed: ' . $e->getMessage()];
        }
    }

    private function parseHtml(string $html, string $url): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($dom);

        $title = $dom->getElementsByTagName('title')->item(0)?->textContent ?? '';

        $metaDesc = '';
        foreach ($xpath->query("//meta[@name='description']") as $node) {
            $metaDesc = $node->getAttribute('content');
            break;
        }

        $h1 = [];
        foreach ($dom->getElementsByTagName('h1') as $node) {
            $t = trim(preg_replace('/\s+/', ' ', $node->textContent));
            if ($t) $h1[] = $t;
        }
        $h2 = [];
        foreach ($dom->getElementsByTagName('h2') as $node) {
            $t = trim(preg_replace('/\s+/', ' ', $node->textContent));
            if ($t) $h2[] = $t;
        }
        $h3 = [];
        foreach ($dom->getElementsByTagName('h3') as $node) {
            $t = trim(preg_replace('/\s+/', ' ', $node->textContent));
            if ($t) $h3[] = $t;
        }

        $schemas = [];
        $schemaTypes = [];
        foreach ($xpath->query("//script[@type='application/ld+json']") as $node) {
            $decoded = json_decode($node->textContent, true);
            if ($decoded) {
                $schemas[] = $decoded;
                $type = $decoded['@type'] ?? ($decoded['@graph'][0]['@type'] ?? null);
                if ($type) $schemaTypes[] = $type;
            }
        }
        $schemaTypes = array_unique($schemaTypes);

        $hasBreadcrumbs = false;
        foreach ($xpath->query("//*[contains(@class,'breadcrumb')]") as $node) { $hasBreadcrumbs = true; break; }
        foreach ($schemas as $s) {
            $type = $s['@type'] ?? null;
            if ($type === 'BreadcrumbList') $hasBreadcrumbs = true;
            if (isset($s['@graph'])) {
                foreach ($s['@graph'] as $item) {
                    if (($item['@type'] ?? null) === 'BreadcrumbList') $hasBreadcrumbs = true;
                }
            }
        }

        $hasFaq = false;
        foreach ($schemas as $s) {
            if (($s['@type'] ?? null) === 'FAQPage') $hasFaq = true;
        }

        $imgs = $dom->getElementsByTagName('img');
        $totalImages = $imgs->length;
        $imagesWithAlt = 0;
        foreach ($imgs as $img) {
            if (trim($img->getAttribute('alt')) !== '') $imagesWithAlt++;
        }

        $socialPlatforms = ['facebook.com', 'twitter.com', 'x.com', 'instagram.com', 'linkedin.com', 'tiktok.com', 'youtube.com', 'pinterest.com'];
        $socialLinks = [];
        foreach ($dom->getElementsByTagName('a') as $link) {
            $href = strtolower($link->getAttribute('href'));
            foreach ($socialPlatforms as $p) {
                if (str_contains($href, $p) && !in_array($p, $socialLinks)) {
                    $socialLinks[] = str_replace('.com', '', $p);
                }
            }
        }

        $hasEmailForm = false;
        foreach ($xpath->query("//input[@type='email']") as $node) { $hasEmailForm = true; break; }

        $ctaKeywords = ['get started', 'sign up', 'contact us', 'contact', 'buy now', 'book', 'schedule', 'free trial', 'get a demo', 'demo', 'learn more', 'get a quote', 'request a quote', 'call now', 'start free', 'try free', 'get in touch'];
        $ctaCount = 0;
        foreach (array_merge(iterator_to_array($dom->getElementsByTagName('a')), iterator_to_array($dom->getElementsByTagName('button'))) as $el) {
            $text = strtolower(trim($el->textContent));
            foreach ($ctaKeywords as $kw) {
                if (str_contains($text, $kw)) { $ctaCount++; break; }
            }
        }

        $canonical = '';
        foreach ($xpath->query("//link[@rel='canonical']") as $node) { $canonical = $node->getAttribute('href'); break; }

        $hasViewport = false;
        foreach ($xpath->query("//meta[@name='viewport']") as $node) { $hasViewport = true; break; }

        $ogTitle = '';
        foreach ($xpath->query("//meta[@property='og:title']") as $node) { $ogTitle = $node->getAttribute('content'); break; }
        $ogImage = '';
        foreach ($xpath->query("//meta[@property='og:image']") as $node) { $ogImage = $node->getAttribute('content'); break; }

        $firstParagraph = '';
        foreach ($dom->getElementsByTagName('p') as $p) {
            $text = trim(preg_replace('/\s+/', ' ', $p->textContent));
            if (strlen($text) > 40) { $firstParagraph = $text; break; }
        }
        $firstWords = implode(' ', array_slice(explode(' ', $firstParagraph), 0, 60));

        $baseHost = parse_url($url, PHP_URL_HOST);
        $internalLinks = 0; $externalLinks = 0;
        foreach ($dom->getElementsByTagName('a') as $link) {
            $href = $link->getAttribute('href');
            if (empty($href) || str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) continue;
            if (str_starts_with($href, '/') || str_contains($href, (string) $baseHost)) $internalLinks++;
            elseif (str_starts_with($href, 'http')) $externalLinks++;
        }

        $bodyText = '';
        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body) $bodyText = preg_replace('/\s+/', ' ', strip_tags($body->textContent));
        $wordCount = str_word_count($bodyText);

        $baseUrl = rtrim(parse_url($url, PHP_URL_SCHEME) . '://' . $baseHost, '/');
        $hasRobotsTxt = false;
        $hasSitemap = false;
        try {
            $hasRobotsTxt = Http::timeout(5)->get($baseUrl . '/robots.txt')->successful();
            $hasSitemap   = Http::timeout(5)->get($baseUrl . '/sitemap.xml')->successful()
                || Http::timeout(5)->get($baseUrl . '/sitemap_index.xml')->successful();
        } catch (\Exception) {}

        return compact(
            'title', 'metaDesc', 'h1', 'h2', 'h3',
            'totalImages', 'imagesWithAlt',
            'socialLinks', 'hasEmailForm', 'ctaCount',
            'canonical', 'hasViewport', 'ogTitle', 'ogImage',
            'firstWords', 'hasBreadcrumbs', 'hasFaq',
            'internalLinks', 'externalLinks',
            'hasRobotsTxt', 'hasSitemap', 'wordCount',
            'schemaTypes'
        );
    }

    private function analyzeWithAi(string $url, array $data): array
    {
        $dataJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $prompt = 'You are a professional SEO and digital marketing analyst. Analyze this website audit data and return a scoring report as valid JSON only — no markdown, no explanation, just JSON.

Website URL: ' . $url . '

Extracted technical data:
' . $dataJson . '

Score each category 0-100 and give specific, actionable findings. Return EXACTLY this JSON structure:

{
  "overall_score": 72,
  "executive_summary": "2-3 sentences summarising the overall digital health and key priority.",
  "categories": {
    "technical_seo": {
      "score": 75,
      "grade": "B",
      "label": "Technical SEO",
      "issues": ["No sitemap.xml detected", "Meta description missing"],
      "wins": ["Title tag present and well-structured"],
      "recommendations": [
        {"priority": "high", "title": "Add XML sitemap", "detail": "Submit a sitemap.xml to Google Search Console to improve crawlability and indexing speed."}
      ]
    },
    "content": {
      "score": 65,
      "grade": "C+",
      "label": "Content Quality",
      "issues": [],
      "wins": [],
      "recommendations": []
    },
    "aeo": {
      "score": 70,
      "grade": "B-",
      "label": "Answer Engine Optimization",
      "issues": [],
      "wins": [],
      "recommendations": []
    },
    "schema": {
      "score": 40,
      "grade": "D",
      "label": "Schema & Structured Data",
      "issues": [],
      "wins": [],
      "recommendations": []
    },
    "performance": {
      "score": 70,
      "grade": "B-",
      "label": "Page Experience",
      "issues": [],
      "wins": [],
      "recommendations": []
    },
    "social_conversion": {
      "score": 60,
      "grade": "C",
      "label": "Social & Conversion",
      "issues": [],
      "wins": [],
      "recommendations": []
    }
  },
  "top_recommendations": [
    {"priority": "critical", "title": "Title", "category": "technical_seo", "detail": "Specific recommendation", "impact": "high"},
    {"priority": "high", "title": "Title", "category": "content", "detail": "Specific recommendation", "impact": "medium"}
  ]
}

Scoring reference:
- technical_seo: title 50-70 chars +15pts, meta description 50-160 chars +20pts, H1 exactly 1 +15pts, canonical present +10pts, robots.txt +10pts, sitemap +15pts, schema present +15pts
- content: H1-H3 hierarchy +20pts, word count 300+ +25pts, H2 tags 3+ +20pts, content structure clear +20pts, H3 used +15pts
- aeo: first 60 words answer a question clearly +40pts, FAQ schema +25pts, opening paragraph direct and scannable +20pts, numbered/bulleted answers +15pts
- schema: any JSON-LD present +30pts, Organization or LocalBusiness +25pts, breadcrumbs +25pts, FAQ +20pts
- performance: viewport meta present +30pts, images alt text 80%+ +30pts, clean link structure (2-5 external) +20pts, low external link ratio +20pts
- social_conversion: 3+ social platforms +25pts, email capture form +30pts, 2+ CTA elements +25pts, og:image present +20pts';

        try {
            $apiKey = config('services.anthropic.key');
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])->timeout(90)->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->model,
                'max_tokens' => 3500,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

            $text = $response->json('content.0.text', '');
            $text = preg_replace('/^```json\s*|\s*```$/m', '', trim($text));

            $result = json_decode($text, true);

            if (!$result || !isset($result['categories'])) {
                Log::error('AiWebsiteScanService parse fail', ['text' => substr($text, 0, 500)]);
                return $this->fallbackResult($data);
            }

            $result['scan_data'] = $data;
            return $result;
        } catch (\Exception $e) {
            Log::error('AiWebsiteScanService API fail', ['error' => $e->getMessage()]);
            return $this->fallbackResult($data);
        }
    }

    private function fallbackResult(array $data): array
    {
        return [
            'overall_score'    => 0,
            'executive_summary'=> 'Scan completed but AI analysis could not be generated. Please try again.',
            'categories'       => [],
            'top_recommendations' => [],
            'scan_data'        => $data,
            'error'            => true,
        ];
    }
}
