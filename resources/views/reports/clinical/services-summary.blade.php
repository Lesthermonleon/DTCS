@extends('layouts.app')
@section('title', 'Clinical Services Summary')
@section('page-title', 'Clinical Services Summary')
@section('content')
@include('reports._print-header', ['reportTitle' => 'Clinical Services Cross-Module Executive Summary'])
@include('reports._filters', ['from' => $from, 'to' => $to, 'statuses' => []])

<div class="row g-4 mb-4">
    {{-- Laboratory Card --}}
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-primary bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-primary mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Laboratory (LIS)</h6>
                <span class="badge bg-primary">{{ $summary['lab']['total'] }} Total</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Completed:</span><strong class="text-success">{{ $summary['lab']['completed'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Pending:</span><strong class="text-warning">{{ $summary['lab']['pending'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Cancelled:</span><strong class="text-danger">{{ $summary['lab']['cancelled'] }}</strong></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold"><span>Completion Rate:</span><span class="text-primary">{{ $summary['lab']['completion_rate'] }}%</span></div>
            </div>
        </div>
    </div>

    {{-- Radiology Card --}}
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-info bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-info mb-0"><i class="bi bi-activity me-2"></i>Radiology (RIS)</h6>
                <span class="badge bg-info text-dark">{{ $summary['radiology']['total'] }} Total</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Completed:</span><strong class="text-success">{{ $summary['radiology']['completed'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Pending:</span><strong class="text-warning">{{ $summary['radiology']['pending'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>In Progress:</span><strong class="text-info">{{ $summary['radiology']['in_progress'] }}</strong></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold"><span>Completion Rate:</span><span class="text-info">{{ $summary['radiology']['completion_rate'] }}%</span></div>
            </div>
        </div>
    </div>

    {{-- Pharmacy Card --}}
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-success bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-success mb-0"><i class="bi bi-capsule me-2"></i>Pharmacy (PMS)</h6>
                <span class="badge bg-success">{{ $summary['pharmacy']['total'] }} Total</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Dispensed:</span><strong class="text-success">{{ $summary['pharmacy']['dispensed'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Verified:</span><strong class="text-info">{{ $summary['pharmacy']['verified'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Pending:</span><strong class="text-warning">{{ $summary['pharmacy']['pending'] }}</strong></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold"><span>Completion Rate:</span><span class="text-success">{{ $summary['pharmacy']['completion_rate'] }}%</span></div>
            </div>
        </div>
    </div>

    {{-- Surgery Card --}}
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-danger bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-danger mb-0"><i class="bi bi-heart-pulse me-2"></i>Surgery (SORS)</h6>
                <span class="badge bg-danger">{{ $summary['surgery']['total'] }} Total</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Completed:</span><strong class="text-success">{{ $summary['surgery']['completed'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Scheduled:</span><strong class="text-primary">{{ $summary['surgery']['scheduled'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Cancelled:</span><strong class="text-danger">{{ $summary['surgery']['cancelled'] }}</strong></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold"><span>Completion Rate:</span><span class="text-danger">{{ $summary['surgery']['completion_rate'] }}%</span></div>
            </div>
        </div>
    </div>

    {{-- Diet & Nutrition Card --}}
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-apple me-2"></i>Diet & Nutrition (DNMS)</h6>
                <span class="badge bg-warning text-dark">{{ $summary['diet']['total_requests'] }} Total</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Total Plans:</span><strong class="text-primary">{{ $summary['diet']['total_plans'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Active Plans:</span><strong class="text-success">{{ $summary['diet']['active_plans'] }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Pending Requests:</span><strong class="text-danger">{{ $summary['diet']['pending_requests'] }}</strong></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold"><span>Completed Plans:</span><span class="text-dark">{{ $summary['diet']['completed_plans'] }}</span></div>
            </div>
        </div>
    </div>
</div>

<div class="text-end print-hide">
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print Executive Summary</button>
</div>

@push('styles')<style>@media print { .print-hide, #sidebar, .topbar, nav { display: none !important; } .print-header { display: block !important; } .card { border: none !important; box-shadow: none !important; } }</style>@endpush
@endsection
