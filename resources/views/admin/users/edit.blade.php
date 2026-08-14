@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User: ' . $user->name)
@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="card-header"><i class="bi bi-person-gear me-2"></i>Edit User Account</div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Employee ID</label>
                        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $user->employee_id) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department', $user->department) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        @if($user->id === auth()->id())
                            <select class="form-select bg-light" disabled>
                                <option selected>{{ $user->roles->first()?->name ?? 'System Administrator' }}</option>
                            </select>
                            <input type="hidden" name="role_id" value="{{ $user->roles->first()?->id }}">
                        @else
                            <select name="role_id" class="form-select" required>
                                <option value="">— Select Role —</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}" {{ (old('role_id', $user->roles->first()?->id)==$r->id)?'selected':'' }}>{{ $r->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch mb-0">
                            @if($user->id === auth()->id())
                                <input type="checkbox" class="form-check-input" checked disabled>
                                <input type="hidden" name="is_active" value="1">
                            @else
                                <input type="checkbox" class="form-check-input" name="is_active" id="isActive" value="1" {{ $user->is_active?'checked':'' }}>
                            @endif
                            <label class="form-check-label fw-semibold" for="isActive">Active Account</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2 align-items-center">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
            </form>
            <div class="card-footer bg-light border-top d-flex align-items-center justify-content-between py-2">
                <small class="text-muted"><i class="bi bi-shield-lock me-1"></i>Security Actions</small>
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm"
                            data-confirm="Are you sure you want to reset {{ $user->name }}'s password to 'password'?"
                            data-confirm-title="Reset Password"
                            data-confirm-btn="btn-warning"
                            data-confirm-icon="bi-key-fill"
                            data-confirm-action-text="Reset Password"><i class="bi bi-key me-1"></i>Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Specific System Capabilities Column --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-light"><i class="bi bi-shield-check me-2"></i>Specific System Capabilities</div>
            <div class="card-body">
                @php
                    $primaryRole = $user->roles->first();
                @endphp
                @if($primaryRole && $primaryRole->permissions->isNotEmpty())
                    <div class="mb-3">
                        <span class="badge bg-primary fs-6 px-3 py-2 me-1">{{ $primaryRole->name }}</span>
                        <small class="text-muted d-block mt-1">Assigned capabilities in administration scope:</small>
                    </div>
                    <ul class="list-group list-group-flush border rounded">
                        @foreach($primaryRole->permissions as $p)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span class="fw-semibold text-dark small">{{ $p->name }}</span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 extra-small">Enabled</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i> No granular permission capabilities assigned to this user's role.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
