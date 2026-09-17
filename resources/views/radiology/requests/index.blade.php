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
        @if(auth()->user()->hasRole('doctor'))
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
                        @php
                            $rowActions = [
                                [
                                    'type' => 'link',
                                    'url' => route('radiology.requests.show', $req),
                                    'title' => 'View Request Details',
                                    'icon' => 'bi-eye',
                                    'btn_class' => 'action-view',
                                    'text_class' => 'text-primary'
                                ]
                            ];
                            if ($req->status === 'Pending' && auth()->user()->hasRole('rad-tech')) {
                                $rowActions[] = [
                                    'type' => 'form',
                                    'url' => route('radiology.requests.schedule', $req),
                                    'method' => 'PATCH',
                                    'title' => 'Schedule Procedure',
                                    'icon' => 'bi-calendar-event',
                                    'btn_class' => 'action-success',
                                    'text_class' => 'text-success'
                                ];
                            } elseif ($req->status === 'Scheduled' && auth()->user()->hasRole('rad-tech')) {
                                $rowActions[] = [
                                    'type' => 'form',
                                    'url' => route('radiology.requests.start', $req),
                                    'method' => 'PATCH',
                                    'title' => 'Start Imaging Procedure',
                                    'icon' => 'bi-play-circle',
                                    'btn_class' => 'action-view',
                                    'text_class' => 'text-primary'
                                ];
                            } elseif (in_array($req->status, ['Scheduled', 'In Progress']) && auth()->user()->hasRole('rad-tech')) {
                                $rowActions[] = [
                                    'type' => 'form',
                                    'url' => route('radiology.requests.complete', $req),
                                    'method' => 'PATCH',
                                    'title' => 'Complete Procedure',
                                    'icon' => 'bi-check-circle',
                                    'btn_class' => 'action-info',
                                    'text_class' => 'text-info',
                                    'confirm' => 'Complete procedure and send study for radiologist interpretation?'
                                ];
                            } elseif (in_array($req->status, ['Completed', 'In Progress']) && auth()->user()->hasRole('radiologist') && !$req->report) {
                                $rowActions[] = [
                                    'type' => 'link',
                                    'url' => route('radiology.reports.create') . '?radiology_request_id=' . $req->id,
                                    'title' => 'Create Diagnostic Report',
                                    'icon' => 'bi-journal-medical',
                                    'btn_class' => 'action-success',
                                    'text_class' => 'text-success'
                                ];
                            } elseif ($req->report) {
                                $rowActions[] = [
                                    'type' => 'link',
                                    'url' => route('radiology.reports.show', $req->report),
                                    'title' => 'View Diagnostic Report',
                                    'icon' => 'bi-file-earmark-medical',
                                    'btn_class' => 'action-info',
                                    'text_class' => 'text-info'
                                ];
                            }

                            if ($req->status === 'Pending' && auth()->user()->hasRole('doctor')) {
                                $rowActions[] = [
                                    'type' => 'link',
                                    'url' => route('radiology.requests.edit', $req),
                                    'title' => 'Edit Request',
                                    'icon' => 'bi-pencil',
                                    'btn_class' => 'action-edit',
                                    'text_class' => 'text-success'
                                ];
                            }

                            $actionCount = count($rowActions);
                            $directLimit = ($actionCount <= 3) ? $actionCount : 2;
                        @endphp
                        <div class="table-actions">
                            @foreach(array_slice($rowActions, 0, $directLimit) as $act)
                                @if($act['type'] === 'link')
                                    <a href="{{ $act['url'] }}" class="table-action-btn {{ $act['btn_class'] }}" title="{{ $act['title'] }}" aria-label="{{ $act['title'] }}">
                                        <i class="bi {{ $act['icon'] }}"></i>
                                    </a>
                                @elseif($act['type'] === 'form')
                                    <form action="{{ $act['url'] }}" method="POST" class="d-inline">
                                        @csrf
                                        @method($act['method'] ?? 'POST')
                                        <button type="submit" class="table-action-btn {{ $act['btn_class'] }}" title="{{ $act['title'] }}" aria-label="{{ $act['title'] }}" @if(!empty($act['confirm'])) data-confirm="{{ $act['confirm'] }}" @endif>
                                            <i class="bi {{ $act['icon'] }}"></i>
                                        </button>
                                    </form>
                                @endif
                            @endforeach

                            @if($actionCount > 3)
                                <div class="dropdown d-inline">
                                    <button class="table-action-btn dropdown-toggle no-arrow" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions" aria-label="More Actions">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                                        @foreach(array_slice($rowActions, $directLimit) as $act)
                                            <li>
                                                @if($act['type'] === 'link')
                                                    <a class="dropdown-item small" href="{{ $act['url'] }}">
                                                        <i class="bi {{ $act['icon'] }} me-2 {{ $act['text_class'] ?? '' }}"></i>{{ $act['title'] }}
                                                    </a>
                                                @elseif($act['type'] === 'form')
                                                    <form action="{{ $act['url'] }}" method="POST">
                                                        @csrf
                                                        @method($act['method'] ?? 'POST')
                                                        <button type="submit" class="dropdown-item small {{ $act['text_class'] ?? '' }}" @if(!empty($act['confirm'])) data-confirm="{{ $act['confirm'] }}" @endif>
                                                            <i class="bi {{ $act['icon'] }} me-2"></i>{{ $act['title'] }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
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
