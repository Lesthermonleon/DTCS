@extends('layouts.app')
@section('title', 'Pharmacy Dashboard')
@section('page-title', 'Pharmacy Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Pharmacy Dashboard</li>
@endsection

@section('content')

{{-- ── Quick Action Bar ── --}}
<div class="card mb-4 border-0 shadow-sm bg-body workspace-header-card">
    <div class="card-body p-3 workspace-header-body">
        <div class="workspace-header-title">
            <span class="badge bg-success bg-opacity-10 text-success fs-6 px-3 py-2">
                <i class="bi bi-capsule me-1"></i> Pharmacist Operational Workspace
            </span>
            <span class="text-muted small d-none d-md-inline dashboard-description">Perform prescription verification, medication safety checks, and patient dispensing.</span>
        </div>
        <div class="workspace-header-actions">
            <a href="{{ route('pharmacy.prescriptions.index') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-prescription2 me-1"></i> Prescriptions Queue
            </a>
            @if(Route::has('pharmacy.dispensing.index'))
            <a href="{{ route('pharmacy.dispensing.index') }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-bag-check me-1"></i> Dispensing Records
            </a>
            @endif
            @if(Route::has('pharmacy.medicines.index'))
            <a href="{{ route('pharmacy.medicines.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-box-seam me-1"></i> Drug Inventory
            </a>
            @endif
        </div>
    </div>
</div>

{{-- ── Stat Cards (Admin & Doctor Style) ── --}}
<div class="balanced-grid balanced-grid-4 mb-4">
    {{-- Card 1: Total Prescriptions --}}
    <a href="{{ route('pharmacy.prescriptions.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Prescriptions</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                        <i class="bi bi-prescription2 fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_prescriptions']) }}</h3>
                <div class="small text-primary mt-2">
                    View Prescriptions <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 2: Pending Verification --}}
    <a href="{{ route('pharmacy.prescriptions.index', ['status' => 'Pending']) }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pending Verification</span>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['pending_prescriptions']) }}</h3>
                <div class="small text-warning mt-2">
                    Verify Pending <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 3: Dispensed Today --}}
    @if(Route::has('pharmacy.dispensing.index'))
    <a href="{{ route('pharmacy.dispensing.index') }}" class="text-decoration-none">
    @else
    <a href="{{ route('pharmacy.prescriptions.index') }}" class="text-decoration-none">
    @endif
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Dispensed Today</span>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                        <i class="bi bi-bag-check-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['dispensed_today']) }}</h3>
                <div class="small text-success mt-2">
                    View Dispensed <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>

    {{-- Card 4: Low Stock Alerts --}}
    @if(Route::has('pharmacy.medicines.index'))
    <a href="{{ route('pharmacy.medicines.index', ['stock' => 'low']) }}" class="text-decoration-none">
    @else
    <a href="{{ route('pharmacy.prescriptions.index') }}" class="text-decoration-none">
    @endif
        <div class="card border-0 shadow-sm h-100 card-hover-elevate transition-all">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Low Stock Alert</span>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['low_stock'] ?? 0) }}</h3>
                <div class="small text-danger mt-2">
                    Check Inventory <i class="bi bi-arrow-right"></i>
                </div>
            </div>
        </div>
    </a>
</div>

