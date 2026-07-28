@extends('layouts.app')
@section('title', 'Encode Lab Result')
@section('page-title', 'Encode Lab Result')
@section('content')
<form method="POST" action="{{ route('lab.results.store') }}">
@csrf
<div class="card" style="max-width:560px;">
    <div class="card-header"><i class="bi bi-journal-medical me-2"></i>Result Entry</div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label fw-semibold">Select Pending Test <span class="text-danger">*</span></label>
            <select name="lab_request_item_id" class="form-select @error('lab_request_item_id') is-invalid @enderror" required>
                <option value="">— Select Test —</option>
                @foreach($pendingItems as $item)
                    <option value="{{ $item->id }}" {{ request('item_id')==$item->id||old('lab_request_item_id')==$item->id?'selected':'' }}>
                        {{ $item->labRequest->request_no }} — {{ $item->labTest->name }} ({{ $item->labRequest->patient->last_name ?? '' }})
                    </option>
                @endforeach
            </select>
            @error('lab_request_item_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Result Value <span class="text-danger">*</span></label>
            <input type="text" name="result_value" class="form-control @error('result_value') is-invalid @enderror" value="{{ old('result_value') }}" placeholder="e.g. 5.2, Positive, No growth…" required>
            @error('result_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Remarks / Interpretation</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Abnormal flag, interpretation…">{{ old('remarks') }}</textarea>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Result</button>
        <a href="{{ route('lab.results.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
</form>
@endsection
