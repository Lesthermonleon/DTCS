@extends('layouts.app')
@section('title', 'Patient: ' . $patient->last_name . ', ' . $patient->first_name)
@section('page-title', 'Patient Profile')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
    <li class="breadcrumb-item active">{{ $patient->last_name }}, {{ $patient->first_name }}</li>
@endsection

@push('styles')
<style>
    /* ── Patient Profile Layout ── */
    .patient-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--signal);
        color: var(--ink);
        font-family: var(--font-display);
        font-size: 1.85rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin: 0 auto;
        letter-spacing: -0.02em;
    }

    .patient-name {
        font-family: var(--font-display);
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text);
        line-height: 1.25;
        word-break: break-word;
    }

    .patient-id {
        font-family: var(--font-mono);
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--signal-dark);
        letter-spacing: 0.04em;
    }

    html[data-theme="dark"] .patient-id {
        color: var(--signal);
    }

    /* ── Info Section ── */
    .info-section-title {
        font-family: var(--font-display);
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: var(--text-soft);
        margin-bottom: 0.65rem;
        border-bottom: 1px solid var(--line);
        padding-bottom: 0.35rem;
    }

    .info-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.55rem;
        min-width: 0;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-soft);
        flex-shrink: 0;
        width: 110px;
        padding-top: 1px;
    }

    .info-value {
        font-size: 0.82rem;
        color: var(--text);
        min-width: 0;
        word-break: break-word;
        overflow-wrap: break-word;
        flex: 1;
    }

    /* ── Patient Type Pill ── */
    .patient-type-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.72rem;
        font-weight: 600;
        font-family: var(--font-mono);
        letter-spacing: 0.04em;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .patient-type-pill.type-inpatient {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }
    .patient-type-pill.type-outpatient {
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .patient-type-pill.type-emergency {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    html[data-theme="dark"] .patient-type-pill {
        background: #1f2937;
        color: #d1d5db;
        border-color: #374151;
    }
    html[data-theme="dark"] .patient-type-pill.type-inpatient {
        background: rgba(30, 58, 138, 0.4);
        color: #93c5fd;
        border-color: rgba(59, 130, 246, 0.4);
    }
    html[data-theme="dark"] .patient-type-pill.type-outpatient {
        background: rgba(20, 83, 45, 0.4);
        color: #86efac;
        border-color: rgba(34, 197, 94, 0.4);
    }
    html[data-theme="dark"] .patient-type-pill.type-emergency {
        background: rgba(127, 29, 29, 0.4);
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.4);
    }

    /* ── Clinical Records Tabs ── */
    .patient-tabs .nav-link {
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--text-soft);
        padding: 0.5rem 0.85rem;
        border: 1px solid transparent;
        border-bottom: none;
        border-radius: 6px 6px 0 0;
        transition: color 0.15s, background 0.15s;
        white-space: nowrap;
    }

    .patient-tabs .nav-link:hover {
        color: var(--text);
        background: rgba(20, 199, 154, 0.06);
    }

    .patient-tabs .nav-link.active {
        color: var(--signal-dark);
        background: var(--card);
        border-color: var(--line);
        font-weight: 600;
    }

    html[data-theme="dark"] .patient-tabs .nav-link.active {
        color: var(--signal);
        background: var(--card);
        border-color: var(--line);
    }

    .patient-tabs .nav-link .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 4px;
        border-radius: 9px;
        font-size: 0.66rem;
        font-weight: 700;
        background: rgba(20, 199, 154, 0.12);
        color: var(--signal-dark);
        margin-left: 0.3rem;
        font-family: var(--font-mono);
    }

    html[data-theme="dark"] .patient-tabs .nav-link .tab-count {
        background: rgba(20, 199, 154, 0.18);
        color: var(--signal);
    }

    .patient-tabs .nav-link.active .tab-count {
        background: rgba(20, 199, 154, 0.2);
        color: var(--signal-dark);
    }

    .clinical-table th {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--text-soft);
        background: transparent;
        padding: 0.6rem 0.85rem;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }

    .clinical-table td {
        font-size: 0.82rem;
        color: var(--text);
        padding: 0.65rem 0.85rem;
        vertical-align: middle;
    }

    .clinical-table td a {
        color: var(--signal-dark);
        font-weight: 600;
        font-family: var(--font-mono);
        font-size: 0.78rem;
        text-decoration: none;
    }

    html[data-theme="dark"] .clinical-table td a {
        color: var(--signal);
    }

    .clinical-table td a:hover {
        text-decoration: underline;
    }

    .clinical-table .empty-row td {
        color: var(--text-soft);
        font-size: 0.82rem;
        padding: 2rem 0.85rem;
        text-align: center;
    }

    /* ── Record Status Pills (override raw Bootstrap badge classes) ── */
    .record-status {
        display: inline-block;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-family: var(--font-mono);
    }

    /* ── Edit Button ── */
    .btn-patient-edit {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 6px;
        color: var(--text-soft);
        background: transparent;
        border: 1px solid var(--line);
        text-decoration: none;
        transition: all 0.15s;
    }

    .btn-patient-edit:hover {
        background: rgba(20, 199, 154, 0.08);
        border-color: var(--signal);
        color: var(--signal-dark);
    }

    html[data-theme="dark"] .btn-patient-edit {
        border-color: var(--line);
        color: #94A3B8;
    }

    html[data-theme="dark"] .btn-patient-edit:hover {
        border-color: var(--signal);
        color: var(--signal);
        background: rgba(20, 199, 154, 0.1);
    }

    /* ── Responsive ── */
    @media (max-width: 575.98px) {
        .info-label { width: 90px; }
        .patient-name { font-size: 1rem; }
        .patient-tabs .nav-link { padding: 0.45rem 0.55rem; font-size: 0.75rem; }
    }
