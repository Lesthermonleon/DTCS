@extends('layouts.app')
@section('title', 'Surgery Calendar')
@section('page-title', 'Operating Room Surgery Calendar')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('surgery.dashboard') }}">Surgery (SORS)</a></li>
    <li class="breadcrumb-item active">Calendar</li>
@endsection

@section('content')
<div class="apple-calendar-container">
    {{-- ── Top Navigation & Filter Header Bar ── --}}
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 0.75rem; background: var(--card);">
        <div class="card-body p-3">
                {{-- ── Top Row: Surgery Calendar (Left), Centered Search (Center), Actions (Right) ── --}}
                <div class="row g-3 align-items-center calendar-top-row">
                    {{-- Surgery Calendar Title --}}
                    <div class="col-12 col-md-4 text-center text-md-start">
                        <h4 class="mb-0 fw-bold text-dark" style="font-family: var(--font-display); font-size: 1.35rem; letter-spacing: -0.02em;">Surgery Calendar</h4>
                    </div>

                    {{-- Centered Search Input --}}
                    <div class="col-12 col-md-4 d-flex justify-content-center">
                        <div class="topbar-search calendar-search-box px-0" style="width: 240px; max-width: 100%; position: relative;">
                            <div class="topbar-search-wrap">
                                <i class="bi bi-search"></i>
                                <input type="search" id="filterSearch" placeholder="Search..." aria-label="Search" autocomplete="off">
                                <div id="calendarSearchDropdown" class="search-dropdown-results d-none" style="top: 100% !important; min-width: 280px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Right-aligned Action Buttons --}}
                    <div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-end gap-2">
                        @if(auth()->user()->hasRole('or-coordinator'))
                            <a href="{{ route('surgery.schedules.create') }}" class="btn btn-sm btn-primary px-3 fw-semibold d-inline-flex align-items-center gap-1.5" style="border-radius: 0.5rem; font-size: 0.85rem;">
                                <i class="bi bi-plus-lg fs-6"></i> <span>Schedule</span>
                            </a>
                        @endif

                        <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 d-none d-sm-inline-flex" id="btnPrintSchedule" onclick="window.print()" title="Print Surgery Schedule" style="font-size: 0.85rem; border-radius: 0.5rem;">
                            <i class="bi bi-printer me-1"></i>Print
                        </button>
                    </div>
                </div>

        </div>
    </div>

    {{-- ── Main Dual-Panel Layout (Mini Calendar Sidebar + Main Weekly Viewport) ── --}}
    <div class="row g-3">
        
        {{-- Left Sidebar Panel: Mini Calendar & Upcoming Surgeries (Collapsible on mobile) --}}
        <div class="col-lg-4 col-xl-3 collapse d-lg-block" id="sidebarPanelCollapse">
            {{-- Apple Dark Mini-Calendar Card --}}
            <div class="card shadow-sm border-0 mb-3 overflow-hidden" style="border-radius: 0.75rem; background: var(--card);">
                <div class="card-header border-bottom py-2.5 px-3 d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.02);">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size: 0.88rem;" id="miniMonthTitle">
                        <i class="bi bi-calendar-date text-primary"></i> <span id="miniMonthYearText">August 2026</span>
                    </h6>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-xs btn-outline-secondary px-1.5" id="miniPrevMonth"><i class="bi bi-chevron-left"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary px-1.5" id="miniNextMonth"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
                <div class="card-body p-2.5">
                    {{-- Mini Month Days Header --}}
                    <div class="mini-calendar-grid header-grid mb-1">
                        <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                    </div>
                    {{-- Mini Month Date Grid --}}
                    <div class="mini-calendar-grid body-grid" id="miniCalendarGrid">
                        {{-- Populated dynamically via JS --}}
                    </div>
                </div>
            </div>

            @php
                $emergencies = $upcomingSchedules->filter(fn($s) => ($s->surgeryRequest->urgency ?? 'Elective') === 'Emergency');
                $urgents = $upcomingSchedules->filter(fn($s) => ($s->surgeryRequest->urgency ?? 'Elective') === 'Urgent');
                $electives = $upcomingSchedules->filter(fn($s) => ($s->surgeryRequest->urgency ?? 'Elective') === 'Elective');
            @endphp

            {{-- 1. Emergency Surgeries Card --}}
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 0.75rem; background: var(--card);">
                <div class="card-header border-bottom py-2.5 px-3 d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.02);">
                    <h6 class="mb-0 fw-bold text-danger d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
                        <i class="bi bi-exclamation-triangle-fill text-danger"></i> Emergency Surgeries
                    </h6>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                        {{ $emergencies->count() }} Active
                    </span>
                </div>
                <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                    <div class="list-group list-group-flush" id="upcomingEmergencyAgendaList">
                        @forelse($emergencies as $sched)
                            @php
                                $isToday = $sched->scheduled_at->isToday();
                                $statusBg = 'bg-primary-subtle text-primary border-primary-subtle';
                                if ($sched->status === 'In Progress') {
                                    $statusBg = 'bg-warning-subtle text-warning border-warning-subtle';
                                } elseif ($sched->status === 'Postponed') {
                                    $statusBg = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                                }
                            @endphp
                            <div class="list-group-item p-2 border-bottom agenda-item-card position-relative cursor-pointer" data-schedule-id="{{ $sched->id }}" data-procedure="{{ $sched->surgeryRequest->procedure_name ?? '' }}" data-patient="{{ $sched->surgeryRequest->patient->full_name ?? '' }}" data-request-no="{{ $sched->surgeryRequest->request_no ?? '' }}">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge {{ $isToday ? 'bg-danger text-white' : 'bg-light text-dark border' }}" style="font-size: 0.65rem;">
                                        {{ $isToday ? 'TODAY' : $sched->scheduled_at->format('M d') }} &bull; {{ $sched->scheduled_at->format('g:i A') }}
                                    </span>
                                    <span class="badge bg-light text-dark border" style="font-size: 0.62rem;">
                                        <i class="bi bi-door-open me-0.5"></i>{{ $sched->operatingRoom->name ?? 'OR' }}
                                    </span>
                                </div>
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem; line-height: 1.25;">
                                    {{ $sched->surgeryRequest->procedure_name ?? 'Surgical Procedure' }}
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 text-muted" style="font-size: 0.72rem;">
                                    <span class="text-truncate" style="max-width: 140px;">
                                        <i class="bi bi-person me-0.5"></i>{{ $sched->surgeryRequest->patient->full_name ?? 'Patient' }}
                                    </span>
                                    <span class="badge {{ $statusBg }} border px-1.5 py-0.5" style="font-size: 0.62rem;">
                                        {{ $sched->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted">
                                <span class="small" style="font-size: 0.75rem;">No emergency surgeries.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 2. Urgent Surgeries Card --}}
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 0.75rem; background: var(--card);">
                <div class="card-header border-bottom py-2.5 px-3 d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.02);">
                    <h6 class="mb-0 fw-bold text-warning d-flex align-items-center gap-1.5" style="font-size: 0.88rem; color: #b58100 !important;">
                        <i class="bi bi-exclamation-circle-fill text-warning"></i> Urgent Surgeries
                    </h6>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5" style="font-size: 0.68rem; color: #b58100 !important;">
                        {{ $urgents->count() }} Urgent
                    </span>
                </div>
                <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                    <div class="list-group list-group-flush" id="upcomingUrgentAgendaList">
                        @forelse($urgents as $sched)
                            @php
                                $isToday = $sched->scheduled_at->isToday();
                                $statusBg = 'bg-primary-subtle text-primary border-primary-subtle';
                                if ($sched->status === 'In Progress') {
                                    $statusBg = 'bg-warning-subtle text-warning border-warning-subtle';
                                } elseif ($sched->status === 'Postponed') {
                                    $statusBg = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                                }
                            @endphp
                            <div class="list-group-item p-2 border-bottom agenda-item-card position-relative cursor-pointer" data-schedule-id="{{ $sched->id }}" data-procedure="{{ $sched->surgeryRequest->procedure_name ?? '' }}" data-patient="{{ $sched->surgeryRequest->patient->full_name ?? '' }}" data-request-no="{{ $sched->surgeryRequest->request_no ?? '' }}">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge {{ $isToday ? 'bg-danger text-white' : 'bg-light text-dark border' }}" style="font-size: 0.65rem;">
                                        {{ $isToday ? 'TODAY' : $sched->scheduled_at->format('M d') }} &bull; {{ $sched->scheduled_at->format('g:i A') }}
                                    </span>
                                    <span class="badge bg-light text-dark border" style="font-size: 0.62rem;">
                                        <i class="bi bi-door-open me-0.5"></i>{{ $sched->operatingRoom->name ?? 'OR' }}
                                    </span>
                                </div>
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem; line-height: 1.25;">
                                    {{ $sched->surgeryRequest->procedure_name ?? 'Surgical Procedure' }}
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 text-muted" style="font-size: 0.72rem;">
                                    <span class="text-truncate" style="max-width: 140px;">
                                        <i class="bi bi-person me-0.5"></i>{{ $sched->surgeryRequest->patient->full_name ?? 'Patient' }}
                                    </span>
                                    <span class="badge {{ $statusBg }} border px-1.5 py-0.5" style="font-size: 0.62rem;">
                                        {{ $sched->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted">
                                <span class="small" style="font-size: 0.75rem;">No urgent surgeries.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- 3. Elective Surgeries Card --}}
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 0.75rem; background: var(--card);">
                <div class="card-header border-bottom py-2.5 px-3 d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.02);">
                    <h6 class="mb-0 fw-bold text-secondary d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
                        <i class="bi bi-calendar-event text-secondary"></i> Elective Surgeries
                    </h6>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                        {{ $electives->count() }} Elective
                    </span>
                </div>
                <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                    <div class="list-group list-group-flush" id="upcomingElectiveAgendaList">
                        @forelse($electives as $sched)
                            @php
                                $isToday = $sched->scheduled_at->isToday();
                                $statusBg = 'bg-primary-subtle text-primary border-primary-subtle';
                                if ($sched->status === 'In Progress') {
                                    $statusBg = 'bg-warning-subtle text-warning border-warning-subtle';
                                } elseif ($sched->status === 'Postponed') {
                                    $statusBg = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                                }
                            @endphp
                            <div class="list-group-item p-2 border-bottom agenda-item-card position-relative cursor-pointer" data-schedule-id="{{ $sched->id }}" data-procedure="{{ $sched->surgeryRequest->procedure_name ?? '' }}" data-patient="{{ $sched->surgeryRequest->patient->full_name ?? '' }}" data-request-no="{{ $sched->surgeryRequest->request_no ?? '' }}">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge {{ $isToday ? 'bg-danger text-white' : 'bg-light text-dark border' }}" style="font-size: 0.65rem;">
                                        {{ $isToday ? 'TODAY' : $sched->scheduled_at->format('M d') }} &bull; {{ $sched->scheduled_at->format('g:i A') }}
                                    </span>
                                    <span class="badge bg-light text-dark border" style="font-size: 0.62rem;">
                                        <i class="bi bi-door-open me-0.5"></i>{{ $sched->operatingRoom->name ?? 'OR' }}
                                    </span>
                                </div>
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem; line-height: 1.25;">
                                    {{ $sched->surgeryRequest->procedure_name ?? 'Surgical Procedure' }}
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1 text-muted" style="font-size: 0.72rem;">
                                    <span class="text-truncate" style="max-width: 140px;">
                                        <i class="bi bi-person me-0.5"></i>{{ $sched->surgeryRequest->patient->full_name ?? 'Patient' }}
                                    </span>
                                    <span class="badge {{ $statusBg }} border px-1.5 py-0.5" style="font-size: 0.62rem;">
                                        {{ $sched->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted">
                                <span class="small" style="font-size: 0.75rem;">No elective surgeries.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Main Panel: Apple Calendar Grid Viewport --}}
        <div class="col-lg-8 col-xl-9">
            <div class="card shadow-sm border-0" style="border-radius: 0.75rem; background: var(--card);">
                <div class="card-body p-2 p-md-3">
                    {{-- Calendar Header Row: Mobile Toggle (Left), Centered Title (Center), View Switcher (Right) ── --}}
                    <div class="calendar-card-header mb-3">
                        <div class="row g-2 align-items-center">
                            {{-- Left Column: Agenda Toggle & Filters --}}
                            <div class="col-12 col-md-4 order-1 order-md-1 d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary px-2.5 d-lg-none" data-bs-toggle="collapse" data-bs-target="#sidebarPanelCollapse" aria-expanded="false" aria-controls="sidebarPanelCollapse" title="Toggle Sidebar" style="font-size: 0.8rem;">
                                    <i class="bi bi-layout-sidebar-inset me-1"></i>Agenda
                                </button>

                                {{-- OR Dropdown --}}
                                <select id="filterRoom" class="form-select form-select-sm calendar-filter-item-grid" style="width: 120px; border-color: rgba(0,0,0,0.08); font-size: 0.8rem; height: 31px;">
                                    <option value="">All ORs</option>
                                    @foreach($operatingRooms as $room)
                                        <option value="{{ $room->name }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>

                                {{-- Status Dropdown --}}
                                <select id="filterStatus" class="form-select form-select-sm calendar-filter-item-grid" style="width: 110px; border-color: rgba(0,0,0,0.08); font-size: 0.8rem; height: 31px;">
                                    <option value="">All Statuses</option>
                                    @foreach(['Scheduled', 'In Progress', 'Completed', 'Postponed'] as $status)
                                        <option value="{{ $status }}">{{ $status }}</option>
                                    @endforeach
                                </select>

                            </div>
                            
                            {{-- Center Column: Dynamic Title --}}
                            <div class="col-12 col-md-4 order-2 order-md-2 text-center">
                                <h5 class="mb-0 fw-bold text-dark text-nowrap" id="calendarTitle" style="font-family: var(--font-display); font-size: 1.1rem; color: var(--text) !important; letter-spacing: -0.01em;">
                                    Loading Date...
                                </h5>
                            </div>
                            
                            {{-- Right Column: View Switcher --}}
                            <div class="col-12 col-md-4 order-3 order-md-3 d-flex justify-content-center justify-content-md-end">
                                <div class="btn-group btn-group-sm p-0.5 rounded-2 border calendar-view-group" style="background: var(--paper, #fff);" role="group">
                                    <button type="button" class="btn btn-xs calendar-view-btn" data-view="timeGridDay" id="btnViewDay">Day</button>
                                    <button type="button" class="btn btn-xs calendar-view-btn active" data-view="timeGridWeek" id="btnViewWeek">Week</button>
                                    <button type="button" class="btn btn-xs calendar-view-btn" data-view="dayGridMonth" id="btnViewMonth">Month</button>
                                    <button type="button" class="btn btn-xs calendar-view-btn" data-view="multiMonthYear" id="btnViewYear">Year</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="calendarWrapper" class="w-100 overflow-x-auto">
                        <div id="calendar" class="w-100" style="min-width: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Event Details Modal --}}
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm-fullscreen">
        <div class="modal-content" style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 0.75rem; overflow: hidden; background: var(--card); color: var(--text); box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom py-3 px-4" style="background: rgba(0,0,0,0.015)">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="eventDetailsModalLabel" style="font-family: var(--font-display); font-size: 0.98rem; color: var(--text);">
                    <i class="bi bi-activity text-primary"></i>Surgery Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem; opacity: 0.5; filter: var(--close-filter, none);"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="text-muted small mb-1" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em;">Procedure</div>
                        <h5 class="fw-bold mb-0 text-primary" id="modalProcedure" style="font-family: var(--font-display); font-size: 1.15rem; line-height: 1.4;"></h5>
                    </div>

                    <div class="row g-3 pt-2">
                        <div class="col-6">
                            <div class="text-muted small mb-1" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em;">Patient</div>
                            <div class="fw-semibold" id="modalPatient" style="font-size: 0.88rem; color: var(--text);"></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small mb-1" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em;">Operating Room</div>
                            <div class="fw-semibold text-success d-flex align-items-center gap-1" style="font-size: 0.88rem;">
                                <i class="bi bi-door-open" style="font-size: 0.95rem;"></i>
                                <span id="modalOR"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 border-top pt-3" style="border-color: rgba(0,0,0,0.06) !important;">
                        <div class="col-6">
                            <div class="text-muted small mb-1" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em;">Date & Time</div>
                            <div class="fw-semibold" id="modalScheduleTime" style="font-size: 0.85rem; color: var(--text); line-height: 1.35;"></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small mb-1" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em;">Duration</div>
                            <div style="font-size: 0.85rem; color: var(--text);"><span class="fw-semibold" id="modalDuration"></span> mins</div>
                        </div>
                    </div>

                    <div class="row g-3 border-top pt-3" style="border-color: rgba(0,0,0,0.06) !important;">
                        <div class="col-6">
                            <div class="text-muted small mb-1" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em;">Surgeon</div>
                            <div class="fw-semibold" id="modalSurgeon" style="font-size: 0.85rem; color: var(--text-soft);"></div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small mb-1" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em;">Requested By</div>
                            <div id="modalRequestedBy" style="font-size: 0.85rem; color: var(--text-soft);"></div>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-2 d-flex align-items-center justify-content-between" style="border-color: rgba(0,0,0,0.06) !important;">
                        <div>
                            <div class="text-muted small mb-1" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.03em;">Status</div>
                            <span class="badge" id="modalStatus" style="font-size: 0.75rem; padding: 0.4em 0.85em; font-weight: 600; border-radius: 0.35rem;"></span>
                        </div>
                        <div class="d-flex align-items-center gap-1.5">
                            <a id="modalViewScheduleLink" href="#" class="btn btn-xs btn-outline-primary px-3 py-1.5 fw-semibold d-flex align-items-center gap-1" style="border-radius: 0.375rem; font-size: 0.78rem;">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ── Apple Calendar View Switcher Buttons ── */
    .calendar-view-btn {
        border: none !important;
        background: transparent !important;
        color: var(--text-soft, #64748b) !important;
        font-weight: 600 !important;
        font-size: 0.78rem !important;
        padding: 0.28rem 0.65rem !important;
        border-radius: 0.25rem !important;
        transition: all 0.15s ease !important;
        text-align: center;
    }

    .calendar-view-btn.active {
        background: var(--paper, #ffffff) !important;
        color: var(--bs-primary, #0d6efd) !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        font-weight: 700 !important;
    }

    html[data-theme="dark"] .calendar-view-btn.active {
        background: #1e3630 !important;
        color: #14c79a !important;
    }

    /* ── Mini Month Calendar Sidebar Grid ── */
    .mini-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        text-align: center;
    }

    .mini-calendar-grid.header-grid {
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-soft, #64748b);
        padding-bottom: 4px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .mini-day-cell {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.15s ease;
        color: var(--text, #1e293b);
        position: relative;
    }

    .mini-day-cell:hover {
        background: rgba(13, 110, 253, 0.1);
        color: var(--bs-primary, #0d6efd);
    }

    .mini-day-cell.other-month {
        opacity: 0.3;
    }

    .mini-day-cell.is-today {
        background: #0d6efd !important;
        color: #ffffff !important;
        font-weight: 700 !important;
    }

    .mini-day-cell.is-selected {
        box-shadow: 0 0 0 2px #0d6efd;
    }

    .mini-day-cell .event-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: #0d6efd;
        position: absolute;
        bottom: 2px;
    }

    .mini-day-cell.is-today .event-dot {
        background-color: #ffffff;
    }

    /* ── Agenda List Hover State ── */
    .agenda-item-card {
        transition: background-color 0.15s ease;
    }
    .agenda-item-card:hover {
        background-color: rgba(13, 110, 253, 0.04) !important;
    }

    /* ── FullCalendar Styling & Theme Variables ── */
    #calendar {
        font-family: var(--font-body), sans-serif;
        --fc-border-color: #e2e8f0;
        --fc-today-bg-color: rgba(13, 110, 253, 0.04);
        --fc-now-indicator-color: #dc3545;
        --fc-list-event-hover-bg-color: rgba(13, 110, 253, 0.06);
    }

    html[data-theme="dark"] #calendar {
        --fc-border-color: #1e3630;
        --fc-today-bg-color: rgba(20, 199, 154, 0.08);
        --fc-list-event-hover-bg-color: rgba(20, 199, 154, 0.1);
        --close-filter: invert(1) grayscale(1) brightness(2);
    }

    html[data-theme="dark"] .fc-theme-standard td,
    html[data-theme="dark"] .fc-theme-standard th,
    html[data-theme="dark"] .fc-theme-standard .fc-scrollgrid {
        border-color: #1e3630 !important;
    }

    html[data-theme="dark"] .fc-list-day-cushion {
        background-color: #12221e !important;
    }

    .fc-day-today {
        background-color: var(--fc-today-bg-color) !important;
    }
    
    .fc-day-today .fc-daygrid-day-number {
        font-weight: 800 !important;
        color: var(--bs-primary, #0d6efd) !important;
    }

    .fc-event {
        cursor: pointer !important;
        border-radius: 0.35rem !important;
        border: 1px solid rgba(0,0,0,0.06) !important;
        margin: 1.5px 2px !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: transform 0.15s ease, box-shadow 0.15s ease !important;
        overflow: hidden !important;
    }

    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0,0,0,0.08) !important;
        opacity: 0.96;
    }

    .fc-compact-event {
        padding: 3px 5px;
        line-height: 1.25;
        font-size: 0.72rem;
    }

    /* ── Mobile Screen Specific Overrides ── */
    @media (max-width: 767.98px) {
        .calendar-top-row,
        .calendar-bottom-row,
        .calendar-filters-row {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }

        .calendar-search-group,
        .calendar-nav-group,
        .calendar-view-wrapper {
            width: 100% !important;
            justify-content: space-between !important;
        }

        .calendar-search-box {
            flex: 1 1 auto !important;
            width: auto !important;
            max-width: none !important;
        }

        .calendar-filter-group {
            width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }

        .calendar-filter-item {
            flex: 1 1 calc(50% - 0.25rem) !important;
            width: auto !important;
            margin: 0 !important;
        }

        .calendar-view-group {
            width: 100% !important;
            display: flex !important;
            margin: 0 !important;
        }

        .calendar-view-btn {
            flex: 1 !important;
            text-align: center !important;
        }

        .fc .fc-col-header-cell-cushion {
            font-size: 0.7rem !important;
            padding: 3px 1px !important;
        }

        .fc-daygrid-day-number {
            font-size: 0.7rem !important;
            padding: 2px 3px !important;
        }

        #calendarTitle {
            font-size: 0.9rem !important;
        }

        .fc-compact-event {
            padding: 2px 3px !important;
            font-size: 0.65rem !important;
        }

        .fc-timegrid-slot {
            height: 2.2rem !important;
        }
    }

    /* Modal Backdrop Layering */
    .modal-backdrop {
        z-index: 1050 !important;
    }
    #eventDetailsModal {
        z-index: 1060 !important;
    }

    /* ── Medical A4 Print Stylesheet ── */
    @media print {
        body * {
            visibility: hidden;
        }
        .apple-calendar-container,
        .apple-calendar-container * {
            visibility: visible;
        }
        .apple-calendar-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn, .input-group, select, #miniMonthTitle, .btn-group, #miniCalendarGrid, .card-header, #sidebarPanelCollapse {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
let globalCalendarInstance = null;
let allSurgeryEvents = [];
let miniCurrentDate = new Date();

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const titleEl = document.getElementById('calendarTitle');
    const filterSearch = document.getElementById('filterSearch');
    const filterRoom = document.getElementById('filterRoom');
    const filterStatus = document.getElementById('filterStatus');
    const viewBtns = document.querySelectorAll('.calendar-view-btn');

    let currentView = window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek';

    function updateViewButtons(viewName) {
        currentView = viewName;
        viewBtns.forEach(btn => {
            if (btn.getAttribute('data-view') === viewName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function checkActiveFilters() {
        // No-op: btnClearFilters removed
    }

    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: currentView,
        headerToolbar: false,
        navLinks: true,
        nowIndicator: true,
        scrollTime: '07:00:00',
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        windowResize: function(arg) {
            if (window.innerWidth < 768 && calendar.view.type === 'timeGridWeek') {
                calendar.changeView('timeGridDay');
            }
        },

        // Fetch Events Feed
        events: function(info, successCallback, failureCallback) {
            fetch('{{ route("surgery.calendar.events") }}')
                .then(res => res.json())
                .then(data => {
                    allSurgeryEvents = data;
                    renderMiniCalendar(miniCurrentDate);

                    const searchVal = filterSearch.value.trim().toLowerCase();
                    const roomVal = filterRoom.value;
                    const statusVal = filterStatus.value;

                    const filtered = allSurgeryEvents.filter(e => {
                        const p = e.extendedProps || {};
                        const titleMatch = !searchVal || 
                            (e.title && e.title.toLowerCase().includes(searchVal)) ||
                            (p.procedure && p.procedure.toLowerCase().includes(searchVal)) ||
                            (p.patient_name && p.patient_name.toLowerCase().includes(searchVal)) ||
                            (p.surgeon && p.surgeon.toLowerCase().includes(searchVal)) ||
                            (p.request_no && p.request_no.toLowerCase().includes(searchVal));

                        const roomMatch = !roomVal || p.or === roomVal;
                        const statusMatch = !statusVal || p.status === statusVal;

                        return titleMatch && roomMatch && statusMatch;
                    });

                    successCallback(filtered);
                })
                .catch(err => failureCallback(err));
        },

        // Update Title on Date/View change
        datesSet: function(info) {
            if (titleEl) titleEl.textContent = info.view.title;
            updateViewButtons(info.view.type);
        },

        // Event Card Renderer
        eventContent: function(arg) {
            const p = arg.event.extendedProps || {};
            const status = p.status || 'Scheduled';
            const timeStr = arg.timeText || p.start_time || '';
            const orName = p.or || 'OR';
            const patient = p.patient_name || '';
            const procedure = p.procedure || arg.event.title;

            let borderClass = 'border-start border-3 border-primary';
            if (status === 'Completed') borderClass = 'border-start border-3 border-success';
            else if (status === 'In Progress') borderClass = 'border-start border-3 border-warning';
            else if (status === 'Postponed') borderClass = 'border-start border-3 border-secondary';

            const container = document.createElement('div');
            container.className = `fc-compact-event ${borderClass} w-100 h-100 text-truncate`;
            container.title = `${procedure} — ${patient} (${orName})`;

            container.innerHTML = `
                <div class="d-flex align-items-center justify-content-between gap-1 mb-0.5">
                    <span class="fw-bold text-truncate" style="font-size: 0.68rem;">${timeStr}</span>
                    <span class="badge bg-light text-dark border p-0.5 px-1 rounded-pill" style="font-size: 0.58rem; font-weight: 600;">${orName}</span>
                </div>
                <div class="fw-bold text-truncate" style="font-size: 0.72rem; line-height: 1.2;">${procedure}</div>
                ${patient ? `<div class="text-muted text-truncate" style="font-size: 0.65rem; opacity: 0.85;">${patient}</div>` : ''}
            `;
            return { domNodes: [container] };
        },

        // Event Click details modal
        eventClick: function(info) {
            showScheduleModal(info.event);
        },

        height: 'auto',
    });

    calendar.render();
    globalCalendarInstance = calendar;

    // Navigation buttons
    document.getElementById('btnToday')?.addEventListener('click', () => {
        calendar.today();
        miniCurrentDate = new Date();
        renderMiniCalendar(miniCurrentDate);
    });
    document.getElementById('btnPrev')?.addEventListener('click', () => calendar.prev());
    document.getElementById('btnNext')?.addEventListener('click', () => calendar.next());

    // View selector buttons
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetView = this.getAttribute('data-view');
            calendar.changeView(targetView);
            updateViewButtons(targetView);
        });
    });

    // Calendar Dropdown Elements & Helper
    const calendarSearchDropdown = document.getElementById('calendarSearchDropdown');
    const localEscapeHtml = (str) => typeof escapeHtml === 'function' ? escapeHtml(str) : (str || '').replace(/[&<>"']/g, m => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[m]));

    function hideCalendarDropdown() {
        if (calendarSearchDropdown) {
            calendarSearchDropdown.classList.add('d-none');
            calendarSearchDropdown.innerHTML = '';
        }
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (filterSearch && calendarSearchDropdown && !filterSearch.contains(e.target) && !calendarSearchDropdown.contains(e.target)) {
            hideCalendarDropdown();
        }
    });

    // Search and Filter Listeners
    let searchTimeout;
    filterSearch?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchInputVal = this.value.trim().toLowerCase();
        
        searchTimeout = setTimeout(() => {
            checkActiveFilters();
            calendar.refetchEvents();

            // Filter Sidebar Agenda list cards
            const agendaItems = document.querySelectorAll('.agenda-item-card');
            agendaItems.forEach(item => {
                const procedure = (item.getAttribute('data-procedure') || '').toLowerCase();
                const patient = (item.getAttribute('data-patient') || '').toLowerCase();
                const reqNo = (item.getAttribute('data-request-no') || '').toLowerCase();

                const match = !searchInputVal || 
                              procedure.includes(searchInputVal) || 
                              patient.includes(searchInputVal) || 
                              reqNo.includes(searchInputVal);

                if (match) {
                    item.style.setProperty('display', 'block', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });

            // Populate and Show/Hide Dropdown
            if (searchInputVal.length < 2) {
                hideCalendarDropdown();
                return;
            }

            if (allSurgeryEvents && calendarSearchDropdown) {
                const matches = allSurgeryEvents.filter(e => {
                    const p = e.extendedProps || {};
                    return (e.title && e.title.toLowerCase().includes(searchInputVal)) ||
                           (p.procedure && p.procedure.toLowerCase().includes(searchInputVal)) ||
                           (p.patient_name && p.patient_name.toLowerCase().includes(searchInputVal)) ||
                           (p.surgeon && p.surgeon.toLowerCase().includes(searchInputVal)) ||
                           (p.request_no && p.request_no.toLowerCase().includes(searchInputVal));
                });

                if (matches.length === 0) {
                    calendarSearchDropdown.innerHTML = `
                        <div class="px-3 py-3 text-center text-soft" style="font-size: .83rem;">
                            <i class="bi bi-search d-block mb-1 fs-5 opacity-50"></i>
                            No matching surgeries found.
                        </div>`;
                    calendarSearchDropdown.classList.remove('d-none');
                } else {
                    let html = '';
                    matches.slice(0, 8).forEach(item => {
                        const props = item.extendedProps || {};
                        const status = props.status || '';
                        let statusBg = 'bg-primary-subtle text-primary border-primary-subtle';
                        if (status === 'Completed') statusBg = 'bg-success-subtle text-success border-success-subtle';
                        else if (status === 'In Progress') statusBg = 'bg-warning-subtle text-warning border-warning-subtle';
                        else if (status === 'Postponed') statusBg = 'bg-secondary-subtle text-secondary border-secondary-subtle';

                        html += `
                            <div class="search-result-item calendar-dropdown-item d-flex align-items-center justify-content-between cursor-pointer" data-id="${item.id}" style="padding: .45rem .85rem; border-bottom: 1px solid var(--line); transition: background .12s;">
                                <div class="text-start">
                                    <div class="item-title fw-semibold" style="font-size: .83rem; color: var(--text);">${localEscapeHtml(props.procedure)}</div>
                                    <div class="item-subtitle text-muted" style="font-size: .72rem;">Patient: ${localEscapeHtml(props.patient_name)} &bull; ${localEscapeHtml(props.request_no)}</div>
                                    <div class="item-subtitle text-muted" style="font-size: .7rem; opacity: 0.8;">Date: ${localEscapeHtml(props.scheduled_at)} &bull; Room: ${localEscapeHtml(props.or)}</div>
                                </div>
                                <span class="badge ${statusBg} border px-1.5 py-0.5" style="font-size: 0.62rem;">${status}</span>
                            </div>
                        `;
                    });
                    calendarSearchDropdown.innerHTML = html;
                    calendarSearchDropdown.classList.remove('d-none');

                    // Bind item click selectors to focus event
                    calendarSearchDropdown.querySelectorAll('.calendar-dropdown-item').forEach(el => {
                        el.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            hideCalendarDropdown();
                            const schedId = this.getAttribute('data-id');
                            const fcEvent = calendar.getEventById(schedId);
                            if (fcEvent) {
                                calendar.gotoDate(fcEvent.start);
                                showScheduleModal(fcEvent);
                            }
                        });
                    });
                }
            }
        }, 250);
    });

    filterRoom?.addEventListener('change', function() {
        checkActiveFilters();
        calendar.refetchEvents();
    });

    filterStatus?.addEventListener('change', function() {
        checkActiveFilters();
        calendar.refetchEvents();
    });



    // Mini Calendar Navigation
    document.getElementById('miniPrevMonth')?.addEventListener('click', () => {
        miniCurrentDate.setMonth(miniCurrentDate.getMonth() - 1);
        renderMiniCalendar(miniCurrentDate);
    });

    document.getElementById('miniNextMonth')?.addEventListener('click', () => {
        miniCurrentDate.setMonth(miniCurrentDate.getMonth() + 1);
        renderMiniCalendar(miniCurrentDate);
    });

    // Render Initial Mini Calendar
    renderMiniCalendar(miniCurrentDate);
});

