@extends('layouts.app')

@section('title', 'New Message')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('messages.index') }}">Staff Messages</a></li>
    <li class="breadcrumb-item active" aria-current="page">New Message</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-pencil-square text-primary me-2"></i>New Staff Message
        </h4>
        <p class="text-muted small mb-0">Compose a direct message to a HIMS staff member</p>
    </div>
    <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Back to Messages
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title fw-bold text-dark mb-0">Compose Direct Message</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('messages.store') }}" method="POST" id="composeMessageForm">
                    @csrf

                    {{-- Recipient Selection --}}
                    <div class="mb-4">
                        <label for="recipient_id" class="form-label fw-semibold text-dark small">
                            Recipient <span class="text-danger">*</span>
                        </label>
                        <select name="recipient_id" id="recipient_id" class="form-select @error('recipient_id') is-invalid @enderror" required>
                            <option value="" disabled {{ !old('recipient_id', request('recipient_id')) ? 'selected' : '' }}>
                                -- Select a staff member --
                            </option>
                            @foreach($staffUsers as $staff)
                                <option value="{{ $staff->id }}" {{ old('recipient_id', request('recipient_id')) == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->roleName }} — {{ $staff->department ?? 'Staff' }})
                                </option>
                            @endforeach
                        </select>
                        @error('recipient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">Select an active staff member or clinician from the list.</small>
                    </div>

                    {{-- Message Textarea --}}
                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold text-dark small">
                            Message <span class="text-danger">*</span>
                        </label>
                        <textarea name="message" id="message" rows="6" class="form-control @error('message') is-invalid @enderror" placeholder="Type your message here..." required style="resize: vertical;">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('messages.index') }}" class="btn btn-light px-4 rounded-pill">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill">
                            <i class="bi bi-send-fill me-1"></i>Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
