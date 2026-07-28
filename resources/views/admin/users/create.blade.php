@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Create New User')
@section('content')
<form method="POST" action="{{ route('admin.users.store') }}">
@csrf
<div class="card" style="max-width:600px;">
    <div class="card-header"><i class="bi bi-person-plus me-2"></i>New System User</div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Employee ID</label>
                <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}" placeholder="EMP-0001">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Department</label>
                <input type="text" name="department" class="form-control" value="{{ old('department') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                    <option value="">— Select Role —</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ old('role_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Create User</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
</form>
@endsection