// Render Apple Mini Calendar Sidebar Widget
function renderMiniCalendar(date) {
    const gridEl = document.getElementById('miniCalendarGrid');
    const titleEl = document.getElementById('miniMonthYearText');
    if (!gridEl || !titleEl) return;

    const year = date.getFullYear();
    const month = date.getMonth();

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    titleEl.textContent = `${monthNames[month]} ${year}`;

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    const today = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

    // Collect dates that have surgery events
    const eventDates = new Set();
    allSurgeryEvents.forEach(e => {
        const p = e.extendedProps || {};
        if (p.scheduled_date) eventDates.add(p.scheduled_date);
    });

    gridEl.innerHTML = '';

    // Prev Month Days
    for (let i = firstDay - 1; i >= 0; i--) {
        const dayNum = daysInPrevMonth - i;
        const cell = document.createElement('div');
        cell.className = 'mini-day-cell other-month';
        cell.textContent = dayNum;
        gridEl.appendChild(cell);
    }

    // Current Month Days
    for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('div');
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        
        let classes = 'mini-day-cell';
        if (dateStr === todayStr) classes += ' is-today';

        cell.className = classes;
        cell.textContent = day;

        if (eventDates.has(dateStr)) {
            const dot = document.createElement('div');
            dot.className = 'event-dot';
            cell.appendChild(dot);
        }

        cell.addEventListener('click', function() {
            if (globalCalendarInstance) {
                globalCalendarInstance.gotoDate(dateStr);
                if (window.innerWidth < 768) {
                    globalCalendarInstance.changeView('timeGridDay');
                }
            }
        });

        gridEl.appendChild(cell);
    }

    // Next Month Days padding
    const totalCells = firstDay + daysInMonth;
    const remainingCells = (7 - (totalCells % 7)) % 7;
    for (let i = 1; i <= remainingCells; i++) {
        const cell = document.createElement('div');
        cell.className = 'mini-day-cell other-month';
        cell.textContent = i;
        gridEl.appendChild(cell);
    }
}

