@extends('layouts.app')
@section('title', 'Radiology Dashboard')
@section('page-title', 'Radiology Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Radiology Dashboard</li>
@endsection

@section('content')

{{-- ── Quick Action Bar ── --}}
<div class="card mb-4 border-0 shadow-sm bg-body">
    <div class="card-body p-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 gap-sm-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-info bg-opacity-10 text-info fs-6 px-3 py-2 text-nowrap">
                <i class="bi bi-camera-reels me-1"></i> {{ $isRadiologist ? 'Radiologist Interpretation Center' : 'Radiologic Technologist Workspace' }}
            </span>
            <span class="text-muted small text-nowrap d-none d-md-inline">
                {{ $isRadiologist ? 'Review diagnostic imaging studies, record clinical interpretations, and authorize reports.' : 'Manage imaging procedure scheduling, technologist workflow, and image acquisition.' }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-sm-auto justify-content-start justify-content-sm-end">
            <a href="{{ route('radiology.requests.index') }}" class="btn btn-sm btn-primary text-nowrap flex-grow-1 flex-sm-grow-0">
                <i class="bi bi-images me-1"></i> View Imaging Requests
            </a>
            @if(($isRadiologist || auth()->user()->hasAnyRole(['admin', 'doctor', 'rad-tech'])) && Route::has('radiology.reports.index'))
            <a href="{{ route('radiology.reports.index') }}" class="btn btn-sm btn-outline-info text-nowrap flex-grow-1 flex-sm-grow-0">
                <i class="bi bi-file-earmark-medical me-1"></i> View Radiology Reports
            </a>
            @endif
        </div>
    </div>
</div>

{{-- ── Stat Cards (Admin & Doctor Style) ── --}}
<div class="row g-3 mb-4">
    {{-- Card 1: Total Procedures --}}
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('radiology.requests.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Total Procedures</span>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                            <i class="bi bi-camera-reels-fill fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_requests']) }}</h3>
                    <div class="small text-primary mt-2">
                        View Procedures <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card 2: Pending Imaging --}}
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('radiology.requests.index', ['status' => 'Pending']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Pending Requests</span>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['pending']) }}</h3>
                    <div class="small text-warning mt-2">
                        Process Pending <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card 3: Completed Today --}}
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('radiology.requests.index', ['status' => 'Completed']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Completed Today</span>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                            <i class="bi bi-check2-circle fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['today_completed']) }}</h3>
                    <div class="small text-success mt-2">
                        View Completed <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card 4: Role-Aware Card --}}
    <div class="col-sm-6 col-xl-3">
        @if($isRadiologist || auth()->user()->hasAnyRole(['admin', 'doctor']))
            <a href="{{ route('radiology.reports.index', ['status' => 'Draft']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Pending Interpretation</span>
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2">
                                <i class="bi bi-file-earmark-text-fill fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['reports_pending'] ?? 0) }}</h3>
                        <div class="small text-danger mt-2">
                            Review Reports <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        @else
            <a href="{{ route('radiology.requests.index', ['status' => 'In Progress']) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-semibold text-uppercase">Scans In Progress</span>
                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                                <i class="bi bi-cpu-fill fs-5"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">{{ number_format(($stats['in_progress'] ?? 0) + ($stats['scheduled'] ?? 0)) }}</h3>
                        <div class="small text-info mt-2">
                            Manage Scans <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </a>
        @endif
    </div>
</div>

