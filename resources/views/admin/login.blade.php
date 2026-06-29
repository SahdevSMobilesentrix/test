@extends('admin.layout')
@section('title', 'Sign in')

@section('content')
    <div style="max-width:380px;margin:8vh auto">
        <div class="card">
            <h1 style="margin:0 0 4px;font-size:1.4rem">Blogify Admin</h1>
            <p class="muted" style="margin:0 0 18px">Sign in to manage posts.</p>
            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <label style="font-size:.78rem;font-weight:600;color:var(--muted)">Password</label>
                <input type="password" name="password" autofocus required
                       style="width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:9px;margin:6px 0 16px;font-size:.95rem">
                <button class="btn btn-primary" style="width:100%">Sign in</button>
            </form>
        </div>
        <p class="muted" style="text-align:center;margin-top:14px;font-size:.8rem">
            Set <code>ADMIN_PASSWORD</code> in your <code>.env</code> file.
        </p>
    </div>
@endsection
