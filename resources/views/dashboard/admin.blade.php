@extends('layouts.app')
@section('title', 'System Administrator Dashboard')
@section('page-title', 'System Administrator Dashboard')

@push('styles')
<style>
    .module-card-clickable,
    .admin-tool-tile {
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .module-card-clickable:hover,
    .module-card-clickable:focus-visible,
    .admin-tool-tile:hover,
    .admin-tool-tile:focus-visible {
        border-color: #198754 !important;
        background-color: #f0fdf4 !important;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.12) !important;
        transform: translateY(-2px);
    }
    .module-card-clickable:hover .icon-shape,
    .module-card-clickable:focus-visible .icon-shape,
    .admin-tool-tile:hover .icon-shape,
    .admin-tool-tile:focus-visible .icon-shape {
        background-color: #198754 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')

{{-- ── 1. SYSTEM OVERVIEW KPI CARDS ── --}}
{{-- ── 1. SYSTEM OVERVIEW KPI CARDS ── --}}
<div class="balanced-grid balanced-grid-6 mb-4">
    {{-- Card 1: Total Users --}}
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Users</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_users']) }}</h3>
                <div class="small text-primary mt-2">
                    Manage Users <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 2: Active Users --}}
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Active Users</span>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                        <i class="bi bi-person-check-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['active_users']) }}</h3>
                <div class="small text-success mt-2">
                    Active Accounts <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 3: Total Patients --}}
    <a href="{{ route('patients.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Patients</span>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                        <i class="bi bi-folder2-open fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_patients']) }}</h3>
                <div class="small text-info mt-2">
                    Patient Directory <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 4: Pending Administrative Tasks --}}
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pending Tasks</span>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                        <i class="bi bi-list-task fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['pending_admin_tasks']) }}</h3>
                <div class="small text-warning mt-2">
                    Review Tasks <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 5: System Alerts --}}
    <a href="#system-alerts-section" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">System Alerts</span>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2">
                        <i class="bi bi-shield-exclamation fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['system_alerts_count']) }}</h3>
                <div class="small text-danger mt-2">
                    Review Warnings <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 6: Today's System Activity --}}
    <a href="#recent-activity-section" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Today Activity</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                        <i class="bi bi-clock-history fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['today_activity_count']) }}</h3>
                <div class="small text-primary mt-2">
                    Audit Logs <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>
</div>

