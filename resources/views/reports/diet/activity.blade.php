@extends('layouts.app')
@section('title', 'Diet Activity Report')
@section('page-title', 'Diet Plan Activity Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Diet Plan Activity Report'])
@php $statuses = ['Pending', 'Approved', 'Rejected']; @endphp
@include('reports._filters', compact('from', 'to', 'statuses'))
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Diet Requests', 'value' => $summary['total_requests'],  'icon' => 'bi-apple',           'color' => 'warning'],
    ['label' => 'Total Diet Plans',    'value' => $summary['total_plans'],     'icon' => 'bi-journal-medical', 'color' => 'primary'],
    ['label' => 'Active Plans',        'value' => $summary['active_plans'],    'icon' => 'bi-play-circle',     'color' => 'success'],
    ['label' => 'Pending Requests',    'value' => $summary['pending_requests'], 'icon' => 'bi-hourglass-split','color' => 'danger'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-table me-2"></i>Diet Requests & Plans</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Request #</th><th>Patient</th><th>Diet Type</th><th>Ordered By</th><th>Dietitian</th><th>Plan Status</th><th>Requested</th></tr></thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->request_no }}</td>
                        <td>{{ $r->patient->full_name ?? '—' }}</td>
                        <td><span class="badge bg-warning bg-opacity-10 text-dark border">{{ $r->diet_type }}</span></td>
                        <td>{{ $r->doctor->name ?? '—' }}</td>
                        <td>{{ $r->dietPlan?->dietitian?->name ?? 'Unassigned' }}</td>
                        <td><span class="badge bg-{{ $r->dietPlan?->status === 'Active' ? 'success' : ($r->dietPlan?->status === 'Completed' ? 'secondary' : 'warning') }}">{{ $r->dietPlan?->status ?? 'No Plan' }}</span></td>
                        <td class="text-muted small">{{ $r->requested_at?->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No diet requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
