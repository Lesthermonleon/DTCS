@extends('layouts.app')
@section('title', 'Radiology Dashboard')
@section('page-title', 'Radiology Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Radiology Dashboard</li>
@endsection

@section('content')

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card card-steel">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['total_requests'] }}</div>
                    <div class="stat-label">Total Requests</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-image"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,15 15,12 30,14 45,9 60,11 75,7 100,9"
                          fill="none" stroke="#4C7EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card card-amber">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
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
                    <div class="stat-value">{{ $stats['completed'] }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-check2-circle"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,17 20,14 38,16 55,10 72,12 87,8 100,10"
                          fill="none" stroke="#14C79A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card card-steel">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['reports_pending'] ?? 0 }}</div>
                    <div class="stat-label">Reports Pending</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-file-text"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,14 18,11 36,13 52,8 66,10 80,7 100,8"
                          fill="none" stroke="#4C7EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── Analytics Row ── --}}
<div class="row g-3 mb-4">
    {{-- Status Donut --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pie-chart-fill me-2" style="color:var(--steel);"></i>Request Status</span>
                <span class="pill pill-steel">All Time</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position:relative;width:150px;height:150px;">
                    <canvas id="radStatusDonut"></canvas>
                </div>
                <div class="mt-3 w-100" style="font-size:.78rem;">
                    <div class="d-flex justify-content-between mb-1"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E0A030;margin-right:5px;"></span>Pending</span><strong>{{ $stats['pending'] }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#4C7EA8;margin-right:5px;"></span>Scheduled</span><strong>{{ $stats['scheduled'] }}</strong></div>
                    <div class="d-flex justify-content-between"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#14C79A;margin-right:5px;"></span>Completed</span><strong>{{ $stats['completed'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-check me-2" style="color:var(--signal);"></i>Today's Activity</span>
                <span class="pill pill-signal">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(76,126,168,.08);border:1px solid rgba(76,126,168,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--steel);">{{ $stats['today_scheduled'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Scheduled Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(20,199,154,.08);border:1px solid rgba(20,199,154,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--signal);">{{ $stats['today_completed'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Completed Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(224,160,48,.08);border:1px solid rgba(224,160,48,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:#a06800;">{{ $stats['reports_pending'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Reports Pending</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(110,124,116,.08);border:1px solid var(--line);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--text-soft);">{{ $stats['reports_released'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Reports Released</div>
                        </div>
                    </div>
                </div>
                @php $rate = $stats['total_requests'] > 0 ? round(($stats['completed'] / $stats['total_requests']) * 100) : 0; @endphp
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:.78rem;color:var(--text-soft);">Overall Completion Rate</span>
                        <span style="font-size:.78rem;font-weight:700;color:var(--signal-dark);">{{ $rate }}%</span>
                    </div>
                    <div style="height:7px;border-radius:4px;background:rgba(20,199,154,.15);overflow:hidden;">
                        <div class="progress-bar-fill" data-width="{{ $rate }}"></div>
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
                <span><i class="bi bi-image me-2" style="color:var(--steel);"></i>Recent Imaging Requests</span>
                <a href="{{ route('radiology.requests.index') }}" class="pill pill-steel">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Request No</th><th>Patient</th><th>Modality</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($recentRequests as $req)
                            <tr>
                                <td><a href="{{ route('radiology.requests.show', $req) }}">{{ $req->request_no }}</a></td>
                                <td>{{ $req->patient->last_name }}, {{ $req->patient->first_name }}</td>
                                <td style="font-family:var(--font-mono);font-size:.72rem;color:var(--text-soft);">{{ $req->modality }}</td>
                                <td><span class="pill pill-{{ in_array($req->statusBadge, ['success','primary']) ? 'signal' : (($req->statusBadge === 'danger') ? 'coral' : (($req->statusBadge === 'warning') ? 'amber' : 'muted')) }}">{{ $req->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3" style="color:var(--text-soft)">No requests yet.</td></tr>
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
                <span><i class="bi bi-file-text me-2" style="color:var(--text-soft);"></i>Pending Reports</span>
                <a href="{{ route('radiology.reports.index') }}" class="pill pill-muted">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Report No</th><th>Patient</th><th>Modality</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($pendingReports as $rpt)
                            <tr>
                                <td><a href="{{ route('radiology.reports.show', $rpt) }}">{{ $rpt->report_no }}</a></td>
                                <td>{{ $rpt->imagingRequest->patient->last_name ?? '—' }}, {{ $rpt->imagingRequest->patient->first_name ?? '' }}</td>
                                <td style="font-family:var(--font-mono);font-size:.72rem;color:var(--text-soft);">{{ $rpt->imagingRequest->modality ?? '—' }}</td>
                                <td><span class="pill pill-{{ in_array($rpt->statusBadge, ['success','primary']) ? 'signal' : (($rpt->statusBadge === 'danger') ? 'coral' : (($rpt->statusBadge === 'warning') ? 'amber' : 'muted')) }}">{{ $rpt->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3" style="color:var(--text-soft)">No pending reports.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="rad-chart-data">
    {"pending": {{ $stats['pending'] }}, "scheduled": {{ $stats['scheduled'] }}, "completed": {{ $stats['completed'] }}}
</script>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const _d = JSON.parse(document.getElementById('rad-chart-data').textContent);
    const bgColor = getComputedStyle(document.documentElement).getPropertyValue('--surface').trim() || '#fff';
    const ctx = document.getElementById('radStatusDonut').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Scheduled', 'Completed'],
            datasets: [{
                data: [_d.pending, _d.scheduled, _d.completed],
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
