@extends('layouts.guest')
@section('single_card', 'true')
@section('title', 'Reset Password')
@section('card-title', 'Reset Password')
@section('card-hint', 'Set a new secure password for your HIMS account.')

@section('content')

    {{-- Session Flash --}}
    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" id="reset-password-form" class="auth-form-grid">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email Address --}}
        <div class="form-group mb-3">
            <label class="field-label" for="email">Email Address</label>
            <div class="input-wrap">
                <svg class="leading" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 7 8.2 5.9a1.4 1.4 0 0 0 1.6 0L21 7"/></svg>
                <input type="email" id="email" name="email"
                       class="auth-input @error('email') is-invalid @enderror"
                       value="{{ old('email', $request->email) }}"
                       required readonly autocomplete="username"
                       placeholder="you@hospital.com">
            </div>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- New Password --}}
        <div class="form-group mb-3">
            <label class="field-label" for="password">New Password</label>
            <div class="input-wrap has-toggle">
                <svg class="leading" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="10.5" width="17" height="10" rx="2"/><path d="M7 10.5V7a5 5 0 0 1 10 0v3.5"/></svg>
                <input type="password" id="password" name="password"
                       class="auth-input @error('password') is-invalid @enderror"
                       required autocomplete="new-password" placeholder="••••••••">
                <button type="button" class="toggle-pw" id="togglePw" aria-label="Show password">
                    <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg id="eyeClosed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="m3 3 18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.5 0 10 7 10 7a17.5 17.5 0 0 1-3.1 4"/><path d="M6.6 6.6C3.8 8.4 2 12 2 12s3.5 7 10 7a10 10 0 0 0 5.3-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group mb-3">
            <label class="field-label" for="password_confirmation">Confirm Password</label>
            <div class="input-wrap has-toggle">
                <svg class="leading" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="10.5" width="17" height="10" rx="2"/><path d="M7 10.5V7a5 5 0 0 1 10 0v3.5"/></svg>
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="auth-input"
                       required autocomplete="new-password" placeholder="••••••••">
                <button type="button" class="toggle-pw" id="toggleConfirmPw" aria-label="Show password">
                    <svg id="confirmEyeOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg id="confirmEyeClosed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="m3 3 18 18"/><path d="M10.6 5.1A10 10 0 0 1 12 5c6.5 0 10 7 10 7a17.5 17.5 0 0 1-3.1 4"/><path d="M6.6 6.6C3.8 8.4 2 12 2 12s3.5 7 10 7a10 10 0 0 0 5.3-1.6"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn-auth" id="reset-submit-btn">
            <span>Reset Password</span>
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 11V7a5 5 0 0 1 9.9-1"/><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/></svg>
        </button>
    </form>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── 1. Password show / hide for main password ──
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
        });
    }

    // ── 2. Password show / hide for confirm password ──
    var confirmPwEl      = document.getElementById('password_confirmation');
    var toggleConfirmPw  = document.getElementById('toggleConfirmPw');
    var confirmEyeOpen   = document.getElementById('confirmEyeOpen');
    var confirmEyeClosed = document.getElementById('confirmEyeClosed');

    if (toggleConfirmPw && confirmPwEl) {
        toggleConfirmPw.addEventListener('click', function () {
            var showing = confirmPwEl.type === 'text';
            confirmPwEl.type = showing ? 'password' : 'text';
            confirmEyeOpen.style.display   = showing ? '' : 'none';
            confirmEyeClosed.style.display = showing ? 'none' : '';
        });
    }
});
</script>
@endsection
