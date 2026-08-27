@extends('layouts.app')
@section('title', auth()->user()?->primaryRole === 'doctor' ? 'Clinical Reports' : 'Report Hub')
@section('page-title', auth()->user()?->primaryRole === 'doctor' ? 'Clinical Reports' : 'Report Hub')
@section('content')

@if(auth()->user()?->primaryRole === 'doctor')
<div class="mb-4">
    <p class="text-muted mb-0 small"><i class="bi bi-info-circle me-1 text-success"></i> Patient-focused clinical reports and records relevant to diagnosis, treatment, and care.</p>
</div>
@endif

@php
$isDoctor = auth()->user()?->primaryRole === 'doctor';

$reportCategories = [
    'laboratory' => [
        'title' => 'Laboratory Reports',
        'icon'  => 'bi-clipboard2-pulse',
        'color' => 'primary',
        'badge' => 'LIS',
        'reports' => $isDoctor ? [
            ['name' => 'Laboratory Activity & Results', 'route' => 'reports.laboratory.activity', 'icon' => 'bi-clipboard-data'],
        ] : [
            ['name' => 'Laboratory Activity Report', 'route' => 'reports.laboratory.activity', 'icon' => 'bi-graph-up'],
            ['name' => 'Test Volume Report',         'route' => 'reports.laboratory.volume',   'icon' => 'bi-bar-chart'],
            ['name' => 'Completed Tests Report',     'route' => 'reports.laboratory.completed','icon' => 'bi-check-circle'],
            ['name' => 'Pending Tests Report',       'route' => 'reports.laboratory.pending',  'icon' => 'bi-hourglass-split'],
        ],
    ],
    'radiology' => [
        'title' => 'Radiology Reports',
        'icon'  => 'bi-activity',
        'color' => 'info',
        'badge' => 'RIS',
        'reports' => $isDoctor ? [
            ['name' => 'Radiology Activity & Imaging Records', 'route' => 'reports.radiology.activity', 'icon' => 'bi-card-checklist'],
        ] : [
            ['name' => 'Imaging Activity Report',       'route' => 'reports.radiology.activity',  'icon' => 'bi-graph-up'],
            ['name' => 'Imaging Volume Report',          'route' => 'reports.radiology.volume',    'icon' => 'bi-bar-chart'],
            ['name' => 'Completed Procedures Report',    'route' => 'reports.radiology.completed', 'icon' => 'bi-check-circle'],
            ['name' => 'Pending Interpretation Report',  'route' => 'reports.radiology.pending',   'icon' => 'bi-hourglass-split'],
        ],
    ],
    'pharmacy' => [
        'title' => 'Prescription Reports',
        'icon'  => 'bi-capsule',
        'color' => 'success',
        'badge' => 'PMS',
        'reports' => $isDoctor ? [
            ['name' => 'Prescription & Medication Activity', 'route' => 'reports.pharmacy.activity', 'icon' => 'bi-prescription2'],
        ] : [
            ['name' => 'Prescription Activity Report', 'route' => 'reports.pharmacy.activity',   'icon' => 'bi-graph-up'],
            ['name' => 'Dispensing Report',            'route' => 'reports.pharmacy.dispensing',  'icon' => 'bi-bag-check'],
            ['name' => 'Pending Prescription Report',  'route' => 'reports.pharmacy.pending',    'icon' => 'bi-hourglass-split'],
        ],
    ],
    'surgery' => [
        'title' => 'Surgery Reports',
        'icon'  => 'bi-heart-pulse',
        'color' => 'danger',
        'badge' => 'SORS',
        'reports' => $isDoctor ? [
            ['name' => 'Surgical Activity & Procedure Records', 'route' => 'reports.surgery.activity', 'icon' => 'bi-scissors'],
        ] : [
            ['name' => 'Surgery Activity Report',   'route' => 'reports.surgery.activity',       'icon' => 'bi-graph-up'],
            ['name' => 'Completed Surgery Report',   'route' => 'reports.surgery.completed',     'icon' => 'bi-check-circle'],
            ['name' => 'Cancelled Surgery Report',   'route' => 'reports.surgery.cancelled',     'icon' => 'bi-x-circle'],
            ['name' => 'OR Utilization Report',      'route' => 'reports.surgery.or-utilization','icon' => 'bi-building'],
        ],
    ],
    'diet' => [
        'title' => 'Nutrition Reports',
        'icon'  => 'bi-apple',
        'color' => 'warning',
        'badge' => 'DNMS',
        'reports' => $isDoctor ? [
            ['name' => 'Patient Diet & Nutrition Activity', 'route' => 'reports.diet.activity', 'icon' => 'bi-journal-bookmark'],
        ] : [
            ['name' => 'Diet Plan Activity Report',   'route' => 'reports.diet.activity',  'icon' => 'bi-graph-up'],
            ['name' => 'Active Diet Plans Report',     'route' => 'reports.diet.active',    'icon' => 'bi-play-circle'],
            ['name' => 'Completed Diet Plans Report',  'route' => 'reports.diet.completed', 'icon' => 'bi-check-circle'],
        ],
    ],
    'clinical' => [
        'title' => 'Clinical Summary Reports',
        'icon'  => 'bi-file-earmark-medical',
        'color' => 'secondary',
        'badge' => 'ALL',
        'reports' => [
            ['name' => 'Patient Clinical Activity Report', 'route' => 'reports.clinical.patient-activity', 'icon' => 'bi-person-lines-fill'],
            ['name' => 'Doctor Activity Summary',          'route' => 'reports.clinical.doctor-activity',  'icon' => 'bi-person-badge'],
            ['name' => 'Clinical Services Summary',        'route' => 'reports.clinical.services-summary', 'icon' => 'bi-pie-chart'],
        ],
    ],
];
@endphp

<div class="row g-4">
@foreach($reportCategories as $catKey => $cat)
    @if(in_array($catKey, $categories))
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm card-hover-elevate">
            <div class="card-header bg-{{ $cat['color'] }} bg-opacity-10 d-flex align-items-center gap-2 border-bottom-0 py-3">
                <div class="rounded-circle bg-{{ $cat['color'] }} bg-opacity-25 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                    <i class="bi {{ $cat['icon'] }} text-{{ $cat['color'] }}" style="font-size:1rem;"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-body">{{ $cat['title'] }}</h6>
                </div>
                <span class="badge bg-{{ $cat['color'] }} bg-opacity-25 text-{{ $cat['color'] }} ms-auto" style="font-size:.65rem;letter-spacing:.05em;">{{ $cat['badge'] }}</span>
            </div>
            <div class="card-body pt-2 pb-3">
                <div class="list-group list-group-flush">
                    @foreach($cat['reports'] as $report)
                    <a href="{{ route($report['route']) }}" class="list-group-item list-group-item-action border-0 rounded px-3 py-2 d-flex align-items-center gap-2">
                        <i class="bi {{ $report['icon'] }} text-{{ $cat['color'] }} opacity-75" style="font-size:.85rem;"></i>
                        <span class="small fw-medium">{{ $report['name'] }}</span>
                        <i class="bi bi-chevron-right ms-auto text-muted" style="font-size:.65rem;"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
</div>

@endsection
