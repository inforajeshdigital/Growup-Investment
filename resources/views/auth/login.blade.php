@extends('layouts.guest')

@section('title', 'Login')

@section('content')
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <a href="{{ route('password.forgot') }}" class="small">Forgot password?</a>
    </div>
    <button class="btn btn-git w-100">Login</button>
</form>
@endsection
