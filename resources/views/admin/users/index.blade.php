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
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Add User</a>
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
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-warning" title="Edit User"><i class="bi bi-pencil"></i></a>
                        @if($u->locked_at)
                            <form method="POST" action="{{ route('admin.users.unlock', $u) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-info" title="Unlock Account"
                                        data-confirm="Unlock {{ $u->name }}'s account? They will be able to log in again."
                                        data-confirm-title="Unlock Account"
                                        data-confirm-btn="btn-info"
                                        data-confirm-icon="bi-unlock-fill"
                                        data-confirm-action-text="Unlock Account"><i class="bi bi-unlock"></i></button>
                            </form>
                        @endif
                        @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User"
                                        data-confirm="WARNING: Are you sure you want to archive {{ $u->name }}'s account? They will lose access immediately."
                                        data-confirm-title="Archive User Account"
                                        data-confirm-btn="btn-danger"
                                        data-confirm-icon="bi-archive-fill"
                                        data-confirm-action-text="Archive Account"><i class="bi bi-trash"></i></button>
                            </form>
                        @endif
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
                        <form method="POST" action="{{ route('admin.users.restore', $u->id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore User"
                                    data-confirm="Are you sure you want to restore {{ $u->name }}'s account?"
                                    data-confirm-title="Restore User Account"
                                    data-confirm-btn="btn-success"
                                    data-confirm-icon="bi-arrow-counterclockwise"
                                    data-confirm-action-text="Restore Account"><i class="bi bi-arrow-counterclockwise"></i></button>
                        </form>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('filter-search');
        const roleSelect = document.getElementById('filter-role');
        const statusSelect = document.getElementById('filter-status');
        const filterForm = document.getElementById('filter-form');
        
        let searchTimeout;

        function performFilter(resetPage = true) {
            if (resetPage) {
                let pageInput = filterForm.querySelector('input[name="page"]');
                if (pageInput) {
                    pageInput.value = '1';
                }
            }
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            const newUrl = `${window.location.pathname}?${params.toString()}`;
            
            window.history.replaceState(null, '', newUrl);

            fetch(newUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                document.getElementById('users-table-body').innerHTML = doc.getElementById('users-table-body').innerHTML;
                document.getElementById('users-pagination-container').innerHTML = doc.getElementById('users-pagination-container').innerHTML;
                document.getElementById('archived-users-table-body').innerHTML = doc.getElementById('archived-users-table-body').innerHTML;
                
                const countBadge = document.getElementById('archived-users-count');
                const newCountBadge = doc.getElementById('archived-users-count');
                if (countBadge && newCountBadge) {
                    countBadge.textContent = newCountBadge.textContent;
                }
            })
            .catch(err => console.error('Error filtering users:', err));
        }

        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            performFilter(true);
        });

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => performFilter(true), 300);
        });

        roleSelect.addEventListener('change', () => performFilter(true));
        statusSelect.addEventListener('change', () => performFilter(true));

        // Intercept pagination clicks dynamically
        document.addEventListener('click', function (e) {
            const pageLink = e.target.closest('#users-pagination-container a');
            if (pageLink) {
                e.preventDefault();
                const urlObj = new URL(pageLink.href);
                const page = urlObj.searchParams.get('page');
                
                let pageInput = filterForm.querySelector('input[name="page"]');
                if (!pageInput) {
                    pageInput = document.createElement('input');
                    pageInput.type = 'hidden';
                    pageInput.name = 'page';
                    filterForm.appendChild(pageInput);
                }
                pageInput.value = page;
                performFilter(false);
            }
        });
    });
</script>
@endpush
@endsection
