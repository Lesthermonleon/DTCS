@extends('layouts.app')
@section('title', 'Schedule Surgery')
@section('page-title', 'Schedule Surgery')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('surgery.dashboard') }}">Surgery (SORS)</a></li>
    <li class="breadcrumb-item"><a href="{{ route('surgery.schedules.index') }}">Schedules</a></li>
    <li class="breadcrumb-item active">Schedule Surgery</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card shadow-sm border-0" style="border-radius: 0.75rem; background: var(--card);">
            <div class="card-header border-bottom py-3 px-4" style="background: rgba(0,0,0,0.015);">
                <h5 class="mb-0 fw-bold d-flex align-items-center gap-2" style="font-family: var(--font-display); font-size: 1rem; color: var(--text);">
                    <i class="bi bi-calendar-plus text-primary"></i>Schedule Operating Room Procedure
                </h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('surgery.schedules.store') }}" method="POST">
                    @csrf

                    <!-- 1. Surgery Request Selection -->
                    <div class="mb-4">
                        <label for="surgery_request_id" class="form-label fw-semibold">Surgery Request <span class="text-danger">*</span></label>
                        <select name="surgery_request_id" id="surgery_request_id" class="form-select @error('surgery_request_id') is-invalid @enderror" required>
                            <option value="">-- Select Pending Surgery Request --</option>
                            @foreach($pendingRequests as $req)
                                <option value="{{ $req->id }}" {{ (old('surgery_request_id', $selectedRequestId ?? '') == $req->id) ? 'selected' : '' }}>
                                    [{{ $req->request_no }}] {{ $req->procedure_name }} — Patient: {{ $req->patient->last_name ?? '' }}, {{ $req->patient->first_name ?? '' }} (Urgency: {{ $req->urgency }})
                                </option>
                            @endforeach
                        </select>
                        @error('surgery_request_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select the approved doctor surgery request to schedule in an Operating Room.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- 2. Operating Room -->
                        <div class="col-md-6">
                            <label for="operating_room_id" class="form-label fw-semibold">Operating Room (OR) <span class="text-danger">*</span></label>
                            <select name="operating_room_id" id="operating_room_id" class="form-select @error('operating_room_id') is-invalid @enderror" required>
                                <option value="">-- Select Operating Room --</option>
                                @foreach($operatingRooms as $room)
                                    <option value="{{ $room->id }}" {{ old('operating_room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} ({{ $room->location ?? 'General' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('operating_room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 3. Surgical Team -->
                        <div class="col-md-6">
                            <label for="surgical_team_id" class="form-label fw-semibold">Assigned Surgical Team <span class="text-danger">*</span></label>
                            <select name="surgical_team_id" id="surgical_team_id" class="form-select @error('surgical_team_id') is-invalid @enderror" required>
                                <option value="">-- Select Surgical Team --</option>
                                @foreach($surgicalTeams as $team)
                                    <option value="{{ $team->id }}" {{ old('surgical_team_id') == $team->id ? 'selected' : '' }}>
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
                        <!-- 4. Scheduled Date & Time -->
                        <div class="col-md-6">
                            <label for="scheduled_at" class="form-label fw-semibold">Scheduled Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at', now()->addDay()->format('Y-m-d\TH:00')) }}" required>
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- 5. Duration (Minutes) -->
                        <div class="col-md-6">
                            <label for="duration_minutes" class="form-label fw-semibold">Estimated Duration (Minutes) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', 60) }}" min="15" max="600" step="15" required>
                            @error('duration_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- 6. Special Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Pre-operative Notes / Instructions</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Add equipment requirements, special patient prep, or anesthesia notes…">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('surgery.schedules.index') }}" class="btn btn-outline-secondary px-4" style="border-radius: 0.5rem;">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold d-flex align-items-center gap-1.5" style="border-radius: 0.5rem;">
                            <i class="bi bi-calendar-check"></i> Confirm & Schedule Surgery
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
