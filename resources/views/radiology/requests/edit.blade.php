@extends('layouts.app')
@section('title', 'Edit Radiology Request')
@section('page-title', 'Edit Radiology Request')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('radiology.requests.index') }}">Requests</a></li>
    <li class="breadcrumb-item"><a href="{{ route('radiology.requests.show', $radiologyRequest) }}">{{ $radiologyRequest->request_no }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('radiology.requests.update', $radiologyRequest) }}">
            @csrf
            @method('PUT')
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-pencil me-2"></i>Modify Request #{{ $radiologyRequest->request_no }}</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Patient</label>
                        <input type="text" class="form-control text-muted" value="{{ $radiologyRequest->patient->last_name }}, {{ $radiologyRequest->patient->first_name }} ({{ $radiologyRequest->patient->patient_no }})" disabled>
                        <input type="hidden" name="patient_id" value="{{ $radiologyRequest->patient_id }}">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Modality / Procedure <span class="text-danger">*</span></label>
                            <select name="modality" class="form-select @error('modality') is-invalid @enderror" required>
                                @foreach($modalities as $m)
                                    <option value="{{ $m }}" {{ old('modality', $radiologyRequest->modality) === $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                            @error('modality')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Body Part / Region <span class="text-danger">*</span></label>
                            <input type="text" name="body_part" class="form-control @error('body_part') is-invalid @enderror" value="{{ old('body_part', $radiologyRequest->body_part) }}" required>
                            @error('body_part')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                <option value="Routine" {{ old('priority', $radiologyRequest->priority) === 'Routine' ? 'selected' : '' }}>Routine</option>
                                <option value="Urgent" {{ old('priority', $radiologyRequest->priority) === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                <option value="STAT" {{ old('priority', $radiologyRequest->priority) === 'STAT' ? 'selected' : '' }}>STAT (Emergency)</option>
                            </select>
                            @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Clinical Information / History</label>
                        <textarea name="clinical_information" class="form-control @error('clinical_information') is-invalid @enderror" rows="4">{{ old('clinical_information', $radiologyRequest->clinical_information) }}</textarea>
                        @error('clinical_information')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Changes</button>
                <a href="{{ route('radiology.requests.show', $radiologyRequest) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
