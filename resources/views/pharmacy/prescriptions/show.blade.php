@extends('layouts.app')
@section('title', 'Prescription ' . $prescription->prescription_no)
@section('page-title', 'Prescription: ' . $prescription->prescription_no)
@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-white py-3 fw-bold text-dark"><i class="bi bi-info-circle me-2 text-primary"></i>Prescription Details</div>
            <div class="card-body">
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Rx No</dt><dd class="col-7 fw-bold text-primary">{{ $prescription->prescription_no }}</dd>
                    <dt class="col-5 text-muted">Patient</dt><dd class="col-7 fw-semibold">{{ $prescription->patient->full_name }}</dd>
                    <dt class="col-5 text-muted">Doctor</dt><dd class="col-7">{{ $prescription->doctor->name }}</dd>
                    <dt class="col-5 text-muted">Diagnosis</dt><dd class="col-7">{{ $prescription->diagnosis ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Status</dt><dd class="col-7"><span class="badge bg-{{ $prescription->statusBadge }}">{{ $prescription->status }}</span></dd>
                    <dt class="col-5 text-muted">Issued</dt><dd class="col-7">{{ $prescription->prescribed_at?->format('M d, Y H:i') }}</dd>
                    @if($prescription->verifiedBy)<dt class="col-5 text-muted">Verified By</dt><dd class="col-7">{{ $prescription->verifiedBy->name }}<br><small class="text-muted">{{ $prescription->verified_at?->format('M d, Y H:i') }}</small></dd>@endif
                    @if($prescription->notes)<dt class="col-5 text-muted">Notes</dt><dd class="col-7">{{ $prescription->notes }}</dd>@endif
                </dl>
            </div>
        </div>
        <div class="d-flex flex-column gap-2">
            @if($prescription->status==='Pending' && auth()->user()->hasAnyRole(['admin','pharmacist']))
                <form method="POST" action="{{ route('pharmacy.prescriptions.verify', $prescription) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-success w-100 shadow-sm"><i class="bi bi-shield-check me-1"></i>Verify Prescription</button>
                </form>
            @endif
            @if(in_array($prescription->status, ['Verified', 'Partially Dispensed']) && auth()->user()->hasAnyRole(['admin','pharmacist']))
                <a href="{{ route('pharmacy.dispensing.create') }}?rx={{ $prescription->id }}" class="btn btn-primary shadow-sm"><i class="bi bi-capsule-pill me-1"></i>Dispense Medications</a>
            @endif
            <a href="{{ route('pharmacy.prescriptions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Prescriptions</a>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 fw-bold text-dark"><i class="bi bi-capsule me-2 text-primary"></i>Prescribed Medications</div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Medication</th>
                            <th>Dosage & Route</th>
                            <th>Frequency</th>
                            <th>Duration</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($prescription->items as $item)
                    @php
                        $dispensingRecord = $item->dispensingRecords->first();
                    @endphp
                    <tr>
                        <td class="ps-3 fw-bold text-dark">{{ $item->medication_name }}</td>
                        <td>{{ $item->dosage }} <small class="text-muted">({{ $item->route ?? 'Oral' }})</small></td>
                        <td>{{ $item->frequency }}</td>
                        <td>{{ $item->duration }}</td>
                        <td><span class="fw-bold">{{ $item->quantity }}</span></td>
                        <td><span class="badge bg-{{ $item->status==='Dispensed'?'success':'warning-subtle text-warning border border-warning' }}">{{ $item->status ?? 'Pending' }}</span></td>
                        <td class="text-end pe-3">
                            @if($item->status === 'Pending' && in_array($prescription->status, ['Verified', 'Partially Dispensed']) && auth()->user()->hasAnyRole(['admin','pharmacist']))
                                <a href="{{ route('pharmacy.dispensing.create') }}?rx={{ $prescription->id }}&item={{ $item->id }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-capsule me-1"></i> Dispense
                                </a>
                            @elseif($item->status === 'Dispensed' && $dispensingRecord)
                                <a href="{{ route('pharmacy.dispensing.show', $dispensingRecord) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-receipt me-1"></i> Receipt
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @if($item->instructions)
                        <tr>
                            <td colspan="7" class="text-muted small ps-3 pt-0 pb-2"><i class="bi bi-info-circle me-1"></i><em>Instructions: {{ $item->instructions }}</em></td>
                        </tr>
                    @endif
                    @if($dispensingRecord)
                        <tr class="table-light">
                            <td colspan="7" class="ps-4 small text-success py-2">
                                <i class="bi bi-check2-square me-1"></i> Dispensed by <strong>{{ $dispensingRecord->pharmacist?->name ?? 'Pharmacist' }}</strong> on {{ $dispensingRecord->dispensed_at?->format('M d, Y H:i') }} &bull; Lot: <code>{{ $dispensingRecord->lot_number }}</code> &bull; Exp: {{ $dispensingRecord->expiry_date?->format('M Y') }}
                            </td>
                        </tr>
                    @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
