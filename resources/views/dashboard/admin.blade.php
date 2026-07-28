@extends('layouts.app')
@section('title', 'System Administrator Dashboard')
@section('page-title', 'System Administrator Dashboard')
@section('breadcrumb')
    <li class="breadcrumb-item active">System Administrator</li>
@endsection

@section('content')

{{-- ── Statistics Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card card-signal">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-people-fill"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,16 12,12 24,14 36,8 48,10 60,6 72,9 84,5 100,7"
                          fill="none" stroke="#14C79A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card card-steel">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['active_users_today'] }}</div>
                    <div class="stat-label">Active Accounts</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-person-check-fill"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,14 15,10 30,12 45,7 60,9 75,5 85,8 100,6"
                          fill="none" stroke="#4C7EA8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card card-amber">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['new_user_requests'] }}</div>
                    <div class="stat-label">New (Last 7 Days)</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-person-plus-fill"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,15 20,11 35,13 50,8 65,10 80,6 100,9"
                          fill="none" stroke="#E0A030" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card card-coral">
            <div class="stat-card-top">
                <div>
                    <div class="stat-value">{{ $stats['failed_login_attempts'] }}</div>
                    <div class="stat-label">Failed Logins</div>
                </div>
                <div class="stat-icon-wrap"><i class="bi bi-shield-exclamation"></i></div>
            </div>
            <svg class="stat-sparkline" viewBox="0 0 100 20" preserveAspectRatio="none" aria-hidden="true">
                <polyline points="0,18 20,14 35,16 50,10 65,12 80,8 100,10"
                          fill="none" stroke="#E85C55" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── Analytics Row ── --}}
