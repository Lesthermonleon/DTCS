@extends('layouts.app')
@section('title', 'New Diagnostic Report')
@section('page-title', 'New Diagnostic Report')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('radiology.reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('radiology.reports.store') }}">
            @csrf
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-file-earmark-medical me-2"></i>Create Diagnostic Report</div>
                <div class="card-body">
                    {{-- Selected Request (via query parameter or select dropdown) --}}
                    @if(request('radiology_request_id'))
                        @php
                            $req = \App\Models\RadiologyRequest::with(['patient', 'images.uploadedBy'])->find(request('radiology_request_id'));
                        @endphp
                        @if($req)
                            <div class="mb-3 p-3 bg-light rounded border text-secondary small">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <strong>Request No:</strong> <span class="fw-bold text-primary">{{ $req->request_no }}</span><br>
                                        <strong>Patient:</strong> {{ $req->patient->last_name }}, {{ $req->patient->first_name }} ({{ $req->patient->patient_no }})
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Modality:</strong> {{ $req->modality }}<br>
                                        <strong>Region/Body Part:</strong> {{ $req->body_part }}
                                    </div>
                                </div>
                                @if($req->images->isNotEmpty())
                                    <div class="border-top pt-2 mt-2">
                                        <div class="fw-semibold text-dark mb-1"><i class="bi bi-images me-1 text-primary"></i>Uploaded Study Scan Files ({{ $req->images->count() }}):</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($req->images as $img)
                                                @php
                                                    $isImage = in_array(strtolower($img->file_type), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                    $viewUrl = route('radiology.images.view', $img);
                                                @endphp
                                                <a href="{{ $viewUrl }}" target="_blank" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-{{ $isImage ? 'image' : 'file-earmark-pdf' }}"></i>
                                                    <span>{{ $img->file_name }}</span>
                                                    <span class="badge bg-secondary ms-1">{{ strtoupper($img->file_type) }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <input type="hidden" name="radiology_request_id" value="{{ $req->id }}">
                            </div>
                        @else
                            <div class="alert alert-danger">Specified radiology request not found.</div>
                        @endif
                    @else
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Radiology Request <span class="text-danger">*</span></label>
                            <select name="radiology_request_id" class="form-select @error('radiology_request_id') is-invalid @enderror" required>
                                <option value="">— Select Pending Uploaded Imaging Scan —</option>
                                @foreach($pendingRequests as $pr)
                                    <option value="{{ $pr->id }}" {{ old('radiology_request_id') == $pr->id ? 'selected' : '' }}>
                                        {{ $pr->request_no }} — {{ $pr->patient->last_name }}, {{ $pr->patient->first_name }} ({{ $pr->modality }}: {{ $pr->body_part }})
                                    </option>
                                @endforeach
                            </select>
                            @error('radiology_request_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Findings (Observations) <span class="text-danger">*</span></label>
                        <textarea name="findings" class="form-control @error('findings') is-invalid @enderror" rows="6" placeholder="Describe the structural findings visible on the scan regions..." required>{{ old('findings') }}</textarea>
                        @error('findings')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Impression (Diagnosis / Summary) <span class="text-danger">*</span></label>
                        <textarea name="impression" class="form-control @error('impression') is-invalid @enderror" rows="3" placeholder="Provide the clinical impression or tentative diagnostic conclusion..." required>{{ old('impression') }}</textarea>
                        @error('impression')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recommendations (Advice / Suggestion)</label>
                        <textarea name="recommendations" class="form-control @error('recommendations') is-invalid @enderror" rows="3" placeholder="Suggest follow-ups, additional imaging projections, or notes...">{{ old('recommendations') }}</textarea>
                        @error('recommendations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-file-earmark-check me-1"></i>Save as Draft Report</button>
                <a href="{{ route('radiology.reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
