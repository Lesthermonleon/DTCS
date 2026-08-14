@extends('layouts.app')
@section('title', 'Doctor Activity Report')
@section('page-title', 'Doctor Activity Report')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Doctor Activity Report'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => []])
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-person-badge me-2"></i>Doctor Orders & Requests Breakdown</h6>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-hide"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-3">Doctor Name</th><th>Department</th><th class="text-center">Lab Requests</th><th class="text-center">Radiology</th><th class="text-center">Prescriptions</th><th class="text-center">Surgeries Requested</th><th class="text-center">Total Orders</th></tr></thead>
                <tbody>
                    @forelse($doctors as $d)
                    @php $totalOrders = $d->lab_requests_as_doctor_count + $d->radiology_requests_as_doctor_count + $d->prescriptions_as_doctor_count + $d->surgery_requests_as_doctor_count; @endphp
                    <tr>
                        <td class="ps-3 fw-semibold">{{ $d->name }}</td>
                        <td>{{ $d->department ?? 'General' }}</td>
                        <td class="text-center"><span class="badge bg-primary bg-opacity-10 text-primary">{{ $d->lab_requests_as_doctor_count }}</span></td>
                        <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info">{{ $d->radiology_requests_as_doctor_count }}</span></td>
                        <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success">{{ $d->prescriptions_as_doctor_count }}</span></td>
                        <td class="text-center"><span class="badge bg-danger bg-opacity-10 text-danger">{{ $d->surgery_requests_as_doctor_count }}</span></td>
                        <td class="text-center fw-bold">{{ $totalOrders }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>No doctor activity recorded for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($doctors->hasPages())<div class="card-footer bg-white print-hide">{{ $doctors->links() }}</div>@endif
</div>
@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
