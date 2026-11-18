@extends('layouts.guest')

@section('title', 'Reset Password')
@section('heading', 'Reset Password')

@section('content')
<p class="text-muted small text-center">Enter the OTP sent to <strong>{{ $email }}</strong> and choose a new password.</p>

<form method="POST" action="{{ route('password.reset') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label" for="otp">OTP</label>
        <input type="text" id="otp" name="otp" inputmode="numeric" maxlength="6" autocomplete="one-time-code" class="form-control otp-input @error('otp') is-invalid @enderror" required autofocus>
        @error('otp')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">New Password</label>
        <input type="password" id="password" name="password" autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">At least 8 characters.</div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="password_confirmation">Confirm New Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="form-control" required>
    </div>
    <button class="btn btn-git w-100">Reset Password</button>
</form>

<div class="d-flex justify-content-between mt-3">
    <form method="POST" action="{{ route('password.send-otp') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <button class="btn btn-link btn-sm p-0">Resend OTP</button>
    </form>
    <a href="{{ route('login') }}" class="small">Back to login</a>
</div>
@endsection
