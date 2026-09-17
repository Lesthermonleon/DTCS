@extends('layouts.app')
@section('title', 'Lab Dashboard')
@section('page-title', 'Laboratory Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Lab Dashboard</li>
@endsection

@section('content')

{{-- ── Quick Action Bar ── --}}
<div class="card mb-4 border-0 shadow-sm bg-body workspace-header-card">
    <div class="card-body p-3 workspace-header-body">
        <div class="workspace-header-title">
            <span class="badge bg-primary bg-opacity-10 text-primary fs-6 px-3 py-2">
                <i class="bi bi-box-seam me-1"></i> Medical Technologist Workspace
            </span>
            <span class="text-muted small d-none d-md-inline dashboard-description">Manage laboratory queue, specimen testing, and result authorization.</span>
        </div>
        <div class="workspace-header-actions">
            <a href="{{ route('lab.requests.index') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-list-task me-1"></i> View Lab Requests Queue
            </a>
            @if(Route::has('lab.requests.create'))
            <a href="{{ route('lab.requests.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-circle me-1"></i> Create Lab Request
            </a>
            @endif
        </div>
    </div>
</div>

{{-- ── Stat Cards (Admin & Doctor Style) ── --}}
<div class="balanced-grid balanced-grid-4 mb-4">
    {{-- Card 1: Total Requests --}}
    <a href="{{ route('lab.requests.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Requests</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                        <i class="bi bi-clipboard2-pulse-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_requests']) }}</h3>
                <div class="small text-primary mt-2">
                    View Requests <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 2: Pending Requests --}}
    <a href="{{ route('lab.requests.index', ['status' => 'Pending']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pending Queue</span>
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

    {{-- Card 3: Tests In Progress --}}
    <a href="{{ route('lab.requests.index', ['status' => 'In Progress']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Tests In Progress</span>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                        <i class="bi bi-arrow-repeat fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['in_progress']) }}</h3>
                <div class="small text-info mt-2">
                    Active Tests <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 4: STAT Priority --}}
    <a href="{{ route('lab.requests.index', ['priority' => 'STAT']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">STAT Priority</span>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['stat_count']) }}</h3>
                <div class="small text-danger mt-2">
                    Urgent Attention <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>
</div>

