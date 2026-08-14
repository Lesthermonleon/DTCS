@extends('layouts.app')
@section('title', 'Dispensing History — Pharmacy')
@section('page-title', 'Medication Dispensing History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 text-dark fw-bold"><i class="bi bi-capsule me-2 text-primary"></i>Medication Dispensing History</h4>
        <p class="text-muted small mb-0">Track and manage pharmaceutical dispensing records across all verified prescriptions.</p>
    </div>
    <div>
        <a href="{{ route('pharmacy.dispensing.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Dispense Medication
        </a>
    </div>
</div>

{{-- Summary Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                    <i class="bi bi-capsule fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Dispensed Today</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['dispensed_today']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Dispensed This Month</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['dispensed_month']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                    <i class="bi bi-hourglass-split fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Ready to Dispense</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['ready_to_dispense']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                    <i class="bi bi-receipt fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Total Records</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_dispensings']) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Card --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('pharmacy.dispensing.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 bg-light"
                           placeholder="Search by Rx #, Patient Name, Medication, or Lot #"
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if(request('search'))
                    <a href="{{ route('pharmacy.dispensing.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">ID / Dispensed Date</th>
                        <th>Patient</th>
                        <th>Prescription No</th>
                        <th>Medication & Qty</th>
                        <th>Lot / Batch #</th>
                        <th>Expiry Date</th>
                        <th>Pharmacist</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        @php
                            $item = $record->prescriptionItem;
                            $rx   = $item?->prescription;
                            $pt   = $rx?->patient;
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold text-dark">#DSP-{{ str_pad($record->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <div class="small text-muted"><i class="bi bi-clock me-1"></i>{{ $record->dispensed_at?->format('M d, Y H:i') }}</div>
                            </td>
                            <td>
                                @if($pt)
                                    <div class="fw-semibold text-dark">{{ $pt->full_name }}</div>
                                    <div class="small text-muted"><i class="bi bi-person-v me-1"></i>{{ $pt->patient_no }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($rx)
                                    <a href="{{ route('pharmacy.prescriptions.show', $rx) }}" class="badge bg-primary-subtle text-primary text-decoration-none border border-primary-subtle">
                                        <i class="bi bi-prescription me-1"></i>{{ $rx->prescription_no }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->medication_name ?? '—' }}</div>
                                <div class="small text-muted">{{ $item->dosage ?? '' }} ({{ $item->frequency ?? '' }}) &bull; <strong class="text-primary">{{ $record->quantity_dispensed }} unit(s)</strong></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><i class="bi bi-qr-code me-1"></i>{{ $record->lot_number }}</span>
                            </td>
                            <td>
                                @if($record->expiry_date)
                                    @php
                                        $isNear = $record->expiry_date->isPast() || $record->expiry_date->diffInDays(now()) < 30;
                                    @endphp
                                    <span class="badge bg-{{ $isNear ? 'danger-subtle text-danger' : 'success-subtle text-success' }} border border-{{ $isNear ? 'danger' : 'success' }}-subtle">
                                        <i class="bi bi-calendar-event me-1"></i>{{ $record->expiry_date->format('M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold"><i class="bi bi-person-badge me-1"></i>{{ $record->pharmacist?->name ?? 'Pharmacist' }}</div>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('pharmacy.dispensing.show', $record) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> View Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                <h5>No dispensing records found</h5>
                                <p class="small text-muted">No medications have been dispensed matching your criteria yet.</p>
                                <a href="{{ route('pharmacy.dispensing.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Dispense Medication Now
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($records->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $records->links() }}
        </div>
    @endif
</div>
@endsection
