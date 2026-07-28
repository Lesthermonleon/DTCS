@extends('layouts.app')
@section('title', 'Surgery Calendar')
@section('page-title', 'Surgery Calendar')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-week me-2"></i>Operating Room Schedule</span>
        @if(auth()->user()->hasAnyRole(['admin','or-coordinator']))
            <a href="{{ route('surgery.schedules.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle me-1"></i>Schedule Surgery</a>
        @endif
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: '{{ route("surgery.calendar.events") }}',
        eventClick: function(info) {
            const p = info.event.extendedProps;
            alert(`${info.event.title}\nOR: ${p.or}\nStatus: ${p.status}`);
        },
        height: 'auto',
        nowIndicator: true,
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
    });
    calendar.render();
});
</script>
@endpush
