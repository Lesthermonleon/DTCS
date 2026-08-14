@extends('layouts.app')
@section('title', 'Lab Requests')
@section('page-title', 'Laboratory Requests')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}">LIS</a></li>
    <li class="breadcrumb-item active">Requests</li>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        {{-- Search & Filters --}}
        <form class="d-flex gap-2 flex-wrap flex-grow-1" method="GET" id="filter-form">
            <input type="text" name="search" id="filter-search" class="form-control form-control-sm" placeholder="Search request no, patient…" value="{{ request('search') }}" style="max-width:220px">
            <select name="status" id="filter-status" class="form-select form-select-sm" style="max-width:140px">
                <option value="">All Statuses</option>
                @foreach($statuses as $s)<option value="{{ $s }}" {{ request('status')===$s ? 'selected':'' }}>{{ $s }}</option>@endforeach
            </select>
            <select name="priority" id="filter-priority" class="form-select form-select-sm" style="max-width:120px">
                <option value="">All Priorities</option>
                @foreach($priorities as $p)<option value="{{ $p }}" {{ request('priority')===$p ? 'selected':'' }}>{{ $p }}</option>@endforeach
            </select>
            <button class="btn btn-primary btn-sm d-none">Filter</button>
            @if(request()->hasAny(['search','status','priority']))
                <a href="{{ route('lab.requests.index') }}" class="btn btn-outline-secondary btn-sm" id="filter-clear">Clear</a>
            @endif
        </form>
        @if(auth()->user()->hasAnyRole(['admin','doctor']))
            <a href="{{ route('lab.requests.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>New Request</a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Request No</th><th>Patient</th><th>Doctor</th>
                        <th>Tests</th><th>Priority</th><th>Specimen</th>
                        <th>Status</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="lab-requests-table-body">
                @forelse($labRequests as $req)
                <tr>
                    <td><a href="{{ route('lab.requests.show', $req) }}" class="fw-semibold">{{ $req->request_no }}</a></td>
                    <td>
                        {{ $req->patient->last_name }}, {{ $req->patient->first_name }}<br>
                        <small class="text-muted">{{ $req->patient->patient_no }}</small>
                    </td>
                    <td>{{ $req->doctor->name }}</td>
                    <td><span class="badge bg-info text-dark">{{ $req->items->count() }} test(s)</span></td>
                    <td><span class="badge bg-{{ $req->priority==='STAT'?'danger':($req->priority==='Urgent'?'warning text-dark':'secondary') }}">{{ $req->priority }}</span></td>
                    <td>{{ $req->specimen_type }}</td>
                    <td><span class="badge bg-{{ $req->statusBadge }}">{{ $req->status }}</span></td>
                    <td><small>{{ $req->requested_at->format('M d, Y H:i') }}</small></td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ route('lab.requests.show', $req) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                            @if($req->status==='Pending' && auth()->user()->hasAnyRole(['admin','med-tech']))
                                <form method="POST" action="{{ route('lab.requests.receive', $req) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Received" data-confirm="Are you sure you want to mark {{ $req->request_no }} as received?"><i class="bi bi-inbox-fill"></i></button>
                                </form>
                            @else
                                <a href="{{ route('lab.requests.print', ['labRequest' => $req]) }}" class="btn btn-sm btn-outline-secondary" title="Print" target="_blank"><i class="bi bi-printer"></i></a>
                            @endif

                            <div class="dropdown d-inline">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle no-arrow" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    @if($req->status === 'Pending')
                                        <li><a class="dropdown-item small" href="{{ route('lab.requests.print', ['labRequest' => $req]) }}" target="_blank"><i class="bi bi-printer me-2 text-secondary"></i>Print Request</a></li>
                                        @if(auth()->user()->hasAnyRole(['admin','doctor']))
                                            <li><a class="dropdown-item small" href="{{ route('lab.requests.edit', $req) }}"><i class="bi bi-pencil me-2 text-warning"></i>Edit Request</a></li>
                                        @endif
                                    @else
                                        <li><a class="dropdown-item small" href="{{ route('lab.requests.show', $req) }}"><i class="bi bi-folder2-open me-2 text-primary"></i>Open Record</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No lab requests found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div id="lab-requests-pagination-container">
        @if($labRequests->hasPages())
            <div class="card-footer">{{ $labRequests->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('filter-search');
        const statusSelect = document.getElementById('filter-status');
        const prioritySelect = document.getElementById('filter-priority');
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
                
                document.getElementById('lab-requests-table-body').innerHTML = doc.getElementById('lab-requests-table-body').innerHTML;
                document.getElementById('lab-requests-pagination-container').innerHTML = doc.getElementById('lab-requests-pagination-container').innerHTML;
                
                // Keep Clear button in sync if present
                const clearBtn = document.getElementById('filter-clear');
                const newClearBtn = doc.getElementById('filter-clear');
                if (clearBtn && !newClearBtn) {
                    clearBtn.remove();
                } else if (!clearBtn && newClearBtn) {
                    filterForm.appendChild(newClearBtn);
                    setupClearListener();
                }
            })
            .catch(err => console.error('Error filtering lab requests:', err));
        }

        function setupClearListener() {
            const clearBtn = document.getElementById('filter-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    searchInput.value = '';
                    statusSelect.value = '';
                    prioritySelect.value = '';
                    performFilter(true);
                });
            }
        }

        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            performFilter(true);
        });

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => performFilter(true), 300);
        });

        statusSelect.addEventListener('change', () => performFilter(true));
        prioritySelect.addEventListener('change', () => performFilter(true));
        setupClearListener();

        // Intercept pagination clicks dynamically
        document.addEventListener('click', function (e) {
            const pageLink = e.target.closest('#lab-requests-pagination-container a');
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
