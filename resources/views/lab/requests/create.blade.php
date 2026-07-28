@extends('layouts.app')
@section('title', 'New Lab Request')
@section('page-title', 'New Laboratory Request')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('lab.requests.index') }}">Lab Requests</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection
@section('content')
<form method="POST" action="{{ route('lab.requests.store') }}">
@csrf
<div class="row g-3">
    {{-- Left column --}}
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-2"></i>Patient & Request Details</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">— Select Patient —</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id')==$p->id ? 'selected':'' }}>
                                {{ $p->last_name }}, {{ $p->first_name }} ({{ $p->patient_no }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            @foreach(['Routine','Urgent','STAT'] as $p)
                                <option value="{{ $p }}" {{ old('priority')===$p ? 'selected':'' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Specimen Type <span class="text-danger">*</span></label>
                        <select name="specimen_type" class="form-select @error('specimen_type') is-invalid @enderror" required>
                            @foreach(['Blood','Urine','Stool','Sputum','Swab','CSF','Other'] as $s)
                                <option value="{{ $s }}" {{ old('specimen_type')===$s ?'selected':'' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('specimen_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label fw-semibold">Clinical Notes</label>
                    <textarea name="clinical_notes" class="form-control" rows="3" placeholder="Relevant history, diagnosis…">{{ old('clinical_notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>
    {{-- Right column — test selection --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-list-check me-2"></i>Select Tests <span class="text-danger">*</span></div>
            <div class="card-body" style="max-height:400px;overflow-y:auto;">
                @error('tests')<div class="alert alert-danger py-1 mb-2">{{ $message }}</div>@enderror
                @foreach($labTests->groupBy(fn($t)=>$t->category->name) as $catName => $tests)
                    <div class="fw-semibold text-primary small mb-1 mt-2">{{ $catName }}</div>
                    @foreach($tests as $test)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="tests[]"
                               value="{{ $test->id }}" id="test_{{ $test->id }}"
                               {{ in_array($test->id, old('tests', [])) ? 'checked':'' }}>
                        <label class="form-check-label small" for="test_{{ $test->id }}">
                            {{ $test->name }}
                            <span class="text-muted">({{ $test->code }})</span>
                        </label>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Submit Request</button>
    <a href="{{ route('lab.requests.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>
@endsection
