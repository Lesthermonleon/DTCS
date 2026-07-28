@extends('layouts.app')
@section('title', 'Create Diet Plan')
@section('page-title', 'Design Therapeutic Diet Plan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('diet.plans.index') }}">Diet Plans</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection
@section('content')
<form method="POST" action="{{ route('diet.plans.store') }}">
@csrf
<div class="row g-3">
    <div class="col-lg-8">
        {{-- Plan Details Card --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-file-earmark-medical me-2"></i>Plan Core Details</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Diet Request / Patient <span class="text-danger">*</span></label>
                    <select name="diet_request_id" class="form-select @error('diet_request_id') is-invalid @enderror" required>
                        <option value="">— Select Request —</option>
                        @foreach($pendingRequests as $req)
                            <option value="{{ $req->id }}" {{ (old('diet_request_id', request('diet_request_id')) == $req->id) ? 'selected' : '' }}>
                                {{ $req->request_no }} — {{ $req->patient->last_name }}, {{ $req->patient->first_name }} (Diet: {{ $req->diet_type }})
                            </option>
                        @endforeach
                    </select>
                    @error('diet_request_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Diet Plan Details & Instructions <span class="text-danger">*</span></label>
                    <textarea name="plan_details" class="form-control @error('plan_details') is-invalid @enderror" rows="5" placeholder="Enter specific breakfast, lunch, dinner guidelines, meal sizing, and prep instructions (minimum 20 characters)..." required>{{ old('plan_details') }}</textarea>
                    <small class="text-muted">Give comprehensive instructions matching the patient's therapeutic needs.</small>
                    @error('plan_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold">Additional Plan Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Special remarks, scheduling instructions... (optional)">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Right column — Nutrition breakdown & Dates --}}
    <div class="col-lg-4">
        {{-- Macronutrient Target Card --}}
        <div class="card mb-3">
            <div class="card-header bg-success-subtle text-success-emphasis"><i class="bi bi-apple me-2"></i>Nutritional Targets</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Daily Calories (kcal)</label>
                    <input type="number" name="total_calories" class="form-control @error('total_calories') is-invalid @enderror" value="{{ old('total_calories', 2000) }}" placeholder="e.g. 2000">
                    @error('total_calories')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Protein (g)</label>
                        <input type="number" step="0.1" name="protein_grams" class="form-control @error('protein_grams') is-invalid @enderror" value="{{ old('protein_grams') }}" placeholder="e.g. 75">
                        @error('protein_grams')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Carbs (g)</label>
                        <input type="number" step="0.1" name="carb_grams" class="form-control @error('carb_grams') is-invalid @enderror" value="{{ old('carb_grams') }}" placeholder="e.g. 250">
                        @error('carb_grams')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6 mx-auto mt-2">
                        <label class="form-label fw-semibold">Fat (g)</label>
                        <input type="number" step="0.1" name="fat_grams" class="form-control @error('fat_grams') is-invalid @enderror" value="{{ old('fat_grams') }}" placeholder="e.g. 60">
                        @error('fat_grams')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Dates and Timeline Card --}}
        <div class="card mb-3">
            <div class="card-header bg-info-subtle text-info-emphasis"><i class="bi bi-calendar-event me-2"></i>Duration / Timeline</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                    @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold">End Date</label>
                    <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                    @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Diet Plan</button>
    <a href="{{ route('diet.plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>
@endsection