</style>
@endpush

@section('content')

@php
    $labCount         = $patient->labRequests->count();
    $radCount         = $patient->radiologyRequests->count();
    $rxCount          = $patient->prescriptions->count();
    $surCount         = $patient->surgeryRequests->count();
    $dietCount        = $patient->dietRequests->count();
    $initials         = strtoupper(substr($patient->first_name, 0, 1));
    $canEdit          = auth()->user()->hasAnyRole(['admin', 'doctor']);
@endphp

<div class="row g-3">

    {{-- ══════════ LEFT COLUMN — Patient Sidebar ══════════ --}}
    <div class="col-lg-4">

        {{-- Patient Identity Card --}}
        <div class="card mb-3">
            <div class="card-body text-center pt-4 pb-3">
                <div class="patient-avatar mb-3">{{ $initials }}</div>
                <div class="patient-name mb-1">
                    {{ $patient->last_name }}, {{ $patient->first_name }}
                    @if($patient->middle_name) {{ $patient->middle_name }}@endif
                </div>
                <div class="patient-id mb-2">{{ $patient->patient_no }}</div>
                <span class="patient-type-pill type-{{ strtolower($patient->patient_type ?? 'outpatient') }}">
                    <i class="bi bi-person-badge" style="font-size:.7rem;opacity:.7;"></i>
                    {{ $patient->patient_type }}
                </span>
            </div>

            @if($canEdit)
            <div class="card-footer bg-transparent py-2 px-3">
                <a href="{{ route('patients.edit', $patient) }}" class="btn-patient-edit w-100">
                    <i class="bi bi-pencil" style="font-size:.85rem;"></i> Edit Record
                </a>
            </div>
            @endif
        </div>

        {{-- Personal Information --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="info-section-title">
                    <i class="bi bi-person-lines-fill me-1" style="font-size:.75rem;"></i> Personal Information
                </div>
                <div class="info-row">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('M d, Y') }}
                        <span style="color:var(--text-soft);font-size:.75rem;">({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} yrs)</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Gender</span>
                    <span class="info-value">{{ $patient->gender }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Blood Type</span>
                    <span class="info-value">
                        @if($patient->blood_type)
                            <span style="font-family:var(--font-mono);font-weight:700;color:var(--coral);">{{ $patient->blood_type }}</span>
                        @else
                            <span style="color:var(--text-soft);">—</span>
                        @endif
                    </span>
                </div>
                @if($patient->phone)
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $patient->phone }}</span>
                </div>
                @endif
                @if($patient->email)
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value" style="font-size:.79rem;">{{ $patient->email }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Admission Information (only if ward exists) --}}
        @if($patient->ward)
        <div class="card mb-3">
            <div class="card-body">
                <div class="info-section-title">
                    <i class="bi bi-hospital me-1" style="font-size:.75rem;"></i> Admission
                </div>
                <div class="info-row">
                    <span class="info-label">Ward / Bed</span>
                    <span class="info-value fw-semibold">{{ $patient->ward }} / {{ $patient->bed_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Patient Type</span>
                    <span class="info-value">{{ $patient->patient_type }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Address (only if exists) --}}
        @if($patient->address)
        <div class="card mb-3">
            <div class="card-body">
                <div class="info-section-title">
                    <i class="bi bi-geo-alt me-1" style="font-size:.75rem;"></i> Address
                </div>
                <div style="font-size:.82rem;color:var(--text);word-break:break-word;line-height:1.45;">{{ $patient->address }}</div>
            </div>
        </div>
        @endif

        {{-- Emergency Contact --}}
        @if($patient->emergency_contact_name)
        <div class="card mb-3">
            <div class="card-body">
                <div class="info-section-title">
                    <i class="bi bi-telephone-fill me-1" style="font-size:.75rem;"></i> Emergency Contact
                </div>
                <div class="info-row">
                    <span class="info-label">Name</span>
                    <span class="info-value fw-semibold">{{ $patient->emergency_contact_name }}</span>
                </div>
                @if($patient->emergency_contact_phone)
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $patient->emergency_contact_phone }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- ══════════ RIGHT COLUMN — Clinical Records ══════════ --}}
    <div class="col-lg-8">

        <div class="card">
            <div class="card-header border-bottom-0 pb-0" style="background:transparent;">
                <ul class="nav patient-tabs" id="patientTabs" role="tablist" style="flex-wrap:wrap;gap:2px;">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-lab" role="tab" aria-selected="true">
                            <i class="bi bi-clipboard2-pulse" style="font-size:.8rem;margin-right:3px;"></i>Lab
                            <span class="tab-count">{{ $labCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-radiology" role="tab" aria-selected="false">
                            <i class="bi bi-activity" style="font-size:.8rem;margin-right:3px;"></i>Radiology
                            <span class="tab-count">{{ $radCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-rx" role="tab" aria-selected="false">
                            <i class="bi bi-capsule" style="font-size:.8rem;margin-right:3px;"></i>Prescriptions
                            <span class="tab-count">{{ $rxCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-surgery" role="tab" aria-selected="false">
                            <i class="bi bi-heart-pulse" style="font-size:.8rem;margin-right:3px;"></i>Surgery
                            <span class="tab-count">{{ $surCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-diet" role="tab" aria-selected="false">
                            <i class="bi bi-apple" style="font-size:.8rem;margin-right:3px;"></i>Diet
                            <span class="tab-count">{{ $dietCount }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">

                {{-- Lab Requests --}}
                <div class="tab-pane show active" id="tab-lab" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table clinical-table mb-0">
                            <thead>
                                <tr>
                                    <th>Request No</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($patient->labRequests as $lr)
                                <tr>
                                    <td><a href="{{ route('lab.requests.show', $lr) }}">{{ $lr->request_no }}</a></td>
                                    <td>{{ $lr->priority }}</td>
                                    <td>
                                        <span class="record-status pill-{{ in_array($lr->status, ['Completed','Approved','Released']) ? 'signal' : (in_array($lr->status, ['Cancelled','Rejected']) ? 'coral' : 'muted') }}">
                                            {{ $lr->status }}
                                        </span>
                                    </td>
                                    <td style="white-space:nowrap;">{{ $lr->requested_at?->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr class="empty-row"><td colspan="4"><i class="bi bi-clipboard2 me-1 opacity-50"></i>No laboratory records</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Radiology Requests --}}
                <div class="tab-pane" id="tab-radiology" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table clinical-table mb-0">
                            <thead>
                                <tr>
                                    <th>Request No</th>
                                    <th>Modality</th>
                                    <th>Body Part</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($patient->radiologyRequests as $rr)
                                <tr>
                                    <td><a href="{{ route('radiology.requests.show', $rr) }}">{{ $rr->request_no }}</a></td>
                                    <td>{{ $rr->modality }}</td>
                                    <td>{{ $rr->body_part }}</td>
                                    <td>
                                        <span class="record-status pill-{{ in_array($rr->status, ['Completed','Approved','Released']) ? 'signal' : (in_array($rr->status, ['Cancelled','Rejected']) ? 'coral' : 'muted') }}">
                                            {{ $rr->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row"><td colspan="4"><i class="bi bi-activity me-1 opacity-50"></i>No radiology records</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Prescriptions --}}
                <div class="tab-pane" id="tab-rx" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table clinical-table mb-0">
                            <thead>
                                <tr>
                                    <th>Rx No</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($patient->prescriptions as $rx)
                                <tr>
                                    <td><a href="{{ route('pharmacy.prescriptions.show', $rx) }}">{{ $rx->prescription_no }}</a></td>
                                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $rx->diagnosis }}</td>
                                    <td>
                                        <span class="record-status pill-{{ in_array($rx->status, ['Dispensed','Completed','Approved']) ? 'signal' : (in_array($rx->status, ['Cancelled','Rejected']) ? 'coral' : 'muted') }}">
                                            {{ $rx->status }}
                                        </span>
                                    </td>
                                    <td style="white-space:nowrap;">{{ $rx->prescribed_at?->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr class="empty-row"><td colspan="4"><i class="bi bi-capsule me-1 opacity-50"></i>No prescription records</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Surgery Requests --}}
                <div class="tab-pane" id="tab-surgery" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table clinical-table mb-0">
                            <thead>
                                <tr>
                                    <th>Request No</th>
                                    <th>Procedure</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($patient->surgeryRequests as $sr)
                                <tr>
                                    <td><a href="{{ route('surgery.requests.show', $sr) }}">{{ $sr->request_no }}</a></td>
                                    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $sr->procedure_name }}</td>
                                    <td>{{ $sr->urgency }}</td>
                                    <td>
                                        <span class="record-status pill-{{ in_array($sr->status, ['Completed','Approved','Scheduled']) ? 'signal' : (in_array($sr->status, ['Cancelled','Rejected']) ? 'coral' : 'muted') }}">
                                            {{ $sr->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row"><td colspan="4"><i class="bi bi-heart-pulse me-1 opacity-50"></i>No surgery records</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Diet Requests --}}
                <div class="tab-pane" id="tab-diet" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table clinical-table mb-0">
                            <thead>
                                <tr>
                                    <th>Request No</th>
                                    <th>Diet Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($patient->dietRequests as $dr)
                                <tr>
                                    <td><a href="{{ route('diet.requests.show', $dr) }}">{{ $dr->request_no }}</a></td>
                                    <td>{{ $dr->diet_type }}</td>
                                    <td>
                                        <span class="record-status pill-{{ in_array($dr->status, ['Completed','Approved','Active']) ? 'signal' : (in_array($dr->status, ['Cancelled','Rejected']) ? 'coral' : 'muted') }}">
                                            {{ $dr->status }}
                                        </span>
                                    </td>
                                    <td style="white-space:nowrap;">{{ $dr->requested_at?->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr class="empty-row"><td colspan="4"><i class="bi bi-apple me-1 opacity-50"></i>No diet records</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>{{-- /tab-content --}}
        </div>{{-- /card --}}

    </div>
</div>
@endsection
