<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="current_password" class="form-label text-soft fw-semibold" style="font-size:0.8rem;">Current Password</label>
        <input type="password" name="current_password" id="current_password"
               class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
               autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label text-soft fw-semibold" style="font-size:0.8rem;">New Password</label>
        <input type="password" name="password" id="password"
               class="form-control @error('password', 'updatePassword') is-invalid @enderror"
               autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label text-soft fw-semibold" style="font-size:0.8rem;">Confirm New Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation"
               class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
               autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn text-white px-4" style="background-color: var(--amber);">
            Update Password
        </button>

        @if (session('status') === 'password-updated')
            <span class="text-success small fw-medium" id="pw-status-alert">
                <i class="bi bi-check-circle-fill me-1"></i> Password updated.
            </span>
            <script>
                setTimeout(() => {
                    const alert = document.getElementById('pw-status-alert');
                    if (alert) alert.style.display = 'none';
                }, 3000);
            </script>
        @endif
    </div>
</form>
