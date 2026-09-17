@extends('layouts.app')
@section('title', 'Surgery Dashboard')
@section('page-title', 'Surgery & OR Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Surgery Dashboard</li>
@endsection

@section('content')

{{-- ── Quick Action Bar ── --}}
<div class="card mb-4 border-0 shadow-sm bg-body workspace-header-card">
    <div class="card-body p-3 workspace-header-body">
        <div class="workspace-header-title">
            <span class="badge bg-danger bg-opacity-10 text-danger fs-6 px-3 py-2">
                <i class="bi bi-scissors me-1"></i> OR Coordinator Operational Center
            </span>
            <span class="text-muted small d-none d-md-inline dashboard-description">Manage operating room allocations, surgical scheduling, and preoperative requests.</span>
        </div>
        <div class="workspace-header-actions">
            <a href="{{ route('surgery.requests.index') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-file-earmark-plus me-1"></i> Surgery Requests
            </a>
            @if(Route::has('surgery.schedules.index'))
            <a href="{{ route('surgery.schedules.index') }}" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-calendar3 me-1"></i> OR Master Schedule
            </a>
            @endif
        </div>
    </div>
</div>

{{-- ── Stat Cards (Admin & Doctor Style) ── --}}
<div class="balanced-grid balanced-grid-4 mb-4">
    {{-- Card 1: Total Surgery Requests --}}
    <a href="{{ route('surgery.requests.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Surgery Requests</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                        <i class="bi bi-scissors fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_requests']) }}</h3>
                <div class="small text-primary mt-2">
                    View Requests <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 2: Pending Scheduling --}}
    <a href="{{ route('surgery.requests.index', ['status' => 'Pending']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pending Scheduling</span>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['pending']) }}</h3>
                <div class="small text-warning mt-2">
                    Schedule Pending <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 3: Scheduled Surgeries --}}
    @if(Route::has('surgery.schedules.index'))
    <a href="{{ route('surgery.schedules.index') }}" class="text-decoration-none">
    @else
    <a href="{{ route('surgery.requests.index') }}" class="text-decoration-none">
    @endif
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Scheduled Surgeries</span>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                        <i class="bi bi-calendar3 fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['scheduled']) }}</h3>
                <div class="small text-info mt-2">
                    View Schedule <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 4: Completed Surgeries --}}
    <a href="{{ route('surgery.requests.index', ['status' => 'Completed']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Completed Surgeries</span>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['completed']) }}</h3>
                <div class="small text-success mt-2">
                    View Completed <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>
</div>

