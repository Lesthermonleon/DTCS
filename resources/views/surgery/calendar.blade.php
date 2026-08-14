@extends('layouts.app')
@section('title', 'Surgery Calendar')
@section('page-title', 'Surgery Calendar')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('surgery.dashboard') }}">Surgery (SORS)</a></li>
    <li class="breadcrumb-item active">Calendar</li>
@endsection

@section('content')
<!-- iOS Style Calendar Container -->
<div class="card shadow-sm border-0 ios-calendar-card" style="border-radius: 1rem; overflow: hidden; background: var(--card);">
    <!-- Card Header with iOS Segmented View Controls -->
    <div class="card-header border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: rgba(0,0,0,0.015);">
        <div class="d-flex align-items-center gap-2">
            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2" style="font-family: var(--font-display); font-size: 1.1rem; color: var(--text);">
                <i class="bi bi-calendar3 text-primary" style="font-size: 1.2rem;"></i>Operating Room Surgery Calendar
            </h5>
            <span id="calendarCurrentYearBadge" class="badge bg-primary-subtle text-primary fw-bold rounded-pill px-2.5 py-1" style="font-size: 0.78rem;">
                {{ date('Y') }}
            </span>
        </div>

        <!-- iOS Segmented Control & Action Buttons -->
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="ios-segmented-control d-inline-flex p-1 rounded-3" style="background: rgba(0, 0, 0, 0.05); border: 1px solid var(--border-color, #e2e8f0);">
                <button type="button" class="btn btn-xs ios-seg-btn active" data-view="multiMonthYear" id="btnViewYear">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i>Year
                </button>
                <button type="button" class="btn btn-xs ios-seg-btn" data-view="dayGridMonth" id="btnViewMonth">
                    <i class="bi bi-calendar-month me-1"></i>Month
                </button>
                <button type="button" class="btn btn-xs ios-seg-btn" data-view="timeGridWeek" id="btnViewWeek">
                    <i class="bi bi-calendar-week me-1"></i>Week
                </button>
                <button type="button" class="btn btn-xs ios-seg-btn" data-view="listMonth" id="btnViewAgenda">
                    <i class="bi bi-list-ul me-1"></i>Agenda
                </button>
            </div>

            @if(auth()->user()->hasAnyRole(['admin','or-coordinator']))
                <a href="{{ route('surgery.schedules.create') }}" class="btn btn-sm btn-primary px-3 fw-semibold d-flex align-items-center gap-1.5" style="border-radius: 0.5rem; font-size: 0.8rem;">
                    <i class="bi bi-plus-circle"></i> Schedule Surgery
                </a>
            @endif
        </div>
    </div>

    <!-- Calendar Viewport with iOS smooth transition wrapper -->
    <div class="card-body p-4">
        <div class="ios-calendar-hint mb-3 d-flex align-items-center justify-content-between p-2.5 px-3 rounded-3" style="background: rgba(20, 199, 154, 0.06); border: 1px solid rgba(20, 199, 154, 0.15);">
            <div class="d-flex align-items-center gap-2 text-muted small" style="font-size: 0.82rem;">
                <i class="bi bi-info-circle-fill text-primary" style="font-size: 0.95rem;"></i>
                <span><strong>iOS Quick Navigation:</strong> Click any month or date in <strong>Year View</strong> to expand into full month grid. Click <strong>Year</strong> to shrink back.</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill bg-primary" style="font-size: 0.7rem;">Scheduled</span>
                <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.7rem;">In Progress</span>
                <span class="badge rounded-pill bg-success" style="font-size: 0.7rem;">Completed</span>
            </div>
        </div>

        <div id="calendarWrapper" class="ios-calendar-wrapper">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: 1px solid var(--border-color, #e2e8f0); border-radius: 0.85rem; overflow: hidden; background: var(--card); color: var(--text); box-shadow: 0 12px 36px rgba(0,0,0,0.18);">
            <div class="modal-header border-bottom d-flex align-items-center justify-content-between py-3 px-4" style="background: rgba(0,0,0,0.015)">
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
                        <a id="modalViewScheduleLink" href="#" class="btn btn-xs btn-outline-primary px-3 py-1.5 fw-semibold d-flex align-items-center gap-1" style="border-radius: 0.375rem; font-size: 0.78rem;">
                            <i class="bi bi-eye"></i> View Full Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* iOS Segmented Control styling */
    .ios-segmented-control .ios-seg-btn {
        border: none !important;
        background: transparent !important;
        color: var(--text-soft) !important;
        font-family: var(--font-body), sans-serif;
        font-weight: 600 !important;
        font-size: 0.78rem !important;
        padding: 0.35rem 0.85rem !important;
        border-radius: 0.35rem !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .ios-segmented-control .ios-seg-btn.active {
        background: var(--paper) !important;
        color: var(--signal-dark, #0284c7) !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08) !important;
        font-weight: 700 !important;
    }

    html[data-theme="dark"] .ios-segmented-control {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: #1e3630 !important;
    }

    html[data-theme="dark"] .ios-segmented-control .ios-seg-btn.active {
        background: #162a24 !important;
        color: #14c79a !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
    }

    /* Smooth Zoom / Shrink Transition for Calendar Viewport */
    .ios-calendar-wrapper {
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease;
        transform-origin: center center;
    }

    .ios-calendar-wrapper.animating {
        opacity: 0.3;
        transform: scale(0.97);
    }

    /* FullCalendar styling */
    #calendar {
        font-family: var(--font-body), sans-serif;
        --fc-border-color: #e2e8f0;
        --fc-today-bg-color: rgba(20, 199, 154, 0.05);
        --fc-list-event-hover-bg-color: rgba(20, 199, 154, 0.08);
    }

    html[data-theme="dark"] #calendar {
        --fc-border-color: #1e3630;
        --fc-today-bg-color: rgba(20, 199, 154, 0.06);
        --fc-list-event-hover-bg-color: rgba(20, 199, 154, 0.1);
        --close-filter: invert(1) grayscale(1) brightness(2);
    }

    html[data-theme="dark"] .fc-theme-standard td,
    html[data-theme="dark"] .fc-theme-standard th,
    html[data-theme="dark"] .fc-theme-standard .fc-scrollgrid {
        border-color: #1e3630 !important;
    }

    /* Year View 12-Month Grid styling */
    .fc-multimonth {
        border: none !important;
        gap: 1.25rem !important;
    }

    .fc-multimonth-month {
        border: 1px solid var(--border-color, #e2e8f0) !important;
        border-radius: 0.75rem !important;
        padding: 0.75rem !important;
        background: var(--paper, #ffffff);
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .fc-multimonth-month:hover {
        border-color: var(--signal, #14c79a) !important;
        box-shadow: 0 4px 14px rgba(20, 199, 154, 0.12);
        transform: translateY(-2px);
    }

    html[data-theme="dark"] .fc-multimonth-month {
        background: #101e1a !important;
        border-color: #1e3630 !important;
    }

    .fc-multimonth-title {
        font-family: var(--font-display), sans-serif;
        font-weight: 700 !important;
        font-size: 1rem !important;
        color: var(--signal-dark, #0284c7) !important;
        padding-bottom: 0.5rem !important;
    }

    /* FullCalendar header toolbar styling */
    .fc .fc-toolbar-title {
        font-family: var(--font-display), sans-serif;
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: var(--text);
    }

    .fc .fc-button-primary {
        background-color: var(--paper) !important;
        border: 1px solid var(--border-color, #e2e8f0) !important;
        color: var(--text-soft) !important;
        font-weight: 600 !important;
        border-radius: 0.375rem !important;
    }

    html[data-theme="dark"] .fc .fc-button-primary {
        background-color: #12221e !important;
        border-color: #1e3630 !important;
    }

    .fc .fc-button-primary:hover {
        background-color: rgba(20, 199, 154, 0.09) !important;
        border-color: var(--signal) !important;
        color: var(--signal-dark) !important;
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background-color: var(--signal) !important;
        border-color: var(--signal) !important;
        color: #0c1c18 !important;
    }

    .fc-event {
        cursor: pointer !important;
        border-radius: 0.35rem !important;
        border: none !important;
        margin: 1px 3px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        transition: transform 0.1s ease !important;
    }

    .fc-event:hover {
        transform: translateY(-0.5px);
        opacity: 0.95;
    }

    .fc-event-title {
        font-family: var(--font-body), sans-serif;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        padding: 3px 5px !important;
    }

    /* Ensure modal backdrop stays behind modal dialog */
    .modal-backdrop {
        z-index: 1050 !important;
    }
    #eventDetailsModal {
        z-index: 1060 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendarWrapper = document.getElementById('calendarWrapper');
    const segBtns = document.querySelectorAll('.ios-seg-btn');
    let currentView = 'multiMonthYear';

    // Helper for smooth iOS zoom/shrink transition
    function triggerTransition(callback) {
        calendarWrapper.classList.add('animating');
        setTimeout(() => {
            callback();
            setTimeout(() => {
                calendarWrapper.classList.remove('animating');
            }, 100);
        }, 150);
    }

    // Helper to update iOS segmented control active state
    function updateSegmentedControl(viewName) {
        currentView = viewName;
        segBtns.forEach(btn => {
            if (btn.getAttribute('data-view') === viewName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'multiMonthYear',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: '' // Handled by iOS segmented control header
        },
        navLinks: true, // Enables click on dates & week numbers
        multiMonthMaxColumns: 4, // 4 x 3 layout for 12 months on desktop
        events: '{{ route("surgery.calendar.events") }}',

        // iOS Interactive Zoom: Click any date in Year View to zoom into Month View
        dateClick: function(info) {
            if (currentView === 'multiMonthYear') {
                triggerTransition(() => {
                    calendar.changeView('dayGridMonth', info.dateStr);
                    updateSegmentedControl('dayGridMonth');
                });
            }
        },

        // iOS Interactive Zoom: Click any month header in Year View
        navLinkDayClick: function(date, jsEvent) {
            triggerTransition(() => {
                calendar.changeView('dayGridMonth', date);
                updateSegmentedControl('dayGridMonth');
            });
        },

        // Update segmented control state if user changes view internally
        datesSet: function(info) {
            const activeView = info.view.type;
            updateSegmentedControl(activeView);
        },

        // Event click details modal
        eventClick: function(info) {
            const p = info.event.extendedProps;
            
            document.getElementById('modalProcedure').textContent = p.procedure;
            document.getElementById('modalPatient').textContent = p.patient_name;
            document.getElementById('modalOR').textContent = p.or;
            document.getElementById('modalScheduleTime').textContent = p.scheduled_at;
            document.getElementById('modalDuration').textContent = p.duration;
            document.getElementById('modalSurgeon').textContent = p.surgeon;
            document.getElementById('modalRequestedBy').textContent = p.request_by;
            
            const statusEl = document.getElementById('modalStatus');
            statusEl.textContent = p.status;
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

            document.getElementById('modalViewScheduleLink').href = `/surgery/schedules/${info.event.id}`;
            
            const modalEl = document.getElementById('eventDetailsModal');
            const detailsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            detailsModal.show();
        },
        height: 'auto',
        nowIndicator: true,
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
    });

    calendar.render();

    // Ensure modal element is direct child of document.body to avoid parent container stacking context trapping
    const eventDetailsModalEl = document.getElementById('eventDetailsModal');
    if (eventDetailsModalEl) {
        if (eventDetailsModalEl.parentNode !== document.body) {
            document.body.appendChild(eventDetailsModalEl);
        }
        eventDetailsModalEl.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        });
    }

    // Bind iOS Segmented Control buttons
    segBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetView = this.getAttribute('data-view');
            if (targetView !== currentView) {
                triggerTransition(() => {
                    calendar.changeView(targetView);
                    updateSegmentedControl(targetView);
                });
            }
        });
    });
});
</script>
@endpush
