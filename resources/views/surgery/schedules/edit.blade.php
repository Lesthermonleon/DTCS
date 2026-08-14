@extends('layouts.app')
@section('title', 'Edit Surgery Schedule')
@section('page-title', 'Edit Surgery Schedule')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('surgery.dashboard') }}">Surgery (SORS)</a></li>
    <li class="breadcrumb-item"><a href="{{ route('surgery.schedules.index') }}">Schedules</a></li>
    <li class="breadcrumb-item active">Edit Schedule</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card shadow-sm border-0" style="border-radius: 0.75rem; background: var(--card);">
            <div class="card-header border-bottom py-3 px-4" style="background: rgba(0,0,0,0.015);">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2" style="font-family: var(--font-display); font-size: 1rem; color: var(--text);">
                    <i class="bi bi-pencil-square text-primary"></i>Edit Surgery Schedule #{{ $surgerySchedule->id }}
                </h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('surgery.schedules.update', $surgerySchedule) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Procedure Info (Read-only reference) -->
                    <div class="alert alert-light border mb-4 p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Procedure Request</div>
                            <div class="fw-bold text-primary">{{ $surgerySchedule->surgeryRequest->procedure_name ?? 'N/A' }}</div>
                            <div class="small">Patient: {{ $surgerySchedule->surgeryRequest->patient->last_name ?? '' }}, {{ $surgerySchedule->surgeryRequest->patient->first_name ?? '' }}</div>
                        </div>
                        <input type="hidden" name="surgery_request_id" value="{{ $surgerySchedule->surgery_request_id }}">
                        <span class="badge bg-secondary">Req #: {{ $surgerySchedule->surgeryRequest->request_no ?? 'N/A' }}</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Operating Room -->
                        <div class="col-md-6">
                            <label for="operating_room_id" class="form-label fw-semibold">Operating Room (OR) <span class="text-danger">*</span></label>
                            <select name="operating_room_id" id="operating_room_id" class="form-select @error('operating_room_id') is-invalid @enderror" required>
                                @foreach($operatingRooms as $room)
                                    <option value="{{ $room->id }}" {{ old('operating_room_id', $surgerySchedule->operating_room_id) == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} ({{ $room->location ?? 'General' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('operating_room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Surgical Team -->
                        <div class="col-md-6">
                            <label for="surgical_team_id" class="form-label fw-semibold">Assigned Surgical Team <span class="text-danger">*</span></label>
                            <select name="surgical_team_id" id="surgical_team_id" class="form-select @error('surgical_team_id') is-invalid @enderror" required>
                                @foreach($surgicalTeams as $team)
                                    <option value="{{ $team->id }}" {{ old('surgical_team_id', $surgerySchedule->surgical_team_id) == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }} (Lead Surgeon: {{ $team->surgeon->name ?? 'Unassigned' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('surgical_team_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Scheduled Date & Time -->
                        <div class="col-md-6">
                            <label for="scheduled_at" class="form-label fw-semibold">Scheduled Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at', $surgerySchedule->scheduled_at->format('Y-m-d\TH:i')) }}" required>
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration (Minutes) -->
                        <div class="col-md-6">
                            <label for="duration_minutes" class="form-label fw-semibold">Estimated Duration (Minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', $surgerySchedule->duration_minutes) }}" min="15" max="600" step="15" required>
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Special Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Pre-operative Notes / Instructions</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $surgerySchedule->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('surgery.schedules.show', $surgerySchedule) }}" class="btn btn-outline-secondary px-4" style="border-radius: 0.5rem;">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold d-flex align-items-center gap-1.5" style="border-radius: 0.5rem;">
                            <i class="bi bi-save"></i> Save Schedule Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
