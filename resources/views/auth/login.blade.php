@extends('layouts.guest')
@section('title', 'Login')

@section('content')

    {{-- Session flash (e.g. password reset success) --}}
    @if (session('status'))
        <div class="alert alert-success py-2 mb-3">{{ session('status') }}</div>
    @endif

    {{-- Lockout Countdown Alert --}}
    @if (session('lockout_seconds'))
        <div class="alert alert-warning py-2 mb-3" id="lockout-alert">
            <i class="bi bi-hourglass-split me-1"></i>
            Account temporarily locked. Please wait <strong id="countdown-display">{{ session('lockout_seconds') }}</strong> seconds.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="you@hospital.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       required autocomplete="current-password" placeholder="••••••••">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Remember me + Forgot password --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label" for="remember_me">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth" id="login-submit-btn">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
    </form>

    {{-- Lockout Countdown Timer Script --}}
    @if (session('lockout_seconds'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let seconds = {{ (int) session('lockout_seconds') }};
            const display = document.getElementById('countdown-display');
            const submitBtn = document.getElementById('login-submit-btn');
            const alert = document.getElementById('lockout-alert');

            if (seconds > 0 && submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';

                const timer = setInterval(function () {
                    seconds--;
                    if (display) {
                        if (seconds >= 60) {
                            const mins = Math.floor(seconds / 60);
                            const secs = seconds % 60;
                            display.textContent = mins + 'm ' + (secs < 10 ? '0' : '') + secs + 's';
                        } else {
                            display.textContent = seconds;
                        }
                    }

                    if (seconds <= 0) {
                        clearInterval(timer);
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                        submitBtn.style.cursor = 'pointer';
                        if (alert) {
                            alert.classList.remove('alert-warning');
                            alert.classList.add('alert-success');
                            alert.innerHTML = '<i class="bi bi-check-circle me-1"></i> You may now try logging in again.';
                        }
                    }
                }, 1000);
            }
        });
    </script>
    @endif
@endsection
