@extends('layouts.app')
@section('title', 'Diet & Nutrition Dashboard')
@section('page-title', 'Diet & Nutrition Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">Diet Dashboard</li>
@endsection

@section('content')

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card card-signal">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['total_requests'] }}</div>
                    <div class="stat-label">Diet Requests</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-apple"></i></div>
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
                    <div class="stat-value">{{ $stats['active_plans'] }}</div>
                    <div class="stat-label">Active Plans</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-clipboard2-heart"></i></div>
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
                    <div class="stat-value">{{ $stats['completed_today'] ?? 0 }}</div>
                    <div class="stat-label">Completed Today</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-check2-circle"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,18 20,15 38,17 55,11 72,13 87,9 100,11"
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
                <span><i class="bi bi-pie-chart-fill me-2" style="color:var(--signal);"></i>Request Status</span>
                <span class="pill pill-signal">All Time</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position:relative;width:150px;height:150px;">
                    <canvas id="dietStatusDonut"></canvas>
                </div>
                <div class="mt-3 w-100" style="font-size:.78rem;">
                    <div class="d-flex justify-content-between mb-1"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E0A030;margin-right:5px;"></span>Pending</span><strong>{{ $stats['pending'] }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#4C7EA8;margin-right:5px;"></span>In Progress</span><strong>{{ $stats['in_progress'] }}</strong></div>
                    <div class="d-flex justify-content-between"><span style="color:var(--text-soft);"><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#14C79A;margin-right:5px;"></span>Completed</span><strong>{{ $stats['completed_total'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-day me-2" style="color:var(--steel);"></i>Today's Activity</span>
                <span class="pill pill-steel">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(20,199,154,.08);border:1px solid rgba(20,199,154,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--signal);">{{ $stats['completed_today'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Completed Today</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(76,126,168,.08);border:1px solid rgba(76,126,168,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--steel);">{{ $stats['active_plans'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Active Plans</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(224,160,48,.08);border:1px solid rgba(224,160,48,.2);">
                            <div style="font-size:1.8rem;font-weight:700;color:#a06800;">{{ $stats['pending'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Pending</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3" style="background:rgba(110,124,116,.08);border:1px solid var(--line);">
                            <div style="font-size:1.8rem;font-weight:700;color:var(--text-soft);">{{ $stats['completed_total'] }}</div>
                            <div style="font-size:.72rem;color:var(--text-soft);margin-top:2px;">Total Done</div>
                        </div>
                    </div>
                </div>
                @php $rate = $stats['total_requests'] > 0 ? round(($stats['completed_total'] / $stats['total_requests']) * 100) : 0; @endphp
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
                <span><i class="bi bi-journal-bookmark me-2" style="color:var(--signal-dark);"></i>Recent Diet Requests</span>
                <a href="{{ route('diet.requests.index') }}" class="pill pill-signal">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Request No</th><th>Patient</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($recentRequests as $req)
                            <tr>
                                <td><a href="{{ route('diet.requests.show', $req) }}">{{ $req->request_no }}</a></td>
                                <td>{{ $req->patient->last_name }}, {{ $req->patient->first_name }}</td>
                                <td><span class="pill pill-{{ in_array($req->statusBadge, ['success','primary']) ? 'signal' : (($req->statusBadge === 'danger') ? 'coral' : (($req->statusBadge === 'warning') ? 'amber' : 'muted')) }}">{{ $req->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3" style="color:var(--text-soft)">No diet requests yet.</td></tr>
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
                <span><i class="bi bi-clipboard2-heart me-2" style="color:var(--signal-dark);"></i>Active Diet Plans</span>
                <a href="{{ route('diet.plans.index') }}" class="pill pill-signal">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Plan No</th><th>Patient</th><th>Type</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($activePlansList as $plan)
                            <tr>
                                <td><a href="{{ route('diet.plans.show', $plan) }}">{{ $plan->plan_no }}</a></td>
                                <td>{{ $plan->patient->last_name ?? '—' }}, {{ $plan->patient->first_name ?? '' }}</td>
                                <td style="font-family:var(--font-mono);font-size:.72rem;color:var(--text-soft);">{{ $plan->diet_type ?? '—' }}</td>
                                <td><span class="pill pill-{{ in_array($plan->statusBadge, ['success','primary']) ? 'signal' : (($plan->statusBadge === 'danger') ? 'coral' : (($plan->statusBadge === 'warning') ? 'amber' : 'muted')) }}">{{ $plan->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3" style="color:var(--text-soft)">No active plans.</td></tr>
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
    const _d = JSON.parse(document.getElementById('diet-chart-data').textContent);
    const bgColor = getComputedStyle(document.documentElement).getPropertyValue('--surface').trim() || '#fff';
    const ctx = document.getElementById('dietStatusDonut').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                data: [_d.pending, _d.in_progress, _d.completed],
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
