@extends('layouts.print')

@section('title', 'Dispensing Record — ' . $dispensing->dispensing_no)
@section('department', 'Pharmacy Services & Medication Dispensing')
@section('document-title', 'DISPENSING RECORD')
@section('document-no', $dispensing->dispensing_no)

@section('content')
<!-- Patient Identification Section -->
<div class="patient-info-box">
    <div class="row g-2">
        <div class="col-4">
            <div class="info-label">Patient Name</div>
            <div class="info-value">{{ $dispensing->prescription->patient->full_name }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Patient ID / No</div>
            <div class="info-value">{{ $dispensing->prescription->patient->patient_no }}</div>
        </div>
        <div class="col-4">
            <div class="info-label">Prescription Ref</div>
            <div class="info-value">{{ $dispensing->prescription->rx_no }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Dispensing Pharmacist</div>
            <div class="info-value">{{ $dispensing->pharmacist->name ?? 'Licensed Pharmacist' }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Dispensed Date / Time</div>
            <div class="info-value">{{ $dispensing->dispensed_at ? \Carbon\Carbon::parse($dispensing->dispensed_at)->format('M d, Y h:i A') : $dispensing->created_at->format('M d, Y h:i A') }}</div>
        </div>
        <div class="col-4 mt-2">
            <div class="info-label">Status</div>
            <div class="info-value text-success">Completed & Dispensed</div>
        </div>
    </div>
</div>

<!-- Dispensing Summary Table -->
<h6 class="fw-bold mb-2 text-uppercase text-secondary" style="font-size: 11px; letter-spacing: 0.04em;">Dispensed Medications & Verification</h6>
<table class="table-clinical">
    <thead>
        <tr>
            <th style="width: 35%;">Medicine Name</th>
            <th style="width: 20%;">Batch / Lot No</th>
            <th style="width: 15%;">Prescribed Qty</th>
            <th style="width: 15%;">Dispensed Qty</th>
            <th style="width: 15%;">Verification</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dispensing->prescription->items as $item)
        <tr>
            <td class="fw-bold">{{ $item->medicine_name }}</td>
            <td>LOT-{{ date('Ym') }}-{{ sprintf('%04d', $item->id) }}</td>
            <td>{{ $item->quantity }}</td>
            <td class="fw-bold text-success">{{ $item->quantity }}</td>
            <td><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1">Verified</span></td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center text-muted">No items recorded in dispensing log.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- Pharmacist Signature -->
<div class="row pt-5 text-center">
    <div class="col-6">
        <div class="text-start text-muted small ms-3">
            <div><strong>Patient Acknowledgment:</strong> I acknowledge receipt of the above medications along with safety usage counseling.</div>
            <div class="signature-line w-75 mt-4">Patient / Representative Signature</div>
        </div>
    </div>
    <div class="col-6">
        <div class="signature-line">
            {{ $dispensing->pharmacist->name ?? 'Licensed Pharmacist' }}, RPh
        </div>
        <div class="text-muted small" style="font-size: 10px;">Registered Pharmacist — License No: RPH-{{ sprintf('%06d', $dispensing->pharmacist_id ?? 1) }}</div>
    </div>
</div>
@endsection
