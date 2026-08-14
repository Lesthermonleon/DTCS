@extends('layouts.app')
@section('title', 'Radiology Requests')
@section('page-title', 'Radiology Requests')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('radiology.dashboard') }}">RIS</a></li>
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
            <select name="modality" id="filter-modality" class="form-select form-select-sm" style="max-width:140px">
                <option value="">All Modalities</option>
                @foreach($modalities as $m)<option value="{{ $m }}" {{ request('modality')===$m ? 'selected':'' }}>{{ $m }}</option>@endforeach
            </select>
            <button class="btn btn-primary btn-sm d-none">Filter</button>
            @if(request()->hasAny(['search','status','modality']))
                <a href="{{ route('radiology.requests.index') }}" class="btn btn-outline-secondary btn-sm" id="filter-clear">Clear</a>
            @endif
        </form>
        @if(auth()->user()->hasAnyRole(['admin','doctor']))
            <a href="{{ route('radiology.requests.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>New Request</a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Request No</th><th>Patient</th><th>Doctor</th>
                        <th>Modality</th><th>Body Part</th><th>Priority</th>
                        <th>Status</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rad-requests-table-body">
                @forelse($radiologyRequests as $req)
                <tr>
                    <td><a href="{{ route('radiology.requests.show', $req) }}" class="fw-semibold">{{ $req->request_no }}</a></td>
                    <td>
                        {{ $req->patient->last_name }}, {{ $req->patient->first_name }}<br>
                        <small class="text-muted">{{ $req->patient->patient_no }}</small>
                    </td>
                    <td>{{ $req->doctor->name }}</td>
                    <td class="fw-semibold text-secondary">{{ $req->modality }}</td>
                    <td>{{ $req->body_part }}</td>
                    <td><span class="badge bg-{{ $req->priority==='STAT'?'danger':($req->priority==='Urgent'?'warning text-dark':'secondary') }}">{{ $req->priority }}</span></td>
                    <td><span class="badge bg-{{ $req->statusBadge }}">{{ $req->status }}</span></td>
                    <td><small>{{ $req->requested_at->format('M d, Y H:i') }}</small></td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ route('radiology.requests.show', $req) }}" class="btn btn-sm btn-outline-primary" title="View Request Details"><i class="bi bi-eye"></i></a>
                            
                            {{-- Primary State Action --}}
                            @if($req->status === 'Pending' && auth()->user()->hasAnyRole(['admin', 'rad-tech']))
                                <form method="POST" action="{{ route('radiology.requests.schedule', $req) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Schedule Procedure"><i class="bi bi-calendar-event"></i></button>
                                </form>
                            @elseif($req->status === 'Scheduled' && auth()->user()->hasAnyRole(['admin', 'rad-tech']))
                                <form method="POST" action="{{ route('radiology.requests.start', $req) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Start Imaging Procedure"><i class="bi bi-play-circle"></i></button>
                                </form>
                            @elseif(in_array($req->status, ['Scheduled', 'In Progress']) && auth()->user()->hasAnyRole(['admin', 'rad-tech']))
                                <form method="POST" action="{{ route('radiology.requests.complete', $req) }}" class="d-inline" data-confirm="Complete procedure and send study for radiologist interpretation?">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Complete Procedure"><i class="bi bi-check-circle"></i></button>
                                </form>
                            @elseif(in_array($req->status, ['Completed', 'In Progress']) && auth()->user()->hasAnyRole(['admin', 'radiologist']) && !$req->report)
                                <a href="{{ route('radiology.reports.create') }}?radiology_request_id={{ $req->id }}" class="btn btn-sm btn-outline-success" title="Create Diagnostic Report"><i class="bi bi-journal-medical"></i></a>
                            @elseif($req->report)
                                <a href="{{ route('radiology.reports.show', $req->report) }}" class="btn btn-sm btn-outline-info" title="View Diagnostic Report"><i class="bi bi-file-earmark-medical"></i></a>
                            @endif

                            {{-- Meatballs Menu Dropdown --}}
                            <div class="dropdown d-inline">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle no-arrow" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    @if($req->status === 'Pending' && auth()->user()->hasAnyRole(['admin', 'doctor']))
                                        <li><a class="dropdown-item small" href="{{ route('radiology.requests.edit', $req) }}"><i class="bi bi-pencil me-2 text-warning"></i>Edit Request</a></li>
                                    @endif
                                    @if($req->report)
                                        <li><a class="dropdown-item small" href="{{ route('radiology.reports.show', $req->report) }}"><i class="bi bi-file-earmark-medical me-2 text-info"></i>View Diagnostic Report</a></li>
                                    @endif
                                    <li><a class="dropdown-item small" href="{{ route('radiology.requests.show', $req) }}"><i class="bi bi-folder2-open me-2 text-primary"></i>Open Request Record</a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No radiology requests found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div id="rad-requests-pagination-container">
        @if($radiologyRequests->hasPages())
            <div class="card-footer">{{ $radiologyRequests->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('filter-search');
        const statusSelect = document.getElementById('filter-status');
        const modalitySelect = document.getElementById('filter-modality');
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
                
                document.getElementById('rad-requests-table-body').innerHTML = doc.getElementById('rad-requests-table-body').innerHTML;
                document.getElementById('rad-requests-pagination-container').innerHTML = doc.getElementById('rad-requests-pagination-container').innerHTML;
                
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
            .catch(err => console.error('Error filtering radiology requests:', err));
        }

        function setupClearListener() {
            const clearBtn = document.getElementById('filter-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    searchInput.value = '';
                    statusSelect.value = '';
                    modalitySelect.value = '';
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
        modalitySelect.addEventListener('change', () => performFilter(true));
        setupClearListener();

        // Intercept pagination clicks dynamically
        document.addEventListener('click', function (e) {
            const pageLink = e.target.closest('#rad-requests-pagination-container a');
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
