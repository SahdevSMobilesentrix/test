<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');
        $q = trim((string) $request->query('q', ''));

        $query = BlogPost::live()
            ->when($category, fn ($x) => $x->where('category', $category))
            ->when($q !== '', function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('title', 'like', "%{$q}%")
                        ->orWhere('excerpt', 'like', "%{$q}%")
                        ->orWhere('meta_description', 'like', "%{$q}%")
                        ->orWhere('focus_keyword', 'like', "%{$q}%")
                        ->orWhere('content', 'like', "%{$q}%");
                });
            });

        $posts = $query->paginate(8)->withQueryString();

        // Hero block only on the unfiltered landing page.
        $isLanding = ! $category && $q === '' && $posts->currentPage() === 1;
        $hero = $isLanding ? BlogPost::live()->first() : null;
        $heroSide = $isLanding
            ? BlogPost::live()->whereKeyNot(optional($hero)->getKey())->limit(3)->get()
            : collect();

        $trending = BlogPost::trending()->limit(6)->get();
        $categories = BlogPost::navCategories();

        return view('blog.index', compact(
            'posts', 'categories', 'category', 'q', 'hero', 'heroSide', 'trending', 'isLanding'
        ));
    }

    public function show(BlogPost $post): View
    {
        abort_if($post->status !== 'published', 404);

        $post->increment('views');

        $related = BlogPost::live()
            ->where('category', $post->category)
            ->whereKeyNot($post->getKey())
            ->limit(3)
            ->get();

        $trending = BlogPost::trending()
            ->whereKeyNot($post->getKey())
            ->limit(6)
            ->get();

        return view('blog.show', compact('post', 'related', 'trending'));
    }
}
