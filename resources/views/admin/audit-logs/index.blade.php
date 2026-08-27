@extends('layouts.app')

@section('title', 'System Audit Logs')

@section('content')
<style>
    /* Scoped container scrolling for Audit Logs data area */
    .audit-log-table-wrapper {
        max-height: 520px;
        overflow-y: auto;
        overflow-x: auto;
        position: relative;
        border-radius: 0 0 0.5rem 0.5rem;
    }

    /* Sticky table header — dark-mode safe via CSS variable fallback */
    .audit-log-table-wrapper table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: var(--bs-table-bg, var(--bs-body-bg));
        box-shadow: inset 0 -1px 0 var(--bs-border-color);
        color: var(--bs-body-color);
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* Meatballs filter button */
    .btn-meatballs {
        color: var(--bs-secondary-color, #6c757d);
        background: transparent;
        border: none;
        transition: color 0.15s ease-in-out;
    }
    .btn-meatballs:hover,
    .btn-meatballs:focus,
    .btn-meatballs[aria-expanded="true"] {
        color: #198754 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    /* Action view button styling matching global design system */
    .table-action-btn.action-view {
        color: var(--bs-secondary-color, #6c757d);
        background-color: transparent;
        border: 1px solid var(--bs-border-color, #dee2e6);
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease-in-out;
    }
    .table-action-btn.action-view:hover {
        color: #198754;
        border-color: #198754;
        background-color: rgba(25, 135, 84, 0.08);
    }
</style>

<div class="container-fluid px-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-journal-text me-2 text-success"></i>System Audit Logs
            </h4>
            <p class="text-muted small mb-0">Complete, append-only activity trail across all system modules.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 {{ $uncategorizedEvents > 0 ? 'col-lg-2' : '' }}">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ number_format($totalEvents) }}</div>
                <div class="small text-muted">Total Events</div>
            </div>
        </div>
        <div class="col-6 col-md-3 {{ $uncategorizedEvents > 0 ? 'col-lg-2' : '' }}">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ number_format($authEvents) }}</div>
                <div class="small text-muted">Authentication Activity</div>
            </div>
        </div>
        <div class="col-6 col-md-3 {{ $uncategorizedEvents > 0 ? 'col-lg-2' : '' }}">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-secondary">{{ number_format($clinicalEvents) }}</div>
                <div class="small text-muted">Clinical Activity</div>
            </div>
        </div>
        <div class="col-6 col-md-3 {{ $uncategorizedEvents > 0 ? 'col-lg-2' : '' }}">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-dark">{{ number_format($adminSystemEvents) }}</div>
                <div class="small text-muted">System &amp; Admin Audit</div>
            </div>
        </div>
        @if($uncategorizedEvents > 0)
        <div class="col-6 col-md-3 col-lg-2">
            <div class="card border-0 shadow-sm text-center py-3 border-start border-danger border-2">
                <div class="fs-2 fw-bold text-danger">{{ number_format($uncategorizedEvents) }}</div>
                <div class="small text-muted">Uncategorized</div>
            </div>
        </div>
        @endif
    </div>

    {{-- Search Bar + Filter Menu --}}
    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="mb-3">
        <div class="d-flex align-items-center gap-2">
            <div class="flex-grow-1">
                <div class="input-group input-group-sm shadow-sm rounded">
                    <span class="input-group-text bg-body-tertiary border-end-0 text-muted ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control form-control-sm border-start-0 py-2"
                           placeholder="Search user..."
                           value="{{ request('search') }}">
                </div>
            </div>

            {{-- Meatballs Menu Dropdown --}}
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-meatballs px-2 py-2 fs-5"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Filters">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-3" style="min-width: 290px; border-radius: 0.5rem; background-color: var(--bs-body-bg); border: 1px solid var(--bs-border-color) !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <span class="fw-bold text-body small mb-0">
                            <i class="bi bi-funnel me-1 text-success"></i>Filters
                        </span>
                        @if(request()->filled('search') || request()->filled('role') || request()->filled('module') || request()->filled('severity') || request()->filled('date'))
                            <span class="badge bg-success-subtle text-success px-2 py-1" style="font-size: 0.7rem;">Active</span>
                        @endif
                    </div>

                    {{-- Module Filter --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold mb-1">Module</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="">All Modules</option>
                            @foreach($moduleOptions as $mod)
                                <option value="{{ $mod }}" @selected(request('module') === $mod)>{{ $mod }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Severity Filter --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold mb-1">Severity</label>
                        <select name="severity" class="form-select form-select-sm">
                            <option value="">All Severities</option>
                            <option value="INFO"     @selected(request('severity') === 'INFO')>INFO</option>
                            <option value="WARNING"  @selected(request('severity') === 'WARNING')>WARNING</option>
                            <option value="CRITICAL" @selected(request('severity') === 'CRITICAL')>CRITICAL</option>
                        </select>
                    </div>

                    {{-- Role Filter --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold mb-1">Role</label>
                        <select name="role" class="form-select form-select-sm">
                            <option value="">All Roles</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" @selected(request('role') == $r->id)>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date Filter --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold mb-1">Date</label>
                        <select name="date" class="form-select form-select-sm">
                            <option value="" @selected(empty(request('date')))>Any Date</option>
                            <option value="today"     @selected(request('date') === 'today')>Today</option>
                            <option value="yesterday" @selected(request('date') === 'yesterday')>Yesterday</option>
                            <option value="7_days"    @selected(request('date') === '7_days')>Last 7 Days</option>
                            <option value="30_days"   @selected(request('date') === '30_days')>Last 30 Days</option>
                            <option value="this_year" @selected(request('date') === 'this_year')>This Year</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-sm btn-success flex-fill">Apply</button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary flex-fill text-center">Clear</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Audit Log Table Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3 border-bottom">
            <span class="fw-semibold small text-body">
                <i class="bi bi-list-ul me-1 text-success"></i>
                {{ number_format($logs->total()) }} Audit Events Found
            </span>
            <small class="text-muted">Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }}</small>
        </div>

        {{-- Scrollable Audit Data Area --}}
        <div class="audit-log-table-wrapper">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="min-width: 150px;">Timestamp</th>
                        <th style="min-width: 130px;">Module</th>
                        <th style="min-width: 160px;">Action</th>
                        <th style="min-width: 100px;">Severity</th>
                        <th style="min-width: 150px;">User</th>
                        <th style="min-width: 120px;">IP Address</th>
                        <th class="pe-3 text-end" style="min-width: 60px;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        {{-- 1. Timestamp --}}
                        <td class="ps-3 text-nowrap small text-muted">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </td>

                        {{-- 2. Module --}}
                        <td>
                            <span class="badge"
                                style="background-color: var(--bs-secondary-bg); color: var(--bs-secondary-color); font-weight: 500; font-size: 0.75rem;">
                                {{ $log->module }}
                            </span>
                        </td>

                        {{-- 3. Action --}}
                        <td class="small fw-semibold">
                            @if(in_array($log->action, ['Account Locked']))
                                <span class="badge bg-danger text-white border border-danger px-2 py-1">
                                    <i class="bi bi-lock-fill me-1"></i>{{ $log->action }}
                                </span>
                            @elseif(in_array($log->action, ['Failed Login', 'Session Replaced', 'Role Assignment Changed', 'Password Reset', 'User Archived', 'Surgery Schedule Removed']))
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-shield-exclamation me-1"></i>{{ $log->action }}
                                </span>
                            @elseif(in_array($log->action, ['Account Unlocked']))
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-shield-check me-1"></i>{{ $log->action }}
                                </span>
                            @else
                                <span class="text-body fw-medium">{{ $log->action }}</span>
                            @endif
                        </td>

                        {{-- 4. Severity --}}
                        <td>
                            @php $sev = $log->severity ?? 'INFO'; @endphp
                            <span class="badge {{ $log->severity_badge_class }}" style="font-size: 0.7rem;">
                                <i class="bi {{ $log->severity_icon }} me-1" style="font-size: 0.65rem;"></i>{{ $sev }}
                            </span>
                        </td>

                        {{-- 5. User --}}
                        <td class="small text-nowrap">
                            @if($log->user)
                                <span class="text-body fw-medium">
                                    <i class="bi bi-person-circle me-1 text-muted"></i>{{ $log->user->name }}
                                </span>
                                @if($log->user->roles->isNotEmpty())
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                                        ({{ $log->user->roles->first()->name }})
                                    </small>
                                @endif
                            @else
                                <span class="text-muted fst-italic">System</span>
                            @endif
                        </td>

                        {{-- 6. IP Address --}}
                        <td class="small text-muted text-nowrap">
                            <code class="text-muted bg-transparent p-0 small font-monospace">{{ $log->ip_address ?? '—' }}</code>
                        </td>

                        {{-- 7. Detail Action Button (Icon only) --}}
                        <td class="pe-3 text-end">
                            <a href="{{ route('admin.audit-logs.show', $log) }}"
                               class="table-action-btn action-view"
                               title="View details"
                               aria-label="View details">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-journal-x fs-2 d-block mb-2 text-secondary"></i>
                            No audit log entries found matching the search or filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="card-footer bg-transparent border-top py-2 d-flex justify-content-end">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
