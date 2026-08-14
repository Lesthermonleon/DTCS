@extends('layouts.app')
@section('title', 'Completed Procedures Report')
@section('page-title', 'Completed Procedures Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Completed Procedures Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => [], 'doctors' => $doctors])
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Completed', 'value' => $summary['completed'], 'icon' => 'bi-check-circle', 'color' => 'success'],
    ['label' => 'Completion Rate',  'value' => $summary['completion_rate'], 'icon' => 'bi-percent', 'color' => 'info'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-check-circle me-2"></i>Completed Imaging Procedures</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Request #</th><th>Patient</th><th>Modality</th><th>Body Part</th><th>Ordered By</th><th>Completed</th></tr></thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->request_no }}</td>
                        <td>{{ $r->patient->full_name ?? '—' }}</td>
                        <td><span class="badge bg-info bg-opacity-10 text-info border">{{ $r->modality }}</span></td>
                        <td>{{ $r->body_part ?? '—' }}</td>
                        <td>{{ $r->doctor->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $r->completed_at?->format('M d, Y h:i A') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No completed procedures found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
