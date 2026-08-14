@extends('layouts.app')
@section('title', 'Prescription Activity Report')
@section('page-title', 'Prescription Activity Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Prescription Activity Report'])
@php $statuses = ['Pending', 'Verified', 'Dispensed', 'Cancelled']; @endphp
@include('reports._filters', compact('from', 'to', 'statuses', 'doctors'))
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Prescriptions','value' => $summary['total'],     'icon' => 'bi-capsule',         'color' => 'primary'],
    ['label' => 'Verified',           'value' => $summary['verified'],  'icon' => 'bi-check2-square',   'color' => 'info'],
    ['label' => 'Dispensed',          'value' => $summary['dispensed'], 'icon' => 'bi-bag-check',       'color' => 'success'],
    ['label' => 'Pending',            'value' => $summary['pending'],   'icon' => 'bi-hourglass-split', 'color' => 'warning'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-table me-2"></i>Prescription Records</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Rx #</th><th>Patient</th><th>Doctor</th><th>Items</th><th>Status</th><th>Prescribed</th></tr></thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->prescription_no }}</td>
                        <td>{{ $r->patient->full_name ?? '—' }}</td>
                        <td>{{ $r->doctor->name ?? '—' }}</td>
                        <td>{{ $r->items->count() }} item(s)</td>
                        <td>{!! $r->statusBadge !!}</td>
                        <td class="text-muted small">{{ $r->prescribed_at?->format('M d, Y h:i A') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No prescriptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
