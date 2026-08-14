@extends('layouts.app')
@section('title', 'Surgery Schedule Details')
@section('page-title', 'Surgery Schedule Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('surgery.dashboard') }}">Surgery (SORS)</a></li>
    <li class="breadcrumb-item"><a href="{{ route('surgery.schedules.index') }}">Schedules</a></li>
    <li class="breadcrumb-item active">Schedule Details</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Main Column: Procedure & Patient Information -->
    <div class="col-lg-8">
        <!-- Procedure Summary Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 0.75rem; background: var(--card);">
            <div class="card-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center" style="background: rgba(0,0,0,0.015);">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="mb-0 fw-bold text-primary" style="font-family: var(--font-display); font-size: 1.1rem;">
                        {{ $surgerySchedule->surgeryRequest->procedure_name ?? 'Procedure Details' }}
                    </h5>
                    <span class="badge bg-{{ $surgerySchedule->status === 'Completed' ? 'success' : ($surgerySchedule->status === 'Scheduled' ? 'primary' : ($surgerySchedule->status === 'In Progress' ? 'warning text-dark' : 'secondary')) }}">
                        {{ $surgerySchedule->status }}
                    </span>
                </div>
                <div class="text-muted small">
                    Req #: 
                    @if($surgerySchedule->surgery_request_id)
                        <a href="{{ route('surgery.requests.show', $surgerySchedule->surgery_request_id) }}" class="fw-semibold">{{ $surgerySchedule->surgeryRequest->request_no ?? 'N/A' }}</a>
                    @else
                        <span class="fw-semibold text-muted">N/A</span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Diagnosis / Indication</div>
                        <div class="fw-semibold" style="color: var(--text);">{{ $surgerySchedule->surgeryRequest?->diagnosis ?? 'Not specified' }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Urgency</div>
                        <span class="badge bg-{{ ($surgerySchedule->surgeryRequest?->urgency === 'Emergency') ? 'danger' : (($surgerySchedule->surgeryRequest?->urgency === 'Urgent') ? 'warning text-dark' : 'secondary') }}">
                            {{ $surgerySchedule->surgeryRequest?->urgency ?? 'Elective' }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Anesthesia</div>
                        <div class="fw-semibold" style="color: var(--text);">{{ $surgerySchedule->surgeryRequest?->anesthesia_type ?? 'Standard' }}</div>
                    </div>
                </div>

                @if($surgerySchedule->notes)
                    <div class="border-top pt-3 mt-2">
                        <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Pre-operative Notes</div>
                        <div class="p-3 rounded" style="background: rgba(0,0,0,0.02); font-size: 0.88rem; color: var(--text);">
                            {{ $surgerySchedule->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Patient Profile Card -->
        <div class="card shadow-sm border-0" style="border-radius: 0.75rem; background: var(--card);">
            <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between" style="background: rgba(0,0,0,0.015);">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2" style="font-family: var(--font-display);">
                    <i class="bi bi-person text-primary"></i>Patient Profile
                </h6>
                @if($surgerySchedule->surgeryRequest && $surgerySchedule->surgeryRequest->patient)
                    <a href="{{ route('patients.show', $surgerySchedule->surgeryRequest->patient_id) }}" class="btn btn-xs btn-outline-primary" style="font-size: 0.78rem;">
                        View Patient Chart
                    </a>
                @endif
            </div>
            <div class="card-body p-4">
                @if($surgerySchedule->surgeryRequest && $surgerySchedule->surgeryRequest->patient)
                    @php $p = $surgerySchedule->surgeryRequest->patient; @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Full Name</div>
                            <div class="fw-bold fs-6">{{ $p->last_name }}, {{ $p->first_name }} {{ $p->middle_name }}</div>
                            <div class="text-muted small">ID: {{ $p->patient_no }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Gender / Type</div>
                            <div class="fw-semibold">{{ $p->gender }} ({{ ucfirst($p->patient_type ?? 'Outpatient') }})</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Blood Type</div>
                            <span class="badge bg-danger">{{ $p->blood_type ?? 'N/A' }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-muted">No patient record linked.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Side Column: Operating Room & Team Information -->
    <div class="col-lg-4">
        <!-- Timing & OR Room Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 0.75rem; background: var(--card);">
            <div class="card-header border-bottom py-3 px-4" style="background: rgba(0,0,0,0.015);">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2" style="font-family: var(--font-display);">
                    <i class="bi bi-clock-history text-primary"></i>Schedule & OR Facility
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Operating Room</div>
                    <div class="fw-bold fs-6 text-success d-flex align-items-center gap-1.5">
                        <i class="bi bi-door-open"></i>
                        {{ $surgerySchedule->operatingRoom->name ?? 'Unassigned' }}
                    </div>
                    <div class="text-muted small">{{ $surgerySchedule->operatingRoom->location ?? '' }}</div>
                </div>

                <div class="mb-3 border-top pt-3">
                    <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Date & Time</div>
                    <div class="fw-semibold text-primary" style="font-size: 0.95rem;">
                        {{ $surgerySchedule->scheduled_at ? $surgerySchedule->scheduled_at->format('F d, Y') : 'Date N/A' }}
                    </div>
                    <div class="fw-bold" style="font-size: 1.1rem; color: var(--text);">
                        {{ $surgerySchedule->scheduled_at ? $surgerySchedule->scheduled_at->format('h:i A') : 'Time N/A' }}
                    </div>
                </div>

                <div class="mb-3 border-top pt-3">
                    <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Duration</div>
                    <div class="fw-semibold" style="color: var(--text);">{{ $surgerySchedule->duration_minutes ?? 60 }} minutes</div>
                    <div class="text-muted small">Est. Finish: {{ $surgerySchedule->scheduled_at ? $surgerySchedule->scheduled_at->copy()->addMinutes($surgerySchedule->duration_minutes ?? 60)->format('h:i A') : 'N/A' }}</div>
                </div>

                <div class="border-top pt-3">
                    <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Scheduled By</div>
                    <div class="fw-semibold" style="color: var(--text);">{{ $surgerySchedule->scheduledBy->name ?? 'System' }}</div>
                </div>
            </div>
        </div>

        <!-- Surgical Team Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 0.75rem; background: var(--card);">
            <div class="card-header border-bottom py-3 px-4" style="background: rgba(0,0,0,0.015);">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2" style="font-family: var(--font-display);">
                    <i class="bi bi-people text-primary"></i>Surgical Team
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Team Name</div>
                    <div class="fw-bold" style="color: var(--text);">{{ $surgerySchedule->surgicalTeam->name ?? 'Unassigned' }}</div>
                </div>

                <div class="border-top pt-3">
                    <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">Lead Surgeon</div>
                    <div class="fw-bold text-primary">{{ $surgerySchedule->surgicalTeam->surgeon->name ?? 'Surgeon N/A' }}</div>
                    <div class="text-muted small">{{ $surgerySchedule->surgicalTeam->surgeon->department ?? 'Surgery' }}</div>
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="card shadow-sm border-0" style="border-radius: 0.75rem; background: var(--card);">
            <div class="card-body p-4 d-flex flex-column gap-2">
                @if(auth()->user()->hasAnyRole(['admin','doctor','or-coordinator']))
                    @if($surgerySchedule->status === 'Scheduled')
                        <form action="{{ route('surgery.schedules.start', ['surgerySchedule' => $surgerySchedule->id]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning w-100 fw-semibold d-flex align-items-center justify-content-center gap-2" style="border-radius: 0.5rem;">
                                <i class="bi bi-play-circle"></i> Start Procedure (In Progress)
                            </button>
                        </form>
                    @endif

                    @if($surgerySchedule->status !== 'Completed')
                        <form action="{{ route('surgery.schedules.complete', ['surgerySchedule' => $surgerySchedule->id]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100 fw-semibold d-flex align-items-center justify-content-center gap-2" style="border-radius: 0.5rem;">
                                <i class="bi bi-check-circle"></i> Mark Surgery Completed
                            </button>
                        </form>

                        <a href="{{ route('surgery.schedules.edit', ['surgerySchedule' => $surgerySchedule->id]) }}" class="btn btn-outline-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2" style="border-radius: 0.5rem;">
                            <i class="bi bi-pencil"></i> Edit Schedule
                        </a>
                    @endif
                @endif

                <a href="{{ route('surgery.schedules.index') }}" class="btn btn-outline-secondary w-100" style="border-radius: 0.5rem;">
                    Back to Schedules
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
