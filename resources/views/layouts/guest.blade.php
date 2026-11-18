<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0b5cc4 0%, #083f88 100%); min-height: 100vh; }
        .login-card { max-width: 400px; width: 100%; border: 0; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,.25); }
        .login-card .brand { max-width: 220px; }
        .btn-git { background: #0b5cc4; color: #fff; }
        .btn-git:hover { background: #094ea6; color: #fff; }
        .otp-input { letter-spacing: 10px; font-size: 1.5rem; text-align: center; font-weight: 600; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
<div class="card login-card">
    <div class="card-body p-4">
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="GIT" class="img-fluid brand">
            <h1 class="h5 mt-3 mb-0">@yield('heading', 'Investment Bill Generator')</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success small">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger small">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>
</body>
</html>