{{-- ── 2. CRITICAL ALERTS & PENDING SYSTEM WORK ── --}}
<div class="row g-4 mb-4">
    {{-- System Security Alerts --}}
    <div class="col-lg-5" id="system-alerts-section">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger">
                    <i class="bi bi-shield-alert me-2"></i>Critical System & Security Alerts
                </h6>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                    {{ $systemAlerts->count() }} Active
                </span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height: 280px; overflow-y: auto;">
                    @forelse($systemAlerts as $alert)
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex align-items-start gap-3">
                                <div class="p-2 rounded bg-{{ $alert['type'] === 'danger' ? 'danger' : ($alert['type'] === 'warning' ? 'warning' : 'info') }} bg-opacity-10 text-{{ $alert['type'] === 'danger' ? 'danger' : ($alert['type'] === 'warning' ? 'warning' : 'info') }}">
                                    <i class="bi {{ $alert['icon'] }} fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-dark mb-0">{{ $alert['title'] }}</h6>
                                        @if(isset($alert['timestamp']))
                                            <span class="small text-muted" style="font-size: 0.75rem;">{{ $alert['timestamp'] }}</span>
                                        @endif
                                    </div>
                                    <p class="small text-muted mb-2">{{ $alert['description'] }}</p>
                                    <a href="{{ $alert['action_route'] }}" class="btn btn-sm btn-outline-{{ $alert['type'] === 'danger' ? 'danger' : ($alert['type'] === 'warning' ? 'warning' : 'primary') }}">
                                        {{ $alert['action_label'] }} <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-shield-check fs-1 text-success opacity-50 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">No system alerts</h6>
                            <p class="small text-muted mb-0">Everything is operating normally. Security status is healthy.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Administrative Quick Actions & Module Status --}}
    <div class="col-lg-7">
        {{-- Administrative Tools --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-tools me-2 text-success"></i>Administrative Tools
                </h6>
                <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                    Manage users, access, records, security, and system configuration
                </small>
            </div>
            <div class="card-body">
                <div class="balanced-grid balanced-grid-3 admin-tools-grid">
                    {{-- Tile 1: Create User --}}
                    <a href="{{ route('admin.users.create') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-person-plus-fill fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">Create User</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Add a new system account
                        </p>
                    </a>

                    {{-- Tile 2: User Management --}}
                    <a href="{{ route('admin.users.index') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">User Management</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Manage system user accounts
                        </p>
                    </a>

                    {{-- Tile 3: Roles & Permissions --}}
                    <a href="{{ route('admin.roles.index') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-shield-lock-fill fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">Roles & Permissions</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Manage roles and access permissions
                        </p>
                    </a>

                    {{-- Tile 4: Patient Directory --}}
                    <a href="{{ route('patients.index') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-folder2-open fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">Patient Directory</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Access patient information
                        </p>
                    </a>

                    {{-- Tile 5: Audit Logs --}}
                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-journal-text fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">Audit Logs</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Review system activity and security events
                        </p>
                    </a>

                    {{-- Tile 6: System Settings --}}
                    <a href="{{ route('settings.index') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-gear-fill fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">System Settings</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Configure system settings
                        </p>
                    </a>
                </div>
            </div>
        </div>

        {{-- Clinical Module Operational Overview --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-grid-3x3-gap-fill me-2 text-success"></i>Clinical Module Operational Overview
                </h6>
            </div>
            <div class="card-body">
                <div class="balanced-grid balanced-grid-5">
                    {{-- LIS Card --}}
                    <a href="{{ route($moduleStats['lis']['route']) }}"
                       class="card border border-light-subtle shadow-xs h-100 p-2 bg-white text-center text-decoration-none module-card-clickable">
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 mx-auto mb-1 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                            <i class="bi bi-clipboard2-pulse fs-6"></i>
                        </div>
                        <div class="fw-bold small text-dark">LIS</div>
                        <div class="small text-muted" style="font-size: 11px;">{{ number_format($moduleStats['lis']['pending']) }} pending</div>
                    </a>

                    {{-- RIS Card --}}
                    <a href="{{ route($moduleStats['ris']['route']) }}"
                       class="card border border-light-subtle shadow-xs h-100 p-2 bg-white text-center text-decoration-none module-card-clickable">
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 mx-auto mb-1 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                            <i class="bi bi-activity fs-6"></i>
                        </div>
                        <div class="fw-bold small text-dark">RIS</div>
                        <div class="small text-muted" style="font-size: 11px;">{{ number_format($moduleStats['ris']['pending']) }} pending</div>
                    </a>

                    {{-- PMS Card --}}
                    <a href="{{ route($moduleStats['pms']['route']) }}"
                       class="card border border-light-subtle shadow-xs h-100 p-2 bg-white text-center text-decoration-none module-card-clickable">
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 mx-auto mb-1 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                            <i class="bi bi-capsule fs-6"></i>
                        </div>
                        <div class="fw-bold small text-dark">PMS</div>
                        <div class="small text-muted" style="font-size: 11px;">{{ number_format($moduleStats['pms']['pending']) }} pending</div>
                    </a>

                    {{-- SORS Card --}}
                    <a href="{{ route($moduleStats['sors']['route']) }}"
                       class="card border border-light-subtle shadow-xs h-100 p-2 bg-white text-center text-decoration-none module-card-clickable">
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 mx-auto mb-1 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                            <i class="bi bi-scissors fs-6"></i>
                        </div>
                        <div class="fw-bold small text-dark">SORS</div>
                        <div class="small text-muted" style="font-size: 11px;">{{ number_format($moduleStats['sors']['pending']) }} pending</div>
                    </a>

                    {{-- DNMS Card --}}
                    <a href="{{ route($moduleStats['dnms']['route']) }}"
                       class="card border border-light-subtle shadow-xs h-100 p-2 bg-white text-center text-decoration-none module-card-clickable">
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 mx-auto mb-1 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                            <i class="bi bi-apple fs-6"></i>
                        </div>
                        <div class="fw-bold small text-dark">DNMS</div>
                        <div class="small text-muted" style="font-size: 11px;">{{ number_format($moduleStats['dnms']['pending']) }} pending</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 3. SERVICE REQUEST TREND & MODULE VOLUME ANALYTICS ── --}}
<div class="row g-4 mb-4">
    {{-- Chart A: Service Request Monthly Trend --}}
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Service Request Trend</h3>
                    <p class="chart-subtitle">Monthly total requests across all clinical modules</p>
                </div>
                <select id="adminTrendPeriod" class="form-select form-select-sm" style="width:auto;">
                    <option value="6">Last 6 Months</option>
                    <option value="12">Last 12 Months</option>
                </select>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="adminSvcTrendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart B: Module Volume Bar --}}
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-bar-chart-fill me-2 text-success"></i>Requests by Module</h3>
                    <p class="chart-subtitle">Total requests across all clinical modules</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="adminModuleBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── 4. GLOBAL REQUEST STATUS & SYSTEM ACTIVITY ANALYTICS ── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Global Request Status</h3>
                    <p class="chart-subtitle">All-time status breakdown across every clinical module</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="adminStatusDonut"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-activity me-2 text-success"></i>System Activity Log</h3>
                    <p class="chart-subtitle">Daily activity events recorded in the last 7 days</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="adminActivityChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── 5. USER ROLES ANALYTICS ── --}}
<div class="row g-4 mb-4">
    {{-- Maximized User Distribution by Role Donut Chart --}}
    <div class="col-12">
        <div class="chart-card h-100">
            <div class="chart-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h3 class="chart-title"><i class="bi bi-pie-chart-fill me-2 text-success"></i>User Distribution by Role</h3>
                    <p class="chart-subtitle">Breakdown of system accounts across active roles</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-people me-1"></i> Manage Users
                </a>
            </div>
            <div class="chart-container d-flex align-items-center justify-content-center py-2" style="height: 380px;">
                <canvas id="adminRoleChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── 6. RECENT SYSTEM ACTIVITY LOG & PATIENT DIRECTORY OVERVIEW ── --}}
<div class="row g-4 mb-4">
    {{-- System Activity & User Presence Card --}}
    <div class="col-12" id="recent-activity-section">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-person-badge-fill me-2 text-success"></i>System Activity & User Presence
                </h6>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-journal-text me-1 text-success"></i>View System Audit Logs
                </a>
            </div>

            {{-- Presence Summary Header --}}
            <div class="p-3 bg-light border-bottom">
                <div class="d-flex align-items-center justify-content-around text-center">
                    <div>
                        <i class="bi bi-circle-fill text-success fs-6 me-1"></i>
                        <span class="fw-bold text-dark fs-6">{{ $userPresenceStats['online_count'] }}</span>
                        <span class="small text-muted ms-1">Online</span>
                    </div>
                    <div class="vr opacity-25" style="height: 20px;"></div>
                    <div>
                        <i class="bi bi-circle-fill text-warning fs-6 me-1"></i>
                        <span class="fw-bold text-dark fs-6">{{ $userPresenceStats['recently_active_count'] }}</span>
                        <span class="small text-muted ms-1">Active Recently</span>
                    </div>
                    <div class="vr opacity-25" style="height: 20px;"></div>
                    <div>
                        <i class="bi bi-people-fill text-secondary fs-6 me-1"></i>
                        <span class="fw-bold text-dark fs-6">{{ $userPresenceStats['total_users_count'] }}</span>
                        <span class="small text-muted ms-1">Users</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="user-presence-list-wrapper" style="max-height: 320px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @forelse($userPresenceList as $userItem)
                                <tr>
                                    <td class="ps-3" style="width: 32px;">
                                        @if($userItem['is_online'])
                                            <i class="bi bi-circle-fill text-success fs-6" title="Online"></i>
                                        @else
                                            <i class="bi bi-circle-fill text-warning fs-6" title="Active Recently"></i>
                                        @endif
                                    </td>
                                    <td class="fw-semibold text-dark small" style="min-width: 140px; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $userItem['name'] }}
                                    </td>
                                    <td class="small" style="min-width: 120px;">
                                        <span class="badge" style="background-color: #f0f4f1; color: #374151; font-weight: 500; font-size: 0.75rem;">
                                            {{ $userItem['role_name'] }}
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end small" style="min-width: 110px; white-space: nowrap;">
                                        @if($userItem['is_online'])
                                            <span class="fw-semibold text-success">Online</span>
                                        @else
                                            <span class="text-muted">{{ $userItem['last_active_human'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        <i class="bi bi-people fs-3 d-block mb-1 opacity-50"></i>
                                        No active users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Patient Directory Overview Table --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-person-lines-fill me-2 text-primary"></i>Patient Information & Directory Overview
                </h6>
                <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-folder2-open me-1"></i> View Patient Directory
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Patient ID</th>
                                <th>Patient Name</th>
                                <th>Gender / DOB</th>
                                <th>Type / Ward</th>
                                <th>Date Registered</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPatients as $patient)
                                <tr>
                                    <td class="ps-3">
                                        <span class="badge bg-light text-dark border"><i class="bi bi-card-text me-1"></i>{{ $patient->patient_no }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $patient->full_name }}</div>
                                        <div class="small text-muted">{{ $patient->email ?? 'No email recorded' }}</div>
                                    </td>
                                    <td>
                                        <span class="small">{{ $patient->gender }} &bull; {{ $patient->date_of_birth?->format('M d, Y') ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge hims-badge-{{ strtolower($patient->patient_type ?? '') === 'inpatient' ? 'blue' : (strtolower($patient->patient_type ?? '') === 'emergency' ? 'red' : 'green') }}">{{ $patient->patient_type ?? 'Outpatient' }}</span>
                                        @if($patient->ward)
                                            <small class="text-muted d-block">{{ $patient->ward }} (Bed {{ $patient->bed_number ?? '-' }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small text-muted"><i class="bi bi-clock me-1"></i>{{ $patient->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="table-actions justify-content-end">
                                            <a href="{{ route('patients.show', $patient) }}" class="table-action-btn action-view" title="View Patient Profile" aria-label="View Patient Profile">
                                                <i class="bi bi-person-badge"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-people fs-2 d-block mb-1 opacity-50"></i>
                                        No patient records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




<script type="application/json" id="admin-roles-data">@json($usersByRole)</script>
<script type="application/json" id="admin-svc-trend-6m">@json($adminTrend6m)</script>
<script type="application/json" id="admin-svc-trend-12m">@json($adminTrend12m)</script>
<script type="application/json" id="admin-module-volume">@json($adminModuleVolume)</script>
<script type="application/json" id="admin-all-statuses">@json($allStatuses)</script>
<script type="application/json" id="admin-activity-data">@json($adminActivity7d ?? [])</script>


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const rolesData = JSON.parse(document.getElementById('admin-roles-data').textContent);

    // 1. Role Distribution Chart (Doughnut)
    const roleCtx = document.getElementById('adminRoleChart');
    if (roleCtx && rolesData.length > 0 && typeof window.HIMSChart !== 'undefined') {
        window.HIMSChart.createDoughnutChart(
            roleCtx,
            rolesData.map(r => r.name),
            rolesData.map(r => r.count),
            ['#4CAF50', '#009688', '#00838F', '#81C784', '#90A4AE']
        );
    }
})();
</script>

{{-- ── New Admin Analytics Charts ── --}}
<script>
(function () {
    if (typeof window.HIMSChart === 'undefined') return;

    const trend6m  = JSON.parse(document.getElementById('admin-svc-trend-6m').textContent);
    const trend12m = JSON.parse(document.getElementById('admin-svc-trend-12m').textContent);
    const modVol   = JSON.parse(document.getElementById('admin-module-volume').textContent);
    const statuses = JSON.parse(document.getElementById('admin-all-statuses').textContent);
    const actData  = JSON.parse(document.getElementById('admin-activity-data').textContent);

    // ── A. Service Request Trend (Line, with period toggle) ──
    let svcTrendChart = null;
    function renderSvcTrend(data) {
        const ctx = document.getElementById('adminSvcTrendChart');
        if (!ctx) return;
        if (svcTrendChart) { svcTrendChart.destroy(); svcTrendChart = null; }
        if (!data || data.length === 0) return;
        svcTrendChart = window.HIMSChart.createLineChart(
            ctx,
            'Total Requests',
            data.map(d => d.label),
            data.map(d => d.total),
            'requests'
        );
    }
    renderSvcTrend(trend6m);

    const periodSel = document.getElementById('adminTrendPeriod');
    if (periodSel) {
        periodSel.addEventListener('change', function () {
            renderSvcTrend(this.value === '12' ? trend12m : trend6m);
        });
    }

    // ── B. Module Volume Bar Chart ──
    const modCtx = document.getElementById('adminModuleBarChart');
    if (modCtx && modVol.length > 0) {
        const isDark = window.HIMSChart.isDarkMode();
        const gridColor = isDark ? '#262626' : '#eef1f5';
        const tickColor = isDark ? '#94A3B8' : '#929aaa';
        new Chart(modCtx, {
            type: 'bar',
            data: {
                labels: modVol.map(m => m.module),
                datasets: [{
                    label: 'Total Requests',
                    data: modVol.map(m => m.total),
                    backgroundColor: 'rgba(47, 143, 107, 0.75)',
                    borderColor: '#2f8f6b',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#172033', padding: 12, cornerRadius: 8 }
                },
                scales: {
                    x: { ticks: { color: tickColor, font: { size: 12 } }, grid: { display: false }, border: { display: false } },
                    y: { beginAtZero: true, ticks: { color: tickColor, precision: 0, font: { size: 12 } }, grid: { color: gridColor }, border: { display: false } }
                }
            }
        });
    }

    // ── C. Global Request Status Doughnut ──
    const statusCtx = document.getElementById('adminStatusDonut');
    const statusLabels = Object.keys(statuses);
    const statusValues = Object.values(statuses);
    if (statusCtx && statusValues.some(v => v > 0)) {
        window.HIMSChart.createDoughnutChart(
            statusCtx,
            statusLabels,
            statusValues,
            { 'Pending': '#2196F3', 'Completed': '#4CAF50', 'Cancelled': '#D32F2F' }
        );
    }

    // ── D. System Activity Line Chart (7 days) ──
    const actCtx = document.getElementById('adminActivityChart');
    if (actCtx && actData.length > 0) {
        window.HIMSChart.createLineChart(
            actCtx,
            'Activity Events',
            actData.map(d => d.date),
            actData.map(d => d.count),
            'events'
        );
    }
})();
</script>
@endpush

<style>
.card-hover-elevate {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card-hover-elevate:hover {
    transform: translateY(-3px);
    box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.08)!important;
}
.role-hover-card {
    transition: transform 0.15s ease, border-color 0.15s ease;
}
.role-hover-card:hover {
    transform: translateY(-2px);
    border-color: var(--signal) !important;
}

/* ── Dark Theme Overrides for Admin Dashboard ── */
html[data-theme="dark"] .bg-white {
    background-color: var(--card) !important;
}
html[data-theme="dark"] .card-header.bg-white {
    background-color: var(--card) !important;
    border-bottom-color: var(--line) !important;
}
html[data-theme="dark"] .list-group-item {
    background-color: var(--card) !important;
    color: var(--text) !important;
    border-color: var(--line) !important;
}
html[data-theme="dark"] .table-light,
html[data-theme="dark"] .table-light th {
    background-color: #171717 !important;
    color: #D4D4D4 !important;
    border-bottom-color: var(--line) !important;
}
html[data-theme="dark"] .bg-light,
html[data-theme="dark"] .bg-light.bg-opacity-50 {
    background-color: #171717 !important;
    color: var(--text) !important;
}
html[data-theme="dark"] .border-light-subtle {
    border-color: var(--line) !important;
}
html[data-theme="dark"] .text-dark {
    color: #FFFFFF !important;
}
html[data-theme="dark"] .badge.bg-light {
    background-color: #171717 !important;
    color: #FFFFFF !important;
    border-color: var(--line) !important;
}
html[data-theme="dark"] .badge.bg-danger-subtle {
    background-color: rgba(232, 92, 85, 0.2) !important;
    color: #ff766f !important;
}
html[data-theme="dark"] .badge.bg-warning-subtle {
    background-color: rgba(224, 160, 48, 0.2) !important;
    color: var(--amber) !important;
}
html[data-theme="dark"] .badge.bg-info-subtle {
    background-color: rgba(13, 202, 240, 0.2) !important;
    color: #38bdf8 !important;
}
html[data-theme="dark"] .badge.bg-secondary-subtle {
    background-color: rgba(108, 117, 125, 0.2) !important;
    color: #cbd5e1 !important;
}
html[data-theme="dark"] .badge.bg-success-subtle {
    background-color: rgba(20, 199, 154, 0.2) !important;
    color: var(--signal) !important;
}
</style>
@endsection
