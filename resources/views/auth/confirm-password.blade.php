@extends('layouts.guest')
@section('title', 'Confirm Password')

@section('content')
    <p class="text-muted small mb-4">For security, please confirm your password before continuing.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-4">
            <label for="password" class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input id="password" type="password" name="password"
                       class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                       required autocomplete="current-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-shield-check me-2"></i>Confirm
        </button>
    </form>
@endsection
