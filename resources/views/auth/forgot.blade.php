@extends('layouts.guest')

@section('title', 'Forgot Password')
@section('heading', 'Forgot Password')

@section('content')
<p class="text-muted small text-center">Enter your login email. We will send a 6-digit OTP to reset your password.</p>

<form method="POST" action="{{ route('password.send-otp') }}" onsubmit="this.querySelector('button').disabled = true;">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="email">Login Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <button class="btn btn-git w-100">Send OTP</button>
</form>

<div class="text-center mt-3"><a href="{{ route('login') }}" class="small">Back to login</a></div>
@endsection
