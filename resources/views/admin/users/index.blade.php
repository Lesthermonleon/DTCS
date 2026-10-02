@extends('layouts.app')
@section('title', 'Admin — User Management')
@section('page-title', 'User Management')
@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <form class="d-flex gap-2 flex-grow-1" method="GET" id="filter-form">
            <input type="text" name="search" id="filter-search" class="form-control form-control-sm" placeholder="Name, email, employee ID…" value="{{ request('search') }}" style="max-width:220px">
            <select name="role" id="filter-role" class="form-select form-select-sm" style="max-width:160px">
                <option value="">All Roles</option>
                @foreach($roles as $r)<option value="{{ $r->slug }}" {{ request('role')===$r->slug?'selected':'' }}>{{ $r->name }}</option>@endforeach
            </select>
            <select name="status" id="filter-status" class="form-select form-select-sm" style="max-width:160px">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm d-none">Filter</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Employee ID</th><th>Department</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="users-table-body">
                @forelse($users as $u)
                <tr>
                    <td class="fw-semibold">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->employee_id ?? '—' }}</td>
                    <td>{{ $u->department ?? '—' }}</td>
                    <td>@foreach($u->roles as $r)<span class="badge bg-primary me-1">{{ $r->name }}</span>@endforeach</td>
                    <td>
                        <span class="badge bg-{{ $u->is_active ? 'success' : 'secondary' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span>
                        @if($u->locked_at)
                            <span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('admin.users.edit', $u) }}" class="table-action-btn action-edit" title="Edit User" aria-label="Edit User"><i class="bi bi-pencil"></i></a>
                            @if($u->locked_at)
                                <form method="POST" action="{{ route('admin.users.unlock', $u) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="table-action-btn action-unlock" title="Unlock Account" aria-label="Unlock Account"
                                            data-confirm="Unlock {{ $u->name }}'s account? They will be able to log in again."
                                            data-confirm-title="Unlock Account"
                                            data-confirm-btn="btn-info"
                                            data-confirm-icon="bi-unlock-fill"
                                            data-confirm-action-text="Unlock Account"><i class="bi bi-unlock"></i></button>
                                </form>
                            @endif
                            @if($u->id !== auth()->id())
                                {{-- Archive: opens dedicated comment modal instead of generic confirm --}}
                                <button type="button"
                                        class="table-action-btn action-danger"
                                        title="Archive User Account"
                                        aria-label="Archive User Account"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal"
                                        data-user-id="{{ $u->id }}"
                                        data-user-name="{{ $u->name }}"
                                        data-user-role="{{ $u->roles->first()?->name ?? 'No Role' }}"
                                        data-action-url="{{ route('admin.users.destroy', $u) }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No users found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div id="users-pagination-container">
        @if($users->hasPages())
            <div class="card-footer">{{ $users->links() }}</div>
        @endif
    </div>
</div>

