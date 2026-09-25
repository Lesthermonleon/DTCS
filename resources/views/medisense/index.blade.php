@extends('layouts.app')
@section('title', 'MediSense AI — Patient Selection')
@section('page-title', 'Virtual MediSense AI')
@section('breadcrumb')
    <li class="breadcrumb-item active">MediSense</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <form class="d-flex gap-2 flex-wrap flex-grow-1" method="GET" id="filter-form">
            <input type="text" name="search" id="filter-search"
                   class="form-control form-control-sm"
                   placeholder="Search patient ID, name, or ward…"
                   value="{{ request('search') }}"
                   style="max-width:260px">
            <select name="type" id="filter-type" class="form-select form-select-sm" style="max-width:150px">
                <option value="">All Types</option>
                <option value="Inpatient"  {{ request('type') === 'Inpatient'  ? 'selected' : '' }}>Inpatient</option>
                <option value="Outpatient" {{ request('type') === 'Outpatient' ? 'selected' : '' }}>Outpatient</option>
            </select>
            <button class="btn btn-primary btn-sm d-none">Filter</button>
            @if(request()->hasAny(['search','type']))
                <a href="{{ route('medisense.index') }}" class="btn btn-outline-secondary btn-sm" id="filter-clear">Clear</a>
            @endif
        </form>
        <span class="badge bg-success-subtle text-success border border-success-subtle">
            <i class="bi bi-shield-check me-1"></i>Doctor-Authorized Access
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Patient No</th>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Type</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody id="medisense-table-body">
                @forelse($patients as $patient)
                    <tr style="cursor:pointer" data-href="{{ route('medisense.show', $patient) }}"
                        title="Select {{ $patient->full_name }}" class="medisense-patient-row">
                        <td style="color:var(--signal-dark);font-family:var(--font-mono);font-size:.82rem;">
                            {{ $patient->patient_no }}
                        </td>
                        <td class="fw-semibold">{{ $patient->full_name }}</td>
                        <td>{{ $patient->gender }}</td>
                        <td>
                            <span class="badge hims-badge-{{ strtolower($patient->patient_type) === 'inpatient' ? 'blue' : 'green' }}">
                                {{ $patient->patient_type }}
                            </span>
                        </td>
                        <td class="text-muted">
                            @if($patient->ward)
                                <i class="bi bi-hospital me-1"></i>{{ $patient->ward }}{{ $patient->bed_number ? ' / Bed ' . $patient->bed_number : '' }}
                            @else
                                <i class="bi bi-door-open me-1"></i>OPD
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No patients found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="medisense-pagination-container">
        @if($patients->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center gap-2">
                <small class="text-muted">
                    Showing {{ $patients->firstItem() }}–{{ $patients->lastItem() }} of {{ number_format($patients->total()) }} patients
                </small>
                {{ $patients->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('filter-search');
    const typeSelect  = document.getElementById('filter-type');
    const filterForm  = document.getElementById('filter-form');
    let searchTimeout;

    function performFilter(resetPage = true) {
        if (resetPage) {
            const p = filterForm.querySelector('input[name="page"]');
            if (p) p.value = '1';
        }
        const params = new URLSearchParams(new FormData(filterForm));
        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.replaceState(null, '', newUrl);

        fetch(newUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                document.getElementById('medisense-table-body').innerHTML =
                    doc.getElementById('medisense-table-body').innerHTML;
                document.getElementById('medisense-pagination-container').innerHTML =
                    doc.getElementById('medisense-pagination-container').innerHTML;

                // sync Clear button
                const clear    = document.getElementById('filter-clear');
                const newClear = doc.getElementById('filter-clear');
                if (clear && !newClear) clear.remove();
                else if (!clear && newClear) { filterForm.appendChild(newClear); setupClear(); }

                // re-bind row clicks after tbody swap
                bindRows();
            })
            .catch(err => console.error('MediSense filter error:', err));
    }

    function setupClear() {
        const btn = document.getElementById('filter-clear');
        if (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                searchInput.value = '';
                typeSelect.value  = '';
                performFilter(true);
            });
        }
    }

    function bindRows() {
        document.querySelectorAll('.medisense-patient-row[data-href]').forEach(function (row) {
            row.addEventListener('click', function () {
                window.location.href = this.dataset.href;
            });
        });
    }

    filterForm.addEventListener('submit', function (e) { e.preventDefault(); performFilter(true); });
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => performFilter(true), 300);
    });
    typeSelect.addEventListener('change', () => performFilter(true));
    setupClear();
    bindRows();

    // intercept pagination link clicks
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#medisense-pagination-container a');
        if (!link) return;
        e.preventDefault();
        const page = new URL(link.href).searchParams.get('page');
        let pi = filterForm.querySelector('input[name="page"]');
        if (!pi) {
            pi = document.createElement('input');
            pi.type = 'hidden';
            pi.name = 'page';
            filterForm.appendChild(pi);
        }
        pi.value = page;
        performFilter(false);
    });
});
</script>
@endpush
@endsection
