@extends('admin.layout')
@section('title', 'Posts')

@php use Illuminate\Support\Str; @endphp

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px">
        <h1 style="margin:0;font-size:1.5rem">Posts</h1>
        <div class="actions">
            <form method="POST" action="{{ route('admin.generate') }}" class="inline" onsubmit="this.querySelector('button').innerText='Generating… (may take a while)';this.querySelector('button').disabled=true">
                @csrf
                <button class="btn btn-primary">⚡ Generate today's posts</button>
            </form>
            <form method="POST" action="{{ route('admin.generate') }}" class="inline">
                @csrf <input type="hidden" name="market" value="close">
                <button class="btn">📈 Market wrap</button>
            </form>
        </div>
    </div>

    <div class="stats">
        <div class="stat"><div class="n">{{ number_format($stats['total']) }}</div><div class="l">Total posts</div></div>
        <div class="stat"><div class="n">{{ number_format($stats['published']) }}</div><div class="l">Published</div></div>
        <div class="stat"><div class="n">{{ number_format($stats['scheduled']) }}</div><div class="l">Scheduled</div></div>
        <div class="stat"><div class="n">{{ number_format($stats['views']) }}</div><div class="l">Total views</div></div>
    </div>

    <form method="GET" action="{{ route('admin.posts') }}" class="filters">
        <div class="f">
            <label>Search</label>
            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Title, keyword, category…">
        </div>
        <div class="f">
            <label>Category</label>
            <select name="category">
                <option value="">All</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" @selected($filters['category'] === $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div class="f">
            <label>Status</label>
            <select name="status">
                @foreach (['' => 'All', 'published' => 'Published', 'scheduled' => 'Scheduled', 'draft' => 'Draft'] as $v => $l)
                    <option value="{{ $v }}" @selected($filters['status'] === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="f">
            <label>Type</label>
            <select name="type">
                @foreach (['' => 'All', 'general' => 'General', 'market' => 'Market'] as $v => $l)
                    <option value="{{ $v }}" @selected($filters['type'] === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="f">
            <label>Sort</label>
            <select name="sort">
                @foreach (['newest' => 'Newest', 'oldest' => 'Oldest', 'views' => 'Most viewed', 'seo' => 'Best SEO'] as $v => $l)
                    <option value="{{ $v }}" @selected($filters['sort'] === $v)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Filter</button>
        <a class="btn" href="{{ route('admin.posts') }}">Reset</a>
    </form>

    <table>
        <thead>
            <tr><th>Title</th><th>Category</th><th>Status</th><th>Publish</th><th>Views</th><th>SEO</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr>
                    <td>
                        <a href="{{ route('admin.posts.show', $post) }}" style="font-weight:600;color:var(--ink)">{{ Str::limit($post->title, 64) }}</a>
                        @if ($post->type === 'market')<span class="badge market" style="margin-left:6px">MARKET</span>@endif
                        @if ($post->is_featured)<span title="featured">⭐</span>@endif
                        <div class="muted" style="font-size:.78rem">{{ $post->read_time }} min · {{ number_format($post->word_count) }} words</div>
                    </td>
                    <td class="muted">{{ Str::before($post->category, ' &') }}</td>
                    <td><span class="badge {{ $post->status }}">{{ $post->status }}</span></td>
                    <td class="muted">{{ optional($post->published_at)->format('M j, H:i') }}</td>
                    <td>{{ number_format($post->views) }}</td>
                    <td>{{ $post->seo_score ?: '—' }}</td>
                    <td>
                        <div class="actions">
                            @if ($post->status === 'published')
                                <a class="btn btn-sm" href="{{ route('blog.show', $post) }}" target="_blank">View</a>
                                <form method="POST" action="{{ route('admin.posts.status', $post) }}" class="inline">@csrf @method('PATCH')
                                    <input type="hidden" name="status" value="draft">
                                    <button class="btn btn-sm">Unpublish</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.posts.status', $post) }}" class="inline">@csrf @method('PATCH')
                                    <input type="hidden" name="status" value="published">
                                    <button class="btn btn-sm btn-primary">Publish</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Delete this post?')">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted" style="text-align:center;padding:30px">No posts match these filters.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $posts->onEachSide(1)->links() }}
@endsection
