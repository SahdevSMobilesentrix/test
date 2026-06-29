@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', Str::limit($post->title, 60))
@section('meta_description', $post->summary)
@section('canonical', route('blog.show', $post))

@push('head')
    @if ($post->focus_keyword)
        <meta name="keywords" content="{{ collect([$post->focus_keyword])->merge($post->secondary_keywords ?? [])->implode(', ') }}">
    @endif
    {{-- Article + FAQ structured data for Google rich results & AI search --}}
    <script type="application/ld+json">
    {!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $post->title,
        'description' => $post->summary,
        'image' => $post->featured_image_url ? [$post->featured_image_url] : null,
        'datePublished' => optional($post->published_at)->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'articleSection' => $post->category,
        'keywords' => collect([$post->focus_keyword])->merge($post->secondary_keywords ?? [])->filter()->implode(', '),
        'author' => ['@type' => 'Organization', 'name' => config('app.name', 'Blogify')],
        'publisher' => ['@type' => 'Organization', 'name' => config('app.name', 'Blogify')],
        'mainEntityOfPage' => route('blog.show', $post),
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @if (!empty($post->faq))
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($post->faq)->map(fn ($f) => [
            '@type' => 'Question',
            'name' => $f['question'] ?? '',
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['answer'] ?? ''],
        ])->values(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif
@endpush

@section('content')
    <div class="article-head">
        <span class="kicker">{{ $post->category }}</span>
        <h1>{{ $post->title }}</h1>
        <p class="dek">{{ $post->summary }}</p>
        <div class="meta">
            <span>{{ optional($post->published_at)->format('M j, Y') }}</span>
            <span class="dot">•</span><span>{{ $post->read_time }} min read</span>
            @if ($post->word_count)<span class="dot">•</span><span>{{ number_format($post->word_count) }} words</span>@endif
        </div>
    </div>

    @if ($post->featured_image_url)
        <figure class="article-hero">
            <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" fetchpriority="high">
            @if ($post->featured_image_alt)<figcaption>{{ $post->featured_image_alt }}</figcaption>@endif
        </figure>
    @endif

    <div class="post-grid">
        <article class="post-body">
            {{-- A single in-article ad is auto-placed in the middle by ArticleRenderer --}}
            {!! $post->rendered_content !!}

            @if (!empty($post->tags))
                <div class="tagline-tags">
                    @foreach ($post->tags as $tag)
                        <a href="{{ route('blog.index', ['q' => $tag]) }}">#{{ $tag }}</a>
                    @endforeach
                </div>
            @endif

            @if (!empty($post->faq))
                <section class="faq">
                    <h2>Frequently Asked Questions</h2>
                    @foreach ($post->faq as $item)
                        <details>
                            <summary>{{ $item['question'] ?? '' }}</summary>
                            <p>{{ $item['answer'] ?? '' }}</p>
                        </details>
                    @endforeach
                </section>
            @endif

            @if ($related->isNotEmpty())
                <section class="faq" style="border-top:1px solid var(--line)">
                    <h2>More in {{ Str::before($post->category, ' &') }}</h2>
                    <div class="related">
                        @foreach ($related as $r)
                            <a href="{{ route('blog.show', $r) }}">
                                <span class="thumb"><img src="{{ $r->featured_image_url }}" alt="{{ $r->title }}" loading="lazy"></span>
                                {{ $r->title }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <a class="back" href="{{ route('blog.index') }}">← Back to all stories</a>
        </article>

        <aside class="sidebar">
            @include('partials.ad', ['slotKey' => 'sidebar', 'label' => 'sidebar', 'format' => 'rectangle'])

            @if ($trending->isNotEmpty())
                <div class="widget">
                    <h4>🔥 Trending now</h4>
                    <ol class="trend-list">
                        @foreach ($trending as $t)
                            <li><a href="{{ route('blog.show', $t) }}">{{ $t->title }}</a></li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </aside>
    </div>
@endsection
