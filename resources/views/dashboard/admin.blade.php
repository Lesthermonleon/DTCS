@extends('layouts.app')
@section('title', 'System Administrator Dashboard')
@section('page-title', 'System Administrator Dashboard')

@section('content')

{{-- ── 2. Summary Cards (All Clickable to Real Routes) ── --}}
<div class="row g-3 mb-4">
    {{-- Card 1: Total Users --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 2: Active Users --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 3: Total Patients --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 4: Pending Administrative Tasks --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 5: System Alerts --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 6: Today's System Activity --}}
    <div class="col-sm-6 col-xl-2">
        <a href="#recent-activity-section" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Today Activity</span>
                        <div class="bg-purple bg-opacity-10 text-purple rounded-circle p-2" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['today_activity_count']) }}</h3>
                    <div class="small mt-2" style="color: #6f42c1;">
                        Audit Logs <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ── 3. User & Role Overview ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-diagram-3-fill me-2 text-primary"></i>User & Role Distribution Overview
        </h6>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-people me-1"></i> Manage Users
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @forelse($usersByRole as $roleItem)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                        <div class="p-3 border rounded-3 bg-light bg-opacity-50 h-100 d-flex align-items-center justify-content-between role-hover-card transition-all">
                            <div>
                                <div class="fw-bold text-dark mb-1">{{ $roleItem['name'] }}</div>
                                <div class="small text-muted">Role Slug: <code>{{ $roleItem['slug'] }}</code></div>
                            </div>
                            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">{{ $roleItem['count'] }}</span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-3 text-muted">
                    No roles found.
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── 4. System Security Alerts & Recent System Activity Row ── --}}
<div class="row g-4 mb-4">
    {{-- System Security Alerts --}}
    <div class="col-lg-5" id="system-alerts-section">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger">
                    <i class="bi bi-shield-alert me-2"></i>System & Security Alerts
                </h6>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                    {{ $systemAlerts->count() }} Active
                </span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($systemAlerts as $alert)
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex align-items-start gap-3">
                                <div class="p-2 rounded bg-{{ $alert['type'] === 'danger' ? 'danger' : ($alert['type'] === 'warning' ? 'warning' : 'info') }} bg-opacity-10 text-{{ $alert['type'] === 'danger' ? 'danger' : ($alert['type'] === 'warning' ? 'warning' : 'info') }}">
                                    <i class="bi {{ $alert['icon'] }} fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark mb-1">{{ $alert['title'] }}</h6>
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

    {{-- Recent System Activity Log --}}
    <div class="col-lg-7" id="recent-activity-section">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-journal-text me-2 text-primary"></i>Recent System Activity
                </h6>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-eye me-1"></i> View Audit Logs
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">User</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Description</th>
                                <th class="pe-3">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivity as $log)
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark">
                                        {{ $log->user?->name ?? 'System' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            {{ $log->action ?? 'Action' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border">
                                            {{ $log->module ?? 'System' }}
                                        </span>
                                    </td>
                                    <td class="small text-muted" style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $log->description ?? 'N/A' }}
                                    </td>
                                    <td class="pe-3 small text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ $log->created_at?->diffForHumans() ?? 'recently' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-1 opacity-50"></i>
                                        No recent system activity recorded yet.
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

{{-- ── 5. Clinical Module Overview Section ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Clinical Module Operational Overview
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- LIS Card --}}
            <div class="col-md-6 col-lg">
                <div class="card border border-light-subtle shadow-xs h-100 p-3 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary text-white rounded p-2 me-2">
                            <i class="bi bi-clipboard2-pulse fs-5"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Laboratory (LIS)</h6>
                    </div>
                    <p class="small text-muted mb-3">{{ number_format($moduleStats['lis']['pending']) }} pending lab requests</p>
                    <a href="{{ route($moduleStats['lis']['route']) }}" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-box-arrow-in-up-right me-1"></i> Open LIS
                    </a>
                </div>
            </div>

            {{-- RIS Card --}}
            <div class="col-md-6 col-lg">
                <div class="card border border-light-subtle shadow-xs h-100 p-3 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-purple text-white rounded p-2 me-2" style="background-color: #6f42c1;">
                            <i class="bi bi-activity fs-5"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Radiology (RIS)</h6>
                    </div>
                    <p class="small text-muted mb-3">{{ number_format($moduleStats['ris']['pending']) }} pending imaging requests</p>
                    <a href="{{ route($moduleStats['ris']['route']) }}" class="btn btn-sm btn-secondary w-100">
                        <i class="bi bi-box-arrow-in-up-right me-1"></i> Open RIS
                    </a>
                </div>
            </div>

            {{-- PMS Card --}}
            <div class="col-md-6 col-lg">
                <div class="card border border-light-subtle shadow-xs h-100 p-3 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-success text-white rounded p-2 me-2">
                            <i class="bi bi-capsule fs-5"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Pharmacy (PMS)</h6>
                    </div>
                    <p class="small text-muted mb-3">{{ number_format($moduleStats['pms']['pending']) }} pending prescriptions</p>
                    <a href="{{ route($moduleStats['pms']['route']) }}" class="btn btn-sm btn-success w-100">
                        <i class="bi bi-box-arrow-in-up-right me-1"></i> Open PMS
                    </a>
                </div>
            </div>

            {{-- SORS Card --}}
            <div class="col-md-6 col-lg">
                <div class="card border border-light-subtle shadow-xs h-100 p-3 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-danger text-white rounded p-2 me-2">
                            <i class="bi bi-scissors fs-5"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Surgery (SORS)</h6>
                    </div>
                    <p class="small text-muted mb-3">{{ number_format($moduleStats['sors']['pending']) }} pending surgery requests</p>
                    <a href="{{ route($moduleStats['sors']['route']) }}" class="btn btn-sm btn-danger w-100">
                        <i class="bi bi-box-arrow-in-up-right me-1"></i> Open SORS
                    </a>
                </div>
            </div>

            {{-- DNMS Card --}}
            <div class="col-md-6 col-lg">
                <div class="card border border-light-subtle shadow-xs h-100 p-3 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-warning text-dark rounded p-2 me-2">
                            <i class="bi bi-apple fs-5"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Nutrition (DNMS)</h6>
                    </div>
                    <p class="small text-muted mb-3">{{ number_format($moduleStats['dnms']['pending']) }} pending diet requests</p>
                    <a href="{{ route($moduleStats['dnms']['route']) }}" class="btn btn-sm btn-warning w-100">
                        <i class="bi bi-box-arrow-in-up-right me-1"></i> Open DNMS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 6. Administrative Quick Actions Section ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-lightning-charge-fill me-2 text-warning"></i>Administrative Quick Actions
        </h6>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i> + Create User
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-people-fill me-1"></i> User Management
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-shield-lock-fill me-1"></i> Roles & Permissions
            </a>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-info">
                <i class="bi bi-folder2-open me-1"></i> Patient Directory
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark">
                <i class="bi bi-journal-text me-1"></i> Audit Logs
            </a>
            <a href="{{ route('settings.index') }}" class="btn btn-outline-success">
                <i class="bi bi-gear-fill me-1"></i> System Settings
            </a>
        </div>
    </div>
</div>

{{-- ── 7. Patient Information Overview ── --}}
<div class="card border-0 shadow-sm mb-4">
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
                                <span class="badge bg-secondary-subtle text-secondary border">{{ $patient->patient_type ?? 'Outpatient' }}</span>
                                @if($patient->ward)
                                    <small class="text-muted d-block">{{ $patient->ward }} (Bed {{ $patient->bed_number ?? '-' }})</small>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted"><i class="bi bi-clock me-1"></i>{{ $patient->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-person-badge me-1"></i> View Profile
                                </a>
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

{{-- ── 7. System Analytics & Visualizations ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-graph-up-arrow me-2 text-primary"></i>System Analytics & Operational Trends
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-4">
            {{-- Chart 1: Role Distribution --}}
            <div class="col-lg-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-3">User Distribution by Role</h6>
                <div style="position: relative; width: 100%; max-width: 320px; height: 260px; margin: 0 auto;">
                    <canvas id="adminRoleChart"></canvas>
                </div>
            </div>

            {{-- Chart 2: 7-Day Registration Trend --}}
            <div class="col-lg-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-3">User Registration Trend (Last 7 Days)</h6>
                <div style="position: relative; width: 100%; height: 260px;">
                    <canvas id="adminTrendChart"></canvas>
                </div>
            </div>
        </div>
<script type="application/json" id="admin-roles-data">@json($usersByRole)</script>
<script type="application/json" id="admin-trend-data">@json($newUsers7d)</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#E2E8F0' : '#1A2B26';
    const gridColor = isDark ? '#2A3A35' : '#E2E8F0';

    const rolesData = JSON.parse(document.getElementById('admin-roles-data').textContent);
    const trendData = JSON.parse(document.getElementById('admin-trend-data').textContent);

    // 1. Role Distribution Chart
    const roleCtx = document.getElementById('adminRoleChart');
    if (roleCtx && rolesData.length > 0) {
        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: rolesData.map(r => r.name),
                datasets: [{
                    data: rolesData.map(r => r.count),
                    backgroundColor: ['#14C79A', '#4C7EA8', '#E0A030', '#E85C55', '#6F42C1', '#20C997', '#FD7E14', '#0D6EFD'],
                    borderWidth: 2,
                    borderColor: isDark ? '#172B26' : '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 } } }
                }
            }
        });
    }

    // 2. Trend Line Chart
    const trendCtx = document.getElementById('adminTrendChart');
    if (trendCtx && trendData.length > 0) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(t => t.date),
                datasets: [{
                    label: 'Registrations',
                    data: trendData.map(t => t.count),
                    borderColor: '#14C79A',
                    backgroundColor: 'rgba(20, 199, 154, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: textColor } },
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, precision: 0 } }
                }
            }
        });
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
    background-color: #172B26 !important;
    color: #94A3B8 !important;
    border-bottom-color: var(--line) !important;
}
html[data-theme="dark"] .bg-light,
html[data-theme="dark"] .bg-light.bg-opacity-50 {
    background-color: rgba(23, 45, 40, 0.6) !important;
    color: var(--text) !important;
}
html[data-theme="dark"] .border-light-subtle {
    border-color: var(--line) !important;
}
html[data-theme="dark"] .text-dark {
    color: #F8FAFC !important;
}
html[data-theme="dark"] .badge.bg-light {
    background-color: #172B26 !important;
    color: #E2E8F0 !important;
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
    color: #0dcaf0 !important;
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
