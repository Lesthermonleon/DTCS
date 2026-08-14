@extends('layouts.app')
@section('title', 'Doctor Dashboard')
@section('page-title', 'Doctor Main Dashboard')

@section('content')


{{-- ── 2. Summary Cards (All Clickable) ── --}}
<div class="row g-3 mb-4">
    {{-- Card 1: My Patients --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 2: Pending Tasks --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 3: Critical Alerts --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 4: Lab Results Awaiting Review --}}
    <div class="col-sm-6 col-xl-2">
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
    </div>

    {{-- Card 5: Imaging Reports Awaiting Review --}}
    <div class="col-sm-6 col-xl-2">
        <a href="{{ route('radiology.reports.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Imaging Reports</span>
                        <div class="bg-purple bg-opacity-10 text-purple rounded-circle p-2" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                            <i class="bi bi-activity fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['rad_awaiting']) }}</h3>
                    <div class="small mt-2" style="color: #6f42c1;">
                        Open Reports <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card 6: Upcoming Surgeries --}}
    <div class="col-sm-6 col-xl-2">
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
                                        <span class="badge bg-purple me-2" style="background-color: #6f42c1;">Radiology Report</span>
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

{{-- ── 4. Clinical Module Overview Section ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Clinical Module Overview
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
                    <p class="small text-muted mb-3">{{ number_format($stats['my_lab_requests']) }} lab requests recorded</p>
                    <a href="{{ route('lab.dashboard') }}" class="btn btn-sm btn-primary w-100">
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
                    <p class="small text-muted mb-3">{{ number_format($stats['my_radiology']) }} imaging requests recorded</p>
                    <a href="{{ route('radiology.dashboard') }}" class="btn btn-sm btn-secondary w-100">
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
                    <p class="small text-muted mb-3">{{ number_format($stats['my_prescriptions']) }} prescriptions recorded</p>
                    <a href="{{ route('pharmacy.dashboard') }}" class="btn btn-sm btn-success w-100">
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
                    <p class="small text-muted mb-3">{{ number_format($stats['my_surgeries']) }} procedures requested</p>
                    <a href="{{ route('surgery.dashboard') }}" class="btn btn-sm btn-danger w-100">
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
                    <p class="small text-muted mb-3">{{ number_format($stats['my_diet_requests']) }} diet plans requested</p>
                    <a href="{{ route('diet.dashboard') }}" class="btn btn-sm btn-warning w-100">
                        <i class="bi bi-box-arrow-in-up-right me-1"></i> Open DNMS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 5. Recent Patients Section ── --}}
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
                                <span class="badge bg-secondary-subtle text-secondary border">{{ $patient->patient_type ?? 'Outpatient' }}</span>
                                @if($patient->ward)
                                    <small class="text-muted d-block">{{ $patient->ward }} (Bed {{ $patient->bed_number ?? '-' }})</small>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted"><i class="bi bi-clock me-1"></i>Updated {{ $patient->updated_at?->diffForHumans() }}</span>
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
                                No recent patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── 6. Quick Actions Section ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-lightning-charge-fill me-2 text-warning"></i>Quick Actions
        </h6>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> + New Patient
            </a>
            <a href="{{ route('lab.requests.create') }}" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle me-1"></i> New Lab Request
            </a>
            <a href="{{ route('radiology.requests.create') }}" class="btn btn-outline-secondary">
                <i class="bi bi-plus-circle me-1"></i> New Imaging Request
            </a>
            <a href="{{ route('pharmacy.prescriptions.create') }}" class="btn btn-outline-success">
                <i class="bi bi-plus-circle me-1"></i> New Prescription
            </a>
            <a href="{{ route('surgery.requests.create') }}" class="btn btn-outline-danger">
                <i class="bi bi-plus-circle me-1"></i> New Surgery Request
            </a>
            <a href="{{ route('diet.requests.create') }}" class="btn btn-outline-warning">
                <i class="bi bi-plus-circle me-1"></i> New Diet Request
            </a>
        </div>
    </div>
</div>

{{-- ── 6. Clinical Analytics & Volume Breakdown ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Clinical Analytics & Departmental Volume
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-4">
            {{-- Chart 1: Clinical Category Orders --}}
            <div class="col-lg-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-3">Clinical Orders Breakdown</h6>
                <div style="position: relative; width: 100%; max-width: 320px; height: 260px; margin: 0 auto;">
                    <canvas id="doctorOrdersChart"></canvas>
                </div>
            </div>

            {{-- Chart 2: Clinical Activity --}}
            <div class="col-lg-6">
                <h6 class="fw-bold text-muted small text-uppercase mb-3">Pending Tasks & Patient Scope Summary</h6>
                <div class="p-3 border rounded-3 bg-light bg-opacity-50 h-100 d-flex flex-column justify-content-center">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted fw-semibold"><i class="bi bi-folder2-open text-primary me-2"></i>Patient Directory Scope:</span>
                        <span class="fw-bold fs-5 text-dark">{{ number_format($stats['my_patients']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted fw-semibold"><i class="bi bi-check2-square text-warning me-2"></i>Pending Clinical Tasks:</span>
                        <span class="fw-bold fs-5 text-warning">{{ number_format($stats['pending_tasks']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="text-muted fw-semibold"><i class="bi bi-clipboard2-check text-info me-2"></i>Released Lab Results:</span>
                        <span class="fw-bold fs-5 text-info">{{ number_format($stats['lab_awaiting']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted fw-semibold"><i class="bi bi-file-earmark-medical text-success me-2"></i>Released Radiology Reports:</span>
                        <span class="fw-bold fs-5 text-success">{{ number_format($stats['rad_awaiting']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/json" id="doctor-orders-data">
    {"lab": {{ $pendingLabRequests->count() }}, "rad": {{ $pendingRadRequests->count() }}, "pms": {{ $pendingRx->count() }}, "sors": {{ $pendingSurgery->count() }}, "dnms": {{ $pendingDiet->count() }}}
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const textColor = isDark ? '#E2E8F0' : '#1A2B26';
    const ordersData = JSON.parse(document.getElementById('doctor-orders-data').textContent);

    const ordersCtx = document.getElementById('doctorOrdersChart');
    if (ordersCtx) {
        new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending Lab', 'Pending Radiology', 'Pending Prescriptions', 'Pending Surgeries', 'Pending Diets'],
                datasets: [{
                    data: [ordersData.lab, ordersData.rad, ordersData.pms, ordersData.sors, ordersData.dnms],
                    backgroundColor: ['#0DCAF0', '#6F42C1', '#198754', '#DC3545', '#FFC107'],
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
