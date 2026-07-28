@extends('layouts.app')
@section('title', 'Prescriptions')
@section('page-title', 'Prescriptions')
@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <form class="d-flex gap-2 flex-grow-1" method="GET" id="filter-form">
            <input type="text" name="search" id="filter-search" class="form-control form-control-sm" placeholder="Rx no, patient name…" value="{{ request('search') }}" style="max-width:220px">
            <select name="status" id="filter-status" class="form-select form-select-sm" style="max-width:160px">
                <option value="">All Statuses</option>
                @foreach($statuses as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ $s }}</option>@endforeach
            </select>
            <button class="btn btn-primary btn-sm d-none">Filter</button>
            @if(request()->hasAny(['search','status']))<a href="{{ route('pharmacy.prescriptions.index') }}" class="btn btn-outline-secondary btn-sm" id="filter-clear">Clear</a>@endif
        </form>
        @if(auth()->user()->hasAnyRole(['admin','doctor']))
            <a href="{{ route('pharmacy.prescriptions.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>New Prescription</a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Rx No</th><th>Patient</th><th>Doctor</th><th>Diagnosis</th><th>Items</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody id="prescriptions-table-body">
                @forelse($prescriptions as $rx)
                <tr>
                    <td class="fw-semibold"><a href="{{ route('pharmacy.prescriptions.show', $rx) }}">{{ $rx->prescription_no }}</a></td>
                    <td>{{ $rx->patient->last_name }}, {{ $rx->patient->first_name }}</td>
                    <td>{{ $rx->doctor->name }}</td>
                    <td>{{ Str::limit($rx->diagnosis, 30) }}</td>
                    <td><span class="badge bg-info text-dark">{{ $rx->items->count() }}</span></td>
                    <td><span class="badge bg-{{ $rx->statusBadge }}">{{ $rx->status }}</span></td>
                    <td><small>{{ $rx->prescribed_at?->format('M d, Y') }}</small></td>
                    <td>
                        <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        @if($rx->status==='Pending' && auth()->user()->hasAnyRole(['admin','pharmacist']))
                            <form method="POST" action="{{ route('pharmacy.prescriptions.verify', $rx) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-success">Verify</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No prescriptions found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div id="prescriptions-pagination-container">
        @if($prescriptions->hasPages())<div class="card-footer">{{ $prescriptions->links() }}</div>@endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('filter-search');
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
            
            window.history.pushState(null, '', newUrl);

            fetch(newUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                document.getElementById('prescriptions-table-body').innerHTML = doc.getElementById('prescriptions-table-body').innerHTML;
                document.getElementById('prescriptions-pagination-container').innerHTML = doc.getElementById('prescriptions-pagination-container').innerHTML;
                
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
            .catch(err => console.error('Error filtering prescriptions:', err));
        }

        function setupClearListener() {
            const clearBtn = document.getElementById('filter-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    searchInput.value = '';
                    statusSelect.value = '';
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
        setupClearListener();

        // Intercept pagination clicks dynamically
        document.addEventListener('click', function (e) {
            const pageLink = e.target.closest('#prescriptions-pagination-container a');
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
