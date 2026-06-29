<?php

namespace App\Models;

use App\Support\ArticleRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_description',
        'excerpt',
        'focus_keyword',
        'secondary_keywords',
        'category',
        'type',
        'tags',
        'featured_image_alt',
        'featured_image_prompt',
        'faq',
        'status',
        'is_featured',
        'published_at',
        'seo_score',
        'word_count',
        'views',
    ];

    protected $casts = [
        'secondary_keywords' => 'array',
        'tags' => 'array',
        'faq' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'seo_score' => 'integer',
        'word_count' => 'integer',
        'views' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Final article HTML with images + ad units injected. */
    public function getRenderedContentAttribute(): string
    {
        return ArticleRenderer::render($this->content ?? '');
    }

    /** Featured hero image URL (keyless AI image from the stored prompt). */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (! config('blog.images.enabled')) {
            return null;
        }

        $prompt = $this->featured_image_prompt
            ?: $this->featured_image_alt
            ?: ($this->title.' '.$this->category);

        return ArticleRenderer::imageUrl($prompt, 1200, 630);
    }

    public function getReadTimeAttribute(): int
    {
        $wpm = max(1, (int) config('blog.words_per_minute', 200));
        $words = $this->word_count ?: str_word_count(strip_tags($this->content ?? ''));

        return max(1, (int) ceil($words / $wpm));
    }

    public function getSummaryAttribute(): string
    {
        return $this->excerpt
            ?: $this->meta_description
            ?: \Illuminate\Support\Str::limit(strip_tags($this->content ?? ''), 160);
    }

    /**
     * Posts that are live: status published, or scheduled with a past publish time.
     */
    public function scopeLive(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->orderByDesc('published_at');
    }

    /**
     * Scheduled posts whose publish time has arrived and should now go live.
     */
    public function scopeDue(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /** Trending = most viewed in the recent window, newest as tiebreak. */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->orderByDesc('views')
            ->orderByDesc('published_at');
    }

    /** Distinct published categories for the nav (cached per request). */
    public static function navCategories(): \Illuminate\Support\Collection
    {
        return static::query()
            ->where('status', 'published')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }
}
