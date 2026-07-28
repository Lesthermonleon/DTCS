@extends('layouts.app')
@section('title', 'Diet Plans')
@section('page-title', 'Therapeutic Diet Plans')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('diet.dashboard') }}">DNMS</a></li>
    <li class="breadcrumb-item active">Diet Plans</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        {{-- Search & Filters --}}
        <form class="d-flex gap-2 flex-grow-1" method="GET" id="filter-form">
            <input type="text" name="search" id="filter-search" class="form-control form-control-sm" placeholder="Search plan no, patient…" value="{{ request('search') }}" style="max-width:220px">
            <select name="status" id="filter-status" class="form-select form-select-sm" style="max-width:145px">
                <option value="">All Statuses</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <button class="btn btn-primary btn-sm d-none">Filter</button>
            @if(request()->hasAny(['search','status']))
                <a href="{{ route('diet.plans.index') }}" class="btn btn-outline-secondary btn-sm" id="filter-clear">Clear</a>
            @endif
        </form>
        @if(auth()->user()->hasAnyRole(['admin','dietitian']))
            <a href="{{ route('diet.plans.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i>New Plan</a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Plan No</th><th>Patient</th><th>Diet Type</th>
                        <th>Dietitian</th><th>Daily Calories</th><th>Duration</th>
                        <th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody id="diet-plans-table-body">
                @forelse($plans as $plan)
                <tr>
                    <td><a href="{{ route('diet.plans.show', $plan) }}" class="fw-semibold">{{ $plan->plan_no }}</a></td>
                    <td>
                        {{ $plan->dietRequest->patient->last_name ?? '—' }}, {{ $plan->dietRequest->patient->first_name ?? '' }}<br>
                        <small class="text-muted">{{ $plan->dietRequest->patient->patient_no ?? '—' }}</small>
                    </td>
                    <td><span class="badge bg-info text-dark" style="font-size: 0.78rem;">{{ $plan->dietRequest->diet_type ?? '—' }}</span></td>
                    <td>{{ $plan->dietitian->name }}</td>
                    <td class="fw-bold text-success">{{ $plan->total_calories ?? '—' }} kcal</td>
                    <td>
                        <small>
                            {{ $plan->start_date?->format('M d, Y') }} — 
                            {{ $plan->end_date ? $plan->end_date->format('M d, Y') : 'Ongoing' }}
                        </small>
                    </td>
                    <td><span class="badge bg-{{ $plan->statusBadge }}">{{ $plan->status }}</span></td>
                    <td>
                        <a href="{{ route('diet.plans.show', $plan) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                        @if($plan->status !== 'Completed' && auth()->user()->hasAnyRole(['admin','dietitian']))
                            <a href="{{ route('diet.plans.edit', $plan) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No diet plans found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div id="diet-plans-pagination-container">
        @if($plans->hasPages())
            <div class="card-footer">{{ $plans->links() }}</div>
        @endif
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
                
                document.getElementById('diet-plans-table-body').innerHTML = doc.getElementById('diet-plans-table-body').innerHTML;
                document.getElementById('diet-plans-pagination-container').innerHTML = doc.getElementById('diet-plans-pagination-container').innerHTML;
                
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
            .catch(err => console.error('Error filtering diet plans:', err));
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
            const pageLink = e.target.closest('#diet-plans-pagination-container a');
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
