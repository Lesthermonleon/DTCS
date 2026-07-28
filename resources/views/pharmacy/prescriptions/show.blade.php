@extends('layouts.app')
@section('title', 'Prescription ' . $prescription->prescription_no)
@section('page-title', 'Prescription: ' . $prescription->prescription_no)
@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Details</div>
            <div class="card-body">
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Rx No</dt><dd class="col-7 fw-bold">{{ $prescription->prescription_no }}</dd>
                    <dt class="col-5 text-muted">Patient</dt><dd class="col-7">{{ $prescription->patient->full_name }}</dd>
                    <dt class="col-5 text-muted">Doctor</dt><dd class="col-7">{{ $prescription->doctor->name }}</dd>
                    <dt class="col-5 text-muted">Diagnosis</dt><dd class="col-7">{{ $prescription->diagnosis ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Status</dt><dd class="col-7"><span class="badge bg-{{ $prescription->statusBadge }}">{{ $prescription->status }}</span></dd>
                    <dt class="col-5 text-muted">Issued</dt><dd class="col-7">{{ $prescription->prescribed_at?->format('M d, Y H:i') }}</dd>
                    @if($prescription->verifiedBy)<dt class="col-5 text-muted">Verified By</dt><dd class="col-7">{{ $prescription->verifiedBy->name }}<br><small>{{ $prescription->verified_at?->format('M d, Y H:i') }}</small></dd>@endif
                    @if($prescription->notes)<dt class="col-5 text-muted">Notes</dt><dd class="col-7">{{ $prescription->notes }}</dd>@endif
                </dl>
            </div>
        </div>
        <div class="d-flex flex-column gap-2">
            @if($prescription->status==='Pending' && auth()->user()->hasAnyRole(['admin','pharmacist']))
                <form method="POST" action="{{ route('pharmacy.prescriptions.verify', $prescription) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success w-100"><i class="bi bi-shield-check me-1"></i>Verify Prescription</button>
                </form>
            @endif
            @if($prescription->status==='Verified' && auth()->user()->hasAnyRole(['admin','pharmacist']))
                <a href="{{ route('pharmacy.dispensing.create') }}?rx={{ $prescription->id }}" class="btn btn-primary"><i class="bi bi-bag-plus me-1"></i>Dispense Medications</a>
            @endif
            <a href="{{ route('pharmacy.prescriptions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-capsule me-2"></i>Medications</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Medication</th><th>Dosage</th><th>Route</th><th>Frequency</th><th>Duration</th><th>Qty</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($prescription->items as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->medication_name }}</td>
                        <td>{{ $item->dosage }}</td>
                        <td>{{ $item->route }}</td>
                        <td>{{ $item->frequency }}</td>
                        <td>{{ $item->duration }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td><span class="badge bg-{{ $item->status==='Dispensed'?'success':'secondary' }}">{{ $item->status ?? 'Pending' }}</span></td>
                    </tr>
                    @if($item->instructions)<tr><td colspan="7" class="text-muted small pt-0 pb-2"><i class="bi bi-info-circle me-1"></i>{{ $item->instructions }}</td></tr>@endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
