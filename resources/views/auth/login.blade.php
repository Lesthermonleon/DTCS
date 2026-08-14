@extends('layouts.guest')
@section('title', 'Login')

@section('content')

    {{-- Session flash (e.g. password reset success) --}}
    @if (session('status'))
        <div class="alert alert-success py-2 mb-3">{{ session('status') }}</div>
    @endif

    {{-- Lockout Countdown Alert --}}
    @if (session('lockout_seconds'))
        @php $lockoutSeconds = (int) session('lockout_seconds'); @endphp
        <div class="alert alert-warning py-2 mb-3" id="lockout-alert" data-seconds="{{ $lockoutSeconds }}">
            <i class="bi bi-hourglass-split me-1"></i>
            Account temporarily locked. Please wait <strong id="countdown-display">{{ $lockoutSeconds }}</strong> seconds.
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
                       value="{{ old('email', request()->cookie('remember_hims_email')) }}" required autofocus autocomplete="username"
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
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me" value="1" {{ old('remember', request()->cookie('remember_hims_email') ? '1' : '') ? 'checked' : '' }} style="cursor: pointer;">
                <label class="form-check-label" for="remember_me" style="cursor: pointer; user-select: none;">Remember me</label>
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

    {{-- Remember Me & Lockout Countdown Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ── 1. Remember Me Local Storage Sync ──
            var emailInput = document.getElementById('email');
            var rememberCheckbox = document.getElementById('remember_me');
            var loginForm = document.getElementById('login-form');

            if (emailInput && !emailInput.value) {
                var savedEmail = localStorage.getItem('hims_remembered_email');
                if (savedEmail) {
                    emailInput.value = savedEmail;
                    if (rememberCheckbox) rememberCheckbox.checked = true;
                }
            }

            if (loginForm) {
                loginForm.addEventListener('submit', function () {
                    if (rememberCheckbox && rememberCheckbox.checked && emailInput && emailInput.value) {
                        localStorage.setItem('hims_remembered_email', emailInput.value);
                    } else {
                        localStorage.removeItem('hims_remembered_email');
                    }
                });
            }

            // ── 2. Lockout Countdown Alert ──
            var lockoutEl = document.getElementById('lockout-alert');
            if (!lockoutEl) return;

            var seconds = parseInt(lockoutEl.dataset.seconds, 10);
            var display = document.getElementById('countdown-display');
            var submitBtn = document.getElementById('login-submit-btn');

            if (seconds > 0 && submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                submitBtn.style.cursor = 'not-allowed';

                var timer = setInterval(function () {
                    seconds--;
                    if (display) {
                        if (seconds >= 60) {
                            var mins = Math.floor(seconds / 60);
                            var secs = seconds % 60;
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
                        lockoutEl.classList.remove('alert-warning');
                        lockoutEl.classList.add('alert-success');
                        lockoutEl.innerHTML = '<i class="bi bi-check-circle me-1"></i> You may now try logging in again.';
                    }
                }, 1000);
            }
        });
    </script>
@endsection