{{-- ── 3. Main Work Queue Table (Actionable Work Priority) ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-task me-2 text-primary"></i>Today's Laboratory Queue</h6>
        <a href="{{ route('lab.requests.index') }}" class="btn btn-sm btn-outline-primary">
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
                        <th>Ordering Doctor</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentRequests as $req)
                    <tr>
                        <td>
                            <a href="{{ route('lab.requests.show', $req) }}" class="fw-semibold text-decoration-none">
                                {{ $req->request_no }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $req->patient->last_name }}, {{ $req->patient->first_name }}</div>
                            <span class="text-muted small">MRN: {{ $req->patient->patient_mrn ?? 'N/A' }}</span>
                        </td>
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
                            <span class="badge bg-{{ in_array($req->status, ['Completed', 'Released']) ? 'success' : ($req->status === 'In Progress' ? 'primary' : ($req->status === 'Cancelled' ? 'danger' : 'warning')) }}">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $req->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            <div class="table-actions justify-content-end">
                                @if($req->status === 'Pending' && auth()->user()->hasAnyRole(['admin','med-tech']))
                                    <form method="POST" action="{{ route('lab.requests.receive', $req) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="table-action-btn action-success" title="Receive Specimen" aria-label="Receive Specimen" data-confirm="Are you sure you want to mark {{ $req->request_no }} as received?">
                                            <i class="bi bi-inbox-fill"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('lab.requests.show', $req) }}" class="table-action-btn action-view" title="Open Lab Request" aria-label="Open Lab Request">
                                    <i class="bi bi-folder2-open"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-check2-circle fs-3 d-block mb-1 text-success"></i>
                            No lab requests in queue. You're all caught up!
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── 4. Operational Metrics & Distribution Row ── --}}
<div class="row g-3 mb-4">
    {{-- Status Distribution Donut --}}
    <div class="col-md-4">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Workload Distribution</h3>
                    <p class="chart-subtitle">Status breakdown across pending, in progress, and completed</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="labStatusDonut"></canvas>
            </div>
        </div>
    </div>

    {{-- Today's Throughput --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-speedometer2 me-2 text-info"></i>Today's Throughput &amp; Performance
                </h6>
                <span class="badge bg-light text-dark border px-2 py-1">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                            <div class="fs-3 fw-bold text-success">{{ $stats['today_received'] }}</div>
                            <div class="small text-muted mt-1">Received Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                            <div class="fs-3 fw-bold text-primary">{{ $stats['today_completed'] }}</div>
                            <div class="small text-muted mt-1">Completed Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25">
                            <div class="fs-3 fw-bold text-danger">{{ $stats['stat_pending'] }}</div>
                            <div class="small text-muted mt-1">STAT Pending</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                            <div class="fs-3 fw-bold text-warning-emphasis">{{ $stats['pending_release'] ?? 0 }}</div>
                            <div class="small text-muted mt-1">Pending Release</div>
                        </div>
                    </div>
                </div>
                @php $rate = $stats['total_requests'] > 0 ? round(($stats['completed'] / $stats['total_requests']) * 100) : 0; @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Overall Laboratory Completion Rate</span>
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

<script type="application/json" id="lab-chart-data">
    {"pending": {{ $stats['pending'] }}, "in_progress": {{ $stats['in_progress'] }}, "completed": {{ $stats['completed'] }}}
</script>

{{-- ── Lab Analytics: Workload Trend + Priority Breakdown ── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Lab Request Trend</h3>
                    <p class="chart-subtitle">Monthly laboratory requests over time</p>
                </div>
                <select id="labTrendPeriod" class="form-select form-select-sm" style="width:auto;">
                    <option value="6">Last 6 Months</option>
                    <option value="12">Last 12 Months</option>
                </select>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="labTrendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Request Priority</h3>
                    <p class="chart-subtitle">Routine, Urgent &amp; STAT lab requests</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="labPriorityChart"></canvas>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="lab-trend-6m">@json($labTrend6m)</script>
<script type="application/json" id="lab-trend-12m">@json($labTrend12m)</script>
<script type="application/json" id="lab-priority-data">@json($labPriority)</script>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    function initLabChart() {
        if (typeof Chart === 'undefined' || typeof window.HIMSChart === 'undefined') return;
        const jsonEl = document.getElementById('lab-chart-data');
        const canvas = document.getElementById('labStatusDonut');
        if (!jsonEl || !canvas) return;

        const _d = JSON.parse(jsonEl.textContent);
        const total = (_d.pending || 0) + (_d.in_progress || 0) + (_d.completed || 0);

        if (total > 0) {
            window.HIMSChart.createDoughnutChart(
                canvas,
                ['Pending', 'In Progress', 'Completed'],
                [_d.pending, _d.in_progress, _d.completed],
                ['#2196F3', '#FFB300', '#4CAF50']
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
        document.addEventListener('DOMContentLoaded', initLabChart);
    } else {
        initLabChart();
    }
})();
</script>

<script>
(function () {
    if (typeof window.HIMSChart === 'undefined') return;
    const trend6m  = JSON.parse(document.getElementById('lab-trend-6m').textContent);
    const trend12m = JSON.parse(document.getElementById('lab-trend-12m').textContent);
    const priority = JSON.parse(document.getElementById('lab-priority-data').textContent);

    let labTrendChart = null;
    function renderLabTrend(data) {
        const ctx = document.getElementById('labTrendChart');
        if (!ctx) return;
        if (labTrendChart) { labTrendChart.destroy(); labTrendChart = null; }
        if (!data || data.length === 0) return;
        labTrendChart = window.HIMSChart.createLineChart(ctx, 'Lab Requests', data.map(d => d.label), data.map(d => d.total), 'requests');
    }
    renderLabTrend(trend6m);

    const sel = document.getElementById('labTrendPeriod');
    if (sel) sel.addEventListener('change', () => renderLabTrend(sel.value === '12' ? trend12m : trend6m));

    const priCtx = document.getElementById('labPriorityChart');
    if (priCtx && priority.some(p => p.count > 0)) {
        window.HIMSChart.createDoughnutChart(
            priCtx,
            priority.map(p => p.label),
            priority.map(p => p.count),
            { 'Routine': '#E0E0E0', 'Urgent': '#F57C00', 'STAT': '#D32F2F' }
        );
    }
})();
</script>
@endpush
