@extends('layouts.app')
@section('title', 'Doctor Dashboard')
@section('page-title', 'Doctor Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="stat-card card-steel">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['my_lab_requests'] }}</div>
                    <div class="stat-label">Lab Requests</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-clipboard2-pulse"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,15 15,11 30,13 45,8 60,10 75,6 100,8"
                          fill="none" stroke="#4C7EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card card-amber">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['pending_lab'] }}</div>
                    <div class="stat-label">Pending Lab</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,14 20,12 40,15 55,9 70,11 85,8 100,10"
                          fill="none" stroke="#E0A030" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card card-signal">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['my_prescriptions'] }}</div>
                    <div class="stat-label">Prescriptions</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-prescription2"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,16 20,13 40,15 55,8 70,10 85,6 100,8"
                          fill="none" stroke="#14C79A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card card-steel">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['my_radiology'] }}</div>
                    <div class="stat-label">Radiology</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-activity"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,14 18,11 36,13 52,8 68,10 82,7 100,9"
                          fill="none" stroke="#4C7EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card card-coral">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['my_surgeries'] }}</div>
                    <div class="stat-label">Surgery Req.</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-scissors"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,17 20,14 38,16 55,10 72,12 87,9 100,11"
                          fill="none" stroke="#E85C55" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="stat-card card-signal">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['my_diet_requests'] }}</div>
                    <div class="stat-label">Diet Requests</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-apple"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,18 22,14 44,16 60,10 76,12 90,8 100,10"
                          fill="none" stroke="#14C79A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── Analytics Row ── --}}
