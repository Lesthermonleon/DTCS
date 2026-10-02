@extends('layouts.app')
@section('title', 'Archived Account: ' . $user->name)
@section('page-title', 'Archived Account')

@php
    /**
     * Extract the admin comment from the ActivityLog description.
     * Description format: "User account [email] (Name) was archived by admin. Reason: [comment]"
     * Using a Closure (not a named function) to avoid redeclaration errors in compiled views.
     */
    $extractReason = function (?string $desc): ?string {
        if (!$desc) return null;
        $pos = strpos($desc, 'Reason: ');
        return $pos !== false ? trim(substr($desc, $pos + 8)) : null;
    };

    $deletionReason    = $extractReason($deletionLog?->description);
    $deletedByName     = $deletionLog?->user?->name ?? null;
    $deletedAt         = $deletionLog?->logged_at;
    $primaryRole       = $user->roles->first();
@endphp

@section('content')
<div class="row g-3">

    {{-- ══════════════════════════════════════════════════════════════
         LEFT COLUMN — User Identity + Deletion Information
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="col-lg-7">

        {{-- User Information --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-person-x me-2 text-secondary"></i>Archived Account</span>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                    <i class="bi bi-archive-fill me-1"></i>Archived
                </span>
            </div>
            <div class="card-body">
                <dl class="row mb-0" style="font-size:.9rem;">
                    <dt class="col-sm-4 fw-semibold text-muted">Full Name</dt>
                    <dd class="col-sm-8">{{ $user->name }}</dd>

                    <dt class="col-sm-4 fw-semibold text-muted">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>

                    <dt class="col-sm-4 fw-semibold text-muted">Employee ID</dt>
                    <dd class="col-sm-8">{{ $user->employee_id ?? '—' }}</dd>

                    <dt class="col-sm-4 fw-semibold text-muted">Department</dt>
                    <dd class="col-sm-8">{{ $user->department ?? '—' }}</dd>

                    <dt class="col-sm-4 fw-semibold text-muted">Phone</dt>
                    <dd class="col-sm-8">{{ $user->phone ?? '—' }}</dd>

                    <dt class="col-sm-4 fw-semibold text-muted">Role</dt>
                    <dd class="col-sm-8">
                        @if($primaryRole)
                            <span class="badge bg-secondary">{{ $primaryRole->name }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4 fw-semibold text-muted">Archived At</dt>
                    <dd class="col-sm-8">
                        {{ $user->deleted_at ? $user->deleted_at->format('M d, Y g:i A') : '—' }}
                    </dd>
                </dl>
            </div>
            <div class="card-footer d-flex gap-2 align-items-center">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to User Management
                </a>
                {{-- Restore via the existing comment modal --}}
                <button type="button"
                        class="btn btn-success btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#restoreUserModal"
                        data-user-id="{{ $user->id }}"
                        data-user-name="{{ $user->name }}"
                        data-user-role="{{ $primaryRole?->name ?? 'No Role' }}"
                        data-action-url="{{ route('admin.users.restore', $user->id) }}">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Account
                </button>
            </div>
        </div>

        {{-- Deletion Information --}}
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-2 text-danger"></i>Deletion Information
                <small class="text-muted ms-2" style="font-size:.75rem;">(read-only — sourced from audit log)</small>
            </div>
            <div class="card-body">
                @if($deletionLog)
                    <dl class="row mb-0" style="font-size:.9rem;">
                        <dt class="col-sm-4 fw-semibold text-muted">Deleted By</dt>
                        <dd class="col-sm-8">
                            {{ $deletedByName ?? 'Unknown Administrator' }}
                        </dd>

                        <dt class="col-sm-4 fw-semibold text-muted">Deleted At</dt>
                        <dd class="col-sm-8">
                            {{ $deletedAt ? $deletedAt->format('M d, Y g:i A') : '—' }}
                        </dd>

                        <dt class="col-sm-4 fw-semibold text-muted">Reason / Comment</dt>
                        <dd class="col-sm-8">
                            @if($deletionReason)
                                <span class="text-dark">{{ $deletionReason }}</span>
                            @else
                                {{-- Description exists but has no explicit "Reason:" prefix — show full description --}}
                                <span class="text-muted fst-italic" style="font-size:.85rem;">{{ $deletionLog->description }}</span>
                            @endif
                        </dd>
                    </dl>
                @else
                    <div class="text-muted d-flex align-items-center gap-2" style="font-size:.875rem;">
                        <i class="bi bi-exclamation-circle text-warning"></i>
                        Deletion reason unavailable. No matching audit record was found for this account.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         RIGHT COLUMN — Account History
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Account History
                <span class="badge bg-secondary ms-2" style="font-size:.7rem;">{{ $history->count() }} event{{ $history->count() !== 1 ? 's' : '' }}</span>
            </div>
            <div class="card-body p-0">
                @if($history->isEmpty())
                    <div class="p-3 text-muted" style="font-size:.875rem;">
                        <i class="bi bi-info-circle me-1"></i>No archive or restore events found in the audit log.
                    </div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($history as $event)
                            @php
                                $isArchive   = $event->action === 'User Archived';
                                $eventReason = $extractReason($event->description);
                                $by          = $event->user?->name ?? 'Unknown Admin';
                            @endphp
                            <li class="list-group-item px-3 py-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge {{ $isArchive ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}" style="font-size:.7rem;">
                                        <i class="bi {{ $isArchive ? 'bi-archive-fill' : 'bi-arrow-counterclockwise' }} me-1"></i>
                                        {{ $event->action }}
                                    </span>
                                    <small class="text-muted">{{ $event->logged_at?->format('M d, Y g:i A') }}</small>
                                </div>
                                <div class="mb-1" style="font-size:.82rem;">
                                    <span class="text-muted">By:</span>
                                    <span class="fw-semibold">{{ $by }}</span>
                                </div>
                                @if($eventReason)
                                    <div style="font-size:.82rem; color: var(--bs-body-color, #333);">
                                        <span class="text-muted">Reason:</span>
                                        {{ $eventReason }}
                                    </div>
                                @else
                                    <div class="text-muted fst-italic" style="font-size:.8rem;">No reason recorded.</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── Restore User Modal (reused from index) ──────────────────────── --}}
<div class="modal fade" id="restoreUserModal" tabindex="-1" aria-labelledby="restoreUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold" id="restoreUserModalLabel">
                    <i class="bi bi-arrow-counterclockwise text-success me-2"></i>Restore User Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="restoreUserForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success py-2 mb-3" role="alert" style="font-size:.875rem;">
                        You are about to restore this user account. They will regain their previous system access.
                    </div>
                    <div class="mb-3">
                        <div class="d-flex gap-3 mb-1">
                            <span class="text-muted" style="min-width:48px;font-size:.8rem;">User</span>
                            <span class="fw-semibold" id="restoreUserName"></span>
                        </div>
                        <div class="d-flex gap-3">
                            <span class="text-muted" style="min-width:48px;font-size:.8rem;">Role</span>
                            <span id="restoreUserRole"></span>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="restoreUserComment" class="form-label fw-semibold mb-1" style="font-size:.875rem;">
                            Reason / Comment <span class="text-danger">*</span>
                        </label>
                        <textarea id="restoreUserComment"
                                  name="comment"
                                  class="form-control"
                                  rows="4"
                                  maxlength="1000"
                                  placeholder="Enter the reason for restoring this account..."
                                  required></textarea>
                        <div class="form-text text-muted" style="font-size:.75rem;">This comment is required for audit purposes.</div>
                        <div id="restoreCommentError" class="text-danger mt-1" style="font-size:.8rem;display:none;">A reason is required before restoring.</div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const restoreModal   = document.getElementById('restoreUserModal');
    const restoreForm    = document.getElementById('restoreUserForm');
    const restoreComment = document.getElementById('restoreUserComment');
    const restoreErrMsg  = document.getElementById('restoreCommentError');

    if (restoreModal) {
        restoreModal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            restoreForm.action = btn.dataset.actionUrl;
            document.getElementById('restoreUserName').textContent = btn.dataset.userName;
            document.getElementById('restoreUserRole').textContent = btn.dataset.userRole;
            restoreComment.value = '';
            restoreErrMsg.style.display = 'none';
            restoreComment.classList.remove('is-invalid');
        });
        restoreModal.addEventListener('hidden.bs.modal', function () {
            restoreComment.value = '';
            restoreErrMsg.style.display = 'none';
            restoreComment.classList.remove('is-invalid');
        });
        restoreForm.addEventListener('submit', function (e) {
            if (!restoreComment.value.trim()) {
                e.preventDefault();
                restoreErrMsg.style.display = 'block';
                restoreComment.classList.add('is-invalid');
                restoreComment.focus();
            }
        });
    }
});
</script>
@endpush
@endsection
