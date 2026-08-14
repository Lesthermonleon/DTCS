@extends('layouts.app')
@section('title', 'Dispense Medication — Pharmacy')
@section('page-title', 'Dispense Medication')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 text-dark fw-bold"><i class="bi bi-capsule-pill me-2 text-primary"></i>Dispense Medication</h4>
        <p class="text-muted small mb-0">Record medication batch dispensing for verified medical prescriptions.</p>
    </div>
    <div>
        <a href="{{ route('pharmacy.dispensing.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Dispensing History
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    {{-- Step 1: Select Prescription --}}
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-search me-2 text-primary"></i>1. Select Verified Prescription</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('pharmacy.dispensing.create') }}" class="row g-3 align-items-center">
                    <div class="col-md-9">
                        <label class="form-label small text-muted">Select from Pending / Partially Dispensed Prescriptions:</label>
                        <select name="rx" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">-- Choose a Verified Prescription --</option>
                            @foreach($prescriptions as $rxOption)
                                <option value="{{ $rxOption->id }}" {{ ($selectedPrescription?->id == $rxOption->id) ? 'selected' : '' }}>
                                    {{ $rxOption->prescription_no }} &mdash; Patient: {{ $rxOption->patient->full_name }} ({{ $rxOption->items->where('status', 'Pending')->count() }} pending items)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-check2-circle me-1"></i> Load Prescription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($selectedPrescription)
        {{-- Step 2: Patient & Doctor Info Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-v me-2"></i>Patient & Rx Overview</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="bi bi-person-fill fs-2"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">{{ $selectedPrescription->patient->full_name }}</h5>
                            <span class="badge bg-secondary-subtle text-secondary border mt-1">ID: {{ $selectedPrescription->patient->patient_no }}</span>
                        </div>
                    </div>

                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted">Rx Number</dt>
                        <dd class="col-7 fw-bold text-primary">{{ $selectedPrescription->prescription_no }}</dd>

                        <dt class="col-5 text-muted">Prescribing Doctor</dt>
                        <dd class="col-7">{{ $selectedPrescription->doctor->name }}</dd>

                        <dt class="col-5 text-muted">Diagnosis</dt>
                        <dd class="col-7">{{ $selectedPrescription->diagnosis ?? '—' }}</dd>

                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $selectedPrescription->statusBadge }}">{{ $selectedPrescription->status }}</span>
                        </dd>

                        <dt class="col-5 text-muted">Prescribed Date</dt>
                        <dd class="col-7">{{ $selectedPrescription->prescribed_at?->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Step 3: Dispensing Form --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-box-seam me-2 text-primary"></i>2. Dispense Item Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pharmacy.dispensing.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Select Medication Item to Dispense <span class="text-danger">*</span></label>
                            <div class="list-group">
                                @forelse($selectedPrescription->items as $item)
                                    @php
                                        $isPending = ($item->status === 'Pending');
                                        $isSelectedItem = ($selectedItem?->id === $item->id) || ($loop->first && $isPending && !$selectedItem);
                                    @endphp
                                    <label class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 {{ $isPending ? '' : 'bg-light text-muted' }}">
                                        <div class="d-flex align-items-start">
                                            <input class="form-check-input me-3 mt-1" type="radio" name="prescription_item_id"
                                                   value="{{ $item->id }}" {{ $isPending ? ($isSelectedItem ? 'checked' : '') : 'disabled' }}>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->medication_name }}</div>
                                                <div class="small text-muted">
                                                    Dosage: <strong>{{ $item->dosage }}</strong> &bull;
                                                    Route: <strong>{{ $item->route ?? 'Oral' }}</strong> &bull;
                                                    Frequency: <strong>{{ $item->frequency }}</strong> &bull;
                                                    Duration: <strong>{{ $item->duration }}</strong>
                                                </div>
                                                @if($item->instructions)
                                                    <div class="small text-info mt-1"><i class="bi bi-info-circle me-1"></i>{{ $item->instructions }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $isPending ? 'warning-subtle text-warning border border-warning' : 'success-subtle text-success border border-success' }} mb-1">
                                                {{ $item->status }}
                                            </span>
                                            <div class="small text-dark fw-bold">Qty: {{ $item->quantity }}</div>
                                        </div>
                                    </label>
                                @empty
                                    <div class="alert alert-warning mb-0">No items found for this prescription.</div>
                                @endforelse
                            </div>
                            @error('prescription_item_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-journal-check me-2 text-primary"></i>Batch & Dispensing Info</h6>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="quantity_dispensed" class="form-label fw-semibold">Quantity Dispensed <span class="text-danger">*</span></label>
                                <input type="number" name="quantity_dispensed" id="quantity_dispensed"
                                       class="form-control @error('quantity_dispensed') is-invalid @enderror"
                                       value="{{ old('quantity_dispensed', $selectedItem?->quantity ?? 1) }}" min="1" required>
                                @error('quantity_dispensed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="lot_number" class="form-label fw-semibold">Lot / Batch Number <span class="text-danger">*</span></label>
                                <input type="text" name="lot_number" id="lot_number"
                                       class="form-control @error('lot_number') is-invalid @enderror"
                                       placeholder="e.g. LOT-2026-9812" value="{{ old('lot_number', 'LOT-' . date('Y') . '-' . rand(1000, 9999)) }}" required>
                                @error('lot_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label fw-semibold">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" name="expiry_date" id="expiry_date"
                                       class="form-control @error('expiry_date') is-invalid @enderror"
                                       value="{{ old('expiry_date', now()->addYear()->format('Y-m-d')) }}"
                                       min="{{ now()->addDay()->format('Y-m-d') }}" required>
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Dispensing Pharmacist</label>
                                <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label fw-semibold">Pharmacist Notes / Patient Advisory</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Enter any special instructions given to patient or lot remarks...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="{{ route('pharmacy.dispensing.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-lg me-1"></i> Confirm & Dispense Medication
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="bi bi-capsule fs-1 text-primary opacity-50 mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">Please select a prescription to begin dispensing</h5>
                    <p class="text-muted small">Choose a verified prescription from the dropdown above, or browse verified prescriptions directly.</p>
                    <a href="{{ route('pharmacy.prescriptions.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list-check me-1"></i> View Prescriptions List
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
