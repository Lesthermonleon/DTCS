@extends('layouts.guest')
@section('title', 'Verify Email')

@section('content')
    <div class="text-center mb-4">
        <i class="bi bi-envelope-check display-4 text-primary"></i>
        <p class="mt-3 text-muted small">
            Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just sent you.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success py-2 mb-3">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="d-flex flex-column gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send me-2"></i>Resend Verification Email
            </button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
            </button>
        </form>
    </div>
@endsection
