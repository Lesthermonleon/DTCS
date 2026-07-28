@extends('layouts.app')
@section('title', 'Admin — Permission Details')
@section('page-title', 'Permission Details')
@section('content')
<div class="card">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <h5 class="mb-0 text-secondary"><i class="bi bi-shield-lock me-2"></i>{{ $role->name }} Access Scope</h5>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Permissions
        </a>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <h6 class="text-uppercase text-muted small fw-semibold">Role Name</h6>
            <p class="fs-5 fw-semibold mb-0">{{ $role->name }}</p>
        </div>
        <div class="mb-4">
            <h6 class="text-uppercase text-muted small fw-semibold">Role Identifier (Slug)</h6>
            <p class="font-monospace text-primary mb-0">{{ $role->slug }}</p>
        </div>
        <div class="mb-4">
            <h6 class="text-uppercase text-muted small fw-semibold">Role Description</h6>
            <p class="mb-0 text-secondary">{{ $role->description ?? 'No description provided.' }}</p>
        </div>
        <div class="mb-4">
            <h6 class="text-uppercase text-muted small fw-semibold">Accessible Modules</h6>
            <div>
                @php
                    $modules = explode(', ', $role->accessible_modules);
                @endphp
                @foreach($modules as $m)
                    <span class="badge bg-teal-soft fs-6 py-2 px-3 me-2 mb-2" style="background-color: rgba(20,199,154,0.12); color: var(--signal-dark); border: 1px solid rgba(20,199,154,0.25);">{{ $m }}</span>
                @endforeach
            </div>
        </div>
        
        @if($role->permissions->isNotEmpty())
        <div>
            <h6 class="text-uppercase text-muted small fw-semibold mb-3">Specific System Capabilities</h6>
            <ul class="list-group">
                @foreach($role->permissions as $p)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-semibold text-dark">{{ $p->name }}</span>
                        @if($p->description)
                            <div class="small text-muted">{{ $p->description }}</div>
                        @endif
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 small">Enabled</span>
                </li>
                @endforeach
            </ul>
        </div>
        @else
        <div>
            <h6 class="text-uppercase text-muted small fw-semibold mb-1">Specific System Capabilities</h6>
            <p class="text-muted small">Inherits basic module level routing permissions.</p>
        </div>
        @endif
    </div>
</div>
@endsection
