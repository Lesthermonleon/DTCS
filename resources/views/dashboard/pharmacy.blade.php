@extends('layouts.app')
@section('title', 'Pharmacy Dashboard')
@section('page-title', 'Pharmacy Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Pharmacy Dashboard</li>
@endsection

@section('content')

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card card-signal">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['total_prescriptions'] }}</div>
                    <div class="stat-label">Total Prescriptions</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-prescription2"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,15 15,12 30,14 45,9 60,11 75,7 100,9"
                          fill="none" stroke="#14C79A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card card-amber">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['pending_prescriptions'] }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,16 20,13 40,15 55,9 70,11 85,7 100,9"
                          fill="none" stroke="#E0A030" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card card-signal">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['dispensed_today'] }}</div>
                    <div class="stat-label">Dispensed Today</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-bag-check"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,18 20,15 38,17 55,10 72,12 87,8 100,10"
                          fill="none" stroke="#14C79A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card card-coral">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['low_stock'] ?? 0 }}</div>
                    <div class="stat-label">Low Stock Alert</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,12 20,14 38,11 55,15 72,10 87,13 100,11"
                          fill="none" stroke="#E85C55" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── Analytics Row ── --}}
<div class="row g-3 mb-4">
    {{-- Prescription Funnel Donut --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pie-chart-fill me-2" style="color:var(--signal);"></i>Prescription Pipeline</span>
                <span class="pill pill-signal">All Time</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position:relative;width:150px;height:150px;">
                    <canvas id="rxPipelineDonut"></canvas>
                </div>
                <div class="mt-3 w-100" style="font-size:.78rem;">
                    <div class="d-flex justify-content-between mb-1"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E0A030;margin-right:5px;"></span>Pending</span><strong>{{ $stats['pending_prescriptions'] }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#4C7EA8;margin-right:5px;"></span>Verified</span><strong>{{ $stats['verified'] }}</strong></div>
                    <div class="d-flex justify-content-between"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#14C79A;margin-right:5px;"></span>Dispensed</span><strong>{{ $stats['dispensed_total'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Summary --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-day me-2" style="color:var(--steel);"></i>Today's Summary</span>
                <span class="pill pill-steel">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(20,199,154,.08);border:1px solid rgba(20,199,154,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--signal);">{{ $stats['dispensed_today'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Dispensed Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(76,126,168,.08);border:1px solid rgba(76,126,168,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--steel);">{{ $stats['verified_today'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Verified Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(224,160,48,.08);border:1px solid rgba(224,160,48,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:#a06800;">{{ $stats['pending_prescriptions'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Pending Queue</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(110,124,116,.08);border:1px solid var(--line);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--text-soft);">{{ $stats['pending_rate'] }}%</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Pending Rate</div>
                        </div>
                    </div>
                </div>
                @php $dispRate = $stats['total_prescriptions'] > 0 ? round(($stats['dispensed_total'] / $stats['total_prescriptions']) * 100) : 0; @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:.78rem;color:var(--text-soft);">Overall Dispensing Rate</span>
                        <span style="font-size:.78rem;font-weight:700;color:var(--signal-dark);">{{ $dispRate }}%</span>
                    </div>
                    <div style="height:7px;border-radius:4px;background:rgba(20,199,154,.15);overflow:hidden;">
                        <div class="progress-bar-fill" data-width="{{ $dispRate }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-prescription2 me-2" style="color:var(--signal-dark);"></i>Pending Prescriptions</span>
                <a href="{{ route('pharmacy.prescriptions.index') }}" class="pill pill-signal">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Rx No</th><th>Patient</th><th>Doctor</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($pendingPrescriptionsList as $rx)
                            <tr>
                                <td><a href="{{ route('pharmacy.prescriptions.show', $rx) }}">{{ $rx->prescription_no }}</a></td>
                                <td>{{ $rx->patient->last_name }}, {{ $rx->patient->first_name }}</td>
                                <td style="color:var(--text-soft);font-size:.82rem;">{{ $rx->doctor->name ?? '—' }}</td>
                                <td><span class="pill pill-{{ in_array($rx->statusBadge, ['success','primary']) ? 'signal' : (($rx->statusBadge === 'danger') ? 'coral' : (($rx->statusBadge === 'warning') ? 'amber' : 'muted')) }}">{{ $rx->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3" style="color:var(--text-soft)">No pending prescriptions.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bag-plus me-2" style="color:var(--steel);"></i>Recent Dispensing</span>
                <a href="{{ route('pharmacy.dispensing.index') }}" class="pill pill-steel">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Record No</th><th>Patient</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($recentDispensing as $d)
                            <tr>
                                <td><a href="{{ route('pharmacy.dispensing.show', $d) }}">{{ $d->dispensing_no }}</a></td>
                                <td>{{ $d->prescription->patient->last_name ?? '—' }}, {{ $d->prescription->patient->first_name ?? '' }}</td>
                                <td><span class="pill pill-{{ in_array($d->statusBadge, ['success','primary']) ? 'signal' : (($d->statusBadge === 'danger') ? 'coral' : (($d->statusBadge === 'warning') ? 'amber' : 'muted')) }}">{{ $d->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3" style="color:var(--text-soft)">No dispensing records.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
    const _d = JSON.parse(document.getElementById('rx-chart-data').textContent);
    const bgColor = getComputedStyle(document.documentElement).getPropertyValue('--surface').trim() || '#fff';
    const ctx = document.getElementById('rxPipelineDonut').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Verified', 'Dispensed'],
            datasets: [{
                data: [_d.pending, _d.verified, _d.dispensed],
                backgroundColor: ['#E0A030','#4C7EA8','#14C79A'],
                borderColor: bgColor,
                borderWidth: 3,
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '68%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } }
        }
    });
})();
</script>
@endpush
