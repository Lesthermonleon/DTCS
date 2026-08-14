@extends('layouts.app')
@section('title', 'Diet & Nutrition Dashboard')
@section('page-title', 'Diet & Nutrition Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Diet Dashboard</li>
@endsection

@section('content')

{{-- ── Quick Action Bar ── --}}
<div class="card mb-4 border-0 shadow-sm bg-body">
    <div class="card-body p-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 gap-sm-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-success bg-opacity-10 text-success fs-6 px-3 py-2 text-nowrap">
                <i class="bi bi-apple me-1"></i> Dietitian Operational Workspace
            </span>
            <span class="text-muted small text-nowrap d-none d-md-inline">Manage therapeutic nutrition consultations, meal plans, and clinical diet requests.</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-sm-auto justify-content-start justify-content-sm-end">
            <a href="{{ route('diet.requests.index') }}" class="btn btn-sm btn-primary text-nowrap flex-grow-1 flex-sm-grow-0">
                <i class="bi bi-journal-bookmark me-1"></i> Consultations Queue
            </a>
            @if(Route::has('diet.plans.index'))
            <a href="{{ route('diet.plans.index') }}" class="btn btn-sm btn-outline-success text-nowrap flex-grow-1 flex-sm-grow-0">
                <i class="bi bi-clipboard2-heart me-1"></i> Active Diet Plans
            </a>
            @endif
        </div>
    </div>
</div>

{{-- ── Stat Cards (Admin & Doctor Style) ── --}}
<div class="row g-3 mb-4">
    {{-- Card 1: Total Diet Requests --}}
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('diet.requests.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Total Diet Requests</span>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                            <i class="bi bi-apple fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_requests']) }}</h3>
                    <div class="small text-primary mt-2">
                        View Requests <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card 2: Pending Consults --}}
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('diet.requests.index', ['status' => 'Pending']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Pending Consults</span>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                            <i class="bi bi-hourglass-split fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['pending']) }}</h3>
                    <div class="small text-warning mt-2">
                        Review Pending <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card 3: Active Diet Plans --}}
    <div class="col-sm-6 col-xl-3">
        @if(Route::has('diet.plans.index'))
        <a href="{{ route('diet.plans.index') }}" class="text-decoration-none">
        @else
        <a href="{{ route('diet.requests.index') }}" class="text-decoration-none">
        @endif
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Active Diet Plans</span>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                            <i class="bi bi-clipboard2-heart-fill fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['active_plans']) }}</h3>
                    <div class="small text-success mt-2">
                        Manage Plans <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Card 4: Completed Today --}}
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('diet.requests.index', ['status' => 'Completed']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Completed Today</span>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                            <i class="bi bi-check2-circle fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['completed_today'] ?? 0) }}</h3>
                    <div class="small text-info mt-2">
                        View Completed <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- ── Operational Metrics & Activity Row ── --}}
<div class="row g-3 mb-4">
    {{-- Status Donut --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Diet Request Breakdown
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">All Time</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position:relative;width:140px;height:140px;">
                    <canvas id="dietStatusDonut"></canvas>
                </div>
                <div class="mt-3 w-100 small">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#ffc107;"></span>Pending</span><strong>{{ $stats['pending'] }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#0dcaf0;"></span>In Progress</span><strong>{{ $stats['in_progress'] }}</strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#198754;"></span>Completed</span><strong>{{ $stats['completed_total'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-calendar-day me-2 text-success"></i>Today's Operational Summary
                </h6>
                <span class="badge bg-light text-dark border px-2 py-1">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                            <div class="fs-3 fw-bold text-success">{{ $stats['completed_today'] }}</div>
                            <div class="small text-muted mt-1">Completed Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25">
                            <div class="fs-3 fw-bold text-info">{{ $stats['active_plans'] }}</div>
                            <div class="small text-muted mt-1">Active Plans</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                            <div class="fs-3 fw-bold text-warning-emphasis">{{ $stats['pending'] }}</div>
                            <div class="small text-muted mt-1">Pending Consults</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                            <div class="fs-3 fw-bold text-primary">{{ $stats['completed_total'] }}</div>
                            <div class="small text-muted mt-1">Total Done</div>
                        </div>
                    </div>
                </div>
                @php $rate = $stats['total_requests'] > 0 ? round(($stats['completed_total'] / $stats['total_requests']) * 100) : 0; @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Consultation Completion Rate</span>
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

{{-- ── Main Diet Consultations & Active Plans Work Queue Row ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-journal-bookmark me-2 text-success"></i>Dietary Consult Requests</h6>
                <a href="{{ route('diet.requests.index') }}" class="btn btn-sm btn-outline-primary">
                    View Queue <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Request No</th>
                                <th>Patient</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($recentRequests as $req)
                            <tr>
                                <td>
                                    <a href="{{ route('diet.requests.show', $req) }}" class="fw-semibold text-decoration-none">
                                        {{ $req->request_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $req->patient->last_name }}, {{ $req->patient->first_name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $req->status === 'Completed' ? 'success' : ($req->status === 'In Progress' ? 'info' : 'warning text-dark') }}">
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('diet.requests.show', $req) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-apple me-1"></i> Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-check2-circle fs-3 d-block mb-1 text-success"></i>
                                    No pending diet requests in queue.
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
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clipboard2-heart me-2 text-primary"></i>Active Therapeutic Diet Plans</h6>
                @if(Route::has('diet.plans.index'))
                <a href="{{ route('diet.plans.index') }}" class="btn btn-sm btn-outline-primary">
                    View Plans <i class="bi bi-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Plan No</th>
                                <th>Patient</th>
                                <th>Diet Type</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($activePlansList as $plan)
                            <tr>
                                <td>
                                    <a href="{{ route('diet.plans.show', $plan) }}" class="fw-semibold text-decoration-none">
                                        {{ $plan->plan_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $plan->patient->last_name ?? 'N/A' }}, {{ $plan->patient->first_name ?? '' }}</div>
                                </td>
                                <td><span class="text-muted small">{{ $plan->diet_type ?? 'Standard' }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $plan->status === 'Active' ? 'success' : 'secondary' }}">
                                        {{ $plan->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('diet.plans.show', $plan) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-journal-x fs-3 d-block mb-1 text-secondary"></i>
                                    No active diet plans currently registered.
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
<script type="application/json" id="diet-chart-data">
    {"pending": {{ $stats['pending'] }}, "in_progress": {{ $stats['in_progress'] }}, "completed": {{ $stats['completed_total'] }}}
</script>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    function initDietChart() {
        if (typeof Chart === 'undefined') return;
        const jsonEl = document.getElementById('diet-chart-data');
        const canvas = document.getElementById('dietStatusDonut');
        if (!jsonEl || !canvas) return;

        const _d = JSON.parse(jsonEl.textContent);
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const borderColor = isDark ? '#172B26' : '#FFFFFF';

        const total = (_d.pending || 0) + (_d.in_progress || 0) + (_d.completed || 0);
        const dataValues = total > 0 ? [_d.pending, _d.in_progress, _d.completed] : [1];
        const bgColors   = total > 0 ? ['#FFC107', '#0DCAF0', '#198754'] : [isDark ? '#2A3A35' : '#E9ECEF'];

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: total > 0 ? ['Pending', 'In Progress', 'Completed'] : ['No Data'],
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
        document.addEventListener('DOMContentLoaded', initDietChart);
    } else {
        initDietChart();
    }
})();
</script>
@endpush
