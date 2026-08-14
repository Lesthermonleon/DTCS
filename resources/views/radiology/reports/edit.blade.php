@extends('layouts.app')
@section('title', 'Edit Diagnostic Report')
@section('page-title', 'Edit Diagnostic Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('radiology.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item"><a href="{{ route('radiology.reports.show', $radiologyReport) }}">Report Detail</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('radiology.reports.update', $radiologyReport) }}">
            @csrf
            @method('PUT')
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-pencil me-2"></i>Modify Diagnostic Report findings</div>
                <div class="card-body">
                    @if($radiologyReport->radiologyRequest)
                        <div class="mb-3 p-3 bg-light rounded text-secondary small">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Request No:</strong> {{ $radiologyReport->radiologyRequest->request_no }}<br>
                                    <strong>Patient:</strong> {{ $radiologyReport->radiologyRequest->patient->last_name }}, {{ $radiologyReport->radiologyRequest->patient->first_name }} ({{ $radiologyReport->radiologyRequest->patient->patient_no }})
                                </div>
                                <div class="col-sm-6">
                                    <strong>Modality:</strong> {{ $radiologyReport->radiologyRequest->modality }}<br>
                                    <strong>Region/Body Part:</strong> {{ $radiologyReport->radiologyRequest->body_part }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Findings (Observations) <span class="text-danger">*</span></label>
                        <textarea name="findings" class="form-control @error('findings') is-invalid @enderror" rows="6" required>{{ old('findings', $radiologyReport->findings) }}</textarea>
                        @error('findings')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Impression (Diagnosis / Summary) <span class="text-danger">*</span></label>
                        <textarea name="impression" class="form-control @error('impression') is-invalid @enderror" rows="3" required>{{ old('impression', $radiologyReport->impression) }}</textarea>
                        @error('impression')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recommendations (Advice / Suggestion)</label>
                        <textarea name="recommendations" class="form-control @error('recommendations') is-invalid @enderror" rows="3">{{ old('recommendations', $radiologyReport->recommendations) }}</textarea>
                        @error('recommendations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Changes</button>
                <a href="{{ route('radiology.reports.show', $radiologyReport) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
