@extends('layouts.guest')
@section('title', 'Forgot Password')

@section('content')
    <p class="text-muted small mb-4">Enter your email address and we will send you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success py-2 mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input id="email" type="email" name="email"
                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus placeholder="you@hospital.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" class="btn-auth">
            <i class="bi bi-send me-2"></i>Email Password Reset Link
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="small text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Back to Login
        </a>
    </div>
@endsection