// Show Surgery Event Details Modal
function showScheduleModal(eventObj) {
    const p = eventObj.extendedProps || {};
    
    document.getElementById('modalProcedure').textContent = p.procedure || eventObj.title;
    document.getElementById('modalPatient').textContent = p.patient_name ? `${p.patient_name} (${p.patient_no || 'N/A'})` : 'N/A';
    document.getElementById('modalOR').textContent = p.or || 'Unassigned';
    document.getElementById('modalScheduleTime').textContent = p.scheduled_at || 'N/A';
    document.getElementById('modalDuration').textContent = p.duration || 60;
    document.getElementById('modalSurgeon').textContent = p.surgeon || 'N/A';
    document.getElementById('modalRequestedBy').textContent = p.request_by || 'N/A';
    
    const statusEl = document.getElementById('modalStatus');
    statusEl.textContent = p.status || 'Scheduled';
    statusEl.className = 'badge';
    
    if (p.status === 'Completed') {
        statusEl.classList.add('bg-success');
    } else if (p.status === 'Scheduled') {
        statusEl.classList.add('bg-primary');
    } else if (p.status === 'In Progress') {
        statusEl.classList.add('bg-warning', 'text-dark');
    } else if (p.status === 'Postponed') {
        statusEl.classList.add('bg-secondary');
    } else {
        statusEl.classList.add('bg-danger');
    }

    document.getElementById('modalViewScheduleLink').href = `/surgery/schedules/${eventObj.id}`;
    
    const modalEl = document.getElementById('eventDetailsModal');
    const detailsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    detailsModal.show();
}

document.addEventListener('click', function(e) {
    const item = e.target.closest('.agenda-item-card');
    if (item) {
        const schedId = item.getAttribute('data-schedule-id');
        if (schedId) openAgendaDetails(schedId);
    }
});

function openAgendaDetails(scheduleId) {
    const foundEvent = allSurgeryEvents.find(e => String(e.id) === String(scheduleId));
    if (foundEvent) {
        showScheduleModal(foundEvent);
    } else {
        window.location.href = `/surgery/schedules/${scheduleId}`;
    }
}
</script>
@endpush
