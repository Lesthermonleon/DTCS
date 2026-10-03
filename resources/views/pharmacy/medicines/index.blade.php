@extends('layouts.app')
@section('title', 'Medicine Availability')
@section('page-title', 'Medicine Availability')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('pharmacy.dashboard') }}" class="text-decoration-none">Pharmacy</a></li>
    <li class="breadcrumb-item active">Medicine Availability</li>
@endsection

@section('content')

{{-- ── Provider Error Banner (if stock provider is unavailable) ── --}}
@if(!empty($providerError))
<div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4 role="alert"">
    <i class="bi bi-exclamation-octagon-fill fs-4 me-3 text-danger"></i>
    <div>
        <h6 class="alert-heading fw-bold mb-1">Stock Integration Error</h6>
        <p class="mb-0 small">{{ $errorMessage ?: 'Medicine availability is currently unavailable. Please try again later.' }}</p>
    </div>
</div>
@endif


{{-- ── Search & Filter Controls ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('pharmacy.medicines.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-select-sm form-control border-start-0 ps-0" 
                           placeholder="Search medicine or lot number..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="stock" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ request('stock', 'all') === 'all' ? 'selected' : '' }}>All Stock Statuses</option>
                    <option value="in_stock" {{ request('stock') === 'in_stock' ? 'selected' : '' }}>In Stock (> 20)</option>
                    <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low Stock (1 - 20)</option>
                    <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Out of Stock (0)</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="expiry" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ request('expiry', 'all') === 'all' ? 'selected' : '' }}>All Expiration Statuses</option>
                    <option value="unexpired" {{ request('expiry') === 'unexpired' ? 'selected' : '' }}>Unexpired</option>
                    <option value="expiring_soon" {{ request('expiry') === 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (≤ 90d)</option>
                    <option value="expired" {{ request('expiry') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-12 col-md-1 d-grid">
                @if(request()->hasAny(['search', 'stock', 'expiry']))
                    <a href="{{ route('pharmacy.medicines.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @else
                    <button type="submit" class="btn btn-sm btn-secondary" title="Apply Filter">
                        <i class="bi bi-funnel"></i>
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── Main Medicine Availability Table ── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-table me-2 text-success"></i>Available Medication Stock Catalog
        </h6>
        <span class="badge bg-light text-dark border px-2 py-1">
            Showing {{ count($medicines) }} item(s)
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Medicine Name</th>
                        <th>Available Quantity</th>
                        <th>Batch / Lot</th>
                        <th>Expiration Date</th>
                        <th>Stock Status</th>
                        <th>Expiration Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($medicines as $med)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $med['medication_name'] }}</div>
                        </td>
                        <td>
                            <span class="fw-bold fs-6 {{ $med['available_quantity'] == 0 ? 'text-danger' : ($med['available_quantity'] <= 20 ? 'text-warning-emphasis' : 'text-dark') }}">
                                {{ number_format($med['available_quantity']) }}
                            </span>
                            <span class="text-muted small">units</span>
                        </td>
                        <td>
                            <span class="font-monospace small text-muted">{{ $med['lot_number'] ?: 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $med['expiry_date'] ?: 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $med['stock_badge_class'] }} px-2 py-1">
                                {{ $med['stock_status_label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $med['expiry_badge_class'] }} px-2 py-1">
                                {{ $med['expiry_status_label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-2 d-block mb-2 text-muted"></i>
                            <h6 class="fw-bold mb-1">No medicine availability data</h6>
                            <p class="small text-muted mb-0">
                                @if(request()->hasAny(['search', 'stock', 'expiry']))
                                    No stock records match your selected search or filter criteria.
                                @else
                                    No stock information is currently available from the connected stock source.
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
