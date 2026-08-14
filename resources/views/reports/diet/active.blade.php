@extends('layouts.app')
@section('title', 'Active Diet Plans Report')
@section('page-title', 'Active Diet Plans Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Active Diet Plans Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => [], 'extraFilters' => !empty($dietitians) ? '<div class="col-md-2 col-sm-6"><label class="form-label fw-semibold small mb-1">Dietitian</label><select name="dietitian_id" class="form-select form-select-sm"><option value="">All</option>' . $dietitians->map(fn($d) => '<option value="'.$d->id.'" '.(request('dietitian_id')==$d->id?'selected':'').'>'.$d->name.'</option>')->join('') . '</select></div>' : ''])
@include('reports._summary-cards', ['cards' => [
    ['label' => 'Active Diet Plans', 'value' => $summary['active_plans'], 'icon' => 'bi-play-circle', 'color' => 'success'],
    ['label' => 'Total Plans',       'value' => $summary['total_plans'],  'icon' => 'bi-journal-medical', 'color' => 'primary'],
]])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-play-circle me-2"></i>Active Nutrition Plans</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Plan #</th><th>Patient</th><th>Diet Type</th><th>Dietitian</th><th>Start Date</th><th>End Date</th></tr></thead>
                <tbody>
                    @forelse($records as $r)
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $r->plan_no }}</td>
                        <td>{{ $r->dietRequest->patient->full_name ?? '—' }}</td>
                        <td><span class="badge bg-warning bg-opacity-10 text-dark border">{{ $r->dietRequest->diet_type ?? '—' }}</span></td>
                        <td>{{ $r->dietitian->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $r->start_date?->format('M d, Y') }}</td>
                        <td class="text-muted small">{{ $r->end_date?->format('M d, Y') ?? 'Ongoing' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No active diet plans found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
