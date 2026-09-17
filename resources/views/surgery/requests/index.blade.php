@extends('layouts.app')
@section('title', 'Surgery Requests')
@section('page-title', 'Surgery Requests')
@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <form class="d-flex gap-2 flex-grow-1" method="GET" id="filter-form">
            <input type="text" name="search" id="filter-search" class="form-control form-control-sm" placeholder="Search request, procedure, patient…" value="{{ request('search') }}" style="max-width:220px">
            <select name="status" id="filter-status" class="form-select form-select-sm" style="max-width:140px">
                <option value="">All Statuses</option>
                @foreach($statuses as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ $s }}</option>@endforeach
            </select>
            <select name="urgency" id="filter-urgency" class="form-select form-select-sm" style="max-width:120px">
                <option value="">All Urgencies</option>
                @foreach($urgencies as $u)<option value="{{ $u }}" {{ request('urgency')===$u?'selected':'' }}>{{ $u }}</option>@endforeach
            </select>
            <button class="btn btn-primary btn-sm d-none">Filter</button>
            @if(request()->hasAny(['search','status','urgency']))<a href="{{ route('surgery.requests.index') }}" class="btn btn-outline-secondary btn-sm" id="filter-clear">Clear</a>@endif
        </form>
        @if(auth()->user()->hasRole('doctor'))
            <a href="{{ route('surgery.requests.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>New Request</a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Request No</th><th>Patient</th><th>Procedure</th><th>Urgency</th><th>Status</th><th>Doctor</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody id="surgery-requests-table-body">
                @forelse($surgeryRequests as $sr)
                <tr>
                    <td><a href="{{ route('surgery.requests.show', $sr) }}" class="fw-semibold">{{ $sr->request_no }}</a></td>
                    <td>{{ $sr->patient->last_name }}, {{ $sr->patient->first_name }}</td>
                    <td>{{ $sr->procedure_name }}</td>
                    <td><span class="badge bg-{{ $sr->urgency==='Emergency'?'danger':($sr->urgency==='Urgent'?'warning text-dark':'secondary') }}">{{ $sr->urgency }}</span></td>
                    <td><span class="badge bg-{{ $sr->statusBadge }}">{{ $sr->status }}</span></td>
                    <td>{{ $sr->doctor->name }}</td>
                    <td><small>{{ $sr->requested_at?->format('M d, Y') }}</small></td>
                    <td>
                        @php
                            $rowActions = [
                                [
                                    'type' => 'link',
                                    'url' => route('surgery.requests.show', $sr),
                                    'title' => 'View Request Details',
                                    'icon' => 'bi-eye',
                                    'btn_class' => 'action-view',
                                    'text_class' => 'text-primary'
                                ]
                            ];
                            if ($sr->status === 'Pending' && auth()->user()->hasRole('or-coordinator')) {
                                $rowActions[] = [
                                    'type' => 'link',
                                    'url' => route('surgery.schedules.create') . '?request=' . $sr->id,
                                    'title' => 'Schedule Surgery',
                                    'icon' => 'bi-calendar-plus',
                                    'btn_class' => 'action-success',
                                    'text_class' => 'text-success'
                                ];
                            }
                            if ($sr->status === 'Pending' && auth()->user()->hasRole('doctor')) {
                                $rowActions[] = [
                                    'type' => 'link',
                                    'url' => route('surgery.requests.edit', $sr),
                                    'title' => 'Edit Request',
                                    'icon' => 'bi-pencil',
                                    'btn_class' => 'action-edit',
                                    'text_class' => 'text-success'
                                ];
                            }
                            if ($sr->status === 'Pending' && auth()->user()->hasAnyRole(['doctor','or-coordinator'])) {
                                $rowActions[] = [
                                    'type' => 'form',
                                    'url' => route('surgery.requests.cancel', $sr),
                                    'method' => 'PATCH',
                                    'title' => 'Cancel Request',
                                    'icon' => 'bi-x-circle',
                                    'btn_class' => 'action-danger',
                                    'text_class' => 'text-danger',
                                    'confirm' => "Are you sure you want to cancel surgery request {$sr->request_no}?"
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
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
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
                <tr><td colspan="8" class="text-center py-4 text-muted">No surgery requests found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div id="surgery-requests-pagination-container">
        @if($surgeryRequests->hasPages())<div class="card-footer">{{ $surgeryRequests->links() }}</div>@endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('filter-search');
        const statusSelect = document.getElementById('filter-status');
        const urgencySelect = document.getElementById('filter-urgency');
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
                
                document.getElementById('surgery-requests-table-body').innerHTML = doc.getElementById('surgery-requests-table-body').innerHTML;
                document.getElementById('surgery-requests-pagination-container').innerHTML = doc.getElementById('surgery-requests-pagination-container').innerHTML;
                
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
            .catch(err => console.error('Error filtering surgery requests:', err));
        }

        function setupClearListener() {
            const clearBtn = document.getElementById('filter-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    searchInput.value = '';
                    statusSelect.value = '';
                    urgencySelect.value = '';
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
        urgencySelect.addEventListener('change', () => performFilter(true));
        setupClearListener();

        // Intercept pagination clicks dynamically
        document.addEventListener('click', function (e) {
            const pageLink = e.target.closest('#surgery-requests-pagination-container a');
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
