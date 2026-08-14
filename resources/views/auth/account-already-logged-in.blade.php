@extends('layouts.guest')

@section('title', 'Account Already Logged In')

@section('content')

<div class="account-security-wrapper">

    <div class="account-security-card">

        <div class="security-icon">
            <i class="bi bi-shield-exclamation"></i>
        </div>

        <h1>
            Account Already Logged In
        </h1>

        <p class="security-description">
            This account is already logged in on another device or browser.
        </p>

        <p class="security-secondary">
            Only one active session is allowed for each user account. For security reasons, this login attempt will be cancelled automatically.
        </p>

        <div class="countdown-container">

            <div
                id="duplicateLoginCountdown"
                class="countdown-number"
            >
                5
            </div>

            <div class="countdown-label">
                seconds remaining
            </div>

        </div>

        <div class="progress-wrapper">

            <div
                id="duplicateLoginProgress"
                class="progress-bar"
            ></div>

        </div>

        <div class="redirect-message mb-3">
            <i class="bi bi-arrow-right-circle me-1"></i>
            Returning to login...
        </div>

        <div class="pt-2">
            <a href="{{ $loginUrl ?? route('login') }}" class="auth-link text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> Return to login immediately
            </a>
        </div>

    </div>

</div>


<style>

.account-security-wrapper {
    min-height: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    background: transparent;
}

.account-security-card {
    width: 100%;
    max-width: 100%;
    background: transparent;
    border: none;
    border-radius: 0;
    padding: 5px 0 0;
    text-align: center;
    box-shadow: none;
}

.security-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: rgba(255,193,7,.15);
    color: #d39e00;

    font-size: 28px;
}

.account-security-card h1 {
    margin: 0 0 12px;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--text, #132420);
    font-family: 'Space Grotesk', sans-serif;
}

.security-description {
    margin: 0 auto 8px;
    max-width: 380px;
    color: var(--text, #132420);
    font-size: .92rem;
    line-height: 1.55;
    font-weight: 500;
}

.security-secondary {
    margin: 0 auto;
    max-width: 380px;
    color: var(--text-soft, #6E7C74);
    font-size: .83rem;
    line-height: 1.5;
}

.countdown-container {
    margin-top: 20px;
}

.countdown-number {
    font-size: 3.5rem;
    line-height: 1;
    font-weight: 800;
    color: #d39e00;
    font-family: 'Space Grotesk', sans-serif;
    font-feature-settings: 'tnum';
}

.countdown-label {
    margin-top: 6px;
    color: var(--text-soft, #6E7C74);
    font-size: .82rem;
}

.progress-wrapper {
    height: 6px;
    margin: 18px 0 14px;
    overflow: hidden;
    border-radius: 999px;
    background: rgba(0,0,0,.08);
}

.progress-bar {
    height: 100%;
    width: 100%;
    border-radius: 999px;
    background: #d39e00;
    transition: width 1s linear;
}

.redirect-message {
    color: var(--text-soft, #6E7C74);
    font-size: .82rem;
}

@media (max-width:576px) {

    .account-security-card h1 {
        font-size: 1.2rem;
    }

    .countdown-number {
        font-size: 2.75rem;
    }

}

</style>


<script>

(function () {

    'use strict';

    const countdownElement =
        document.getElementById('duplicateLoginCountdown');

    const progressElement =
        document.getElementById('duplicateLoginProgress');

    if (!countdownElement) {
        console.error(
            'Duplicate login countdown element not found.'
        );
        return;
    }

    let seconds = 5;

    countdownElement.textContent = seconds;

    if (progressElement) {
        progressElement.style.width = '100%';
    }

    const loginUrl = "{{ $loginUrl ?? route('login') }}";

    const timer = setInterval(function () {

        seconds--;

        if (seconds >= 0) {

            countdownElement.textContent = seconds;

            if (progressElement) {

                const percentage =
                    (seconds / 5) * 100;

                progressElement.style.width =
                    percentage + '%';
            }

        }

        if (seconds <= 0) {

            clearInterval(timer);

            /*
             * Force the rejected login session to remain
             * unauthenticated before returning to login.
             */

            window.location.replace(loginUrl);

        }

    }, 1000);

})();

</script>

@endsection
