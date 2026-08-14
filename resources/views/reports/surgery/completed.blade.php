@extends('layouts.app')
@section('title', 'Completed Surgery Report')
@section('page-title', 'Completed Surgery Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Completed Surgery Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => [], 'doctors' => $doctors])
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Completed Surgeries', 'value' => $summary['completed'], 'icon' => 'bi-check-circle', 'color' => 'success'],
    ['label' => 'Completion Rate',           'value' => $summary['completion_rate'], 'icon' => 'bi-percent', 'color' => 'info'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-check-circle me-2"></i>Completed Surgery Records</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Request #</th><th>Patient</th><th>Procedure</th><th>Surgeon</th><th>Operating Room</th><th>Scheduled Date</th></tr></thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->request_no }}</td>
                        <td>{{ $r->patient->full_name ?? '—' }}</td>
                        <td>{{ $r->procedure_name }}</td>
                        <td>{{ $r->doctor->name ?? '—' }}</td>
                        <td>{{ $r->schedule?->operatingRoom?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $r->schedule?->scheduled_at?->format('M d, Y h:i A') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No completed surgeries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
