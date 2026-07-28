@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Account</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="row g-4">
    {{-- Left: Personal Information Summary --}}
    <div class="col-md-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom-0 pb-0 pt-4 text-center">
                <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center text-white"
                     style="width: 80px; height: 80px; background-color: var(--signal-dark); font-size: 2rem; font-family: var(--font-display); font-weight: 700;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <h4 class="mt-3 mb-1" style="font-family: var(--font-display); font-weight: 600; color: var(--text);">
                    {{ auth()->user()->name }}
                </h4>
                <div class="mb-3">
                    <span class="pill pill-signal">{{ auth()->user()->roleName }}</span>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.72rem; letter-spacing: 0.05em; font-family: var(--font-mono);">
                    Personal Details
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="text-soft fw-medium py-2 ps-0" style="width: 35%; font-size: 0.8rem;">Employee ID</td>
                                <td class="text-dark fw-bold py-2" style="font-size: 0.8rem; font-family: var(--font-mono);">{{ auth()->user()->employee_id ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-soft fw-medium py-2 ps-0" style="font-size: 0.8rem;">Department</td>
                                <td class="text-dark fw-bold py-2" style="font-size: 0.8rem;">{{ auth()->user()->department ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-soft fw-medium py-2 ps-0" style="font-size: 0.8rem;">Email Address</td>
                                <td class="text-dark py-2" style="font-size: 0.8rem;">{{ auth()->user()->email }}</td>
                            </tr>
                            <tr>
                                <td class="text-soft fw-medium py-2 ps-0" style="font-size: 0.8rem;">Phone Number</td>
                                <td class="text-dark py-2" style="font-size: 0.8rem;">{{ auth()->user()->phone ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-soft fw-medium py-2 ps-0" style="font-size: 0.8rem;">Account Status</td>
                                <td class="py-2">
                                    <span class="pill pill-{{ auth()->user()->is_active ? 'signal' : 'coral' }}">
                                        {{ auth()->user()->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Edit Account Forms --}}
    <div class="col-md-7">
        <div class="d-flex flex-column gap-4">
            {{-- Update Password Card --}}
            <div class="card shadow-sm">
                <div class="card-header border-bottom">
                    <i class="bi bi-key me-2" style="color: var(--amber);"></i>
                    Update Password
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
