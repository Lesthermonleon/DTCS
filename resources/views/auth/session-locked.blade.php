@extends('layouts.guest')
@section('title', 'Session Locked')
@section('card-title', 'Session Locked')
@section('card-hint', 'Your session was locked due to inactivity. Enter your password to continue.')

@section('content')
    {{-- User identity indicator --}}
    <div class="d-flex align-items-center gap-3 p-3 mb-3 rounded-3" style="background: #f8fafc; border: 1px solid var(--line);">
        <div style="width: 42px; height: 42px; border-radius: 50%; background: var(--signal-dark); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">
            {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
        </div>
        <div>
            <div style="font-weight: 600; font-size: 0.95rem; color: var(--ink);">{{ auth()->user()?->name }}</div>
            <div style="font-size: 0.78rem; color: var(--soft);">{{ auth()->user()?->email }}</div>
        </div>
    </div>

    {{-- Error container --}}
    <div id="fullLockError" class="text-danger text-start mb-3 d-none" style="font-size: 0.8rem;">
        <i class="bi bi-shield-exclamation me-1"></i>
        <span id="fullLockErrorText"></span>
    </div>

    {{-- Password Unlock Form --}}
    <form id="fullLockForm" class="auth-form-grid">
        @csrf
        <div class="form-group mb-3">
            <label class="field-label" for="fullLockPassword">Password</label>
            <div class="input-wrap">
                <svg class="leading" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input
                    type="password"
                    id="fullLockPassword"
                    name="password"
                    class="auth-input"
                    placeholder="Enter account password"
                    required
                    autofocus>
            </div>
        </div>

        <button type="submit" class="btn-auth" id="fullLockSubmitBtn">
            <span>Unlock Session</span>
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            </svg>
        </button>
    </form>

    {{-- Sign Out Link --}}
    <div class="text-center mt-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="forgot" style="background: none; border: none; padding: 0; cursor: pointer;">
                &larr; Sign out
            </button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('fullLockForm');
        var errBox = document.getElementById('fullLockError');
        var errText = document.getElementById('fullLockErrorText');
        var submitBtn = document.getElementById('fullLockSubmitBtn');
        var passInput = document.getElementById('fullLockPassword');

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                errBox.classList.add('d-none');
                submitBtn.disabled = true;

                fetch("{{ route('session.unlock') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: passInput.value })
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    submitBtn.disabled = false;
                    if (data.success) {
                        window.location.href = "{{ route('dashboard') }}";
                    } else if (data.max_attempts_exceeded) {
                        window.location.href = data.redirect || "{{ route('login') }}";
                    } else {
                        errText.textContent = data.message || 'Incorrect password.';
                        errBox.classList.remove('d-none');
                        passInput.value = '';
                        passInput.focus();
                    }
                })
                .catch(function () {
                    submitBtn.disabled = false;
                    errText.textContent = 'An unexpected error occurred. Please try again.';
                    errBox.classList.remove('d-none');
                });
            });
        }
    });
    </script>
@endsection
