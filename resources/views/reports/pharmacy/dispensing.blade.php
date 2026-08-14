@extends('layouts.app')
@section('title', 'Dispensing Report')
@section('page-title', 'Dispensing Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Dispensing Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => [], 'doctors' => [], 'extraFilters' => !empty($pharmacists) ? '<div class="col-md-2 col-sm-6"><label class="form-label fw-semibold small mb-1">Pharmacist</label><select name="pharmacist_id" class="form-select form-select-sm"><option value="">All</option>' . $pharmacists->map(fn($p) => '<option value="'.$p->id.'" '.(request('pharmacist_id')==$p->id?'selected':'').'>'.$p->name.'</option>')->join('') . '</select></div>' : ''])
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Dispensed', 'value' => $summary['dispensed'], 'icon' => 'bi-bag-check', 'color' => 'success'],
    ['label' => 'Total Prescriptions', 'value' => $summary['total'], 'icon' => 'bi-capsule', 'color' => 'primary'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-bag-check me-2"></i>Dispensing Records</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Medication</th><th>Patient</th><th>Qty</th><th>Pharmacist</th><th>Lot #</th><th>Dispensed At</th></tr></thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->prescriptionItem->medication_name ?? '—' }}</td>
                        <td>{{ $r->prescriptionItem->prescription->patient->full_name ?? '—' }}</td>
                        <td>{{ $r->quantity_dispensed }}</td>
                        <td>{{ $r->pharmacist->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $r->lot_number ?? '—' }}</td>
                        <td class="text-muted small">{{ $r->dispensed_at?->format('M d, Y h:i A') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No dispensing records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
