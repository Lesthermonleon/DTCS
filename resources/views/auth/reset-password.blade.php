@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')
    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input id="email" type="email" name="email"
                       class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                       value="{{ old('email', $request->email) }}" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">New Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input id="password" type="password" name="password"
                       class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                       required autocomplete="new-password" placeholder="Min. 8 characters">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control border-start-0 ps-0"
                       required autocomplete="new-password" placeholder="Repeat password">
            </div>
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-key me-2"></i>Reset Password
        </button>
    </form>
@endsection
