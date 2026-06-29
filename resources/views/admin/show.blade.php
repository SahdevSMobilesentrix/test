@extends('admin.layout')
@section('title', 'Edit · '.$post->title)

@php use Illuminate\Support\Str; @endphp

@section('content')
    <a href="{{ route('admin.posts') }}" class="muted">← All posts</a>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:22px;margin-top:14px">
        <div class="card">
            <div style="display:flex;gap:8px;align-items:center;margin-bottom:6px">
                <span class="badge {{ $post->status }}">{{ $post->status }}</span>
                <span class="muted">{{ $post->category }}</span>
                @if ($post->type === 'market')<span class="badge market">MARKET</span>@endif
            </div>
            <h1 style="margin:.2em 0;font-size:1.5rem">{{ $post->title }}</h1>
            <p class="muted">{{ $post->summary }}</p>
            @if ($post->featured_image_url)
                <img src="{{ $post->featured_image_url }}" alt="" style="width:100%;border-radius:12px;margin:12px 0">
            @endif
            <div style="border-top:1px solid var(--line);padding-top:14px;line-height:1.7">
                {!! $post->rendered_content !!}
            </div>
        </div>

        <aside style="display:flex;flex-direction:column;gap:16px">
            <div class="card">
                <h3 style="margin:0 0 12px;font-size:1rem">Manage</h3>
                <form method="POST" action="{{ route('admin.posts.status', $post) }}">@csrf @method('PATCH')
                    <label class="muted" style="font-size:.78rem;font-weight:600">Status</label>
                    <select name="status" style="width:100%;padding:8px;border:1px solid var(--line);border-radius:8px;margin:6px 0 10px">
                        @foreach (['published','scheduled','draft'] as $s)
                            <option value="{{ $s }}" @selected($post->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary" style="width:100%">Update status</button>
                </form>
                <form method="POST" action="{{ route('admin.posts.featured', $post) }}" style="margin-top:10px">@csrf @method('PATCH')
                    <button class="btn" style="width:100%">{{ $post->is_featured ? '★ Unfeature' : '☆ Mark featured' }}</button>
                </form>
                @if ($post->status === 'published')
                    <a class="btn" href="{{ route('blog.show', $post) }}" target="_blank" style="width:100%;text-align:center;margin-top:10px">View live ↗</a>
                @endif
                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" style="margin-top:10px" onsubmit="return confirm('Delete this post permanently?')">@csrf @method('DELETE')
                    <button class="btn btn-danger" style="width:100%">Delete post</button>
                </form>
            </div>

            <div class="card">
                <h3 style="margin:0 0 12px;font-size:1rem">SEO &amp; meta</h3>
                <table style="border:0">
                    <tr><td class="muted" style="border:0;padding:4px 0">Focus keyword</td><td style="border:0;padding:4px 0">{{ $post->focus_keyword ?: '—' }}</td></tr>
                    <tr><td class="muted" style="border:0;padding:4px 0">SEO score</td><td style="border:0;padding:4px 0">{{ $post->seo_score ?: '—' }}/100</td></tr>
                    <tr><td class="muted" style="border:0;padding:4px 0">Read time</td><td style="border:0;padding:4px 0">{{ $post->read_time }} min</td></tr>
                    <tr><td class="muted" style="border:0;padding:4px 0">Words</td><td style="border:0;padding:4px 0">{{ number_format($post->word_count) }}</td></tr>
                    <tr><td class="muted" style="border:0;padding:4px 0">Views</td><td style="border:0;padding:4px 0">{{ number_format($post->views) }}</td></tr>
                    <tr><td class="muted" style="border:0;padding:4px 0">Slug</td><td style="border:0;padding:4px 0;word-break:break-all">{{ $post->slug }}</td></tr>
                </table>
                @if (!empty($post->tags))
                    <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:6px">
                        @foreach ($post->tags as $t)<span class="badge draft">#{{ $t }}</span>@endforeach
                    </div>
                @endif
            </div>
        </aside>
    </div>
@endsection