{{-- ── 3. Main OR Schedule & Requests Queue Row ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-scissors me-2 text-danger"></i>Surgery Requests Queue</h6>
                <a href="{{ route('surgery.requests.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Request No</th>
                                <th>Patient</th>
                                <th>Procedure</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentRequests as $req)
                            <tr>
                                <td>
                                    <a href="{{ route('surgery.requests.show', $req) }}" class="fw-semibold text-decoration-none">
                                        {{ $req->request_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $req->patient->last_name }}, {{ $req->patient->first_name }}</div>
                                </td>
                                <td><span class="text-muted small">{{ $req->surgery_type ?? 'General Surgery' }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $req->status === 'Completed' ? 'success' : ($req->status === 'Scheduled' ? 'info' : ($req->status === 'Cancelled' ? 'danger' : 'warning text-dark')) }}">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="table-actions justify-content-end">
                                        <a href="{{ route('surgery.requests.show', $req) }}" class="table-action-btn action-view" title="Open Surgery Request" aria-label="Open Surgery Request">
                                            <i class="bi bi-folder2-open"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-check2-circle fs-3 d-block mb-1 text-success"></i>
                                    No pending surgery requests.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-check me-2 text-primary"></i>Upcoming OR Master Schedule</h6>
                @if(Route::has('surgery.schedules.index'))
                <a href="{{ route('surgery.schedules.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Schedule / OR</th>
                                <th>Patient</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($upcomingSchedules as $sch)
                            <tr>
                                <td>
                                    <a href="{{ route('surgery.schedules.show', $sch) }}" class="fw-semibold text-decoration-none">
                                        {{ $sch->schedule_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $sch->surgeryRequest->patient->last_name ?? 'N/A' }}, {{ $sch->surgeryRequest->patient->first_name ?? '' }}</div>
                                </td>
                                <td class="text-muted small">{{ $sch->scheduled_at?->format('M d, H:i') ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $sch->status === 'Confirmed' ? 'success' : ($sch->status === 'Scheduled' ? 'info' : 'secondary') }}">
                                        {{ $sch->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="table-actions justify-content-end">
                                        <a href="{{ route('surgery.schedules.show', $sch) }}" class="table-action-btn action-view" title="View Schedule Details" aria-label="View Schedule Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-event fs-3 d-block mb-1 text-info"></i>
                                    No upcoming surgeries scheduled.
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

{{-- ── 4. Operational Metrics & Overview Row ── --}}
<div class="row g-3 mb-4">
    {{-- Status Donut --}}
    <div class="col-md-4">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Surgery Status Distribution</h3>
                    <p class="chart-subtitle">Status breakdown across pending, scheduled, completed, and cancelled</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="surgStatusDonut"></canvas>
            </div>
        </div>
    </div>

    {{-- Upcoming & Activity --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-calendar-week me-2 text-primary"></i>OR Utilization &amp; Schedule Overview
                </h6>
                <span class="badge bg-light text-dark border px-2 py-1">Today &amp; Upcoming</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25">
                            <div class="fs-3 fw-bold text-info">{{ $stats['today_scheduled'] }}</div>
                            <div class="small text-muted mt-1">Today's OR Cases</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                            <div class="fs-3 fw-bold text-warning-emphasis">{{ $stats['upcoming_7d'] }}</div>
                            <div class="small text-muted mt-1">Next 7 Days</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                            <div class="fs-3 fw-bold text-success">{{ $stats['completed'] }}</div>
                            <div class="small text-muted mt-1">Completed</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25">
                            <div class="fs-3 fw-bold text-danger">{{ $stats['cancelled'] }}</div>
                            <div class="small text-muted mt-1">Cancelled</div>
                        </div>
                    </div>
                </div>
                @php $rate = $stats['total_requests'] > 0 ? round(($stats['completed'] / $stats['total_requests']) * 100) : 0; @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Surgical Completion Rate</span>
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
<script type="application/json" id="surg-chart-data">
    {"pending": {{ $stats['pending'] }}, "scheduled": {{ $stats['scheduled'] }}, "completed": {{ $stats['completed'] }}, "cancelled": {{ $stats['cancelled'] }}}
</script>

{{-- ── Surgery Analytics: Request Trend ── --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Surgery Request Trend</h3>
                    <p class="chart-subtitle">Monthly surgery requests over time</p>
                </div>
                <select id="surgTrendPeriod" class="form-select form-select-sm" style="width:auto;">
                    <option value="6">Last 6 Months</option>
                    <option value="12">Last 12 Months</option>
                </select>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="surgTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="surg-trend-6m">@json($surgTrend6m)</script>
<script type="application/json" id="surg-trend-12m">@json($surgTrend12m)</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    function initSurgChart() {
        if (typeof Chart === 'undefined' || typeof window.HIMSChart === 'undefined') return;
        const jsonEl = document.getElementById('surg-chart-data');
        const canvas = document.getElementById('surgStatusDonut');
        if (!jsonEl || !canvas) return;

        const _d = JSON.parse(jsonEl.textContent);
        const total = (_d.pending || 0) + (_d.scheduled || 0) + (_d.completed || 0) + (_d.cancelled || 0);

        if (total > 0) {
            window.HIMSChart.createDoughnutChart(
                canvas,
                ['Pending', 'Scheduled', 'Completed', 'Cancelled'],
                [_d.pending, _d.scheduled, _d.completed, _d.cancelled],
                ['#2196F3', '#00838F', '#4CAF50', '#D32F2F']
            );
        } else {
            window.HIMSChart.createDoughnutChart(
                canvas,
                ['No Data'],
                [1],
                ['#9E9E9E']
            );
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSurgChart);
    } else {
        initSurgChart();
    }
})();
</script>
<script>
(function () {
    if (typeof window.HIMSChart === 'undefined') return;
    const t6  = JSON.parse(document.getElementById('surg-trend-6m').textContent);
    const t12 = JSON.parse(document.getElementById('surg-trend-12m').textContent);

    let surgTrendChart = null;
    function renderSurgTrend(data) {
        const ctx = document.getElementById('surgTrendChart');
        if (!ctx) return;
        if (surgTrendChart) { surgTrendChart.destroy(); surgTrendChart = null; }
        if (!data || data.length === 0) return;
        surgTrendChart = window.HIMSChart.createLineChart(ctx, 'Surgery Requests', data.map(d => d.label), data.map(d => d.total), 'requests');
    }
    renderSurgTrend(t6);
    const sel = document.getElementById('surgTrendPeriod');
    if (sel) sel.addEventListener('change', () => renderSurgTrend(sel.value === '12' ? t12 : t6));
})();
</script>
@endpush
