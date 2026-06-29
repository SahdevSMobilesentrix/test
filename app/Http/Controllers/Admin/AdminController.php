<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class AdminController extends Controller
{
    /* ---------------- Auth ---------------- */

    public function showLogin(): View|RedirectResponse
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.posts');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|string']);

        if (hash_equals((string) config('blog.admin_password'), (string) $request->input('password'))) {
            $request->session()->regenerate();
            $request->session()->put('admin_authenticated', true);

            return redirect()->intended(route('admin.posts'));
        }

        return back()->withErrors(['password' => 'Incorrect password.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authenticated');

        return redirect()->route('admin.login');
    }

    /* ---------------- Posts ---------------- */

    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'category' => $request->query('category', ''),
            'status' => $request->query('status', ''),
            'type' => $request->query('type', ''),
            'sort' => $request->query('sort', 'newest'),
        ];

        $posts = BlogPost::query()
            ->when($filters['q'] !== '', function ($x) use ($filters) {
                $x->where(function ($w) use ($filters) {
                    $w->where('title', 'like', "%{$filters['q']}%")
                        ->orWhere('focus_keyword', 'like', "%{$filters['q']}%")
                        ->orWhere('category', 'like', "%{$filters['q']}%");
                });
            })
            ->when($filters['category'] !== '', fn ($x) => $x->where('category', $filters['category']))
            ->when($filters['status'] !== '', fn ($x) => $x->where('status', $filters['status']))
            ->when($filters['type'] !== '', fn ($x) => $x->where('type', $filters['type']))
            ->when($filters['sort'] === 'oldest', fn ($x) => $x->orderBy('published_at'))
            ->when($filters['sort'] === 'views', fn ($x) => $x->orderByDesc('views'))
            ->when($filters['sort'] === 'seo', fn ($x) => $x->orderByDesc('seo_score'))
            ->when(! in_array($filters['sort'], ['oldest', 'views', 'seo']), fn ($x) => $x->orderByDesc('published_at')->orderByDesc('id'))
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => BlogPost::count(),
            'published' => BlogPost::where('status', 'published')->count(),
            'scheduled' => BlogPost::where('status', 'scheduled')->count(),
            'views' => (int) BlogPost::sum('views'),
        ];

        $categories = BlogPost::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.posts', compact('posts', 'filters', 'stats', 'categories'));
    }

    public function show(BlogPost $post): View
    {
        return view('admin.show', compact('post'));
    }

    public function updateStatus(Request $request, BlogPost $post): RedirectResponse
    {
        $request->validate(['status' => 'required|in:draft,scheduled,published']);

        $post->status = $request->input('status');
        if ($post->status === 'published' && ! $post->published_at) {
            $post->published_at = now();
        }
        $post->save();

        return back()->with('ok', "Status updated to {$post->status}.");
    }

    public function toggleFeatured(BlogPost $post): RedirectResponse
    {
        $post->update(['is_featured' => ! $post->is_featured]);

        return back()->with('ok', $post->is_featured ? 'Marked as featured.' : 'Removed from featured.');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()->route('admin.posts')->with('ok', 'Post deleted.');
    }

    public function generate(Request $request): RedirectResponse
    {
        $market = $request->input('market'); // null | open | close

        try {
            Artisan::call('blogify:generate-posts', array_filter([
                '--market' => in_array($market, ['open', 'close'], true) ? $market : null,
            ]));

            return back()->with('ok', 'Generation finished. '.trim(Artisan::output()));
        } catch (\Throwable $e) {
            return back()->withErrors(['generate' => 'Generation failed: '.$e->getMessage()]);
        }
    }
}