<div class="row g-3 mb-4">
    {{-- User Distribution Donut --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pie-chart-fill me-2" style="color:var(--signal);"></i>User Distribution</span>
                <span class="pill pill-signal">By Role</span>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div style="position:relative;width:160px;height:160px;">
                    <canvas id="roleDonutChart"></canvas>
                </div>
                <div class="mt-3 w-100" id="roleDonutLegend" style="font-size:.78rem;"></div>
            </div>
        </div>
    </div>

    {{-- 7-Day New Users Trend --}}
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up-arrow me-2" style="color:var(--steel);"></i>New Registrations</span>
                <span class="pill pill-steel">Last 7 Days</span>
            </div>
            <div class="card-body d-flex align-items-center">
                <canvas id="newUsersChart" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- Account Health Summary --}}
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-shield-check me-2" style="color:var(--amber);"></i>Account Health
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3" style="border-color:var(--line);background:transparent;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill" style="color:var(--signal);"></i>
                            <span style="font-size:.82rem;color:var(--text);">Active</span>
                        </div>
                        <span class="pill pill-signal">{{ $stats['active_users_today'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3" style="border-color:var(--line);background:transparent;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-dash-circle-fill" style="color:var(--text-soft);"></i>
                            <span style="font-size:.82rem;color:var(--text);">Inactive</span>
                        </div>
                        <span class="pill pill-muted">{{ $stats['inactive_accounts'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3" style="border-color:var(--line);background:transparent;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-lock-fill" style="color:var(--coral);"></i>
                            <span style="font-size:.82rem;color:var(--text);">Locked</span>
                        </div>
                        <span class="pill pill-coral">{{ $stats['locked_accounts'] }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3" style="border-color:var(--line);background:transparent;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-fill" style="color:var(--amber);"></i>
                            <span style="font-size:.82rem;color:var(--text);">Admins</span>
                        </div>
                        <span class="pill pill-amber">{{ $stats['admins_count'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Notification Center & Task Queue ── --}}
<div class="row g-3 mb-4">
    {{-- Notification Center --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bell-fill me-2" style="color:var(--signal);"></i>Notification Center</span>
                <span class="pill pill-signal">{{ $notifications->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notif)
                        <div class="list-group-item d-flex align-items-start gap-3 px-3 py-3" style="border-color:var(--line);background:transparent;">
                            <div style="width:36px;height:36px;border-radius:.5rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;"
                                 class="stat-icon-wrap"
                                 @switch($notif['color'])
                                     @case('signal') style="background:rgba(20,199,154,.12);color:var(--signal-dark);" @break
                                     @case('coral')  style="background:rgba(232,92,85,.12);color:var(--coral);" @break
                                     @case('amber')  style="background:rgba(224,160,48,.12);color:#a06800;" @break
                                     @case('steel')  style="background:rgba(76,126,168,.12);color:var(--steel);" @break
                                 @endswitch>
                                <i class="bi {{ $notif['icon'] }}"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.85rem;color:var(--text);line-height:1.4;">{{ $notif['text'] }}</div>
                                <div style="font-size:.72rem;color:var(--text-soft);margin-top:.2rem;font-family:var(--font-mono);">{{ $notif['time'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-4" style="border-color:var(--line);background:transparent;color:var(--text-soft);font-size:.85rem;">
                            <i class="bi bi-check-circle me-1"></i> No new notifications
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Task Queue --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-task me-2" style="color:var(--amber);"></i>Task Queue</span>
                <span class="pill pill-amber">{{ collect($taskQueue)->sum('count') }}</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($taskQueue as $task)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-3" style="border-color:var(--line);background:transparent;">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;flex-shrink:0;"
                                      class="@if($task['count'] > 0) bg-{{ $task['color'] === 'coral' ? 'danger' : ($task['color'] === 'amber' ? 'warning' : 'success') }} @endif"
                                      @if($task['count'] === 0) style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:var(--signal);" @endif></span>
                                <span style="font-size:.85rem;color:var(--text);">{{ $task['label'] }}</span>
                            </div>
                            <span class="pill pill-{{ $task['color'] }}">{{ $task['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Quick Actions ── --}}
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-lightning-fill me-2" style="color:var(--signal);"></i>Quick Actions
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.users.create') }}" class="d-flex flex-column align-items-center gap-2 text-decoration-none p-3 rounded-3 qa-btn" id="qa-create-user">
                    <div class="qa-icon-wrap" style="background:rgba(20,199,154,.12);color:var(--signal-dark);">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <span class="qa-label">Create User</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.users.index') }}" class="d-flex flex-column align-items-center gap-2 text-decoration-none p-3 rounded-3 qa-btn" id="qa-manage-users">
                    <div class="qa-icon-wrap" style="background:rgba(76,126,168,.12);color:var(--steel);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="qa-label">User Management</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.roles.index') }}" class="d-flex flex-column align-items-center gap-2 text-decoration-none p-3 rounded-3 qa-btn" id="qa-manage-roles">
                    <div class="qa-icon-wrap" style="background:rgba(224,160,48,.12);color:#a06800;">
                        <i class="bi bi-shield-fill"></i>
                    </div>
                    <span class="qa-label">Permission</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.roles.index') }}" class="d-flex flex-column align-items-center gap-2 text-decoration-none p-3 rounded-3 qa-btn" id="qa-manage-permissions">
                    <div class="qa-icon-wrap" style="background:rgba(232,92,85,.12);color:var(--coral);">
                        <i class="bi bi-key-fill"></i>
                    </div>
                    <span class="qa-label">Manage Permissions</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('admin.users.index') }}" class="d-flex flex-column align-items-center gap-2 text-decoration-none p-3 rounded-3 qa-btn" id="qa-audit-logs">
                    <div class="qa-icon-wrap" style="background:rgba(110,124,116,.1);color:var(--text-soft);">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <span class="qa-label">View Audit Logs</span>
                </a>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('settings.index') }}" class="d-flex flex-column align-items-center gap-2 text-decoration-none p-3 rounded-3 qa-btn" id="qa-system-settings">
                    <div class="qa-icon-wrap" style="background:rgba(20,199,154,.08);color:var(--signal-dark);">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    <span class="qa-label">System Settings</span>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Activity ── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2" style="color:var(--steel);"></i>Recent Activity</span>
        <span class="pill pill-steel">{{ $recentActivity->count() }} latest</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentActivity as $log)
                    <tr>
                        <td>
                            @php
                                $actionColors = [
                                    'created' => 'signal', 'updated' => 'steel',
                                    'deleted' => 'coral',  'assigned' => 'amber',
                                    'modified' => 'amber', 'login' => 'signal',
                                ];
                                $color = 'muted';
                                foreach ($actionColors as $key => $val) {
                                    if (str_contains(strtolower($log->action), $key)) { $color = $val; break; }
                                }
                            @endphp
                            <span class="pill pill-{{ $color }}">{{ $log->action }}</span>
                        </td>
                        <td>{{ $log->user?->name ?? 'System' }}</td>
                        <td><span style="font-family:var(--font-mono);font-size:.75rem;color:var(--text-soft);">{{ $log->module ?? '—' }}</span></td>
                        <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->description ?? '—' }}</td>
                        <td style="font-family:var(--font-mono);font-size:.75rem;color:var(--text-soft);white-space:nowrap;">{{ $log->created_at?->format('M d, Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4" style="color:var(--text-soft);">
                            <i class="bi bi-inbox me-1"></i> No activity logged yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Blade data for Charts (type=application/json avoids IDE JS-parse errors) --}}
<script type="application/json" id="admin-chart-data">
    {"usersByRole": {!! json_encode($usersByRole) !!}, "newUsers7d": {!! json_encode($newUsers7d) !!}}
</script>

@endsection

@push('styles')
<style>
    .qa-btn {
        border: 1px solid var(--line);
        transition: all .2s ease;
        cursor: pointer;
    }
    .qa-btn:hover {
        border-color: var(--signal);
        box-shadow: 0 4px 16px rgba(20,199,154,.12);
        transform: translateY(-2px);
    }
    .qa-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: .65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: transform .2s ease;
    }
    .qa-btn:hover .qa-icon-wrap {
        transform: scale(1.1);
    }
    .qa-label {
        font-family: var(--font-body);
        font-size: .78rem;
        font-weight: 600;
        color: var(--text);
        text-align: center;
        line-height: 1.2;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark'
                || document.documentElement.classList.contains('dark');
    const textColor     = getComputedStyle(document.documentElement).getPropertyValue('--text').trim()     || (isDark ? '#e2e8f0' : '#1a2b26');
    const textSoftColor = getComputedStyle(document.documentElement).getPropertyValue('--text-soft').trim() || (isDark ? '#94a3b8' : '#6b8077');
    const lineColor     = getComputedStyle(document.documentElement).getPropertyValue('--line').trim()      || (isDark ? '#2a3a35' : '#dde6e2');
    const bgColor       = getComputedStyle(document.documentElement).getPropertyValue('--surface').trim()   || (isDark ? '#1a2b26' : '#ffffff');

    const PALETTE = ['#14C79A','#4C7EA8','#E0A030','#E85C55','#7c5cbf','#2fb8cc','#e07060','#5ca87c'];

    // ── Read server data ────────────────────────────────────────────────────
    const _d        = JSON.parse(document.getElementById('admin-chart-data').textContent);

    // ── Role Donut ──────────────────────────────────────────────────────────
    const roleData  = _d.usersByRole;
    const roleLabels = roleData.map(r => r.name);
    const roleCounts = roleData.map(r => r.count);

    const donutCtx = document.getElementById('roleDonutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: roleLabels,
            datasets: [{
                data: roleCounts,
                backgroundColor: PALETTE.slice(0, roleLabels.length),
                borderColor: bgColor,
                borderWidth: 3,
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '68%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed}`
                    }
                }
            }
        }
    });

    // Build legend
    const legend = document.getElementById('roleDonutLegend');
    roleLabels.forEach((label, i) => {
        const item = document.createElement('div');
        item.style.cssText = 'display:flex;align-items:center;gap:6px;margin-bottom:4px;';
        item.innerHTML = `<span style="width:10px;height:10px;border-radius:50%;flex-shrink:0;background:${PALETTE[i]}"></span>
                          <span style="color:var(--text-soft);flex:1;">${label}</span>
                          <span style="color:var(--text);font-weight:600;">${roleCounts[i]}</span>`;
        legend.appendChild(item);
    });

    // ── 7-Day New Users Line Chart ────────────────────────────────────────
    const trend    = _d.newUsers7d;
    const tLabels  = trend.map(t => t.date);
    const tCounts  = trend.map(t => t.count);

    const lineCtx = document.getElementById('newUsersChart').getContext('2d');
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: tLabels,
            datasets: [{
                label: 'New Users',
                data: tCounts,
                borderColor: '#4C7EA8',
                backgroundColor: 'rgba(76,126,168,.12)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4C7EA8',
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2,
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
                x: {
                    grid: { color: lineColor },
                    ticks: { color: textSoftColor, font: { size: 11 } }
                },
                y: {
                    grid: { color: lineColor },
                    ticks: { color: textSoftColor, font: { size: 11 }, precision: 0 },
                    beginAtZero: true,
                }
            }
        }
    });
})();
</script>
@endpush
