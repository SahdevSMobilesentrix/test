<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Blogify').' — Trending stories, markets & insights')</title>
    <meta name="description" content="@yield('meta_description', 'Daily in-depth stories across markets, sports, technology, business, health and more.')">
    <link rel="preconnect" href="https://image.pollinations.ai">
    @hasSection('canonical') <link rel="canonical" href="@yield('canonical')"> @endif
    @stack('head')
    @if (config('blog.adsense.client'))
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ config('blog.adsense.client') }}"
                crossorigin="anonymous"></script>
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --ink:#15171a; --body:#33373d; --muted:#6b7280; --line:#e8e8ea;
            --bg:#ffffff; --soft:#f7f7f8; --accent:#0b5cff; --accent-ink:#0a4ad1;
            --max:1180px; --radius:14px; --shadow:0 1px 2px rgba(16,24,40,.06),0 8px 24px rgba(16,24,40,.06);
            --serif:'Fraunces',Georgia,serif; --sans:'Inter',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;
        }
        *{box-sizing:border-box}
        html{-webkit-text-size-adjust:100%}
        body{margin:0;font-family:var(--sans);color:var(--body);background:var(--bg);line-height:1.7;font-size:17px}
        img{max-width:100%;height:auto;display:block}
        a{color:var(--accent);text-decoration:none}
        a:hover{color:var(--accent-ink)}
        .wrap{max-width:var(--max);margin:0 auto;padding:0 20px}

        /* Header */
        header.site{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.92);backdrop-filter:saturate(1.4) blur(8px);border-bottom:1px solid var(--line)}
        .topbar{display:flex;align-items:center;gap:16px;padding:14px 20px;max-width:var(--max);margin:0 auto}
        .brand{font-family:var(--serif);font-weight:700;font-size:1.55rem;letter-spacing:-.5px;color:var(--ink);white-space:nowrap}
        .brand b{color:var(--accent)}
        nav.cats{display:flex;gap:4px;overflow-x:auto;scrollbar-width:none;flex:1}
        nav.cats::-webkit-scrollbar{display:none}
        nav.cats a{font-size:.86rem;font-weight:500;color:var(--muted);padding:8px 12px;border-radius:9px;white-space:nowrap}
        nav.cats a:hover{background:var(--soft);color:var(--ink)}
        nav.cats a.active{background:var(--ink);color:#fff}
        .search-mini{margin-left:auto}
        .search-mini input{font-family:inherit;font-size:.86rem;border:1px solid var(--line);border-radius:10px;padding:8px 12px;width:180px;background:var(--soft)}
        .search-mini input:focus{outline:2px solid var(--accent);border-color:transparent}

        /* Ad slots */
        .ad-slot{margin:26px auto;text-align:center;max-width:100%}
        .ad-label{display:block;font-size:.62rem;letter-spacing:.12em;text-transform:uppercase;color:#aab;margin-bottom:6px}
        .ad-placeholder{display:flex;align-items:center;justify-content:center;min-height:96px;border:1px dashed #d6d8de;border-radius:12px;color:#aab;background:repeating-linear-gradient(45deg,#fafafb,#fafafb 12px,#f4f4f6 12px,#f4f4f6 24px);font-size:.8rem}
        .ad-leaderboard .ad-placeholder{min-height:120px}
        .ad-in_article{margin:34px 0}

        /* Hero */
        .hero{display:grid;grid-template-columns:1.5fr 1fr;gap:26px;margin:30px 0 10px}
        .hero-main{position:relative;border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);min-height:380px;display:flex;align-items:flex-end;background:#111}
        .hero-main img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.86}
        .hero-main .ov{position:relative;padding:26px;background:linear-gradient(to top,rgba(0,0,0,.82),rgba(0,0,0,.05));color:#fff;width:100%}
        .hero-main h1{font-family:var(--serif);font-size:2rem;line-height:1.15;margin:.3rem 0;color:#fff}
        .hero-main h1 a{color:#fff}
        .hero-side{display:flex;flex-direction:column;gap:16px}
        .hero-side article{display:grid;grid-template-columns:96px 1fr;gap:12px;align-items:center}
        .hero-side img{width:96px;height:72px;object-fit:cover;border-radius:10px}
        .hero-side h3{font-family:var(--serif);font-size:1.02rem;line-height:1.25;margin:0}
        .hero-side h3 a{color:var(--ink)}

        .kicker{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--accent)}
        .hero-main .kicker{color:#7fb0ff}
        .meta{font-size:.8rem;color:var(--muted);display:flex;gap:8px;flex-wrap:wrap;align-items:center}
        .meta .dot{color:#cbd0d8}

        /* Layout with sidebar */
        .layout{display:grid;grid-template-columns:1fr 320px;gap:40px;margin:34px 0}
        .section-head{display:flex;align-items:baseline;justify-content:space-between;border-bottom:2px solid var(--ink);padding-bottom:8px;margin:0 0 8px}
        .section-head h2{font-family:var(--serif);font-size:1.3rem;margin:0;color:var(--ink)}

        /* Cards grid */
        .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:26px}
        article.card{border:1px solid var(--line);border-radius:var(--radius);overflow:hidden;background:#fff;transition:transform .15s ease,box-shadow .15s ease;display:flex;flex-direction:column}
        article.card:hover{transform:translateY(-3px);box-shadow:var(--shadow)}
        article.card .thumb{aspect-ratio:16/9;overflow:hidden;background:var(--soft)}
        article.card .thumb img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
        article.card:hover .thumb img{transform:scale(1.04)}
        article.card .body{padding:16px 18px 18px;display:flex;flex-direction:column;gap:8px;flex:1}
        article.card h3{font-family:var(--serif);font-size:1.18rem;line-height:1.25;margin:0}
        article.card h3 a{color:var(--ink)}
        article.card p{margin:0;color:var(--body);font-size:.93rem}
        article.card .meta{margin-top:auto}

        /* Sidebar */
        aside.sidebar{display:flex;flex-direction:column;gap:24px}
        .widget{border:1px solid var(--line);border-radius:var(--radius);padding:18px}
        .widget h4{font-family:var(--serif);font-size:1.05rem;margin:0 0 12px;color:var(--ink)}
        .trend-list{list-style:none;margin:0;padding:0;counter-reset:t}
        .trend-list li{counter-increment:t;display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--line)}
        .trend-list li:last-child{border-bottom:0}
        .trend-list li::before{content:counter(t);font-family:var(--serif);font-weight:700;color:var(--accent);font-size:1.1rem;line-height:1.2}
        .trend-list a{color:var(--ink);font-weight:500;font-size:.92rem;line-height:1.3}
        .chip-row{display:flex;flex-wrap:wrap;gap:8px}
        .chip-row a{font-size:.8rem;padding:5px 11px;border:1px solid var(--line);border-radius:999px;color:var(--muted)}
        .chip-row a:hover{background:var(--ink);color:#fff;border-color:var(--ink)}

        /* Article page */
        .article-head{max-width:760px;margin:34px auto 0}
        .article-head h1{font-family:var(--serif);font-size:2.4rem;line-height:1.12;color:var(--ink);margin:.4rem 0 .6rem;letter-spacing:-.5px}
        .article-head .dek{font-size:1.15rem;color:var(--muted)}
        .article-hero{max-width:980px;margin:24px auto 0;border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
        .article-hero img{width:100%;aspect-ratio:1200/630;object-fit:cover}
        .article-hero figcaption{font-size:.78rem;color:var(--muted);padding:8px 4px;text-align:center}
        .post-grid{display:grid;grid-template-columns:minmax(0,760px) 300px;gap:48px;justify-content:center;margin:26px auto;max-width:1140px}
        .post-grid > .sidebar{position:sticky;top:80px;align-self:start;height:fit-content}
        .post-body{font-size:1.12rem;color:#23262b;overflow-wrap:break-word}
        .post-body h2{font-family:var(--serif);font-size:1.6rem;margin:1.9em 0 .5em;color:var(--ink);line-height:1.2}
        .post-body h3{font-family:var(--serif);font-size:1.25rem;margin:1.5em 0 .4em;color:var(--ink)}
        .post-body p{margin:0 0 1.1em}
        .post-body ul,.post-body ol{margin:0 0 1.2em;padding-left:1.3em}
        .post-body li{margin:.4em 0}
        .post-body strong{color:var(--ink)}
        .post-body blockquote{margin:1.4em 0;padding:.6em 1.2em;border-left:4px solid var(--accent);background:var(--soft);border-radius:0 10px 10px 0;font-style:italic}
        .post-body table{width:100%;border-collapse:collapse;margin:1.4em 0;font-size:.95rem}
        .post-body th,.post-body td{border:1px solid var(--line);padding:10px 12px;text-align:left}
        .post-body thead th{background:var(--soft);font-weight:600;color:var(--ink)}
        .post-body .article-figure{margin:1.8em 0}
        .post-body .article-figure img{width:100%;border-radius:var(--radius);box-shadow:var(--shadow)}
        .post-body .article-figure figcaption{font-size:.82rem;color:var(--muted);text-align:center;margin-top:8px}

        .takeaways{background:var(--soft);border:1px solid var(--line);border-radius:var(--radius);padding:18px 22px;margin:24px 0}
        .takeaways h2{margin:.1em 0 .4em;font-size:1.2rem}

        .tagline-tags{display:flex;flex-wrap:wrap;gap:8px;margin:30px 0}
        .tagline-tags a{font-size:.82rem;background:var(--soft);border:1px solid var(--line);border-radius:8px;padding:4px 11px;color:var(--muted)}
        .faq{margin:42px 0;border-top:1px solid var(--line);padding-top:8px}
        .faq h2{font-family:var(--serif);font-size:1.5rem}
        .faq details{border-bottom:1px solid var(--line);padding:14px 0}
        .faq summary{cursor:pointer;font-weight:600;color:var(--ink);font-size:1.05rem;list-style:none;display:flex;justify-content:space-between;gap:12px}
        .faq summary::after{content:'+';color:var(--accent);font-weight:700}
        .faq details[open] summary::after{content:'–'}
        .faq details p{margin:.7em 0 0;color:var(--body)}
        .related{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:14px}
        .related a{color:var(--ink);font-family:var(--serif);font-weight:600;line-height:1.25}
        .related .thumb{aspect-ratio:16/9;border-radius:10px;overflow:hidden;margin-bottom:8px;background:var(--soft)}
        .related .thumb img{width:100%;height:100%;object-fit:cover}

        .pagination{display:flex;gap:8px;list-style:none;padding:0;margin:34px 0;flex-wrap:wrap;justify-content:center}
        .pagination a,.pagination span{padding:8px 14px;border:1px solid var(--line);border-radius:10px;background:#fff;color:var(--ink);font-size:.9rem}
        .pagination .active span{background:var(--ink);color:#fff;border-color:var(--ink)}
        .pagination [aria-disabled] span{color:#c4c8d0}

        footer.site{border-top:1px solid var(--line);margin-top:50px;padding:34px 0;color:var(--muted);font-size:.88rem;background:var(--soft)}
        footer.site .cols{display:flex;justify-content:space-between;flex-wrap:wrap;gap:20px}
        footer.site a{color:var(--body)}
        .empty{padding:80px 20px;text-align:center;color:var(--muted)}
        .empty code{background:var(--soft);padding:2px 8px;border-radius:6px}
        .back{font-size:.88rem;display:inline-block;margin:20px 0 0}

        @media(max-width:900px){
            .hero{grid-template-columns:1fr}
            .layout{grid-template-columns:1fr}
            aside.sidebar{order:2}
            .post-grid{grid-template-columns:1fr;gap:0}
            .post-grid .sidebar{display:none}
            .related{grid-template-columns:1fr 1fr}
        }
        @media(max-width:640px){
            body{font-size:16px}
            .grid{grid-template-columns:1fr}
            .hero-main{min-height:300px}
            .hero-main h1{font-size:1.55rem}
            .article-head h1{font-size:1.85rem}
            .post-body{font-size:1.06rem}
            .search-mini{display:none}
            .related{grid-template-columns:1fr}
            .topbar{flex-wrap:wrap}
        }
    </style>
</head>
<body>
    <header class="site">
        <div class="topbar">
            <a href="{{ route('blog.index') }}" class="brand">Blog<b>ify</b></a>
            <nav class="cats">
                <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.index') && !request('category') && !request('q') ? 'active' : '' }}">Home</a>
                @foreach (\App\Models\BlogPost::navCategories() as $c)
                    <a href="{{ route('blog.index', ['category' => $c]) }}" class="{{ request('category') === $c ? 'active' : '' }}">{{ \Illuminate\Support\Str::before($c, ' &') }}</a>
                @endforeach
            </nav>
            <form class="search-mini" method="GET" action="{{ route('blog.index') }}">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search stories…" aria-label="Search">
            </form>
        </div>
    </header>

    <main class="wrap">
        @yield('content')
    </main>

    <footer class="site">
        <div class="wrap cols">
            <div><strong style="font-family:var(--serif);color:var(--ink)">Blogify</strong> — trending stories, markets &amp; insights, updated daily.</div>
            <div>&copy; {{ date('Y') }} Blogify. All rights reserved.</div>
        </div>
    </footer>
</body>
</html>
