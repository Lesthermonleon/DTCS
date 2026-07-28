@extends('layouts.app')
@section('title', 'Edit Lab Request')
@section('page-title', 'Edit Lab Request')
@section('content')
<form method="POST" action="{{ route('lab.requests.update', $labRequest) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header">Request Details</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Patient</label>
                    <input type="text" class="form-control" value="{{ $labRequest->patient->full_name }} ({{ $labRequest->patient->patient_no }})" disabled>
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Priority <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            @foreach(['Routine','Urgent','STAT'] as $p)
                                <option value="{{ $p }}" {{ $labRequest->priority===$p?'selected':'' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Specimen Type <span class="text-danger">*</span></label>
                        <select name="specimen_type" class="form-select" required>
                            @foreach(['Blood','Urine','Stool','Sputum','Swab','CSF','Other'] as $s)
                                <option value="{{ $s }}" {{ $labRequest->specimen_type===$s?'selected':'' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label fw-semibold">Clinical Notes</label>
                    <textarea name="clinical_notes" class="form-control" rows="3">{{ $labRequest->clinical_notes }}</textarea>
                </div>
                {{-- Hidden patient_id for validation --}}
                <input type="hidden" name="patient_id" value="{{ $labRequest->patient_id }}">
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">Select Tests <span class="text-danger">*</span></div>
            <div class="card-body" style="max-height:360px;overflow-y:auto;">
                @php $currentTestIds = $labRequest->items->pluck('lab_test_id')->toArray(); @endphp
                @foreach($labTests->groupBy(fn($t)=>$t->category->name) as $catName => $tests)
                    <div class="fw-semibold text-primary small mb-1 mt-2">{{ $catName }}</div>
                    @foreach($tests as $test)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="tests[]"
                               value="{{ $test->id }}" id="test_{{ $test->id }}"
                               {{ in_array($test->id, old('tests', $currentTestIds)) ? 'checked':'' }}>
                        <label class="form-check-label small" for="test_{{ $test->id }}">{{ $test->name }} ({{ $test->code }})</label>
                    </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">Update Request</button>
    <a href="{{ route('lab.requests.show', $labRequest) }}" class="btn btn-outline-secondary">Cancel</a>
</div>
</form>
@endsection
