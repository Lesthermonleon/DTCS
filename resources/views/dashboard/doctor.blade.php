@extends('layouts.app')
@section('title', 'Doctor Dashboard')
@section('page-title', 'Doctor Main Dashboard')

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

    /* Dark Theme Overrides for Admin Tile Cards */
    html[data-theme="dark"] .admin-tool-tile {
        background-color: var(--card) !important;
        border-color: var(--line) !important;
    }
    html[data-theme="dark"] .admin-tool-tile:hover,
    html[data-theme="dark"] .admin-tool-tile:focus-visible {
        background-color: rgba(20, 199, 154, 0.12) !important;
        border-color: var(--signal) !important;
    }
</style>
@endpush

@section('content')


{{-- ── 2. Summary Cards (All Clickable) ── --}}
<div class="balanced-grid balanced-grid-6 mb-4">
    {{-- Card 1: My Patients --}}
    <a href="{{ route('patients.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">My Patients</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['my_patients']) }}</h3>
                <div class="small text-primary mt-2">
                    View Directory <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 2: Pending Tasks --}}
    <a href="{{ route('lab.requests.index', ['status' => 'Pending']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pending Tasks</span>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['pending_tasks']) }}</h3>
                <div class="small text-warning mt-2">
                    Action Required <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 3: Critical Alerts --}}
    <a href="{{ route('lab.results.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Critical Alerts</span>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['critical_alerts']) }}</h3>
                <div class="small text-danger mt-2">
                    Review Alerts <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 4: Lab Results Awaiting Review --}}
    <a href="{{ route('lab.results.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Lab Results</span>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                        <i class="bi bi-clipboard2-pulse-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['lab_awaiting']) }}</h3>
                <div class="small text-info mt-2">
                    Open Results <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 5: Imaging Reports Awaiting Review --}}
    <a href="{{ route('radiology.reports.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Imaging Reports</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                        <i class="bi bi-activity fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['rad_awaiting']) }}</h3>
                <div class="small text-primary mt-2">
                    Open Reports <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 6: Upcoming Surgeries --}}
    <a href="{{ route('surgery.calendar') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Surgeries</span>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                        <i class="bi bi-scissors fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['upcoming_surgeries']) }}</h3>
                <div class="small text-success mt-2">
                    View Calendar <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>
</div>

