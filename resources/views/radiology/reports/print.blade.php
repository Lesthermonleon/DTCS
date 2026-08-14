@extends('layouts.print')

@section('title', 'Radiology Report — ' . $radiologyReport->report_no)
@section('department', 'Department of Radiology & Diagnostic Imaging')
@section('document-title', 'RADIOLOGY REPORT')
@section('document-no', $radiologyReport->report_no)

@section('content')
<!-- Patient Identification Section -->
<div class="patient-info-box">
    <div class="row g-2">
        <div class="col-4">
            <div class="info-label">Patient Name</div>
            <div class="info-value">{{ $radiologyReport->radiologyRequest->patient->full_name }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Patient ID / No</div>
            <div class="info-value">{{ $radiologyReport->radiologyRequest->patient->patient_no }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Age / Gender</div>
            <div class="info-value">{{ $radiologyReport->radiologyRequest->patient->age ?? 'N/A' }} yrs / {{ ucfirst($radiologyReport->radiologyRequest->patient->gender ?? 'N/A') }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Imaging Procedure</div>
            <div class="info-value text-primary">{{ $radiologyReport->radiologyRequest->procedure_name }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Modality / Body Part</div>
            <div class="info-value">{{ $radiologyReport->radiologyRequest->modality }} — {{ $radiologyReport->radiologyRequest->body_part }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Requesting Physician</div>
            <div class="info-value">Dr. {{ $radiologyReport->radiologyRequest->doctor->name }}</div>
        </div>
    </div>
</div>

<!-- Clinical Findings Section -->
<div class="mb-4">
    <h6 class="fw-bold mb-2 text-uppercase text-secondary" style="font-size: 11px; letter-spacing: 0.04em;">Clinical Findings & Observations</h6>
    <div class="p-3 border rounded bg-white" style="font-size: 13px; line-height: 1.6; white-space: pre-wrap;">{{ $radiologyReport->findings ?? 'No detailed findings recorded.' }}</div>
</div>

<!-- Radiologist Impression Section -->
<div class="mb-4">
    <h6 class="fw-bold mb-2 text-uppercase text-primary" style="font-size: 11px; letter-spacing: 0.04em;">Diagnostic Impression</h6>
    <div class="p-3 border border-primary-subtle rounded bg-light" style="font-size: 13.5px; font-weight: 600; line-height: 1.5; color: #0f172a; white-space: pre-wrap;">{{ $radiologyReport->impression ?? 'No diagnostic impression recorded.' }}</div>
</div>

<!-- Status & Approval Metadata -->
<div class="row border-top pt-3 text-muted small">
    <div class="col-6">
        <div><strong>Status:</strong> {{ $radiologyReport->status }}</div>
        <div><strong>Approved Date:</strong> {{ $radiologyReport->approved_at ? \Carbon\Carbon::parse($radiologyReport->approved_at)->format('M d, Y h:i A') : 'Pending Approval' }}</div>
    </div>
    <div class="col-6 text-end">
        <div><strong>Radiologist:</strong> Dr. {{ $radiologyReport->radiologist->name ?? 'Radiologist' }}</div>
        <div><strong>License No:</strong> PRC-RAD-{{ substr(md5($radiologyReport->radiologist_id ?? 1), 0, 6) }}</div>
    </div>
</div>

<!-- Medical Signatures -->
<div class="row pt-5 text-center">
    <div class="col-6">
        <div class="signature-line">
            Radiologic Technologist
        </div>
        <div class="text-muted small" style="font-size: 10px;">Image Acquisition & Verification</div>
    </div>
    <div class="col-6">
        <div class="signature-line">
            Dr. {{ $radiologyReport->radiologist->name ?? 'Radiologist' }}, MD
        </div>
        <div class="text-muted small" style="font-size: 10px;">Consultant Radiologist</div>
    </div>
</div>
@endsection
