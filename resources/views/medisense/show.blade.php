@extends('layouts.app')
@section('title', 'MediSense AI Workspace — ' . $patient->full_name)
@section('page-title', 'MediSense AI Clinical Decision Support')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('medisense.index') }}{{ request()->hasAny(['search','type','page']) ? '?' . http_build_query(request()->only(['search','type','page'])) : '' }}">MediSense</a></li>
    <li class="breadcrumb-item active">{{ $patient->full_name }}</li>
@endsection

@section('content')

<style>
/* MediSense CDS Action Buttons — HIMS Standard UI System Design */
.btn-task-action {
    font-size: 0.84rem;
    font-weight: 500;
    padding: 0.45rem 0.85rem;
    border-radius: 0.625rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    transition: all 0.15s ease-in-out;
    border: 1px solid var(--ms-border, #d9e8de);
    background-color: var(--card, #ffffff);
    color: var(--hims-action-secondary, #374151);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

.btn-task-action:hover,
.btn-task-action:focus {
    background-color: var(--ms-primary-light, #dcfce7) !important;
    border-color: var(--ms-primary, #15803d) !important;
    color: var(--ms-primary-hover, #166534) !important;
    box-shadow: 0 2px 6px rgba(21, 128, 61, 0.15);
}

.btn-task-action.active,
.btn-task-action:active {
    background-color: var(--ms-primary, #15803d) !important;
    border-color: var(--ms-primary, #15803d) !important;
    color: #ffffff !important;
    box-shadow: 0 3px 8px rgba(21, 128, 61, 0.25);
}

.btn-task-action.active i,
.btn-task-action:active i {
    color: #ffffff !important;
}

/* Dark Theme Compatibility */
html[data-theme="dark"] .btn-task-action {
    background-color: #171717;
    border-color: #262626;
    color: #D4D4D4;
}

html[data-theme="dark"] .btn-task-action:hover,
html[data-theme="dark"] .btn-task-action:focus {
    background-color: rgba(20, 199, 154, 0.15) !important;
    border-color: #14C79A !important;
    color: #14C79A !important;
}

html[data-theme="dark"] .btn-task-action.active,
html[data-theme="dark"] .btn-task-action:active {
    background-color: #15803d !important;
    border-color: #15803d !important;
    color: #ffffff !important;
}
</style>

{{-- Patient Header --}}
<div class="card border-0 shadow-sm mb-3 bg-white">
    <div class="card-body p-3">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 d-none d-sm-block">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px; border: 1px solid rgba(13, 110, 253, 0.2);">
                            {{ strtoupper(substr($patient->full_name, 0, 1)) }}{{ strtoupper(substr(last(explode(' ', $patient->full_name)), 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h5 class="fw-bold text-dark mb-0 me-2">{{ $patient->full_name }}</h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-family:var(--font-mono); font-size: 0.75rem;">
                                <i class="bi bi-person-vcard me-1"></i>MRN: {{ $patient->patient_no }}
                            </span>
                            <span class="badge hims-badge-{{ strtolower($patient->patient_type) === 'inpatient' ? 'blue' : 'green' }} px-2 py-1" style="font-size: 0.75rem;">
                                {{ $patient->patient_type }}
                            </span>
                            @if($patient->ward)
                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $patient->ward }} (Bed {{ $patient->bed_number ?? '-' }})
                                </span>
                            @endif
                        </div>
                        <div class="small text-secondary d-flex flex-wrap align-items-center gap-3">
                            <span><strong>Sex:</strong> {{ $patient->gender }}</span>
                            <span class="text-muted">•</span>
                            <span><strong>Age:</strong> {{ $context['patient']['age'] }} <span class="text-muted">(DOB: {{ $context['patient']['date_of_birth'] ?? 'N/A' }})</span></span>
                            <span class="text-muted">•</span>
                            <span><strong>Blood Type:</strong> <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-2">{{ $patient->blood_type ?? 'N/A' }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                @php
                    $activeProvider = config('ai.provider', 'mock');
                    $isEvidence = strtolower($activeProvider) === 'evidencemd';
                @endphp
                @if($isEvidence)
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 bg-success bg-opacity-10 border border-success-subtle rounded-3 text-start">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <div>
                            <div class="fw-semibold text-success small" style="line-height: 1.2;">EvidenceMD Clinical Engine</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Decision Support Active</div>
                        </div>
                    </div>
                @else
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 bg-light border rounded-3 text-start">
                        <i class="bi bi-database text-secondary fs-5"></i>
                        <div>
                            <div class="fw-semibold text-dark small" style="line-height: 1.2;">Clinical Engine (Development)</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Mock AI Provider</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Left Column --}}
    <div class="col-lg-5">

        {{-- Clinical Findings --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clipboard-pulse me-2"></i>Doctor Clinical Findings</span>
                <small class="text-muted">Required for Symptom Assessment</small>
            </div>
            <div class="card-body">
                <form id="findingsForm" method="POST" action="{{ route('medisense.update-findings', $patient) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="clinical_findings" class="form-label small text-muted fw-semibold">Presenting Symptoms &amp; Physical Exam Notes</label>
                        <textarea class="form-control" id="clinical_findings" name="clinical_findings" rows="4"
                                  placeholder="Enter patient reported symptoms, chief complaint, or physical exam findings here...">{{ old('clinical_findings', $patient->clinical_findings) }}</textarea>
                        <div class="form-text">Updating this field provides clinical input context for MediSense Symptom Assessment.</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span id="findingsAlert" class="small text-success fw-semibold opacity-0"><i class="bi bi-check-lg me-1"></i>Saved!</span>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Save Findings</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- HIMS Clinical Records Accordion --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-database me-2"></i>HIMS Integrated Clinical Records</div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="clinicalContextAccordion">

                    {{-- LIS --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed small fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLab">
                                <i class="bi bi-droplet text-danger me-2"></i>Laboratory Results (LIS)
                                <span class="badge bg-secondary ms-auto me-2">{{ count($context['recent_lab_results']) }}</span>
                            </button>
                        </h2>
                        <div id="collapseLab" class="accordion-collapse collapse" data-bs-parent="#clinicalContextAccordion">
                            <div class="accordion-body small">
                                @forelse($context['recent_lab_results'] as $lab)
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="fw-bold">{{ $lab['test_name'] }}</div>
                                        <div>Value: <strong class="text-primary">{{ $lab['result_value'] }}</strong></div>
                                        @if($lab['remarks'])<div class="text-muted">Remarks: {{ $lab['remarks'] }}</div>@endif
                                        <div class="text-muted" style="font-size:.75rem">Released: {{ $lab['released_at'] }}</div>
                                    </div>
                                @empty
                                    <span class="text-muted">No released laboratory results found.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- RIS --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed small fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRad">
                                <i class="bi bi-file-earmark-medical text-primary me-2"></i>Radiology Reports (RIS)
                                <span class="badge bg-secondary ms-auto me-2">{{ count($context['recent_radiology_reports']) }}</span>
                            </button>
                        </h2>
                        <div id="collapseRad" class="accordion-collapse collapse" data-bs-parent="#clinicalContextAccordion">
                            <div class="accordion-body small">
                                @forelse($context['recent_radiology_reports'] as $rad)
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="fw-bold">{{ $rad['modality'] }} -- {{ $rad['body_part'] }}</div>
                                        <div><strong>Impression:</strong> {{ $rad['impression'] }}</div>
                                        <div class="text-muted" style="font-size:.75rem">Approved: {{ $rad['released_at'] }}</div>
                                    </div>
                                @empty
                                    <span class="text-muted">No approved radiology reports found.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- PMS --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed small fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRx">
                                <i class="bi bi-capsule text-info me-2"></i>Prescriptions (PMS)
                                <span class="badge bg-secondary ms-auto me-2">{{ count($context['active_prescriptions']) }}</span>
                            </button>
                        </h2>
                        <div id="collapseRx" class="accordion-collapse collapse" data-bs-parent="#clinicalContextAccordion">
                            <div class="accordion-body small">
                                @forelse($context['active_prescriptions'] as $rx)
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="fw-bold">Rx #{{ $rx['prescription_no'] }} ({{ $rx['status'] }})</div>
                                        <ul class="mb-1 ps-3">
                                            @foreach($rx['medications'] as $med)<li>{{ $med }}</li>@endforeach
                                        </ul>
                                    </div>
                                @empty
                                    <span class="text-muted">No active prescriptions on file.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- SORS & DNMS --}}
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed small fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOther">
                                <i class="bi bi-hospital text-secondary me-2"></i>Surgery (SORS) &amp; Nutrition (DNMS)
                            </button>
                        </h2>
                        <div id="collapseOther" class="accordion-collapse collapse" data-bs-parent="#clinicalContextAccordion">
                            <div class="accordion-body small">
                                <div class="fw-bold mb-1">Surgery Requests:</div>
                                @forelse($context['surgery_requests'] as $surg)
                                    <div class="mb-1">&bull; {{ $surg['procedure_name'] }} ({{ $surg['status'] }})</div>
                                @empty
                                    <div class="text-muted mb-2">None recorded</div>
                                @endforelse
                                <div class="fw-bold mb-1 mt-2">Diet Requests:</div>
                                @forelse($context['diet_requests'] as $diet)
                                    <div class="mb-1">&bull; {{ $diet['diet_type'] }} ({{ $diet['status'] }})</div>
                                @empty
                                    <div class="text-muted">None requested</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-7">

        {{-- CDS Action Buttons --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-lightning-charge text-warning me-2"></i>Clinical Decision Support Functions</span>
                <small class="text-muted">Select a task to execute MediSense analysis</small>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-task-action" data-task="SYMPTOM_ASSESSMENT">
                        <i class="bi bi-clipboard2-pulse text-primary me-1"></i>Assess Symptoms
                    </button>
                    <button type="button" class="btn btn-task-action" data-task="DIAGNOSTIC_ASSISTANCE">
                        <i class="bi bi-search-heart text-success me-1"></i>Diagnostic Assistance
                    </button>
                    <button type="button" class="btn btn-task-action" data-task="TREATMENT_RECOMMENDATION">
                        <i class="bi bi-capsule text-info me-1"></i>Treatment Recommendation
                    </button>
                    <button type="button" class="btn btn-task-action" data-task="CLINICAL_SERVICE_ASSISTANCE">
                        <i class="bi bi-hospital text-secondary me-1"></i>Clinical Service Assistance
                    </button>
                </div>
            </div>
        </div>

        {{-- Loading --}}
        <div id="mediSenseLoading" class="card d-none">
            <div class="card-body text-center py-4">
                <div class="spinner-border text-success mb-3" role="status" style="width:2.5rem;height:2.5rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 class="fw-bold mb-1">Processing Clinical Context...</h6>
                <p class="text-muted small mb-0">MediSense is passing the patient context through the provider pipeline.</p>
            </div>
        </div>

        {{-- Output --}}
        <div id="mediSenseOutputContainer">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-cpu-fill text-muted opacity-50 display-4 d-block mb-3"></i>
                    <h6 class="fw-bold mb-2">MediSense AI Ready</h6>
                    <p class="text-muted small mx-auto mb-0" style="max-width:440px">
                        Select one of the four clinical decision support functions above to run MediSense AI analysis for <strong>{{ $patient->full_name }}</strong>.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const actionButtons    = document.querySelectorAll('.btn-task-action');
    const outputContainer  = document.getElementById('mediSenseOutputContainer');
    const loadingContainer = document.getElementById('mediSenseLoading');
    const findingsForm     = document.getElementById('findingsForm');
    const findingsAlert    = document.getElementById('findingsAlert');

    // ── Clinical Findings Save (AJAX) ────────────────────────────────────────
    if (findingsForm) {
        findingsForm.addEventListener('submit', function (e) {
            e.preventDefault();
            fetch(findingsForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(findingsForm)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    findingsAlert.classList.remove('opacity-0');
                    setTimeout(() => findingsAlert.classList.add('opacity-0'), 3000);
                }
            })
            .catch(err => console.error('Save findings error:', err));
        });
    }

    // ── CDS Task Execution ───────────────────────────────────────────────────
    actionButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const task = this.getAttribute('data-task');

            outputContainer.classList.add('d-none');
            loadingContainer.classList.remove('d-none');

            actionButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            fetch("{{ route('medisense.analyze', $patient) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ task })
            })
            .then(r => r.json())
            .then(resData => {
                loadingContainer.classList.add('d-none');
                outputContainer.classList.remove('d-none');
                if (resData.success && resData.data) {
                    if (resData.data.category === 'insufficient_credits') {
                        renderInsufficientCreditsError();
                    } else {
                        renderMediSenseResult(resData.data);
                    }
                } else {
                    renderError(resData.message || 'An error occurred.');
                }
            })
            .catch(err => {
                loadingContainer.classList.add('d-none');
                outputContainer.classList.remove('d-none');
                renderError(err.message);
            });
        });
    });

    // ── Insufficient EvidenceMD Credits Error Renderer ────────────────────────
    function renderInsufficientCreditsError() {
        outputContainer.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="text-warning mb-3">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Analysis Unavailable</h5>
                    <h6 class="fw-semibold text-secondary mb-3">Insufficient EvidenceMD Credits</h6>
                    <p class="text-muted small mb-3 mx-auto" style="max-width: 520px; line-height: 1.5;">
                        The MediSense clinical analysis could not be completed because the EvidenceMD service currently has insufficient API credits to process this request.
                    </p>
                    <p class="text-muted small mb-0 mx-auto" style="max-width: 520px; line-height: 1.5;">
                        Please contact the system administrator to replenish or verify the EvidenceMD API credits before trying again.
                    </p>
                </div>
            </div>`;
    }

    // ── Doctor-Facing Error renderer ─────────────────────────────────────────
    function renderError(msg) {
        outputContainer.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <div class="text-warning mb-3">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Analysis Unavailable</h5>
                    <h6 class="fw-semibold text-secondary mb-3">The clinical analysis could not be completed.</h6>
                    <p class="text-muted small mb-0 mx-auto" style="max-width: 500px; line-height: 1.5;">
                        The MediSense clinical decision-support service is temporarily unable to process this request. Please try again later or contact the system administrator if the problem persists.
                    </p>
                </div>
            </div>`;
    }

    // ── Doctor-Facing MediSense Result renderer ──────────────────────────────
    function renderMediSenseResult(data) {
        if (data && data.category === 'insufficient_credits') {
            renderInsufficientCreditsError();
            return;
        }
        const taskMeta = {
            'SYMPTOM_ASSESSMENT':          { label: 'Symptom Assessment',         icon: 'bi-clipboard2-pulse' },
            'DIAGNOSTIC_ASSISTANCE':       { label: 'Diagnostic Assistance',       icon: 'bi-search-heart'     },
            'TREATMENT_RECOMMENDATION':    { label: 'Treatment Recommendation',    icon: 'bi-capsule'          },
            'CLINICAL_SERVICE_ASSISTANCE': { label: 'Clinical Service Support',    icon: 'bi-hospital'         },
        };
        const meta = taskMeta[data.task] || { label: data.task || 'Clinical Decision Support', icon: 'bi-cpu' };

        // ── Patient Context & Findings ───────────────────────────────────────
        const ctx = data.context_received || {};
        const patientName = ctx.patient_name || "{{ $patient->full_name }}";
        const ageSex = (ctx.age || "{{ $context['patient']['age'] }}") + ' • ' + (ctx.gender || "{{ $patient->gender }}");
        const encounter = ctx.patient_type || "{{ $patient->patient_type }}";
        const findingsText = data.findings_received || "{{ $patient->clinical_findings ?: 'No clinical findings recorded.' }}";

        // ── HIMS Context Counts ──────────────────────────────────────────────
        const hims = data.hims_context || {};
        const labText  = (hims.laboratory_results ?? 0) ? `${hims.laboratory_results} released result(s)` : 'None available';
        const radText  = (hims.radiology_reports ?? 0)  ? `${hims.radiology_reports} approved report(s)` : 'None available';
        const rxText   = (hims.active_prescriptions ?? 0)? `${hims.active_prescriptions} active prescription(s)` : 'None';
        const surgText = (hims.surgery_requests ?? 0)   ? `${hims.surgery_requests} record(s)` : 'None';
        const dietText = (hims.diet_requests ?? 0)      ? `${hims.diet_requests} record(s)` : 'None';

        // ── Considerations & Next Steps Blocks ───────────────────────────────
        const renderListItems = (items) => {
            if (!items || !items.length) return '';
            return `<ul class="list-unstyled mb-0 small text-dark ms-1">
                ${items.map(item => `<li class="mb-2 d-flex align-items-start gap-2">
                    <i class="bi bi-check2-circle text-primary flex-shrink-0 mt-1"></i>
                    <span>${item}</span>
                </li>`).join('')}
            </ul>`;
        };

        const keyFindingsItems = data.clinical_considerations || [];
        const nextStepsItems   = [
            ...(data.diagnostic_considerations || []),
            ...(data.treatment_considerations || []),
            ...(data.clinical_service_considerations || [])
        ];

        outputContainer.innerHTML = `
            <div class="card border-0 shadow-sm mb-4">
                {{-- Header --}}
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-cpu-fill text-primary"></i>MediSense AI
                            </h5>
                            <span class="text-muted small fw-semibold">Clinical Decision Support</span>
                        </div>
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6 fw-semibold">
                                <i class="bi ${meta.icon} me-1"></i>Selected Task: ${meta.label}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- Patient Context Card --}}
                    <div class="bg-light p-3 rounded-3 border mb-4">
                        <div class="row g-3 small">
                            <div class="col-md-4">
                                <span class="text-muted d-block uppercase fw-semibold" style="font-size:0.75rem;">Patient</span>
                                <strong class="text-dark fs-6">${patientName}</strong>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted d-block uppercase fw-semibold" style="font-size:0.75rem;">Age / Sex</span>
                                <strong class="text-dark">${ageSex}</strong>
                            </div>
                            <div class="col-md-4">
                                <span class="text-muted d-block uppercase fw-semibold" style="font-size:0.75rem;">Encounter</span>
                                <strong class="text-dark">${encounter}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Clinical Findings --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing:.04em">Clinical Findings</h6>
                        <div class="p-3 bg-white border rounded-3 small text-dark" style="line-height: 1.5;">${findingsText}</div>
                    </div>

                    {{-- Available Clinical Information --}}
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing:.04em">Available Clinical Information</h6>
                        <div class="row g-2 small">
                            <div class="col-6 col-md-4">
                                <div class="p-2 border rounded bg-light">
                                    <span class="text-muted d-block" style="font-size:0.75rem;">Laboratory Results</span>
                                    <strong class="text-dark">${labText}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="p-2 border rounded bg-light">
                                    <span class="text-muted d-block" style="font-size:0.75rem;">Radiology Reports</span>
                                    <strong class="text-dark">${radText}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="p-2 border rounded bg-light">
                                    <span class="text-muted d-block" style="font-size:0.75rem;">Active Prescriptions</span>
                                    <strong class="text-dark">${rxText}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-6">
                                <div class="p-2 border rounded bg-light">
                                    <span class="text-muted d-block" style="font-size:0.75rem;">Surgery Records</span>
                                    <strong class="text-dark">${surgText}</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-6">
                                <div class="p-2 border rounded bg-light">
                                    <span class="text-muted d-block" style="font-size:0.75rem;">Nutrition Records</span>
                                    <strong class="text-dark">${dietText}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Analysis Summary Block --}}
                    <div class="card border mb-4">
                        <div class="card-header bg-white py-2 fw-bold text-dark">
                            <i class="bi bi-file-earmark-medical me-2 text-primary"></i>Analysis Summary
                        </div>
                        <div class="card-body p-3">
                            {{-- Clinical Summary --}}
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-1 small text-uppercase text-secondary">Clinical Summary</h6>
                                <p class="small text-dark mb-0" style="line-height: 1.5;">${data.summary || 'MediSense analysis complete.'}</p>
                            </div>

                            {{-- Key Findings --}}
                            ${keyFindingsItems.length ? `
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-2 small text-uppercase text-secondary">Key Findings</h6>
                                ${renderListItems(keyFindingsItems)}
                            </div>` : ''}

                            {{-- Clinical Considerations / Suggested Next Steps --}}
                            ${nextStepsItems.length ? `
                            <div class="mb-3">
                                <h6 class="fw-bold text-dark mb-2 small text-uppercase text-secondary">Suggested Next Steps</h6>
                                ${renderListItems(nextStepsItems)}
                            </div>` : ''}

                            {{-- Doctor's Review --}}
                            <div class="p-3 bg-light rounded border-start border-3 border-primary small text-secondary">
                                <strong class="d-block mb-1 text-dark">Doctor's Review</strong>
                                Please evaluate these suggestions in the context of the patient's complete clinical record and current clinical presentation. The attending doctor remains responsible for final clinical decisions.
                            </div>
                        </div>
                    </div>

                    {{-- MediSense AI Notice --}}
                    <div class="p-3 bg-light rounded-3 border small text-muted">
                        <strong>MediSense AI Notice:</strong> MediSense AI provides intelligent clinical decision support for Symptom Assessment, Diagnostic Assistance, Treatment Recommendation, and Clinical Service Support. Information generated by MediSense AI is intended to assist the attending Doctor and does not replace professional clinical judgment. The attending Doctor remains responsible for final clinical decisions, diagnoses, treatments, and orders.
                    </div>
                </div>
            </div>`;
    }
});
</script>
@endsection
