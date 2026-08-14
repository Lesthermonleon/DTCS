@extends('layouts.app')
@section('title', 'OR Utilization Report')
@section('page-title', 'Operating Room Utilization Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Operating Room Utilization Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => [], 'doctors' => []])
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Surgeries', 'value' => $summary['total'],     'icon' => 'bi-heart-pulse', 'color' => 'primary'],
    ['label' => 'Completed',       'value' => $summary['completed'], 'icon' => 'bi-check-circle', 'color' => 'success'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-building me-2"></i>Utilization by Operating Room</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Operating Room</th><th class="text-center">Total Surgeries</th><th class="text-center">Completed</th><th class="text-center">Total Hours Used</th></tr></thead>
                <tbody>
                    @forelse($utilization as $u)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $u->operatingRoom->name ?? 'Unassigned Room' }}</td>
                        <td class="text-center">{{ number_format($u->total_surgeries) }}</td>
                        <td class="text-center text-success">{{ number_format($u->completed) }}</td>
                        <td class="text-center font-monospace">{{ number_format($u->total_minutes / 60, 1) }} hrs</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No OR utilization records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
