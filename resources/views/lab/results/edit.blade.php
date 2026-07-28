@extends('layouts.app')
@section('title', 'Edit Result')
@section('page-title', 'Edit Lab Result')
@section('content')
<form method="POST" action="{{ route('lab.results.update', $labResult) }}">
@csrf @method('PUT')
<div class="card" style="max-width:560px;">
    <div class="card-header">Edit Result — {{ $labResult->requestItem->labTest->name }}</div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label fw-semibold">Result Value <span class="text-danger">*</span></label>
            <input type="text" name="result_value" class="form-control" value="{{ old('result_value', $labResult->result_value) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Remarks</label>
            <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $labResult->remarks) }}</textarea>
        </div>
    </div>
    <div class="card-footer d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('lab.results.show', $labResult) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
</form>
@endsection
