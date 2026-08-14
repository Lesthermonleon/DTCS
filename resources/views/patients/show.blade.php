@extends('layouts.app')
@section('title', 'Patient: ' . $patient->full_name)
@section('page-title', 'Patient Profile')
@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body text-center pt-4">
                <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle mx-auto mb-3" style="width:72px;height:72px;font-size:2rem;font-weight:700;">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                </div>
                <h5 class="mb-0 fw-bold">{{ $patient->last_name }}, {{ $patient->first_name }} {{ $patient->middle_name }}</h5>
                <p class="text-muted">{{ $patient->patient_no }}</p>
                <span class="badge bg-{{ $patient->patient_type==='Inpatient'?'primary':'success' }} px-3 py-2">{{ $patient->patient_type }}</span>
            </div>
            <hr class="my-0">
            <div class="card-body">
                <dl class="row small mb-0">
                    <dt class="col-6 text-muted">Date of Birth</dt><dd class="col-6">{{ \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') }} ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} yrs)</dd>
                    <dt class="col-6 text-muted">Gender</dt><dd class="col-6">{{ $patient->gender }}</dd>
                    <dt class="col-6 text-muted">Blood Type</dt><dd class="col-6">{{ $patient->blood_type ?? '—' }}</dd>
                    <dt class="col-6 text-muted">Phone</dt><dd class="col-6">{{ $patient->phone ?? '—' }}</dd>
                    <dt class="col-6 text-muted">Email</dt><dd class="col-6">{{ $patient->email ?? '—' }}</dd>
                    @if($patient->ward)<dt class="col-6 text-muted">Ward / Bed</dt><dd class="col-6">{{ $patient->ward }} / {{ $patient->bed_number }}</dd>@endif
                    @if($patient->emergency_contact_name)
                    <dt class="col-6 text-muted">Emergency</dt><dd class="col-6">{{ $patient->emergency_contact_name }}<br><small>{{ $patient->emergency_contact_phone }}</small></dd>
                    @endif
                </dl>
            </div>
            @if(auth()->user()->hasAnyRole(['admin','doctor']))
            <div class="card-footer">
                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-warning btn-sm w-100"><i class="bi bi-pencil me-1"></i>Edit Record</a>
            </div>
            @endif
        </div>
    </div>
    <div class="col-lg-8">
        {{-- Tabs for clinical history --}}
        <ul class="nav nav-tabs mb-3" id="patientTabs">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#lab">Lab ({{ $patient->labRequests->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#radiology">Radiology ({{ $patient->radiologyRequests->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#rx">Prescriptions ({{ $patient->prescriptions->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#surgery">Surgery ({{ $patient->surgeryRequests->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#diet">Diet ({{ $patient->dietRequests->count() }})</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="lab">
                <div class="card"><div class="card-body p-0"><div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Request No</th><th>Priority</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                        @forelse($patient->labRequests as $lr)
                            <tr><td><a href="{{ route('lab.requests.show', $lr) }}">{{ $lr->request_no }}</a></td><td>{{ $lr->priority }}</td><td><span class="badge bg-{{ $lr->statusBadge }}">{{ $lr->status }}</span></td><td>{{ $lr->requested_at?->format('M d, Y') }}</td></tr>
                        @empty <tr><td colspan="4" class="text-center text-muted">None</td></tr> @endforelse
                        </tbody>
                    </table>
                </div></div></div>
            </div>
            <div class="tab-pane" id="radiology">
                <div class="card"><div class="card-body p-0"><div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Request No</th><th>Modality</th><th>Body Part</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($patient->radiologyRequests as $rr)
                            <tr><td><a href="{{ route('radiology.requests.show', $rr) }}">{{ $rr->request_no }}</a></td><td>{{ $rr->modality }}</td><td>{{ $rr->body_part }}</td><td><span class="badge bg-{{ $rr->statusBadge }}">{{ $rr->status }}</span></td></tr>
                        @empty <tr><td colspan="4" class="text-center text-muted">None</td></tr> @endforelse
                        </tbody>
                    </table>
                </div></div></div>
            </div>
            <div class="tab-pane" id="rx">
                <div class="card"><div class="card-body p-0"><div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Rx No</th><th>Diagnosis</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                        @forelse($patient->prescriptions as $rx)
                            <tr><td><a href="{{ route('pharmacy.prescriptions.show', $rx) }}">{{ $rx->prescription_no }}</a></td><td>{{ $rx->diagnosis }}</td><td><span class="badge bg-{{ $rx->statusBadge }}">{{ $rx->status }}</span></td><td>{{ $rx->prescribed_at?->format('M d, Y') }}</td></tr>
                        @empty <tr><td colspan="4" class="text-center text-muted">None</td></tr> @endforelse
                        </tbody>
                    </table>
                </div></div></div>
            </div>
            <div class="tab-pane" id="surgery">
                <div class="card"><div class="card-body p-0"><div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Request No</th><th>Procedure</th><th>Urgency</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse($patient->surgeryRequests as $sr)
                            <tr><td><a href="{{ route('surgery.requests.show', $sr) }}">{{ $sr->request_no }}</a></td><td>{{ $sr->procedure_name }}</td><td>{{ $sr->urgency }}</td><td><span class="badge bg-{{ $sr->statusBadge }}">{{ $sr->status }}</span></td></tr>
                        @empty <tr><td colspan="4" class="text-center text-muted">None</td></tr> @endforelse
                        </tbody>
                    </table>
                </div></div></div>
            </div>
            <div class="tab-pane" id="diet">
                <div class="card"><div class="card-body p-0"><div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Request No</th><th>Diet Type</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                        @forelse($patient->dietRequests as $dr)
                            <tr><td><a href="{{ route('diet.requests.show', $dr) }}">{{ $dr->request_no }}</a></td><td>{{ $dr->diet_type }}</td><td><span class="badge bg-{{ $dr->statusBadge }}">{{ $dr->status }}</span></td><td>{{ $dr->requested_at?->format('M d, Y') }}</td></tr>
                        @empty <tr><td colspan="4" class="text-center text-muted">None</td></tr> @endforelse
                        </tbody>
                    </table>
                </div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection
