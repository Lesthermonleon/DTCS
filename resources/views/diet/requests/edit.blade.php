@extends('layouts.app')
@section('title', 'Edit Diet Request')
@section('page-title', 'Edit Diet Request: ' . $dietRequest->request_no)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('diet.requests.index') }}">Requests</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<form method="POST" action="{{ route('diet.requests.update', $dietRequest) }}">
@csrf @method('PUT')
<div class="card" style="max-width: 700px;">
    <div class="card-header">
        <i class="bi bi-pencil me-2"></i>Edit Request Details
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label fw-semibold">Patient</label>
            <input type="text" class="form-control bg-light" value="{{ $dietRequest->patient->last_name }}, {{ $dietRequest->patient->first_name }} ({{ $dietRequest->patient->patient_no }})" readonly disabled>
            <input type="hidden" name="patient_id" value="{{ $dietRequest->patient_id }}">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Diet Type <span class="text-danger">*</span></label>
            <select name="diet_type" class="form-select @error('diet_type') is-invalid @enderror" required>
                @foreach($dietTypes as $type)
                    <option value="{{ $type }}" {{ old('diet_type', $dietRequest->diet_type) === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            @error('diet_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Allergies</label>
            <textarea name="allergies" class="form-control @error('allergies') is-invalid @enderror" rows="2" placeholder="List food allergies, e.g. Peanuts, Seafood... (optional)">{{ old('allergies', $dietRequest->allergies) }}</textarea>
            @error('allergies')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Food Restrictions</label>
            <textarea name="food_restrictions" class="form-control @error('food_restrictions') is-invalid @enderror" rows="2" placeholder="List food restrictions / preferences, e.g. Vegetarian, Halal, No red meat... (optional)">{{ old('food_restrictions', $dietRequest->food_restrictions) }}</textarea>
            @error('food_restrictions')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Clinical Notes</label>
            <textarea name="clinical_notes" class="form-control @error('clinical_notes') is-invalid @enderror" rows="3" placeholder="Additional clinical notes, reason for request... (optional)">{{ old('clinical_notes', $dietRequest->clinical_notes) }}</textarea>
            @error('clinical_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
        <a href="{{ route('diet.requests.show', $dietRequest) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
</form>
@endsection
