@extends('layouts.app')
@section('title', 'Laboratory Activity Report')
@section('page-title', 'Laboratory Activity Report')
@section('content')

@include('reports._print-header', ['reportTitle' => 'Laboratory Activity Report'])

@php $statuses = ['Pending', 'Received', 'Completed', 'Cancelled']; @endphp
@include('reports._filters', compact('from', 'to', 'statuses', 'doctors'))

@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Requests', 'value' => $summary['total'],     'icon' => 'bi-clipboard2-pulse', 'color' => 'primary'],
    ['label' => 'Completed',      'value' => $summary['completed'], 'icon' => 'bi-check-circle',     'color' => 'success'],
    ['label' => 'Pending',        'value' => $summary['pending'],   'icon' => 'bi-hourglass-split',  'color' => 'warning'],
    ['label' => 'Completion Rate','value' => $summary['completion_rate'], 'icon' => 'bi-percent', 'color' => 'info'],
]])

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-table me-2"></i>Detailed Results</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Request #</th>
                        <th>Patient</th>
                        <th>Tests</th>
                        <th>Ordered By</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Requested</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->request_no }}</td>
                        <td>{{ $r->patient->full_name ?? '—' }}</td>
                        <td>
                            @foreach($r->items as $item)
                                <span class="badge bg-light text-dark border me-1">{{ $item->labTest->name ?? '—' }}</span>
                            @endforeach
                        </td>
                        <td>{{ $r->doctor->name ?? '—' }}</td>
                        <td><span class="badge bg-{{ $r->priority === 'STAT' ? 'danger' : ($r->priority === 'Urgent' ? 'warning' : 'secondary') }}">{{ $r->priority }}</span></td>
                        <td>{!! $r->statusBadge !!}</td>
                        <td class="text-muted small">{{ $r->requested_at?->format('M d, Y h:i A') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No records found for the selected criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())
    <div class="card-footer bg-white print-hide">{{ $records->links() }}</div>
    @endif
</div>

@push('styles')
<style>
@media print {
    .print-hide, #sidebar, .topbar, nav, .pagination { display: none !important; }
    .print-header { display: block !important; }
    .card { border: none !important; box-shadow: none !important; }
    body { font-size: 11pt; }
}
</style>
@endpush
@endsection
