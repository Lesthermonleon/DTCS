@extends('layouts.app')

@section('title', 'Role Scope — ' . $role->name)

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-body">
                <i class="bi bi-shield-lock me-2 text-success"></i>{{ $role->name }}
            </h4>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Roles &amp; Permissions
        </a>
    </div>

    {{-- Role Details Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-bold text-body">
                <i class="bi bi-person-badge me-2 text-success"></i>Role Details
            </span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5">
                    <i class="bi bi-check-circle me-1"></i>Status: Active
                </span>
                <span class="badge hims-badge-gray px-2.5 py-1.5">
                    <i class="bi bi-people me-1"></i>{{ $role->users_count ?? $role->users->count() }} Users
                </span>
                <span class="badge hims-badge-gray px-2.5 py-1.5">
                    <i class="bi bi-key me-1"></i>{{ $role->permissions->count() }} Permissions
                </span>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4 mb-3">
                <div class="col-md-4">
                    <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Role Name</span>
                    <span class="fs-6 fw-bold text-body">{{ $role->name }}</span>
                </div>
                <div class="col-md-4">
                    <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Identifier</span>
                    <code class="font-monospace text-success fs-6">{{ $role->slug }}</code>
                </div>
                <div class="col-md-4">
                    <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Status</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-medium">
                        <i class="bi bi-check-circle me-1"></i>Active
                    </span>
                </div>
            </div>

            @if($role->description)
            <div class="mb-4 pt-3 border-top">
                <span class="text-uppercase text-muted small fw-semibold d-block mb-1">Role Description</span>
                <p class="text-body-secondary small mb-0">{{ $role->description }}</p>
            </div>
            @endif

            <div class="pt-3 border-top">
                <span class="text-uppercase text-muted small fw-semibold d-block mb-2">Access Scope</span>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $rawModules = str_replace(['All Modules (', ')'], '', $role->accessible_modules);
                        $modules = array_map('trim', explode(',', $rawModules));
                        $isAllModules = str_contains($role->accessible_modules, 'All Modules') || in_array($role->slug, ['system-administrator', 'admin']);
                    @endphp

                    @if($isAllModules)
                        <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 fw-medium">
                            <i class="bi bi-shield-check me-1"></i>All System Modules
                        </span>
                    @else
                        @foreach($modules as $m)
                            @if(!empty($m))
                                <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 fw-medium">
                                    <i class="bi bi-check-circle me-1"></i>{{ $m }}
                                </span>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
