@extends('layouts.app')
@section('title', 'Patients')
@section('page-title', 'Patient Directory')
@section('breadcrumb')
    <li class="breadcrumb-item active">Patients</li>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <form class="d-flex gap-2 flex-grow-1" method="GET" id="filter-form">
            <input type="text" name="search" id="filter-search" class="form-control form-control-sm" placeholder="Name, patient no, phone…" value="{{ request('search') }}" style="max-width:250px">
            <select name="type" id="filter-type" class="form-select form-select-sm" style="max-width:150px">
                <option value="">All Types</option>
                <option value="Inpatient" {{ request('type')==='Inpatient'?'selected':'' }}>Inpatient</option>
                <option value="Outpatient" {{ request('type')==='Outpatient'?'selected':'' }}>Outpatient</option>
            </select>
            <button class="btn btn-primary btn-sm d-none">Filter</button>
            @if(request()->hasAny(['search','type']))<a href="{{ route('patients.index') }}" class="btn btn-outline-secondary btn-sm" id="filter-clear">Clear</a>@endif
        </form>
        @if(auth()->user()->hasAnyRole(['admin','doctor']))
            <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>New Patient</a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Patient No</th><th>Name</th><th>DOB / Age</th><th>Gender</th><th>Blood Type</th><th>Type</th><th>Phone</th><th>Actions</th></tr></thead>
                <tbody id="patients-table-body">
                @forelse($patients as $p)
                <tr>
                    <td class="fw-semibold text-primary">{{ $p->patient_no }}</td>
                    <td>
                        <a href="{{ route('patients.show', $p) }}" class="fw-semibold text-dark text-decoration-none">
                            {{ $p->last_name }}, {{ $p->first_name }} {{ $p->middle_name }}
                        </a>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($p->date_of_birth)->format('M d, Y') }}<br><small class="text-muted">{{ \Carbon\Carbon::parse($p->date_of_birth)->age }} yrs</small></td>
                    <td>{{ $p->gender }}</td>
                    <td>{{ $p->blood_type ?? '—' }}</td>
                    <td><span class="badge bg-{{ $p->patient_type==='Inpatient'?'primary':'success' }}">{{ $p->patient_type }}</span></td>
                    <td>{{ $p->phone ?? '—' }}</td>
                    <td>
                        <a href="{{ route('patients.show', $p) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->hasAnyRole(['admin','doctor']))
                            <a href="{{ route('patients.edit', $p) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No patients found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div id="patients-pagination-container">
        @if($patients->hasPages())
            <div class="card-footer">{{ $patients->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('filter-search');
        const typeSelect = document.getElementById('filter-type');
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
                
                document.getElementById('patients-table-body').innerHTML = doc.getElementById('patients-table-body').innerHTML;
                document.getElementById('patients-pagination-container').innerHTML = doc.getElementById('patients-pagination-container').innerHTML;
                
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
            .catch(err => console.error('Error filtering patients:', err));
        }

        function setupClearListener() {
            const clearBtn = document.getElementById('filter-clear');
            if (clearBtn) {
                clearBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    searchInput.value = '';
                    typeSelect.value = '';
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

        typeSelect.addEventListener('change', () => performFilter(true));
        setupClearListener();

        // Intercept pagination clicks dynamically
        document.addEventListener('click', function (e) {
            const pageLink = e.target.closest('#patients-pagination-container a');
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
