@extends('layouts.app')
@section('title', 'New Radiology Request')
@section('page-title', 'New Radiology Request')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('radiology.requests.index') }}">Requests</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('radiology.requests.store') }}">
            @csrf
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-person me-2"></i>Imaging Request Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                            <option value="">— Select Patient —</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->last_name }}, {{ $p->first_name }} ({{ $p->patient_no }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Modality / Procedure <span class="text-danger">*</span></label>
                            <select name="modality" class="form-select @error('modality') is-invalid @enderror" required>
                                <option value="">— Select Modality —</option>
                                @foreach($modalities as $m)
                                    <option value="{{ $m }}" {{ old('modality') === $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                            @error('modality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Body Part / Region <span class="text-danger">*</span></label>
                            <input type="text" name="body_part" class="form-control @error('body_part') is-invalid @enderror" value="{{ old('body_part') }}" placeholder="e.g. Chest, Left Knee, Brain" required>
                            @error('body_part')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="Routine" {{ old('priority') === 'Routine' ? 'selected' : '' }}>Routine</option>
                                <option value="Urgent" {{ old('priority') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="STAT" {{ old('priority') === 'STAT' ? 'selected' : '' }}>STAT (Emergency)</option>
                            </select>
                            @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Clinical Information / History</label>
                        <textarea name="clinical_information" class="form-control @error('clinical_information') is-invalid @enderror" rows="4" placeholder="Describe symptoms, tentative diagnosis, reason for study...">{{ old('clinical_information') }}</textarea>
                        @error('clinical_information')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Submit Request</button>
                <a href="{{ route('radiology.requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