{{-- ── Operational Metrics & Activity Row ── --}}
<div class="row g-3 mb-4">
    {{-- Status Donut --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Request Status Breakdown
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">All Time</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position:relative;width:140px;height:140px;">
                    <canvas id="radStatusDonut"></canvas>
                </div>
                <div class="mt-3 w-100 small">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#ffc107;"></span>Pending</span><strong>{{ $stats['pending'] }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#0dcaf0;"></span>Scheduled / In Progress</span><strong>{{ $stats['scheduled'] + $stats['in_progress'] }}</strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#198754;"></span>Completed</span><strong>{{ $stats['completed'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-calendar-check me-2 text-success"></i>Today's Operational Activity
                </h6>
                <span class="badge bg-light text-dark border px-2 py-1">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25">
                            <div class="fs-3 fw-bold text-info">{{ $stats['today_scheduled'] }}</div>
                            <div class="small text-muted mt-1">Scheduled Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                            <div class="fs-3 fw-bold text-success">{{ $stats['today_completed'] }}</div>
                            <div class="small text-muted mt-1">Completed Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                            <div class="fs-3 fw-bold text-warning-emphasis">{{ $stats['reports_pending'] }}</div>
                            <div class="small text-muted mt-1">Awaiting Report</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                            <div class="fs-3 fw-bold text-primary">{{ $stats['reports_released'] }}</div>
                            <div class="small text-muted mt-1">Reports Released</div>
                        </div>
                    </div>
                </div>
                @php $rate = $stats['total_requests'] > 0 ? round(($stats['completed'] / $stats['total_requests']) * 100) : 0; @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Radiology Procedure Completion Rate</span>
                        <span class="small fw-bold text-success">{{ $rate }}%</span>
                    </div>
                    <div class="progress" style="--completion-rate: {{ $rate }}%; height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: var(--completion-rate);" aria-valuenow="{{ $rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isRadiologist)
{{-- ── Radiologist Primary Work Queue: Studies Awaiting Interpretation ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-file-text me-2 text-warning"></i>Studies Awaiting Interpretation</h6>
        <a href="{{ route('radiology.reports.index') }}" class="btn btn-sm btn-outline-primary">
            View All Reports <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Study / Request No</th>
                        <th>Patient</th>
                        <th>Ordering Doctor</th>
                        <th>Modality</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                {{-- 1. Completed requests without a report yet --}}
                @foreach($completedStudiesAwaitingReport as $req)
                    <tr>
                        <td>
                            <a href="{{ route('radiology.requests.show', $req) }}" class="fw-semibold text-decoration-none">
                                {{ $req->request_no }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $req->patient->last_name }}, {{ $req->patient->first_name }}</div>
                            <span class="text-muted small">MRN: {{ $req->patient->patient_no }}</span>
                        </td>
                        <td>{{ $req->doctor->name ?? 'Dr. Staff' }}</td>
                        <td><span class="badge bg-secondary font-monospace">{{ $req->modality }}</span></td>
                        <td><span class="badge bg-success">Ready for Interpretation</span></td>
                        <td class="text-muted small">{{ $req->completed_at?->format('M d, Y H:i') ?? $req->updated_at->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('radiology.reports.create') }}?radiology_request_id={{ $req->id }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-journal-medical me-1"></i> Interpret Study
                            </a>
                        </td>
                    </tr>
                @endforeach

                {{-- 2. Draft / Approved reports in progress --}}
                @foreach($pendingReports as $rpt)
                    <tr>
                        <td>
                            <a href="{{ route('radiology.reports.show', $rpt) }}" class="fw-semibold text-decoration-none">
                                {{ $rpt->report_no ?? 'RPT-'.$rpt->id }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold">
                                {{ $rpt->radiologyRequest->patient->last_name ?? 'N/A' }}, {{ $rpt->radiologyRequest->patient->first_name ?? '' }}
                            </div>
                            <span class="text-muted small">MRN: {{ $rpt->radiologyRequest->patient->patient_no ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $rpt->radiologyRequest->doctor->name ?? 'Dr. Staff' }}</td>
                        <td>
                            <span class="badge bg-secondary font-monospace">{{ $rpt->radiologyRequest->modality ?? 'General' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $rpt->status === 'Released' ? 'success' : ($rpt->status === 'Draft' ? 'warning text-dark' : 'info') }}">
                                {{ $rpt->status }} Report
                            </span>
                        </td>
                        <td class="text-muted small">{{ $rpt->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('radiology.reports.show', $rpt) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square me-1"></i> Review / Edit Report
                            </a>
                        </td>
                    </tr>
                @endforeach

                @if($completedStudiesAwaitingReport->isEmpty() && $pendingReports->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-file-check fs-3 d-block mb-1 text-success"></i>
                            No radiology studies currently awaiting interpretation. You're all caught up!
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
{{-- ── Radiologic Technologist Work Queue: Today's Imaging Queue ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-camera me-2 text-primary"></i>Today's Imaging Queue</h6>
        <a href="{{ route('radiology.requests.index') }}" class="btn btn-sm btn-outline-primary">
            View Full Queue <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Request No</th>
                        <th>Patient</th>
                        <th>Modality</th>
                        <th>Ordering Doctor</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentRequests as $req)
                    <tr>
                        <td>
                            <a href="{{ route('radiology.requests.show', $req) }}" class="fw-semibold text-decoration-none">
                                {{ $req->request_no }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $req->patient->last_name }}, {{ $req->patient->first_name }}</div>
                            <span class="text-muted small">MRN: {{ $req->patient->patient_no }}</span>
                        </td>
                        <td><span class="badge bg-secondary font-monospace">{{ $req->modality }}</span></td>
                        <td>{{ $req->doctor->name ?? 'Dr. Staff' }}</td>
                        <td>
                            @if($req->priority === 'STAT')
                                <span class="badge bg-danger">STAT</span>
                            @elseif($req->priority === 'Urgent')
                                <span class="badge bg-warning text-dark">Urgent</span>
                            @else
                                <span class="badge bg-secondary">{{ $req->priority }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ in_array($req->status, ['Completed', 'Released']) ? 'success' : ($req->status === 'In Progress' ? 'primary' : ($req->status === 'Scheduled' ? 'info' : 'warning')) }}">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('radiology.requests.show', $req) }}" class="btn btn-sm btn-outline-primary" title="Open Procedure">
                                    <i class="bi bi-folder2-open me-1"></i> Open
                                </a>
                                @if($req->status === 'Pending' && auth()->user()->hasAnyRole(['admin', 'rad-tech']))
                                    <form method="POST" action="{{ route('radiology.requests.schedule', $req) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-success" title="Schedule Procedure"><i class="bi bi-calendar-event"></i></button>
                                    </form>
                                @elseif($req->status === 'Scheduled' && auth()->user()->hasAnyRole(['admin', 'rad-tech']))
                                    <form method="POST" action="{{ route('radiology.requests.start', $req) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-primary" title="Start Procedure"><i class="bi bi-play-circle"></i></button>
                                    </form>
                                @elseif(in_array($req->status, ['Scheduled', 'In Progress']) && auth()->user()->hasAnyRole(['admin', 'rad-tech']))
                                    <form method="POST" action="{{ route('radiology.requests.complete', $req) }}" class="d-inline" onsubmit="return confirm('Complete procedure and send for interpretation?');">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-info text-white" title="Complete Procedure"><i class="bi bi-check-circle"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-check2-circle fs-3 d-block mb-1 text-success"></i>
                            No imaging procedures in queue. You're all caught up!
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
<script type="application/json" id="rad-chart-data">
    {"pending": {{ $stats['pending'] }}, "scheduled": {{ $stats['scheduled'] + $stats['in_progress'] }}, "completed": {{ $stats['completed'] }}}
</script>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    function initRadChart() {
        if (typeof Chart === 'undefined') return;
        const jsonEl = document.getElementById('rad-chart-data');
        const canvas = document.getElementById('radStatusDonut');
        if (!jsonEl || !canvas) return;

        const _d = JSON.parse(jsonEl.textContent);
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const borderColor = isDark ? '#172B26' : '#FFFFFF';

        const total = (_d.pending || 0) + (_d.scheduled || 0) + (_d.completed || 0);
        const dataValues = total > 0 ? [_d.pending, _d.scheduled, _d.completed] : [1];
        const bgColors   = total > 0 ? ['#FFC107', '#0DCAF0', '#198754'] : [isDark ? '#2A3A35' : '#E9ECEF'];

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: total > 0 ? ['Pending', 'Scheduled/In Progress', 'Completed'] : ['No Data'],
                datasets: [{
                    data: dataValues,
                    backgroundColor: bgColors,
                    borderColor: borderColor,
                    borderWidth: 2,
                    hoverOffset: total > 0 ? 6 : 0,
                }]
            },
            options: {
                cutout: '68%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false }, tooltip: { enabled: total > 0 } }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRadChart);
    } else {
        initRadChart();
    }
})();
</script>
@endpush
