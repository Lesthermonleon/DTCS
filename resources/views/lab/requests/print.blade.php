@extends('layouts.print')

@section('title', 'Laboratory Report — ' . $labRequest->request_no)
@section('department', 'Department of Pathology & Laboratory Medicine')
@section('document-title', 'LABORATORY REPORT')
@section('document-no', $labRequest->request_no)

@section('content')
<!-- Patient Identification Section -->
<div class="patient-info-box">
    <div class="row g-2">
        <div class="col-4">
            <div class="info-label">Patient Name</div>
            <div class="info-value">{{ $labRequest->patient->full_name }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Patient ID / No</div>
            <div class="info-value">{{ $labRequest->patient->patient_no }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Age / Gender</div>
            <div class="info-value">{{ $labRequest->patient->age ?? 'N/A' }} yrs / {{ ucfirst($labRequest->patient->gender ?? 'N/A') }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Requesting Physician</div>
            <div class="info-value">Dr. {{ $labRequest->doctor->name }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Specimen Type</div>
            <div class="info-value">{{ $labRequest->specimen_type }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Priority / Status</div>
            <div class="info-value">{{ $labRequest->priority }} — <span class="text-primary">{{ $labRequest->status }}</span></div>
        </div>
    </div>
</div>

<!-- Laboratory Test Results Table -->
<h6 class="fw-bold mb-2 text-uppercase text-secondary" style="font-size: 11px; letter-spacing: 0.04em;">Test Findings & Clinical Results</h6>
<table class="table-clinical">
    <thead>
        <tr>
            <th style="width: 25%;">Test Examination</th>
            <th style="width: 20%;">Category</th>
            <th style="width: 15%;">Result</th>
            <th style="width: 12%;">Unit</th>
            <th style="width: 15%;">Reference Range</th>
            <th style="width: 13%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($labRequest->items as $item)
        <tr>
            <td class="fw-bold">{{ $item->labTest->name }}</td>
            <td>{{ $item->labTest->category->name ?? 'General' }}</td>
            <td class="fw-bold text-primary" style="font-size: 14px;">{{ $item->result?->result_value ?? 'Pending' }}</td>
            <td>{{ $item->labTest->unit ?? '—' }}</td>
            <td>{{ $item->labTest->normal_range ?? 'Standard' }}</td>
            <td>{{ $item->result?->status ?? $item->status }}</td>
        </tr>
        @if($item->result?->remarks)
        <tr style="background: #fafafa;">
            <td colspan="6" class="text-muted small"><strong>Remarks / Clinical Impression:</strong> {{ $item->result->remarks }}</td>
        </tr>
        @endif
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted">No laboratory tests recorded for this request.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Medical Signatures -->
<div class="row pt-4 text-center">
    <div class="col-4">
        <div class="signature-line">
            {{ $labRequest->items->first()?->result?->medTech?->name ?? 'Medical Technologist' }}
        </div>
        <div class="text-muted small" style="font-size: 10px;">Registered Medical Technologist</div>
    </div>
    <div class="col-4">
        <div class="signature-line">
            Dr. {{ $labRequest->doctor->name }}
        </div>
        <div class="text-muted small" style="font-size: 10px;">Attending / Requesting Physician</div>
    </div>
    <div class="col-4">
        <div class="signature-line">
            Pathologist / Dept. Head
        </div>
        <div class="text-muted small" style="font-size: 10px;">Consultant Pathologist</div>
    </div>
</div>
@endsection
