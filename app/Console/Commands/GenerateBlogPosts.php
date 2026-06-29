<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Services\ClaudeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

class GenerateBlogPosts extends Command
{
    protected $signature = 'blogify:generate-posts
        {--retry=1 : Times to retry on a JSON parse failure}
        {--market= : Generate a single market post for the session: open|close}';

    protected $description = 'Discover trending topics and generate comprehensive SEO blog posts via Claude AI.';

    public function handle(ClaudeService $claude): int
    {
        $market = $this->option('market');

        if ($market) {
            $this->info("📈 Generating the {$market}-of-market research post…");
            $prompt = $this->buildMarketPrompt($market);
        } else {
            $this->info('🤖 BlogBot autonomous publishing cycle starting…');
            $prompt = $this->buildLoopingPrompt();
        }

        $attempts = (int) $this->option('retry') + 1;
        $posts = null;

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $raw = $claude->generate($prompt);
                $posts = $this->parsePosts($raw);

                if (! empty($posts)) {
                    break;
                }

                $this->warn("Attempt {$attempt}: no valid posts parsed from response.");
            } catch (Throwable $e) {
                $this->error("Attempt {$attempt} failed: {$e->getMessage()}");
                logger()->error('blogify:generate-posts failed', ['attempt' => $attempt, 'exception' => $e]);
            }
        }

        if (empty($posts)) {
            $this->error('❌ Could not generate any posts after all attempts.');

            return self::FAILURE;
        }

        $created = 0;
        foreach ($posts as $post) {
            try {
                $this->persist($post, $market);
                $created++;
            } catch (Throwable $e) {
                $this->error("Skipping a post: {$e->getMessage()}");
            }
        }

        $this->info("✅ {$created} blog post(s) ".($market ? 'published' : 'scheduled')." successfully.");

        return self::SUCCESS;
    }

    private function persist(array $post, ?string $market = null): void
    {
        $slug = $this->uniqueSlug($post['slug'] ?? $post['title'] ?? Str::random(8));

        $html = $post['content_html'] ?? '';
        $wordCount = (int) ($post['word_count'] ?? 0);
        if ($wordCount < 1) {
            $wordCount = str_word_count(strip_tags(preg_replace('/\[IMAGE:.*?]/', '', $html)));
        }

        BlogPost::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => Str::limit($post['title'] ?? 'Untitled', 250, ''),
                'content' => $html,
                'meta_description' => Str::limit($post['meta_description'] ?? '', 200, ''),
                'excerpt' => Str::limit($post['excerpt'] ?? $post['meta_description'] ?? '', 300, ''),
                'focus_keyword' => $post['focus_keyword'] ?? null,
                'secondary_keywords' => $this->asArray($post['secondary_keywords'] ?? []),
                'category' => $post['category'] ?? ($market ? 'Share Market & Finance' : 'Trending'),
                'type' => $market ? 'market' : 'general',
                'tags' => $this->asArray($post['tags'] ?? []),
                'featured_image_alt' => $post['featured_image_alt'] ?? null,
                'featured_image_prompt' => $post['featured_image_prompt'] ?? null,
                'faq' => $this->asArray($post['faq'] ?? []),
                // Market posts are time-sensitive — publish immediately.
                'status' => $market ? 'published' : 'scheduled',
                'published_at' => $market ? now() : $this->parsePublishAt($post['scheduled_publish_at'] ?? null),
                'seo_score' => (int) ($post['seo_score_estimate'] ?? 0),
                'word_count' => $wordCount,
            ]
        );

        $verb = $market ? 'Published' : 'Scheduled';
        $this->line("  • {$verb}: ".($post['title'] ?? 'Untitled')."  [".($post['category'] ?? '?')."]");
    }

    /**
     * Pull a JSON array out of the model's raw text, tolerating stray
     * prose or code fences if the model added any.
     */
    private function parsePosts(string $raw): array
    {
        $raw = trim($raw);
        $raw = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $raw);

        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            // Fall back to the first bracketed JSON array in the text.
            if (preg_match('/\[\s*\{.*}\s*]/s', $raw, $m)) {
                $decoded = json_decode($m[0], true);
            }
        }

        if (! is_array($decoded)) {
            return [];
        }

        // Allow either a bare array or {"posts": [...]}.
        if (isset($decoded['posts']) && is_array($decoded['posts'])) {
            $decoded = $decoded['posts'];
        }

        return array_values(array_filter($decoded, 'is_array'));
    }

    private function uniqueSlug(string $base): string
    {
        $slug = Str::limit(Str::slug($base), 60, '');
        $slug = $slug !== '' ? $slug : Str::random(8);

        $original = $slug;
        $i = 2;
        while (BlogPost::where('slug', $slug)->whereDate('created_at', '!=', today())->exists()) {
            $slug = Str::limit($original, 55, '')."-{$i}";
            $i++;
        }

        return $slug;
    }

    private function parsePublishAt(?string $value): Carbon
    {
        try {
            // Normalize any offset (e.g. +05:30 from the prompt) to the app
            // timezone so it stores correctly — Eloquent persists the literal
            // wall-clock without converting, so the Carbon must already be in
            // the app tz for `published_at <= now()` comparisons to work.
            $when = $value ? Carbon::parse($value) : now();

            return $when->setTimezone(config('app.timezone', 'UTC'));
        } catch (Throwable) {
            return now();
        }
    }

    private function asArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return array_map('trim', explode(',', $value));
        }

        return [];
    }

    private function buildLoopingPrompt(): string
    {
        $now = now('Asia/Kolkata');
        $date = $now->toDateString();
        $time = $now->format('H:i');

        $recentSlugs = BlogPost::where('created_at', '>=', now()->subDays(7))
            ->pluck('title')
            ->implode('; ');
        $recentSlugs = $recentSlugs !== '' ? $recentSlugs : '(none yet)';

        $schedule = collect(range(0, 7))
            ->map(fn ($i) => $now->copy()->setTime(12, 30)->addHours($i * 2)->toIso8601String())
            ->implode("\n- ");

        return <<<PROMPT
SYSTEM: BlogBot Autonomous Publishing Cycle — Daily Execution

Today's Date: {$date}
Current Time (IST): {$time}
Execution Mode: FULLY AUTOMATIC — NO HUMAN REVIEW

STEP 1 — TOPIC DISCOVERY
Use the web_search tool to find today's 8 most trending topics across India and
globally — one per category (Share Market & Finance, Sports, IT & Technology,
Learning & Education, Management & Business, World Affairs & Trending News,
Health & Wellness, Startup & Entrepreneurship). Each must be trending in the last
12-24 hours, high search volume, with informational/commercial intent, and NOT
already covered by these recently published titles:
{$recentSlugs}

STEP 2 — RESEARCH each topic (facts, 3 key stats/data points, primary + secondary
keywords, content gap, best angle) using web_search.

STEP 3 — WRITE all 8 publish-ready articles. Each MUST be 1,600-2,600 words
(minimum 4-5 minute read), comprehensive, original, with inline [IMAGE: ...] markers,
a featured_image_prompt, a Key Takeaways list, and a 5-7 item FAQ — exactly per the
system prompt. Schedule them ~2 hours apart at these IST times (use these exact
values for scheduled_publish_at):
- {$schedule}

STEP 4 — Return ONLY a valid raw JSON array of all 8 post objects matching the
schema in the system prompt. No markdown fences, no text outside the JSON array.

START EXECUTION NOW.
PROMPT;
    }

    private function buildMarketPrompt(string $session): string
    {
        $now = now('Asia/Kolkata');
        $date = $now->toDateString();
        $time = $now->format('H:i');

        $context = $session === 'open'
            ? "The Indian markets have just OPENED for the day (NSE/BSE open at 9:15 AM IST). Write a pre-market / opening-bell briefing: how indices opened, pre-open data, global cues (US/Asia), key stocks in focus, sectors to watch, and what traders/investors should watch today."
            : "The Indian markets have just CLOSED for the day (NSE/BSE close at 3:30 PM IST). Write a market-wrap: where Sensex & Nifty closed (points and %), top gainers/losers, sectoral performance, FII/DII activity, rupee, what moved the market, and the outlook for tomorrow.";

        return <<<PROMPT
SYSTEM: Daily Market Coverage — {$session}-of-market edition

Today's Date: {$date}
Current Time (IST): {$time}
Execution Mode: FULLY AUTOMATIC — NO HUMAN REVIEW

{$context}

STEP 1 — RESEARCH with web_search NOW. Pull the latest REAL numbers: Sensex & Nifty
levels and % change, Bank Nifty, top gainers/losers, sector indices, FII/DII flows,
USD/INR, and major global indices. Cite the source type for each figure. Do NOT
fabricate any number — if a figure isn't found, omit it rather than guess.

STEP 2 — WRITE ONE comprehensive article (1,600-2,600 words, 5-6 min read) following
the system prompt's structure exactly: direct-answer opening, Key Takeaways list,
6-8 H2 sections, a data <table> of index levels, inline [IMAGE: ...] markers, a
featured_image_prompt, and a 5-7 item FAQ. Category MUST be "Share Market & Finance".
Make it the single best daily market read on the web so traders return every day.

STEP 3 — Return ONLY a valid raw JSON array containing exactly ONE post object
matching the system prompt schema. No markdown fences, no text outside the array.

START EXECUTION NOW.
PROMPT;
    }
}