{{-- ── 3. Main Prescription Verification & Dispensing Work Queue Table ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-prescription2 me-2 text-success"></i>Prescription Verification &amp; Dispensing Queue</h6>
        <a href="{{ route('pharmacy.prescriptions.index') }}" class="btn btn-sm btn-outline-primary">
            View Full Queue <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Rx Number</th>
                        <th>Patient</th>
                        <th>Ordering Doctor</th>
                        <th>Status</th>
                        <th>Prescribed Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pendingPrescriptionsList as $rx)
                    <tr>
                        <td>
                            <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="fw-semibold text-decoration-none">
                                {{ $rx->prescription_no }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $rx->patient->last_name }}, {{ $rx->patient->first_name }}</div>
                            <span class="text-muted small">MRN: {{ $rx->patient->patient_mrn ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $rx->doctor->name ?? 'Dr. Staff' }}</td>
                        <td>
                            <span class="badge bg-{{ $rx->status === 'Dispensed' ? 'success' : ($rx->status === 'Verified' ? 'primary' : 'warning text-dark') }}">
                                {{ $rx->status }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $rx->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            <div class="table-actions justify-content-end">
                                <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="table-action-btn action-view" title="{{ $rx->status === 'Verified' ? 'Dispense Prescription' : 'Verify & Dispense Prescription' }}" aria-label="{{ $rx->status === 'Verified' ? 'Dispense Prescription' : 'Verify & Dispense Prescription' }}">
                                    <i class="bi bi-capsule"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-check2-circle fs-3 d-block mb-1 text-success"></i>
                            No pending prescriptions in queue. All prescriptions are verified/dispensed!
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── 4. Operational Metrics & Summary Row ── --}}
<div class="row g-3 mb-4">
    {{-- Prescription Funnel Donut --}}
    <div class="col-md-4">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Prescription Pipeline</h3>
                    <p class="chart-subtitle">Status breakdown across pending, verified, and dispensed</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="rxPipelineDonut"></canvas>
            </div>
        </div>
    </div>

    {{-- Today's Summary --}}
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
                            <div class="fs-3 fw-bold text-success">{{ $stats['dispensed_today'] }}</div>
                            <div class="small text-muted mt-1">Dispensed Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25">
                            <div class="fs-3 fw-bold text-info">{{ $stats['verified_today'] }}</div>
                            <div class="small text-muted mt-1">Verified Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                            <div class="fs-3 fw-bold text-warning-emphasis">{{ $stats['pending_prescriptions'] }}</div>
                            <div class="small text-muted mt-1">Pending Queue</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3 bg-secondary bg-opacity-10 border border-secondary border-opacity-25">
                            <div class="fs-3 fw-bold text-dark">{{ $stats['pending_rate'] }}%</div>
                            <div class="small text-muted mt-1">Pending Rate</div>
                        </div>
                    </div>
                </div>
                @php $dispRate = $stats['total_prescriptions'] > 0 ? round(($stats['dispensed_total'] / $stats['total_prescriptions']) * 100) : 0; @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Overall Dispensing Rate</span>
                        <span class="small fw-bold text-success">{{ $dispRate }}%</span>
                    </div>
                    <div class="progress" style="--completion-rate: {{ $dispRate }}%; height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: var(--completion-rate);" aria-valuenow="{{ $dispRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="rx-chart-data">
    {"pending": {{ $stats['pending_prescriptions'] }}, "verified": {{ $stats['verified'] }}, "dispensed": {{ $stats['dispensed_total'] }}}
</script>

{{-- ── Pharmacy Analytics: Prescription Trend ── --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="chart-card h-100">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Prescription Trend</h3>
                    <p class="chart-subtitle">Monthly prescription activity over time</p>
                </div>
                <select id="rxTrendPeriod" class="form-select form-select-sm" style="width:auto;">
                    <option value="6">Last 6 Months</option>
                    <option value="12">Last 12 Months</option>
                </select>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="rxTrendChart"></canvas>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="rx-trend-6m">@json($rxTrend6m)</script>
<script type="application/json" id="rx-trend-12m">@json($rxTrend12m)</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    function initRxChart() {
        if (typeof Chart === 'undefined' || typeof window.HIMSChart === 'undefined') return;
        const jsonEl = document.getElementById('rx-chart-data');
        const canvas = document.getElementById('rxPipelineDonut');
        if (!jsonEl || !canvas) return;

        const _d = JSON.parse(jsonEl.textContent);
        const total = (_d.pending || 0) + (_d.verified || 0) + (_d.dispensed || 0);

        if (total > 0) {
            window.HIMSChart.createDoughnutChart(
                canvas,
                ['Pending', 'Verified', 'Dispensed'],
                [_d.pending, _d.verified, _d.dispensed],
                ['#64748B', '#475569', '#2f8f6b']
            );
        } else {
            window.HIMSChart.createDoughnutChart(
                canvas,
                ['No Data'],
                [1],
                ['#94A3B8']
            );
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRxChart);
    } else {
        initRxChart();
    }
})();
</script>
<script>
(function () {
    if (typeof window.HIMSChart === 'undefined') return;
    const t6  = JSON.parse(document.getElementById('rx-trend-6m').textContent);
    const t12 = JSON.parse(document.getElementById('rx-trend-12m').textContent);

    let rxTrendChart = null;
    function renderRxTrend(data) {
        const ctx = document.getElementById('rxTrendChart');
        if (!ctx) return;
        if (rxTrendChart) { rxTrendChart.destroy(); rxTrendChart = null; }
        if (!data || data.length === 0) return;
        rxTrendChart = window.HIMSChart.createLineChart(ctx, 'Prescriptions', data.map(d => d.label), data.map(d => d.total), 'prescriptions');
    }
    renderRxTrend(t6);
    const sel = document.getElementById('rxTrendPeriod');
    if (sel) sel.addEventListener('change', () => renderRxTrend(sel.value === '12' ? t12 : t6));
})();
</script>
@endpush
