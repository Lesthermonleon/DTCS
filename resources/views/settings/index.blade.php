@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Account</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 bg-transparent">
            <div class="card-body p-0">
                <h5 class="text-secondary fw-semibold mb-1" style="font-size: 0.95rem;">Appearance & Theme</h5>
                <p class="text-muted mb-3" style="font-size: 0.82rem;">
                    Customize how the DITC system looks on your device.
                </p>

                <div class="d-flex align-items-center gap-1 p-1 rounded-3" style="background: rgba(110, 124, 116, 0.08); width: fit-content;">
                    <button type="button" class="btn btn-sm border-0 theme-btn px-4 py-2 d-flex align-items-center gap-2 rounded-2" data-theme-val="light" style="font-size: 0.82rem; transition: all 0.2s;">
                        <i class="bi bi-sun"></i>
                        <span>Light</span>
                    </button>
                    <button type="button" class="btn btn-sm border-0 theme-btn px-4 py-2 d-flex align-items-center gap-2 rounded-2" data-theme-val="dark" style="font-size: 0.82rem; transition: all 0.2s;">
                        <i class="bi bi-moon-stars"></i>
                        <span>Dark</span>
                    </button>
                    <button type="button" class="btn btn-sm border-0 theme-btn px-4 py-2 d-flex align-items-center gap-2 rounded-2" data-theme-val="system" style="font-size: 0.82rem; transition: all 0.2s;">
                        <i class="bi bi-display"></i>
                        <span>System</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
