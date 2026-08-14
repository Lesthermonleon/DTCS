@extends('layouts.app')
@section('title', 'Patient Clinical Activity Report')
@section('page-title', 'Patient Clinical Activity Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Patient Clinical Activity Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => []])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2"></i>Cross-Module Patient Activity Summary</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Patient No</th><th>Patient Name</th><th class="text-center">Lab</th><th class="text-center">Radiology</th><th class="text-center">Prescriptions</th><th class="text-center">Surgery</th><th class="text-center">Diet</th><th class="text-center">Total Services</th></tr></thead>
                <tbody>
                    @forelse($records as $p)
                    @php $totalServices = $p->lab_requests_count + $p->radiology_requests_count + $p->prescriptions_count + $p->surgery_requests_count + $p->diet_requests_count; @endphp
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $p->patient_no }}</td>
                        <td>{{ $p->full_name }}</td>
                        <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary">{{ $p->lab_requests_count }}</span></td>
                        <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info">{{ $p->radiology_requests_count }}</span></td>
                        <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success">{{ $p->prescriptions_count }}</span></td>
                        <td class="text-center"><span class="badge bg-danger bg-opacity-10 text-danger">{{ $p->surgery_requests_count }}</span></td>
                        <td class="text-center"><span class="badge bg-warning bg-opacity-10 text-dark">{{ $p->diet_requests_count }}</span></td>
                        <td class="text-center fw-bold">{{ $totalServices }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No patient activity recorded for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($records->hasPages())<div class="card-footer bg-white print-hide">{{ $records->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
