<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GroqBlogService
{
    protected string $apiKey;
    protected string $defaultModel;

    public function __construct()
    {
        $this->apiKey = config('services.groq.key') ?? env('GROQ_API_KEY', '');
        $this->defaultModel = config('services.groq.model') ?? env('GROQ_MODEL', 'openai/gpt-oss-120b');
    }

    /**
     * Generate an authoritative post-tensioning engineering blog post via Groq API.
     *
     * @param array $params
     * @return array{title: string, slug: string, meta_description: string, content: string, tags: array<string>}
     * @throws Exception
     */
    public function generate(array $params): array
    {
        if (empty($this->apiKey)) {
            throw new Exception("GROQ_API_KEY is not configured in backend .env");
        }

        $topic = trim($params['topic'] ?? '');
        if (empty($topic)) {
            throw new Exception("Topic is required for AI blog generation.");
        }

        $keywords = $params['primary_keywords'] ?? '';
        if (is_array($keywords)) {
            $keywords = implode(', ', $keywords);
        }

        $audience = $params['target_audience'] ?? 'Structural consultants, architects, real estate developers, and civil contractors in India';
        $tone = $params['tone'] ?? 'Authoritative Technical';
        $model = $params['model'] ?? $this->defaultModel;
        $approxWords = $params['approx_words'] ?? 900;

        $prompt = $this->buildPrompt($topic, $keywords, $audience, $tone, $approxWords);

        $response = Http::withToken($this->apiKey)
            ->timeout(90)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a seasoned Principal Post-Tensioning and Structural Specialist at Unified Post Tensioning Systems. You write with deep analytical substance, natural cadence, and zero detectable AI cliches or synthetic filler. Output strictly valid JSON with keys: "title", "slug", "metaDescription", "content", "suggestedTags". The "content" field MUST be clean, valid semantic HTML with rich visual hierarchy (h2, h3, p, ul, ol, li, strong, blockquote). Never output markdown formatting or wrapping code blocks.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.6,
                'max_completion_tokens' => 8000,
            ]);

        if (!$response->successful()) {
            $errorMsg = $response->json('error.message') ?? $response->body();
            throw new Exception("Groq API Error ({$response->status()}): {$errorMsg}");
        }

        $json = $response->json();
        $rawContent = $json['choices'][0]['message']['content'] ?? null;

        if (empty($rawContent)) {
            throw new Exception("No content returned from Groq API.");
        }

        $parsed = json_decode($rawContent, true);
        if (!is_array($parsed)) {
            // Try extracting JSON substring if there's any stray text
            if (preg_match('/\{[\s\S]*\}/', $rawContent, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
        }

        if (!is_array($parsed) || empty($parsed['title']) || empty($parsed['content'])) {
            throw new Exception("Failed to parse AI blog post JSON response.");
        }

        $title = trim($parsed['title']);
        $slug = Str::slug($parsed['slug'] ?? $title);
        $metaDescription = trim($parsed['metaDescription'] ?? Str::limit(strip_tags($parsed['content']), 155));
        $content = $this->sanitizeHtml($parsed['content']);
        $tags = is_array($parsed['suggestedTags'] ?? null)
            ? array_values(array_filter(array_map('trim', $parsed['suggestedTags'])))
            : ['post-tensioning', 'structural-engineering'];

        return [
            'title' => $title,
            'slug' => $slug,
            'meta_description' => $metaDescription,
            'content' => $content,
            'tags' => $tags,
        ];
    }

    protected function buildPrompt(string $topic, string $keywords, string $audience, string $tone, int $approxWords): string
    {
        return <<<PROMPT
Topic: {$topic}
Primary Focus Keywords: {$keywords}
Target Audience: {$audience}
Tone: {$tone}
Target Length: Approximately {$approxWords} words

You are the Principal Post-Tensioning Structural Specialist at Unified Post Tensioning Systems (https://unifiedpts.com) with 15+ years of live structural design, tendon profiling, and on-site hydraulic stressing experience across 600+ projects in India.

### VOICE & STYLE GUIDELINES:
1. **Take a firm engineering stance**: Compare structural choices with hard metrics (deflection, floor cycle, steel tonnage, punching shear).
2. **Include concrete engineering details**: Mention strand grade (Grade 1860 / IS 14268 / IS 1343), hydraulic jack calibration, tendon eccentricities, anchorage bursting reinforcement, or grouting pressure (0.3–0.5 MPa).
3. **Company Knowledge Base**: Unified Post Tensioning Systems (in partnership with Pramukh Post Tensioning Systems PVT. LTD.) provides turnkey unbonded and bonded post-tensioning for residential towers, commercial slabs, transfer girders, and podiums across 84+ cities in India.
4. **Strictly banned AI cliches**: Avoid "in today's fast-paced world", "delve into / dive deep", "game-changer / revolutionize", "it is crucial to note", "in conclusion".
5. **Content Format**: Valid semantic HTML inside the "content" JSON key. Use <h2>, <h3>, <p>, <ul>, <ol>, <li>, <strong>, <blockquote>. NEVER use <h1> or markdown (no #, ##, **, ```).

Output strictly a JSON object with this exact shape:
{
  "title": "Engaging, SEO-optimized title without quotes",
  "slug": "kebab-case-url-slug",
  "metaDescription": "140-160 character meta description with search intent",
  "content": "<h2>First Section</h2><p>Engineering body paragraph...</p>",
  "suggestedTags": ["tag1", "tag2", "tag3", "tag4"]
}
PROMPT;
    }

    protected function sanitizeHtml(string $html): string
    {
        // Remove markdown fence if model wrapped it
        $clean = preg_replace('/^```(?:html)?\s*/i', '', trim($html));
        $clean = preg_replace('/```$/', '', $clean);

        // Remove any h1 tags (the page title is h1)
        $clean = preg_replace('/<h1[^>]*>([\s\S]*?)<\/h1>/i', '<h2>$1</h2>', $clean);

        // Remove script or iframe tags for safety
        $clean = preg_replace('/<script\b[^>]*>([\s\S]*?)<\/script>/i', '', $clean);
        $clean = preg_replace('/<iframe\b[^>]*>([\s\S]*?)<\/iframe>/i', '', $clean);

        return trim($clean);
    }
}
