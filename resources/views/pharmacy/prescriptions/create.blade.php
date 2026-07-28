@extends('layouts.app')
@section('title', 'New Prescription')
@section('page-title', 'Create Prescription')
@section('content')
<form method="POST" action="{{ route('pharmacy.prescriptions.store') }}" id="rxForm">
@csrf
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-2"></i>Patient & Diagnosis</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">— Select Patient —</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id')==$p->id?'selected':'' }}>{{ $p->last_name }}, {{ $p->first_name }} ({{ $p->patient_no }})</option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Diagnosis</label>
                    <input type="text" name="diagnosis" class="form-control" value="{{ old('diagnosis') }}" placeholder="Primary diagnosis…">
                </div>
                <div>
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-capsule me-2"></i>Medications <span class="text-danger">*</span></span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addMedBtn"><i class="bi bi-plus"></i> Add Medication</button>
            </div>
            <div class="card-body">
                @error('items')<div class="alert alert-danger py-1 mb-2">{{ $message }}</div>@enderror
                <div id="medicationRows"></div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Submit Prescription</button>
    <a href="{{ route('pharmacy.prescriptions.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>

<template id="medRowTemplate">
<div class="border rounded p-3 mb-2 position-relative med-row">
    <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-med" title="Remove"></button>
    <div class="row g-2">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Medication <span class="text-danger">*</span></label>
            <input type="text" name="items[IDX][medication_name]" class="form-control form-control-sm" placeholder="Drug name & strength" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Dosage</label>
            <input type="text" name="items[IDX][dosage]" class="form-control form-control-sm" placeholder="500mg" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Route</label>
            <select name="items[IDX][route]" class="form-select form-select-sm">
                @foreach(['Oral','IV','IM','SC','Topical','Sublingual'] as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Frequency</label>
            <select name="items[IDX][frequency]" class="form-select form-select-sm" required>
                @foreach(['OD','BID','TID','QID','PRN','Q4H','Q6H','Q8H','Q12H'] as $f)
                    <option value="{{ $f }}">{{ $f }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Duration</label>
            <input type="text" name="items[IDX][duration]" class="form-control form-control-sm" placeholder="7 days" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Qty</label>
            <input type="number" name="items[IDX][quantity]" class="form-control form-control-sm" value="1" min="1" required>
        </div>
        <div class="col-md-10">
            <label class="form-label small fw-semibold">Instructions</label>
            <input type="text" name="items[IDX][instructions]" class="form-control form-control-sm" placeholder="Take after meal…">
        </div>
    </div>
</div>
</template>

@push('scripts')
<script>
let idx = 0;
const template = document.getElementById('medRowTemplate');
const container = document.getElementById('medicationRows');

function addRow() {
    const clone = template.content.cloneNode(true);
    clone.querySelectorAll('[name*="IDX"]').forEach(el => {
        el.name = el.name.replace('IDX', idx);
    });
    const row = clone.querySelector('.med-row');
    row.querySelector('.remove-med').addEventListener('click', () => row.remove());
    container.appendChild(clone);
    idx++;
}

document.getElementById('addMedBtn').addEventListener('click', addRow);
document.getElementById('rxForm').addEventListener('submit', e => {
    if (container.children.length === 0) {
        e.preventDefault();
        alert('Please add at least one medication.');
    }
});
// Start with one row
addRow();
</script>
@endpush
@endsection
