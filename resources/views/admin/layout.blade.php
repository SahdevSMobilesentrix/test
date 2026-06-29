<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · Blogify</title>
    <style>
        :root{--ink:#0f172a;--muted:#64748b;--line:#e2e8f0;--bg:#f1f5f9;--card:#fff;--accent:#0b5cff;--ok:#16a34a;--warn:#d97706;--danger:#dc2626}
        *{box-sizing:border-box}
        body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:var(--bg);color:var(--ink);font-size:14px}
        a{color:var(--accent);text-decoration:none}
        .topbar{background:var(--ink);color:#fff;padding:12px 22px;display:flex;align-items:center;gap:18px;position:sticky;top:0;z-index:10}
        .topbar .brand{font-weight:700;font-size:1.1rem}
        .topbar .brand b{color:#7fb0ff}
        .topbar nav{display:flex;gap:16px;flex:1}
        .topbar nav a{color:#cbd5e1;font-weight:500}
        .topbar nav a:hover{color:#fff}
        .topbar form{margin:0}
        .btn{display:inline-block;border:1px solid var(--line);background:#fff;color:var(--ink);padding:7px 13px;border-radius:8px;font-size:.85rem;cursor:pointer;font-weight:500}
        .btn:hover{background:#f8fafc}
        .btn-primary{background:var(--accent);border-color:var(--accent);color:#fff}
        .btn-primary:hover{background:#0a4ad1}
        .btn-danger{color:var(--danger);border-color:#fecaca}
        .btn-sm{padding:5px 10px;font-size:.78rem}
        .wrap{max-width:1180px;margin:24px auto;padding:0 20px}
        .flash{padding:12px 16px;border-radius:10px;margin-bottom:18px;font-weight:500}
        .flash.ok{background:#dcfce7;color:#166534}
        .flash.err{background:#fee2e2;color:#991b1b}
        .card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:18px}
        .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
        .stat{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:16px 18px}
        .stat .n{font-size:1.7rem;font-weight:700}
        .stat .l{color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:.05em}
        table{width:100%;border-collapse:collapse;background:var(--card);border:1px solid var(--line);border-radius:14px;overflow:hidden}
        th,td{padding:11px 14px;text-align:left;border-bottom:1px solid var(--line);vertical-align:middle}
        th{background:#f8fafc;font-size:.74rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted)}
        tr:last-child td{border-bottom:0}
        .badge{display:inline-block;padding:2px 9px;border-radius:999px;font-size:.72rem;font-weight:600}
        .badge.published{background:#dcfce7;color:#166534}
        .badge.scheduled{background:#fef3c7;color:#92400e}
        .badge.draft{background:#e2e8f0;color:#475569}
        .badge.market{background:#dbeafe;color:#1e40af}
        .filters{display:flex;flex-wrap:wrap;gap:10px;align-items:end;margin-bottom:18px;background:var(--card);border:1px solid var(--line);border-radius:14px;padding:16px}
        .filters .f{display:flex;flex-direction:column;gap:4px}
        .filters label{font-size:.72rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.04em}
        .filters input,.filters select{font-family:inherit;font-size:.86rem;padding:8px 10px;border:1px solid var(--line);border-radius:8px;background:#fff}
        .filters input[type=search]{min-width:220px}
        .muted{color:var(--muted)}
        .actions{display:flex;gap:6px;flex-wrap:wrap}
        .pagination{display:flex;gap:6px;list-style:none;padding:0;margin:18px 0;flex-wrap:wrap}
        .pagination a,.pagination span{padding:6px 11px;border:1px solid var(--line);border-radius:8px;background:#fff}
        .pagination .active span{background:var(--ink);color:#fff;border-color:var(--ink)}
        .inline{display:inline}
        @media(max-width:720px){.stats{grid-template-columns:repeat(2,1fr)}table{font-size:.8rem}.topbar nav{display:none}}
    </style>
</head>
<body>
    @if (!request()->routeIs('admin.login'))
    <div class="topbar">
        <span class="brand">Blog<b>ify</b> · Admin</span>
        <nav>
            <a href="{{ route('admin.posts') }}">Posts</a>
            <a href="{{ route('blog.index') }}" target="_blank">View site ↗</a>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf
            <button class="btn btn-sm">Log out</button>
        </form>
    </div>
    @endif

    <div class="wrap">
        @if (session('ok'))<div class="flash ok">{{ session('ok') }}</div>@endif
        @if ($errors->any())<div class="flash err">{{ $errors->first() }}</div>@endif
        @yield('content')
    </div>
</body>
</html>
