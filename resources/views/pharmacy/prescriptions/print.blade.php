@extends('layouts.print')

@section('title', 'Prescription — ' . $prescription->rx_no)
@section('department', 'Pharmacy Services & Medical Therapeutics')
@section('document-title', 'OFFICIAL PRESCRIPTION (Rx)')
@section('document-no', $prescription->rx_no)

@section('content')
<!-- Patient Identification Section -->
<div class="patient-info-box">
    <div class="row g-2">
        <div class="col-4">
            <div class="info-label">Patient Name</div>
            <div class="info-value">{{ $prescription->patient->full_name }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Patient ID / No</div>
            <div class="info-value">{{ $prescription->patient->patient_no }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Age / Gender</div>
            <div class="info-value">{{ $prescription->patient->age ?? 'N/A' }} yrs / {{ ucfirst($prescription->patient->gender ?? 'N/A') }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Prescribing Physician</div>
            <div class="info-value">Dr. {{ $prescription->doctor->name }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Prescription Date</div>
            <div class="info-value">{{ $prescription->created_at->format('M d, Y') }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Status</div>
            <div class="info-value text-primary">{{ $prescription->status }}</div>
        </div>
    </div>
</div>

<!-- Prescription Meds Symbol & Items Table -->
<div class="d-flex align-items-center gap-2 mb-2">
    <span class="fw-bold fs-3 text-primary" style="font-family: serif;">Rx</span>
    <h6 class="fw-bold mb-0 text-uppercase text-secondary" style="font-size: 11px; letter-spacing: 0.04em;">Prescribed Medications & Dosage Instructions</h6>
</div>

<table class="table-clinical">
    <thead>
        <tr>
            <th style="width: 30%;">Medication Name & Strength</th>
            <th style="width: 15%;">Dosage</th>
            <th style="width: 15%;">Frequency</th>
            <th style="width: 15%;">Duration</th>
            <th style="width: 10%;">Qty</th>
            <th style="width: 15%;">Instructions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($prescription->items as $item)
        <tr>
            <td class="fw-bold">{{ $item->medicine_name }} <small class="text-muted">({{ $item->dosage_strength ?? 'Standard' }})</small></td>
            <td>{{ $item->dosage ?? '1 Tab' }}</td>
            <td>{{ $item->frequency ?? 'Once Daily' }}</td>
            <td>{{ $item->duration_days ? $item->duration_days . ' Days' : 'As directed' }}</td>
            <td class="fw-bold">{{ $item->quantity }}</td>
            <td>{{ $item->instructions ?? 'Take with food' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted">No medication items specified in this prescription.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Physician License & Signature -->
<div class="row pt-5 text-center">
    <div class="col-6">
        <div class="text-start text-muted small ms-3">
            <div><strong>Notes / Precautions:</strong> Refill as prescribed by physician. Keep out of reach of children.</div>
        </div>
    </div>
    <div class="col-6">
        <div class="signature-line">
            Dr. {{ $prescription->doctor->name }}, MD
        </div>
        <div class="text-muted small" style="font-size: 10px;">Lic No: PRC-{{ sprintf('%06d', $prescription->doctor_id) }} | PTR: {{ date('Y') }}-{{ $prescription->doctor_id }}890</div>
    </div>
</div>
@endsection
