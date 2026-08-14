@extends('layouts.app')
@section('title', 'Dispensing Receipt #DSP-' . str_pad($dispensing->id, 5, '0', STR_PAD_LEFT))
@section('page-title', 'Dispensing Record')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 text-dark fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Dispensing Record #DSP-{{ str_pad($dispensing->id, 5, '0', STR_PAD_LEFT) }}</h4>
        <p class="text-muted small mb-0">Official medication dispensing log and inventory audit trail.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('pharmacy.dispensing.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to History
        </a>
        <a href="{{ route('pharmacy.dispensing.print', $dispensing) }}" target="_blank" class="btn btn-primary">
            <i class="bi bi-printer me-1"></i> Print Official Record
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $item = $dispensing->prescriptionItem;
    $rx   = $item?->prescription;
    $pt   = $rx?->patient;
    $doc  = $rx?->doctor;
@endphp

<div class="row g-4">
    {{-- Left: Dispensing Receipt Card --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-2">
                        <i class="bi bi-check-lg fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Dispensing Voucher</h6>
                        <span class="small text-muted">Dispensed on {{ $dispensing->dispensed_at?->format('F d, Y \a\t H:i A') }}</span>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                    <i class="bi bi-check2-circle me-1"></i> Dispensed
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4 pb-3 border-bottom">
                    <div class="col-sm-6">
                        <span class="text-muted small text-uppercase fw-semibold d-block">Patient Details</span>
                        <h5 class="fw-bold text-dark mb-0">{{ $pt?->full_name ?? '—' }}</h5>
                        <div class="small text-muted">Patient ID: <strong>{{ $pt?->patient_no ?? '—' }}</strong></div>
                        <div class="small text-muted">Gender / Type: {{ $pt?->gender ?? '—' }} &bull; {{ $pt?->patient_type ?? 'Outpatient' }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <span class="text-muted small text-uppercase fw-semibold d-block">Prescription Info</span>
                        <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="fw-bold text-primary text-decoration-none">
                            <i class="bi bi-prescription me-1"></i>{{ $rx?->prescription_no ?? '—' }}
                        </a>
                        <div class="small text-muted">Prescribed By: <strong>{{ $doc?->name ?? '—' }}</strong></div>
                        <div class="small text-muted">Diagnosis: {{ $rx?->diagnosis ?? 'N/A' }}</div>
                    </div>
                </div>

                {{-- Dispensed Medication Table --}}
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-capsule me-2 text-primary"></i>Dispensed Medication Line Item</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Medication Name</th>
                                <th>Dosage & Route</th>
                                <th>Frequency</th>
                                <th>Prescribed</th>
                                <th class="text-end">Dispensed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item?->medication_name ?? '—' }}</div>
                                    @if($item?->instructions)
                                        <div class="small text-muted"><i class="bi bi-info-circle me-1"></i>{{ $item->instructions }}</div>
                                    @endif
                                </td>
                                <td>{{ $item?->dosage }} ({{ $item?->route ?? 'Oral' }})</td>
                                <td>{{ $item?->frequency }} &bull; {{ $item?->duration }}</td>
                                <td>{{ $item?->quantity }} unit(s)</td>
                                <td class="text-end fw-bold text-success fs-5">
                                    {{ $dispensing->quantity_dispensed }} unit(s)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Batch / Lot Information Box --}}
                <div class="bg-light rounded-3 p-3 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-qr-code-scan me-2 text-primary"></i>Batch & Inventory Log</h6>
                    <div class="row g-3 small">
                        <div class="col-sm-4">
                            <span class="text-muted d-block">Lot / Batch Number</span>
                            <strong class="text-dark fs-6">{{ $dispensing->lot_number }}</strong>
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted d-block">Medication Expiry Date</span>
                            @if($dispensing->expiry_date)
                                <strong class="text-dark fs-6">{{ $dispensing->expiry_date->format('M d, Y') }}</strong>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <span class="text-muted d-block">Dispensed Timestamp</span>
                            <strong class="text-dark fs-6">{{ $dispensing->dispensed_at?->format('M d, Y H:i:s') }}</strong>
                        </div>
                    </div>
                </div>

                @if($dispensing->notes)
                    <div class="mb-3">
                        <span class="fw-bold text-dark small d-block mb-1"><i class="bi bi-journal-text me-1 text-primary"></i>Pharmacist Advisory / Notes:</span>
                        <div class="p-3 bg-warning-subtle text-dark border border-warning-subtle rounded-3 small">
                            {{ $dispensing->notes }}
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    <i class="bi bi-shield-check me-1 text-success"></i> Recorded in PMS Audit Trail
                </div>
                <div class="text-end">
                    <div class="small text-muted">Dispensing Pharmacist</div>
                    <div class="fw-bold text-dark"><i class="bi bi-person-badge me-1"></i>{{ $dispensing->pharmacist?->name ?? 'Pharmacist' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Status & Actions Sidebar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-info-circle me-2 text-primary"></i>Prescription Status</h6>
            </div>
            <div class="card-body">
                <div class="text-center py-3 border-bottom mb-3">
                    <div class="small text-muted mb-1">Overall Prescription Status</div>
                    <span class="badge bg-{{ $rx?->statusBadge }} fs-6 px-3 py-2">
                        {{ $rx?->status }}
                    </span>
                </div>

                <h6 class="fw-bold text-dark small mb-2">All Medication Items in Rx:</h6>
                <ul class="list-group list-group-flush small">
                    @foreach($rx?->items ?? [] as $rxItem)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <strong class="{{ $rxItem->id === $item->id ? 'text-primary' : '' }}">{{ $rxItem->medication_name }}</strong>
                                <div class="text-muted small">{{ $rxItem->dosage }}</div>
                            </div>
                            <span class="badge bg-{{ $rxItem->status === 'Dispensed' ? 'success' : 'secondary' }}">
                                {{ $rxItem->status }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4">
                    <a href="{{ route('pharmacy.dispensing.create') }}?rx={{ $rx?->id }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-plus-circle me-1"></i> Dispense Another Item
                    </a>
                    <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="btn btn-secondary w-100">
                        <i class="bi bi-prescription me-1"></i> View Prescription
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