<div class="card mt-4" id="archived-users-card">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <h5 class="mb-0 text-secondary"><i class="bi bi-archive me-2"></i>Archived Accounts</h5>
        <span class="badge bg-secondary" id="archived-users-count">{{ count($archivedUsers) }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Email</th><th>Employee ID</th><th>Department</th><th>Role</th><th>Actions</th></tr></thead>
                <tbody id="archived-users-table-body">
                @forelse($archivedUsers as $u)
                <tr>
                    <td class="fw-semibold text-muted">{{ $u->name }}</td>
                    <td class="text-muted">{{ $u->email }}</td>
                    <td class="text-muted">{{ $u->employee_id ?? '—' }}</td>
                    <td class="text-muted">{{ $u->department ?? '—' }}</td>
                    <td>@foreach($u->roles as $r)<span class="badge bg-secondary me-1">{{ $r->name }}</span>@endforeach</td>
                    <td>
                        <div class="table-actions">
                                {{-- View archived profile --}}
                                <a href="{{ route('admin.users.archived', $u->id) }}"
                                   class="table-action-btn action-view"
                                   title="View Archived Account Details"
                                   aria-label="View Archived Account Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                {{-- Restore: opens dedicated comment modal --}}
                                <button type="button"
                                        class="table-action-btn action-success"
                                        title="Restore User Account"
                                        aria-label="Restore User Account"
                                        data-bs-toggle="modal"
                                        data-bs-target="#restoreUserModal"
                                        data-user-id="{{ $u->id }}"
                                        data-user-name="{{ $u->name }}"
                                        data-user-role="{{ $u->roles->first()?->name ?? 'No Role' }}"
                                        data-action-url="{{ route('admin.users.restore', $u->id) }}">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No archived users found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
{{-- ── Archive User Modal ─────────────────────────────────────────────── --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-semibold" id="deleteUserModalLabel">
                    <i class="bi bi-archive-fill text-danger me-2"></i>Archive User Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteUserForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger py-2 mb-3" role="alert" style="font-size:.875rem;">
                        You are about to archive this user account. They will immediately lose all system access.
                    </div>
                    <div class="mb-3">
                        <div class="d-flex gap-3 mb-1">
                            <span class="text-muted" style="min-width:48px;font-size:.8rem;">User</span>
                            <span class="fw-semibold" id="deleteUserName"></span>
                        </div>
                        <div class="d-flex gap-3">
                            <span class="text-muted" style="min-width:48px;font-size:.8rem;">Role</span>
                            <span id="deleteUserRole"></span>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label for="deleteUserComment" class="form-label fw-semibold mb-1" style="font-size:.875rem;">
                            Reason / Comment <span class="text-danger">*</span>
                        </label>
                        <textarea id="deleteUserComment"
                                  name="comment"
                                  class="form-control"
                                  rows="4"
                                  maxlength="1000"
                                  placeholder="Enter the reason for archiving this account..."
                                  required></textarea>
                        <div class="form-text text-muted" style="font-size:.75rem;">This comment is required for audit purposes.</div>
                        <div id="deleteCommentError" class="text-danger mt-1" style="font-size:.8rem;display:none;">A reason is required before archiving.</div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="deleteUserSubmitBtn" class="btn btn-danger btn-sm">
                        <i class="bi bi-archive-fill me-1"></i>Archive Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Restore User Modal ─────────────────────────────────────────────── --}}
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
                    <button type="submit" id="restoreUserSubmitBtn" class="btn btn-success btn-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Restore Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm  = document.getElementById('filter-form');
        const searchInput = document.getElementById('filter-search');
        const roleSelect  = document.getElementById('filter-role');
        const statusSelect= document.getElementById('filter-status');
        let searchTimeout;

        // Submit form and do a normal full-page GET (preserves ?search=&role=&status=)
        function submitFilter() {
            // Remove any stale hidden page input so we go back to page 1
            const stale = filterForm.querySelector('input[name="page"]');
            if (stale) stale.remove();
            filterForm.submit();
        }

        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            submitFilter();
        });

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(submitFilter, 400);
        });

        roleSelect.addEventListener('change', submitFilter);
        statusSelect.addEventListener('change', submitFilter);

        // ── Archive User Modal wiring ───────────────────────────────────────
        const deleteModal    = document.getElementById('deleteUserModal');
        const deleteForm     = document.getElementById('deleteUserForm');
        const deleteComment  = document.getElementById('deleteUserComment');
        const deleteErrMsg   = document.getElementById('deleteCommentError');

        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (e) {
                const btn = e.relatedTarget;
                deleteForm.action          = btn.dataset.actionUrl;
                document.getElementById('deleteUserName').textContent = btn.dataset.userName;
                document.getElementById('deleteUserRole').textContent = btn.dataset.userRole;
                deleteComment.value        = '';
                deleteErrMsg.style.display = 'none';
                deleteComment.classList.remove('is-invalid');
            });
            deleteModal.addEventListener('hidden.bs.modal', function () {
                deleteComment.value        = '';
                deleteErrMsg.style.display = 'none';
                deleteComment.classList.remove('is-invalid');
            });
            deleteForm.addEventListener('submit', function (e) {
                if (!deleteComment.value.trim()) {
                    e.preventDefault();
                    deleteErrMsg.style.display = 'block';
                    deleteComment.classList.add('is-invalid');
                    deleteComment.focus();
                }
            });
        }

        // ── Restore User Modal wiring ───────────────────────────────────────
        const restoreModal   = document.getElementById('restoreUserModal');
        const restoreForm    = document.getElementById('restoreUserForm');
        const restoreComment = document.getElementById('restoreUserComment');
        const restoreErrMsg  = document.getElementById('restoreCommentError');

        if (restoreModal) {
            restoreModal.addEventListener('show.bs.modal', function (e) {
                const btn = e.relatedTarget;
                restoreForm.action           = btn.dataset.actionUrl;
                document.getElementById('restoreUserName').textContent = btn.dataset.userName;
                document.getElementById('restoreUserRole').textContent = btn.dataset.userRole;
                restoreComment.value         = '';
                restoreErrMsg.style.display  = 'none';
                restoreComment.classList.remove('is-invalid');
            });
            restoreModal.addEventListener('hidden.bs.modal', function () {
                restoreComment.value         = '';
                restoreErrMsg.style.display  = 'none';
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
