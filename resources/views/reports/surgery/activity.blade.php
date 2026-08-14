@extends('layouts.app')
@section('title', 'Surgery Activity Report')
@section('page-title', 'Surgery Activity Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Surgery Activity Report'])
@php $statuses = ['Pending', 'Scheduled', 'In Progress', 'Completed', 'Cancelled']; @endphp
@include('reports._filters', compact('from', 'to', 'statuses', 'doctors'))
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Surgeries',   'value' => $summary['total'],     'icon' => 'bi-heart-pulse',     'color' => 'primary'],
    ['label' => 'Completed',         'value' => $summary['completed'], 'icon' => 'bi-check-circle',    'color' => 'success'],
    ['label' => 'Scheduled/Pending', 'value' => $summary['scheduled'] + $summary['pending'], 'icon' => 'bi-calendar-event', 'color' => 'warning'],
    ['label' => 'Cancelled',         'value' => $summary['cancelled'], 'icon' => 'bi-x-circle',        'color' => 'danger'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-table me-2"></i>Surgery Requests & Schedules</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Request #</th><th>Patient</th><th>Procedure</th><th>Surgeon</th><th>Urgency</th><th>OR</th><th>Status</th><th>Requested</th></tr></thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->request_no }}</td>
                        <td>{{ $r->patient->full_name ?? '—' }}</td>
                        <td>{{ $r->procedure_name }}</td>
                        <td>{{ $r->doctor->name ?? '—' }}</td>
                        <td><span class="badge bg-{{ $r->urgency === 'Emergency' ? 'danger' : ($r->urgency === 'Urgent' ? 'warning' : 'secondary') }}">{{ $r->urgency }}</span></td>
                        <td>{{ $r->schedule?->operatingRoom?->name ?? '—' }}</td>
                        <td>{!! $r->statusBadge !!}</td>
                        <td class="text-muted small">{{ $r->requested_at?->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No surgery records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
