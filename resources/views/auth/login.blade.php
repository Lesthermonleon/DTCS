@extends('layouts.guest')
@section('title', 'Login')
@section('card-title', 'Staff Sign In')
@section('card-hint', 'Use your hospital email to continue.')

@section('content')

    {{-- Session flash (e.g. password reset success) --}}
    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    {{-- Session Replaced Alert --}}
    @if (session('session_replaced') || request()->has('session_replaced'))
        <div class="alert alert-warning mb-3" role="alert">
            <i class="bi bi-shield-lock alert-icon"></i>
            <div>
                <strong style="font-family: var(--font-display); display: block; margin-bottom: 0.15rem;">Session Ended</strong>
                <span style="font-size: 0.82rem;">Your account was signed in on another device. You have been logged out for security.</span>
            </div>
        </div>
    @endif

    {{-- Lockout Countdown Alert --}}
    @if (session('lockout_seconds'))
        @php $lockoutSeconds = (int) session('lockout_seconds'); @endphp
        <div class="alert alert-warning mb-3" id="lockout-alert" data-seconds="{{ $lockoutSeconds }}">
            <i class="bi bi-hourglass-split alert-icon"></i>
            <span>Account temporarily locked. Please wait <strong id="countdown-display">{{ $lockoutSeconds }}</strong> seconds.</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="login-form" class="auth-form-grid">
        @csrf

        {{-- Email --}}
        <div class="form-group mb-3">
            <label class="field-label" for="email">Email Address</label>
            <div class="input-wrap">
                <svg class="leading" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 7 8.2 5.9a1.4 1.4 0 0 0 1.6 0L21 7"/></svg>
                <input type="email" id="email" name="email"
                       class="auth-input @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       placeholder="you@hospital.com">
            </div>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Password + Forgot Password Link --}}
        <div class="form-group mb-3">
            <div class="label-header">
                <label class="field-label" for="password">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
                @endif
            </div>
            <div class="input-wrap has-toggle">
                <svg class="leading" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="10.5" width="17" height="10" rx="2"/><path d="M7 10.5V7a5 5 0 0 1 10 0v3.5"/></svg>
                <input type="password" id="password" name="password"
                       class="auth-input @error('password') is-invalid @enderror"
                       required autocomplete="current-password" placeholder="••••••••">
                <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
                    <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg id="eyeClosed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="m3 3 18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.5 0 10 7 10 7a17.5 17.5 0 0 1-3.1 4"/><path d="M6.6 6.6C3.8 8.4 2 12 2 12s3.5 7 10 7a10 10 0 0 0 5.3-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-auth" id="login-submit-btn">
            <span>Sign In</span>
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        </button>
    </form>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── 1. Password show / hide ──
    var pwEl      = document.getElementById('password');
    var togglePw  = document.getElementById('togglePw');
    var eyeOpen   = document.getElementById('eyeOpen');
    var eyeClosed = document.getElementById('eyeClosed');

    if (togglePw && pwEl) {
        togglePw.addEventListener('click', function () {
            var showing = pwEl.type === 'text';
            pwEl.type = showing ? 'password' : 'text';
            eyeOpen.style.display   = showing ? '' : 'none';
            eyeClosed.style.display = showing ? 'none' : '';
            togglePw.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
        });
    }

    // ── 2. Lockout Countdown Alert ──
    var lockoutEl = document.getElementById('lockout-alert');
    if (!lockoutEl) return;

    var seconds   = parseInt(lockoutEl.dataset.seconds, 10);
    var display   = document.getElementById('countdown-display');
    var submitBtn = document.getElementById('login-submit-btn');

    if (seconds > 0 && submitBtn) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.6';
        submitBtn.style.cursor  = 'not-allowed';

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
                submitBtn.disabled      = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor  = 'pointer';
                lockoutEl.classList.remove('alert-warning');
                lockoutEl.classList.add('alert-success');
                lockoutEl.innerHTML = '<i class="bi bi-check-circle alert-icon"></i><span>You may now try logging in again.</span>';
            }
        }, 1000);
    }
});
</script>
@endsection
