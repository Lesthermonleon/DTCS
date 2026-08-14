@extends('layouts.app')
@section('title', 'Cancelled Surgery Report')
@section('page-title', 'Cancelled Surgery Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Cancelled Surgery Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => [], 'doctors' => []])
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Cancelled',  'value' => $summary['cancelled'], 'icon' => 'bi-x-circle',    'color' => 'danger'],
    ['label' => 'Total Requests',   'value' => $summary['total'],     'icon' => 'bi-heart-pulse', 'color' => 'primary'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-x-circle me-2"></i>Cancelled Surgery Requests</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Request #</th><th>Patient</th><th>Procedure</th><th>Surgeon</th><th>Cancellation Reason</th><th>Requested</th></tr></thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->request_no }}</td>
                        <td>{{ $r->patient->full_name ?? '—' }}</td>
                        <td>{{ $r->procedure_name }}</td>
                        <td>{{ $r->doctor->name ?? '—' }}</td>
                        <td class="text-danger small">{{ $r->cancellation_reason ?? 'Not specified' }}</td>
                        <td class="text-muted small">{{ $r->requested_at?->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No cancelled surgeries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