<div class="row g-3 mb-4">
    {{-- This Week Activity Bar Chart --}}
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-fill me-2" style="color:var(--steel);"></i>Activity This Week</span>
                <span class="pill pill-steel">By Request Type</span>
            </div>
            <div class="card-body d-flex align-items-center" style="min-height:180px;">
                <canvas id="doctorWeekChart" height="140"></canvas>
            </div>
        </div>
    </div>

    {{-- Lab Completion Summary --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-clipboard2-check-fill me-2" style="color:var(--signal);"></i>Lab Request Summary
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3" style="border-color:var(--line);background:transparent;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check2-circle" style="color:var(--signal);"></i>
                            <span style="font-size:.82rem;color:var(--text);">Completed</span>
                        </div>
                        <span class="pill pill-signal">{{ $stats['completed_lab'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3" style="border-color:var(--line);background:transparent;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-hourglass-split" style="color:var(--amber);"></i>
                            <span style="font-size:.82rem;color:var(--text);">Pending</span>
                        </div>
                        <span class="pill pill-amber">{{ $stats['pending_lab'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3" style="border-color:var(--line);background:transparent;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clipboard2-pulse" style="color:var(--steel);"></i>
                            <span style="font-size:.82rem;color:var(--text);">Total All-Time</span>
                        </div>
                        <span class="pill pill-steel">{{ $stats['my_lab_requests'] }}</span>
                    </div>
                    @php
                        $labRate = $stats['my_lab_requests'] > 0
                            ? round(($stats['completed_lab'] / $stats['my_lab_requests']) * 100) : 0;
                    @endphp
                    <div class="list-group-item px-3 py-3" style="border-color:var(--line);background:transparent;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:.78rem;color:var(--text-soft);">Completion Rate</span>
                            <span style="font-size:.78rem;font-weight:700;color:var(--signal-dark);">{{ $labRate }}%</span>
                        </div>
                        <div style="height:6px;border-radius:3px;background:rgba(20,199,154,.15);overflow:hidden;">
                            <div class="progress-bar-fill" data-width="{{ $labRate }}"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-lightning-charge me-2" style="color:var(--amber);"></i>Quick Actions</div>
    <div class="card-body d-flex flex-wrap gap-2">
        <a href="{{ route('lab.requests.create') }}"          class="btn btn-sm" style="background:rgba(76,126,168,.12);color:var(--steel);border:1px solid rgba(76,126,168,.25);font-weight:600;"><i class="bi bi-plus-circle me-1"></i>New Lab Request</a>
        <a href="{{ route('radiology.requests.create') }}"    class="btn btn-sm" style="background:rgba(224,160,48,.12);color:#a06800;border:1px solid rgba(224,160,48,.25);font-weight:600;"><i class="bi bi-plus-circle me-1"></i>New Imaging</a>
        <a href="{{ route('pharmacy.prescriptions.create') }}" class="btn btn-sm" style="background:rgba(20,199,154,.12);color:var(--signal-dark);border:1px solid rgba(20,199,154,.25);font-weight:600;"><i class="bi bi-plus-circle me-1"></i>New Prescription</a>
        <a href="{{ route('surgery.requests.create') }}"      class="btn btn-sm" style="background:rgba(232,92,85,.12);color:var(--coral);border:1px solid rgba(232,92,85,.25);font-weight:600;"><i class="bi bi-plus-circle me-1"></i>New Surgery Req.</a>
        <a href="{{ route('diet.requests.create') }}"         class="btn btn-sm" style="background:rgba(20,199,154,.08);color:var(--signal-dark);border:1px solid rgba(20,199,154,.2);font-weight:600;"><i class="bi bi-plus-circle me-1"></i>New Diet Req.</a>
        <a href="{{ route('patients.create') }}"              class="btn btn-sm" style="background:rgba(110,124,116,.08);color:var(--text-soft);border:1px solid var(--line);font-weight:600;"><i class="bi bi-person-plus me-1"></i>New Patient</a>
    </div>
</div>

{{-- Tables --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard2-pulse me-2" style="color:var(--steel);"></i>Recent Lab Requests</span>
                <a href="{{ route('lab.requests.index') }}" class="pill pill-steel">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Request No</th><th>Patient</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($recentLabRequests as $req)
                            <tr>
                                <td><a href="{{ route('lab.requests.show', $req) }}">{{ $req->request_no }}</a></td>
                                <td>{{ $req->patient->last_name }}, {{ $req->patient->first_name }}</td>
                                <td><span class="pill pill-{{ in_array($req->statusBadge, ['success','primary']) ? 'signal' : (($req->statusBadge === 'danger') ? 'coral' : (($req->statusBadge === 'warning') ? 'amber' : 'muted')) }}">{{ $req->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3" style="color:var(--text-soft)">No requests yet.</td></tr>
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
                <span><i class="bi bi-prescription2 me-2" style="color:var(--signal-dark);"></i>Recent Prescriptions</span>
                <a href="{{ route('pharmacy.prescriptions.index') }}" class="pill pill-signal">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Rx No</th><th>Patient</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($recentPrescriptions as $rx)
                            <tr>
                                <td><a href="{{ route('pharmacy.prescriptions.show', $rx) }}">{{ $rx->prescription_no }}</a></td>
                                <td>{{ $rx->patient->last_name }}, {{ $rx->patient->first_name }}</td>
                                <td><span class="pill pill-{{ in_array($rx->statusBadge, ['success','primary']) ? 'signal' : (($rx->statusBadge === 'danger') ? 'coral' : (($rx->statusBadge === 'warning') ? 'amber' : 'muted')) }}">{{ $rx->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3" style="color:var(--text-soft)">No prescriptions yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="doctor-chart-data">
    {"lab": {{ $stats['lab_this_week'] }}, "rad": {{ $stats['rad_this_week'] }}, "rx": {{ $stats['rx_this_week'] }}, "surg": {{ $stats['surg_this_week'] }}, "diet": {{ $stats['diet_this_week'] }}}
</script>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const textSoftColor = getComputedStyle(document.documentElement).getPropertyValue('--text-soft').trim() || '#6b8077';
    const lineColor     = getComputedStyle(document.documentElement).getPropertyValue('--line').trim()      || '#dde6e2';

    const _d = JSON.parse(document.getElementById('doctor-chart-data').textContent);
    const ctx = document.getElementById('doctorWeekChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Lab', 'Imaging', 'Prescription', 'Surgery', 'Diet'],
            datasets: [{
                label: 'This Week',
                data: [_d.lab, _d.rad, _d.rx, _d.surg, _d.diet],
                backgroundColor: ['rgba(76,126,168,.75)','rgba(224,160,48,.75)','rgba(20,199,154,.75)','rgba(232,92,85,.75)','rgba(92,168,124,.75)'],
                borderColor:      ['#4C7EA8','#E0A030','#14C79A','#E85C55','#5ca87c'],
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { grid: { color: lineColor }, ticks: { color: textSoftColor, font: { size: 11 } } },
                y: { grid: { color: lineColor }, ticks: { color: textSoftColor, font: { size: 11 }, precision: 0 }, beginAtZero: true }
            }
        }
    });
})();
</script>
@endpush
