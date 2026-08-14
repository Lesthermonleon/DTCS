@extends('layouts.app')
@section('title', 'Pharmacy Dashboard')
@section('page-title', 'Pharmacy Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Pharmacy Dashboard</li>
@endsection

@section('content')

{{-- ── Quick Action Bar ── --}}
<div class="card mb-4 border-0 shadow-sm bg-body">
    <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success bg-opacity-10 text-success fs-6 px-3 py-2">
                <i class="bi bi-capsule me-1"></i> Pharmacist Operational Workspace
            </span>
            <span class="text-muted small">Perform prescription verification, medication safety checks, and patient dispensing.</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
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
<div class="row g-3 mb-4">
    {{-- Card 1: Total Prescriptions --}}
    <div class="col-sm-6 col-xl-3">
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
    </div>

    {{-- Card 2: Pending Verification --}}
    <div class="col-sm-6 col-xl-3">
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
    </div>

    {{-- Card 3: Dispensed Today --}}
    <div class="col-sm-6 col-xl-3">
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
    </div>

    {{-- Card 4: Low Stock Alerts --}}
    <div class="col-sm-6 col-xl-3">
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
</div>

{{-- ── Operational Metrics & Summary Row ── --}}
<div class="row g-3 mb-4">
    {{-- Prescription Funnel Donut --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Prescription Pipeline
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">All Time</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position:relative;width:140px;height:140px;">
                    <canvas id="rxPipelineDonut"></canvas>
                </div>
                <div class="mt-3 w-100 small">
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#ffc107;"></span>Pending</span><strong>{{ $stats['pending_prescriptions'] }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#0dcaf0;"></span>Verified</span><strong>{{ $stats['verified'] }}</strong></div>
                    <div class="d-flex justify-content-between"><span class="text-muted"><span class="d-inline-block rounded-circle me-2" style="width:8px;height:8px;background:#198754;"></span>Dispensed</span><strong>{{ $stats['dispensed_total'] }}</strong></div>
                </div>
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

{{-- ── Main Prescription Verification & Dispensing Work Queue Table ── --}}
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
                            <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-capsule me-1"></i> {{ $rx->status === 'Verified' ? 'Dispense' : 'Verify & Dispense' }}
                            </a>
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
<script type="application/json" id="rx-chart-data">
    {"pending": {{ $stats['pending_prescriptions'] }}, "verified": {{ $stats['verified'] }}, "dispensed": {{ $stats['dispensed_total'] }}}
</script>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    function initRxChart() {
        if (typeof Chart === 'undefined') return;
        const jsonEl = document.getElementById('rx-chart-data');
        const canvas = document.getElementById('rxPipelineDonut');
        if (!jsonEl || !canvas) return;

        const _d = JSON.parse(jsonEl.textContent);
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const borderColor = isDark ? '#172B26' : '#FFFFFF';

        const total = (_d.pending || 0) + (_d.verified || 0) + (_d.dispensed || 0);
        const dataValues = total > 0 ? [_d.pending, _d.verified, _d.dispensed] : [1];
        const bgColors   = total > 0 ? ['#FFC107', '#0DCAF0', '#198754'] : [isDark ? '#2A3A35' : '#E9ECEF'];

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: total > 0 ? ['Pending', 'Verified', 'Dispensed'] : ['No Data'],
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
        document.addEventListener('DOMContentLoaded', initRxChart);
    } else {
        initRxChart();
    }
})();
</script>
@endpush
