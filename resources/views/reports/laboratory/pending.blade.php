@extends('layouts.app')
@section('title', 'Pending Tests Report')
@section('page-title', 'Pending Tests Report')
@section('content')

@include('reports._print-header', ['reportTitle' => 'Pending Tests Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => [], 'doctors' => $doctors])

@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Pending', 'value' => $summary['pending'], 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
    ['label' => 'Total Requests', 'value' => $summary['total'], 'icon' => 'bi-clipboard2-pulse', 'color' => 'primary'],
]])

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-hourglass-split me-2"></i>Pending Lab Requests</h6>
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
                        <th>Requested</th>
                        <th>Age</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->request_no }}</td>
                        <td>{{ $r->patient->full_name ?? '—' }}</td>
                        <td>@foreach($r->items as $item)<span class="badge bg-light text-dark border me-1">{{ $item->labTest->name ?? '—' }}</span>@endforeach</td>
                        <td>{{ $r->doctor->name ?? '—' }}</td>
                        <td><span class="badge bg-{{ $r->priority === 'STAT' ? 'danger' : ($r->priority === 'Urgent' ? 'warning' : 'secondary') }}">{{ $r->priority }}</span></td>
                        <td class="text-muted small">{{ $r->requested_at?->format('M d, Y') }}</td>
                        <td class="small">{{ $r->requested_at ? $r->requested_at->diffForHumans(null, true) : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No pending tests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>

@push('styles')
<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>
@endpush
@endsection
