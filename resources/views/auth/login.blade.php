<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0b5cc4 0%, #083f88 100%); min-height: 100vh; }
        .login-card { max-width: 400px; width: 100%; border: 0; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,.25); }
        .login-card img { max-width: 220px; }
        .btn-git { background: #0b5cc4; color: #fff; }
        .btn-git:hover { background: #094ea6; color: #fff; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">
<div class="card login-card">
    <div class="card-body p-4">
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo.png') }}" alt="GIT" class="img-fluid">
            <h1 class="h5 mt-3 mb-0">Investment Bill Generator</h1>
        </div>

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button class="btn btn-git w-100">Login</button>
        </form>
    </div>
</div>
</body>
</html>
