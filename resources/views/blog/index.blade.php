@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', ($category ?: ($q !== '' ? "Search: {$q}" : 'Latest stories')).' — Blogify')
@section('meta_description', 'In-depth, up-to-date articles on markets, sports, technology, business, health and more — researched and published daily.')

@section('content')
    @if ($isLanding && $hero)
        <section class="hero">
            <div class="hero-main">
                @if ($hero->featured_image_url)
                    <img src="{{ $hero->featured_image_url }}" alt="{{ $hero->featured_image_alt ?: $hero->title }}" fetchpriority="high">
                @endif
                <div class="ov">
                    <span class="kicker">{{ $hero->category }}</span>
                    <h1><a href="{{ route('blog.show', $hero) }}">{{ $hero->title }}</a></h1>
                    <div class="meta" style="color:#dfe6f2">
                        <span>{{ optional($hero->published_at)->format('M j, Y') }}</span>
                        <span class="dot">•</span><span>{{ $hero->read_time }} min read</span>
                    </div>
                </div>
            </div>
            <div class="hero-side">
                @foreach ($heroSide as $s)
                    <article>
                        <a href="{{ route('blog.show', $s) }}">
                            <img src="{{ $s->featured_image_url }}" alt="{{ $s->featured_image_alt ?: $s->title }}" loading="lazy">
                        </a>
                        <div>
                            <span class="kicker">{{ Str::before($s->category, ' &') }}</span>
                            <h3><a href="{{ route('blog.show', $s) }}">{{ $s->title }}</a></h3>
                            <div class="meta"><span>{{ $s->read_time }} min read</span></div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        @include('partials.ad', ['slotKey' => 'leaderboard', 'label' => 'leaderboard'])
    @endif

    <div class="layout">
        <div>
            <div class="section-head">
                <h2>
                    @if ($q !== '') Results for “{{ $q }}”
                    @elseif ($category) {{ $category }}
                    @else Latest stories @endif
                </h2>
                @if ($category || $q !== '')
                    <a href="{{ route('blog.index') }}" style="font-size:.85rem">Clear ✕</a>
                @endif
            </div>

            @forelse ($posts as $post)
                <article class="card" style="margin-top:18px">
                    <a class="thumb" href="{{ route('blog.show', $post) }}">
                        <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?: $post->title }}" loading="lazy">
                    </a>
                    <div class="body">
                        <span class="kicker">{{ $post->category }}</span>
                        <h3><a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a></h3>
                        <p>{{ Str::limit($post->summary, 150) }}</p>
                        <div class="meta">
                            <span>{{ optional($post->published_at)->format('M j, Y') }}</span>
                            <span class="dot">•</span><span>{{ $post->read_time }} min read</span>
                            @if ($post->seo_score)<span class="dot">•</span><span>SEO {{ $post->seo_score }}</span>@endif
                        </div>
                    </div>
                </article>

                @if ($loop->iteration === 4 && $posts->count() > 4)
                    @include('partials.ad', ['slotKey' => 'in_article', 'label' => 'in-feed'])
                @endif
            @empty
                <div class="empty">
                    <h2 style="font-family:var(--serif)">Nothing here yet</h2>
                    <p>Run <code>php artisan blogify:generate-posts</code> to publish today's stories,
                       or adjust your search.</p>
                </div>
            @endforelse

            {{ $posts->onEachSide(1)->links() }}
        </div>

        <aside class="sidebar">
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

            @include('partials.ad', ['slotKey' => 'sidebar', 'label' => 'sidebar', 'format' => 'rectangle'])

            @if ($categories->isNotEmpty())
                <div class="widget">
                    <h4>Explore topics</h4>
                    <div class="chip-row">
                        @foreach ($categories as $c)
                            <a href="{{ route('blog.index', ['category' => $c]) }}">{{ Str::before($c, ' &') }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>
    </div>
@endsection