{{-- ── 3. Critical Alerts & Pending Tasks Row ── --}}
<div class="row g-4 mb-4">
    {{-- Critical Alerts --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger">
                    <i class="bi bi-bell-fill me-2"></i>Critical Clinical Alerts
                </h6>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                    {{ number_format($stats['critical_alerts']) }} Active
                </span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($releasedLabResults->take(3) as $labResult)
                        @php
                            $pt = $labResult->requestItem?->labRequest?->patient;
                        @endphp
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-danger me-2">Lab Result</span>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $pt?->full_name ?? 'Patient Record' }}</h6>
                                    </div>
                                    <p class="small text-muted mb-1">
                                        Test: <strong>{{ $labResult->requestItem?->test_name ?? 'Laboratory Test' }}</strong> &bull; Value: <span class="text-danger fw-bold">{{ $labResult->result_value ?? 'Released' }}</span>
                                    </p>
                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i>Released {{ $labResult->released_at?->diffForHumans() ?? 'recently' }}</div>
                                </div>
                                <a href="{{ route('lab.results.show', $labResult) }}" class="btn btn-sm btn-outline-danger shadow-sm">
                                    <i class="bi bi-eye me-1"></i> Review
                                </a>
                            </div>
                        </div>
                    @empty
                    @endforelse

                    @forelse($releasedRadReports->take(3) as $radReport)
                        @php
                            $pt = $radReport->radiologyRequest?->patient;
                        @endphp
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-secondary me-2">Radiology Report</span>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $pt?->full_name ?? 'Patient Record' }}</h6>
                                    </div>
                                    <p class="small text-muted mb-1">
                                        Modality: <strong>{{ $radReport->radiologyRequest?->modality ?? 'Imaging' }}</strong> &bull; Impression: {{ Str::limit($radReport->impression ?? 'Report available', 40) }}
                                    </p>
                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i>Approved {{ $radReport->approved_at?->diffForHumans() ?? 'recently' }}</div>
                                </div>
                                <a href="{{ route('radiology.reports.show', $radReport) }}" class="btn btn-sm btn-outline-secondary shadow-sm">
                                    <i class="bi bi-file-earmark-medical me-1"></i> View Report
                                </a>
                            </div>
                        </div>
                    @empty
                    @endforelse

                    @if($releasedLabResults->isEmpty() && $releasedRadReports->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-shield-check fs-1 text-success opacity-50 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">No critical alerts</h6>
                            <p class="small text-muted mb-0">You're all caught up. No diagnostic alerts require immediate action.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Tasks --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-check2-square me-2 text-warning"></i>Pending Clinical Tasks
                </h6>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                    {{ number_format($stats['pending_tasks']) }} Pending
                </span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($pendingLabRequests->take(2) as $pLab)
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle mb-1">Lab Request</span>
                                    <div class="fw-bold text-dark">{{ $pLab->patient->full_name }}</div>
                                    <div class="small text-muted">Rx #: {{ $pLab->request_no }} &bull; Priority: {{ $pLab->priority }}</div>
                                </div>
                                <a href="{{ route('lab.requests.show', $pLab) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Review
                                </a>
                            </div>
                        </div>
                    @empty
                    @endforelse

                    @forelse($pendingRadRequests->take(2) as $pRad)
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-secondary-subtle text-secondary border mb-1">Imaging Order</span>
                                    <div class="fw-bold text-dark">{{ $pRad->patient->full_name }}</div>
                                    <div class="small text-muted">{{ $pRad->modality }} - {{ $pRad->body_part }}</div>
                                </div>
                                <a href="{{ route('radiology.requests.show', $pRad) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Review
                                </a>
                            </div>
                        </div>
                    @empty
                    @endforelse

                    @forelse($pendingRx->take(2) as $pRx)
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-success-subtle text-success border mb-1">Prescription</span>
                                    <div class="fw-bold text-dark">{{ $pRx->patient->full_name }}</div>
                                    <div class="small text-muted">Rx #: {{ $pRx->prescription_no }}</div>
                                </div>
                                <a href="{{ route('pharmacy.prescriptions.show', $pRx) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Review
                                </a>
                            </div>
                        </div>
                    @empty
                    @endforelse

                    @if($pendingLabRequests->isEmpty() && $pendingRadRequests->isEmpty() && $pendingRx->isEmpty() && $pendingSurgery->isEmpty() && $pendingDiet->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-check fs-1 text-primary opacity-50 d-block mb-2"></i>
                            <h6 class="fw-bold text-dark mb-1">No pending tasks</h6>
                            <p class="small text-muted mb-0">No tasks require your attention right now.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 4. Clinical Request Trend & Service Analytics ── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Clinical Request Trend</h3>
                    <p class="chart-subtitle">Monthly clinical requests across all service modules</p>
                </div>
                <select id="doctorTrendPeriod" class="form-select form-select-sm" style="width:auto;">
                    <option value="6">Last 6 Months</option>
                    <option value="12">Last 12 Months</option>
                </select>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="doctorTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-bar-chart-fill me-2 text-success"></i>Requests by Service</h3>
                    <p class="chart-subtitle">Total requests across all clinical services</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="doctorServiceChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── 5. Quick Actions & Clinical Module Overview ── --}}
<div class="row g-4 mb-4">
    {{-- Quick Actions --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-lightning-charge-fill me-2 text-success"></i>Quick Actions
                </h6>
                <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                    Frequently executed clinical actions and order creations
                </small>
            </div>
            <div class="card-body">
                <div class="balanced-grid balanced-grid-3 admin-tools-grid">
                    {{-- Tile 1: New Patient --}}
                    <a href="{{ route('patients.create') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-person-plus-fill fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">New Patient</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Register a new patient record
                        </p>
                    </a>

                    {{-- Tile 2: New Lab Request --}}
                    <a href="{{ route('lab.requests.create') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-clipboard2-pulse-fill fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">New Lab Request</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Create laboratory order
                        </p>
                    </a>

                    {{-- Tile 3: New Imaging Request --}}
                    <a href="{{ route('radiology.requests.create') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-activity fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">New Imaging Request</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Create radiology order
                        </p>
                    </a>

                    {{-- Tile 4: New Prescription --}}
                    <a href="{{ route('pharmacy.prescriptions.create') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-capsule fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">New Prescription</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Create pharmacy prescription
                        </p>
                    </a>

                    {{-- Tile 5: New Surgery Request --}}
                    <a href="{{ route('surgery.requests.create') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-scissors fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">New Surgery Request</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Create operating room request
                        </p>
                    </a>

                    {{-- Tile 6: New Diet Request --}}
                    <a href="{{ route('diet.requests.create') }}"
                       class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                                <i class="bi bi-apple fs-5"></i>
                            </div>
                            <div class="fw-bold text-dark small">New Diet Request</div>
                        </div>
                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                            Create nutrition diet order
                        </p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Clinical Orders Breakdown & Workload Scope --}}
    <div class="col-lg-6">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Clinical Orders Breakdown</h3>
                    <p class="chart-subtitle">Distribution of pending requests across clinical modules</p>
                </div>
            </div>
            <div class="chart-container" style="height: 220px;">
                <canvas id="doctorOrdersChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── 6. Clinical Module Overview Section ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
            <i class="bi bi-grid-3x3-gap-fill me-2 text-success"></i>Clinical Module Overview
        </h6>
        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
            Access clinical departments and view active module workload
        </small>
    </div>
    <div class="card-body">
        <div class="balanced-grid balanced-grid-5">
            {{-- LIS Card --}}
            <a href="{{ route('lab.dashboard') }}"
               class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                        <i class="bi bi-clipboard2-pulse fs-5"></i>
                    </div>
                    <div class="fw-bold text-dark small">Laboratory (LIS)</div>
                </div>
                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                    {{ number_format($stats['my_lab_requests']) }} lab requests recorded
                </p>
            </a>

            {{-- RIS Card --}}
            <a href="{{ route('radiology.dashboard') }}"
               class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                        <i class="bi bi-activity fs-5"></i>
                    </div>
                    <div class="fw-bold text-dark small">Radiology (RIS)</div>
                </div>
                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                    {{ number_format($stats['my_radiology']) }} imaging requests recorded
                </p>
            </a>

            {{-- PMS Card --}}
            <a href="{{ route('pharmacy.dashboard') }}"
               class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                        <i class="bi bi-capsule fs-5"></i>
                    </div>
                    <div class="fw-bold text-dark small">Pharmacy (PMS)</div>
                </div>
                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                    {{ number_format($stats['my_prescriptions']) }} prescriptions recorded
                </p>
            </a>

            {{-- SORS Card --}}
            <a href="{{ route('surgery.dashboard') }}"
               class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                        <i class="bi bi-scissors fs-5"></i>
                    </div>
                    <div class="fw-bold text-dark small">Surgery (SORS)</div>
                </div>
                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                    {{ number_format($stats['my_surgeries']) }} procedures requested
                </p>
            </a>

            {{-- DNMS Card --}}
            <a href="{{ route('diet.dashboard') }}"
               class="card border border-light-subtle shadow-xs h-100 p-3 bg-white text-decoration-none admin-tool-tile">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-2 d-flex align-items-center justify-content-center transition-all" style="width: 36px; height: 36px;">
                        <i class="bi bi-apple fs-5"></i>
                    </div>
                    <div class="fw-bold text-dark small">Nutrition (DNMS)</div>
                </div>
                <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.3;">
                    {{ number_format($stats['my_diet_requests']) }} diet plans requested
                </p>
            </a>
        </div>
    </div>
</div>

{{-- ── 7. Recent Patients Section ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-person-lines-fill me-2 text-primary"></i>Recent Patient Access
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
                        <th>Last Activity</th>
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
                                <span class="small text-muted"><i class="bi bi-clock me-1"></i>Updated {{ $patient->updated_at?->diffForHumans() }}</span>
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
                                No recent patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="application/json" id="doctor-orders-data">
    {"lab": {{ $pendingLabRequests->count() }}, "rad": {{ $pendingRadRequests->count() }}, "pms": {{ $pendingRx->count() }}, "sors": {{ $pendingSurgery->count() }}, "dnms": {{ $pendingDiet->count() }}}
</script>

<script type="application/json" id="doctor-trend-6m">@json($doctorTrend6m)</script>
<script type="application/json" id="doctor-trend-12m">@json($doctorTrend12m)</script>
<script type="application/json" id="doctor-service-breakdown">@json($doctorServiceBreakdown)</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const ordersData = JSON.parse(document.getElementById('doctor-orders-data').textContent);
    const ordersCtx = document.getElementById('doctorOrdersChart');

    if (ordersCtx && typeof window.HIMSChart !== 'undefined') {
        window.HIMSChart.createDoughnutChart(
            ordersCtx,
            ['Pending Lab', 'Pending Radiology', 'Pending Prescriptions', 'Pending Surgeries', 'Pending Diets'],
            [ordersData.lab, ordersData.rad, ordersData.pms, ordersData.sors, ordersData.dnms],
            ['#4CAF50', '#009688', '#00838F', '#81C784', '#90A4AE']
        );
    }

    // Doctor Trend Chart (6m/12m toggle)
    if (typeof window.HIMSChart === 'undefined') return;
    const trend6m  = JSON.parse(document.getElementById('doctor-trend-6m').textContent);
    const trend12m = JSON.parse(document.getElementById('doctor-trend-12m').textContent);
    const svcData  = JSON.parse(document.getElementById('doctor-service-breakdown').textContent);

    let doctorTrendChart = null;
    function renderDoctorTrend(data) {
        const ctx = document.getElementById('doctorTrendChart');
        if (!ctx) return;
        if (doctorTrendChart) { doctorTrendChart.destroy(); doctorTrendChart = null; }
        if (!data || data.length === 0) return;
        doctorTrendChart = window.HIMSChart.createLineChart(ctx, 'Total Requests', data.map(d => d.label), data.map(d => d.total), 'requests');
    }
    renderDoctorTrend(trend6m);

    const sel = document.getElementById('doctorTrendPeriod');
    if (sel) sel.addEventListener('change', () => renderDoctorTrend(sel.value === '12' ? trend12m : trend6m));

    // Service Breakdown Bar
    const svcCtx = document.getElementById('doctorServiceChart');
    if (svcCtx && svcData.length > 0) {
        const isDark = window.HIMSChart.isDarkMode();
        const grid = isDark ? '#262626' : '#eef1f5';
        const tick = isDark ? '#94A3B8' : '#929aaa';
        new Chart(svcCtx, {
            type: 'bar',
            data: {
                labels: svcData.map(s => s.service),
                datasets: [{ label: 'Requests', data: svcData.map(s => s.total), backgroundColor: 'rgba(47,143,107,0.75)', borderColor: '#2f8f6b', borderWidth: 1, borderRadius: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeOutQuart' },
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#172033', padding: 12, cornerRadius: 8 } },
                scales: { x: { ticks: { color: tick, font: { size: 12 } }, grid: { display: false }, border: { display: false } }, y: { beginAtZero: true, ticks: { color: tick, precision: 0 }, grid: { color: grid }, border: { display: false } } }
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

/* ── Dark Theme Overrides for Clinical Dashboard ── */
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
