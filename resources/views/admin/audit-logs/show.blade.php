@extends('layouts.app')

@section('title', 'Audit Log Detail — ' . $auditLog->action)

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="fw-bold mb-0">
                <i class="bi bi-journal-text me-2 text-success"></i>Audit Log Detail
            </h5>
            <small class="text-muted">Event #{{ $auditLog->id }}</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent py-3 d-flex align-items-center gap-2 flex-wrap">
            {{-- Severity Badge --}}
            <span class="badge {{ $auditLog->severity_badge_class }} fw-semibold px-3 py-2">
                <i class="bi {{ $auditLog->severity_icon }} me-1"></i>{{ $auditLog->severity ?? 'INFO' }}
            </span>
            {{-- Action Badge --}}
            @if(in_array($auditLog->action, ['Account Locked']))
                <span class="badge bg-danger text-white fw-semibold px-3 py-2">
                    <i class="bi bi-lock-fill me-1"></i>{{ $auditLog->action }}
                </span>
            @elseif(in_array($auditLog->action, ['Failed Login', 'Session Replaced', 'Role Assignment Changed', 'Password Reset', 'User Archived', 'Surgery Schedule Removed']))
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-3 py-2">
                    <i class="bi bi-shield-exclamation me-1"></i>{{ $auditLog->action }}
                </span>
            @elseif(in_array($auditLog->action, ['Account Unlocked']))
                <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-3 py-2">
                    <i class="bi bi-shield-check me-1"></i>{{ $auditLog->action }}
                </span>
            @else
                <span class="badge bg-success-subtle text-success fw-semibold px-3 py-2">
                    {{ $auditLog->action }}
                </span>
            @endif
            {{-- Module Badge --}}
            <span class="badge" style="background-color: var(--bs-secondary-bg); color: var(--bs-secondary-color); font-size: 0.8rem;">
                {{ $auditLog->module }}
            </span>
        </div>

        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3 text-muted small">Timestamp</dt>
                <dd class="col-sm-9 small">
                    {{ $auditLog->created_at->format('F d, Y \a\t H:i:s') }}
                    <span class="text-muted ms-2">({{ $auditLog->created_at->diffForHumans() }})</span>
                </dd>

                <dt class="col-sm-3 text-muted small">Module</dt>
                <dd class="col-sm-9 small fw-semibold">{{ $auditLog->module }}</dd>

                <dt class="col-sm-3 text-muted small">Action</dt>
                <dd class="col-sm-9 small fw-semibold">{{ $auditLog->action }}</dd>

                <dt class="col-sm-3 text-muted small">Severity</dt>
                <dd class="col-sm-9 small">
                    <span class="badge {{ $auditLog->severity_badge_class }}" style="font-size: 0.75rem;">
                        <i class="bi {{ $auditLog->severity_icon }} me-1"></i>{{ $auditLog->severity ?? 'INFO' }}
                    </span>
                </dd>

                @if($auditLog->result)
                <dt class="col-sm-3 text-muted small">Result</dt>
                <dd class="col-sm-9 small">
                    <span class="badge {{ $auditLog->result_badge_class }}" style="font-size: 0.75rem;">
                        {{ $auditLog->result }}
                    </span>
                </dd>
                @endif

                <dt class="col-sm-3 text-muted small">Performed By</dt>
                <dd class="col-sm-9 small">
                    @if($auditLog->user)
                        <i class="bi bi-person-circle me-1 text-muted"></i>
                        {{ $auditLog->user->name }}
                        <span class="text-muted ms-1">&lt;{{ $auditLog->user->email }}&gt;</span>
                    @else
                        <span class="text-muted fst-italic">System (no authenticated user)</span>
                    @endif
                </dd>

                @if($auditLog->user && $auditLog->user->roles->isNotEmpty())
                <dt class="col-sm-3 text-muted small">Role</dt>
                <dd class="col-sm-9 small">
                    <i class="bi bi-person-badge me-1 text-muted"></i>
                    {{ $auditLog->user->roles->first()->name }}
                </dd>
                @endif

                <dt class="col-sm-3 text-muted small">IP Address</dt>
                <dd class="col-sm-9 small">{{ $auditLog->ip_address ?? '—' }}</dd>

                <dt class="col-sm-3 text-muted small">Logged At</dt>
                <dd class="col-sm-9 small">{{ $auditLog->logged_at?->format('F d, Y H:i:s') ?? '—' }}</dd>

                @if($auditLog->loggable_type)
                <dt class="col-sm-3 text-muted small">Related Entity</dt>
                <dd class="col-sm-9 small">
                    <code>{{ class_basename($auditLog->loggable_type) }}</code>
                    <span class="text-muted ms-1">#{{ $auditLog->loggable_id }}</span>
                </dd>
                @endif

                <dt class="col-sm-3 text-muted small">Description</dt>
                <dd class="col-sm-9 small">{{ $auditLog->description ?? '—' }}</dd>
            </dl>
        </div>

        <div class="card-footer bg-transparent border-0 py-2 text-end">
            <small class="text-muted">
                <i class="bi bi-shield-lock me-1 text-success"></i>
                This record is append-only and cannot be modified or deleted.
            </small>
        </div>
    </div>

</div>
@endsection
