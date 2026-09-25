{{-- MediSense AI — Structured Result Component --}}
@php
    $taskLabels = [
        'SYMPTOM_ASSESSMENT'          => ['label' => 'Symptom Assessment', 'icon' => 'bi-clipboard2-pulse'],
        'DIAGNOSTIC_ASSISTANCE'       => ['label' => 'Diagnostic Assistance', 'icon' => 'bi-search-heart'],
        'TREATMENT_RECOMMENDATION'    => ['label' => 'Treatment Recommendation', 'icon' => 'bi-capsule'],
        'CLINICAL_SERVICE_ASSISTANCE' => ['label' => 'Clinical Service Support', 'icon' => 'bi-hospital'],
    ];

    $taskInfo = $taskLabels[$data['task'] ?? ''] ?? ['label' => $data['task'] ?? 'Clinical Decision Support', 'icon' => 'bi-cpu'];
    $ctx = $data['context_received'] ?? [];
    $hims = $data['hims_context'] ?? [];

    $labText  = !empty($hims['laboratory_results']) ? $hims['laboratory_results'] . ' released result(s)' : 'None available';
    $radText  = !empty($hims['radiology_reports'])  ? $hims['radiology_reports']  . ' approved report(s)' : 'None available';
    $rxText   = !empty($hims['active_prescriptions'])? $hims['active_prescriptions'] . ' active prescription(s)' : 'None';
    $surgText = !empty($hims['surgery_requests'])   ? $hims['surgery_requests']   . ' record(s)' : 'None';
    $dietText = !empty($hims['diet_requests'])      ? $hims['diet_requests']      . ' record(s)' : 'None';

    $keyFindingsItems = $data['clinical_considerations'] ?? [];
    $nextStepsItems = array_merge(
        $data['diagnostic_considerations'] ?? [],
        $data['treatment_considerations'] ?? [],
        $data['clinical_service_considerations'] ?? []
    );
@endphp

<div class="medisense-result-card card border-0 shadow-sm mb-4">
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
                    <i class="bi {{ $taskInfo['icon'] }} me-1"></i>Selected Task: {{ $taskInfo['label'] }}
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
                    <strong class="text-dark fs-6">{{ $ctx['patient_name'] ?? ($patient->full_name ?? '-') }}</strong>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block uppercase fw-semibold" style="font-size:0.75rem;">Age / Sex</span>
                    <strong class="text-dark">{{ $ctx['age'] ?? ($patient->age ?? '-') }} • {{ $ctx['gender'] ?? ($patient->gender ?? '-') }}</strong>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block uppercase fw-semibold" style="font-size:0.75rem;">Encounter</span>
                    <strong class="text-dark">{{ $ctx['patient_type'] ?? ($patient->patient_type ?? '-') }}</strong>
                </div>
            </div>
        </div>

        {{-- Clinical Findings --}}
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing:.04em">Clinical Findings</h6>
            <div class="p-3 bg-white border rounded-3 small text-dark" style="line-height: 1.5;">
                {{ $data['findings_received'] ?? ($patient->clinical_findings ?? 'No clinical findings recorded.') }}
            </div>
        </div>

        {{-- Available Clinical Information --}}
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing:.04em">Available Clinical Information</h6>
            <div class="row g-2 small">
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded bg-light">
                        <span class="text-muted d-block" style="font-size:0.75rem;">Laboratory Results</span>
                        <strong class="text-dark">{{ $labText }}</strong>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded bg-light">
                        <span class="text-muted d-block" style="font-size:0.75rem;">Radiology Reports</span>
                        <strong class="text-dark">{{ $radText }}</strong>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-2 border rounded bg-light">
                        <span class="text-muted d-block" style="font-size:0.75rem;">Active Prescriptions</span>
                        <strong class="text-dark">{{ $rxText }}</strong>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="p-2 border rounded bg-light">
                        <span class="text-muted d-block" style="font-size:0.75rem;">Surgery Records</span>
                        <strong class="text-dark">{{ $surgText }}</strong>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="p-2 border rounded bg-light">
                        <span class="text-muted d-block" style="font-size:0.75rem;">Nutrition Records</span>
                        <strong class="text-dark">{{ $dietText }}</strong>
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
                    <p class="small text-dark mb-0" style="line-height: 1.5;">{{ $data['summary'] ?? 'MediSense analysis complete.' }}</p>
                </div>

                {{-- Key Findings --}}
                @if(!empty($keyFindingsItems))
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2 small text-uppercase text-secondary">Key Findings</h6>
                        <ul class="list-unstyled mb-0 small text-dark ms-1">
                            @foreach($keyFindingsItems as $item)
                                <li class="mb-2 d-flex align-items-start gap-2">
                                    <i class="bi bi-check2-circle text-primary flex-shrink-0 mt-1"></i>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Suggested Next Steps --}}
                @if(!empty($nextStepsItems))
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark mb-2 small text-uppercase text-secondary">Suggested Next Steps</h6>
                        <ul class="list-unstyled mb-0 small text-dark ms-1">
                            @foreach($nextStepsItems as $item)
                                <li class="mb-2 d-flex align-items-start gap-2">
                                    <i class="bi bi-check2-circle text-primary flex-shrink-0 mt-1"></i>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
</div>
