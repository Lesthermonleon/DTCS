@extends('layouts.app')
@section('title', 'Admin — Permissions')
@section('page-title', 'Permissions')
@section('content')
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-secondary"><i class="bi bi-key me-2"></i>Role Permissions Matrix</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Accessible Modules</th>
                        <th>Permissions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td class="fw-semibold">{{ $role->name }}</td>
                        <td>
                            @php
                                $modules = explode(', ', $role->accessible_modules);
                            @endphp
                            @foreach($modules as $m)
                                <span class="badge bg-light text-dark border me-1">{{ $m }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-primary" title="View Permission Details">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
