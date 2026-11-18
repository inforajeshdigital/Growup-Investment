<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --git-blue: #0b5cc4; --git-red: #c8102e; --git-green: #0f7a1c; }
        body { background: #f3f5f9; }
        .navbar-brand img { height: 34px; }
        .navbar { background: #fff; border-bottom: 3px solid var(--git-blue); }
        .navbar .nav-link.active { color: var(--git-blue); font-weight: 600; }
        .btn-git { background: var(--git-blue); color: #fff; }
        .btn-git:hover { background: #094ea6; color: #fff; }
        .card { border: 0; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .paper { background: #fff; max-width: 820px; margin: 0 auto; padding: 32px 40px; box-shadow: 0 2px 12px rgba(0,0,0,.12); }
        @media (max-width: 576px) { .paper { padding: 16px 12px; } .paper .rcpt .cname { font-size: 16px !important; } .paper .rcpt .logo { width: 90px !important; } }
        .form-label { font-weight: 600; font-size: .9rem; }
    </style>
    @stack('head')
</head>
<body>
<nav class="navbar navbar-expand-md">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="GIT">
            <span class="d-none d-sm-inline fw-semibold" style="color: var(--git-red);">Bill Generator</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('bills.create', 'bills.preview*') ? 'active' : '' }}" href="{{ route('bills.create') }}">Generate Bill</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('bills.index', 'bills.show') ? 'active' : '' }}" href="{{ route('bills.index') }}">All Bills</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}">Company Settings</a></li>
            </ul>
            <form method="POST" action="{{ route('logout') }}" class="d-flex">
                @csrf
                <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
