@extends('layouts.app')
@section('title', 'Test Volume Report')
@section('page-title', 'Test Volume Report')
@section('content')

@include('reports._print-header', ['reportTitle' => 'Test Volume Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => [], 'doctors' => []])

@include('reports._summary-cards', ['cards' => [
    ['label' => 'Total Requests', 'value' => $summary['total'],     'icon' => 'bi-clipboard2-pulse', 'color' => 'primary'],
    ['label' => 'Completed',      'value' => $summary['completed'], 'icon' => 'bi-check-circle',     'color' => 'success'],
    ['label' => 'Pending',        'value' => $summary['pending'],   'icon' => 'bi-hourglass-split',  'color' => 'warning'],
]])

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i>Volume by Test Type</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Test Name</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Completed</th>
                        <th class="text-center">Pending</th>
                        <th class="text-center">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($volume as $v)
                    @php $rate = $v->total > 0 ? round(($v->completed / $v->total) * 100) : 0; @endphp
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $v->labTest->name ?? 'Unknown Test' }}</td>
                        <td class="text-center">{{ number_format($v->total) }}</td>
                        <td class="text-center text-success">{{ number_format($v->completed) }}</td>
                        <td class="text-center text-warning">{{ number_format($v->pending) }}</td>
                        <td class="text-center">
                            <div class="progress" style="height:6px;width:80px;display:inline-block;">
                                <div class="progress-bar bg-success" @style(["width: {$rate}%"])></div>
                            </div>
                            <small class="ms-1 text-muted">{{ $rate }}%</small>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No test data found for the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .print-hide, #sidebar, .topbar, nav { display: none !important; }
    .print-header { display: block !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
@endsection
