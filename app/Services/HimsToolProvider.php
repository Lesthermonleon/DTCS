<?php

namespace App\Services;

use App\Models\DietPlan;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\RadiologyReport;
use App\Models\RadiologyRequest;
use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * HimsToolProvider — Controlled Laravel function tools for HIMS database interactions.
 * Provides semantically rich tool declarations for Gemini Function Calling across all HIMS modules.
 */
class HimsToolProvider
{
    /**
     * Get Gemini function declarations schema for HIMS tools with rich semantic descriptions.
     */
    public function getToolDeclarations(): array
    {
        return [
            [
                'name'        => 'getUserWorkloadSummary',
                'description' => 'Retrieves prioritized workload, pending requests, overdue items, and critical alerts relevant for the logged-in user\'s role (Doctor, Nurse, MedTech, RadTech, Pharmacist, Dietitian, OR Coordinator, Admin). Use whenever the user asks open-ended questions like "What should I focus on today?", "What needs my attention?", "What is on my schedule?", "What is unfinished?", or "Show my workload".',
            ],
            [
                'name'        => 'searchPatients',
                'description' => 'Searches for patients in the HIMS database by name, patient number (e.g. P-1001), or search keyword. Use whenever the user asks to look up, find, or search for a patient.',
                'parameters'  => [
                    'type'       => 'OBJECT',
                    'properties' => [
                        'query' => ['type' => 'STRING', 'description' => 'Patient first name, last name, patient_no, or identifier'],
                    ],
                    'required'   => ['query'],
                ],
            ],
            [
                'name'        => 'getPatientSummary',
                'description' => 'Retrieves a full clinical summary for a specific patient, including demographics, blood type, recent laboratory results, radiology findings, and active medication prescriptions.',
                'parameters'  => [
                    'type'       => 'OBJECT',
                    'properties' => [
                        'patientId' => ['type' => 'INTEGER', 'description' => 'Database ID of the patient'],
                    ],
                    'required'   => ['patientId'],
                ],
            ],
            [
                'name'        => 'getLabResults',
                'description' => 'Retrieves laboratory test results (e.g., CBC, Urinalysis, Blood Glucose, Electrolytes). Can filter by a specific patient or fetch general recent hospital lab results. Use when checking test values, abnormal findings, or result trends.',
                'parameters'  => [
                    'type'       => 'OBJECT',
                    'properties' => [
                        'patientId' => ['type' => 'INTEGER', 'description' => 'Optional patient ID'],
                        'limit'     => ['type' => 'INTEGER', 'description' => 'Max number of results to return (default: 5)'],
                    ],
                ],
            ],
            [
                'name'        => 'getPendingLabRequests',
                'description' => 'Retrieves laboratory requests that are pending, in-progress, or waiting for attention in the Laboratory Information System (LIS). Use when the user asks about unfinished lab work, LIS queue, pending orders, or lab workload.',
            ],
            [
                'name'        => 'getRadiologyReports',
                'description' => 'Retrieves completed radiology imaging reports (X-ray, CT, MRI, Ultrasound) and radiologist impressions for a patient or overall queue.',
                'parameters'  => [
                    'type'       => 'OBJECT',
                    'properties' => [
                        'patientId' => ['type' => 'INTEGER', 'description' => 'Optional patient ID'],
                    ],
                ],
            ],
            [
                'name'        => 'getPendingRadiologyRequests',
                'description' => 'Retrieves radiology and imaging procedure requests that are pending or scheduled in the Radiology Information System (RIS). Use when checking radiology workload or pending scans.',
            ],
            [
                'name'        => 'getMedicationOrders',
                'description' => 'Retrieves active medication prescriptions, dosage, administration routes, and pharmacy orders for a patient or department.',
                'parameters'  => [
                    'type'       => 'OBJECT',
                    'properties' => [
                        'patientId' => ['type' => 'INTEGER', 'description' => 'Optional patient ID'],
                    ],
                ],
            ],
            [
                'name'        => 'getPendingPrescriptions',
                'description' => 'Retrieves pharmacy prescriptions awaiting verification, dispensing, or processing in the Pharmacy Management System (PMS).',
            ],
            [
                'name'        => 'getSurgerySchedule',
                'description' => 'Retrieves scheduled operating room (OR) surgical procedures, surgical teams, and operating room allocations in the Surgical Operating Room System (SORS).',
            ],
            [
                'name'        => 'getDietPlans',
                'description' => 'Retrieves clinical diet plans, therapeutic nutrition orders, and meal instructions in the Dietary and Nutrition Management System (DNMS).',
                'parameters'  => [
                    'type'       => 'OBJECT',
                    'properties' => [
                        'patientId' => ['type' => 'INTEGER', 'description' => 'Optional patient ID'],
                    ],
                ],
            ],
            [
                'name'        => 'getCriticalAlerts',
                'description' => 'Retrieves critical lab values, high-risk patient alerts, and emergency findings requiring immediate clinical action.',
            ],
            [
                'name'        => 'createLabRequest',
                'description' => 'Submits a request to create a new laboratory test order (e.g. CBC, Lipid Panel) for a patient. CRITICAL: This is a write action and requires user confirmation before final execution.',
                'parameters'  => [
                    'type'       => 'OBJECT',
                    'properties' => [
                        'patientId'     => ['type' => 'INTEGER', 'description' => 'Patient database ID'],
                        'testName'      => ['type' => 'STRING', 'description' => 'Name or type of lab test (e.g. CBC, Urinalysis, Electrolytes)'],
                        'clinicalNotes' => ['type' => 'STRING', 'description' => 'Clinical indication or reasoning'],
                        'urgency'       => ['type' => 'STRING', 'description' => 'Urgency level: routine, urgent, stat'],
                        'confirmed'     => ['type' => 'BOOLEAN', 'description' => 'Must be set to true ONLY after explicit user confirmation'],
                    ],
                    'required'   => ['patientId', 'testName'],
                ],
            ],
        ];
    }

    /**
     * Execute a function call requested by Gemini.
     */
    public function executeTool(string $functionName, array $arguments, User $user): array
    {
        Log::info("HimsToolProvider executing function: {$functionName}", ['user' => $user->id, 'args' => $arguments]);

        try {
            switch ($functionName) {
                case 'getUserWorkloadSummary':
                case 'getPendingTasks':
                    return $this->getUserWorkloadSummary($user);
                case 'searchPatients':
                    return $this->searchPatients($arguments['query'] ?? '', $user);
                case 'getPatient':
                case 'getPatientSummary':
                    return $this->getPatientSummary((int) ($arguments['patientId'] ?? 0), $user);
                case 'getLabResults':
                    return $this->getLabResults(isset($arguments['patientId']) ? (int) $arguments['patientId'] : null, (int) ($arguments['limit'] ?? 5), $user);
                case 'getPendingLabRequests':
                    return $this->getPendingLabRequests($user);
                case 'getRadiologyReports':
                    return $this->getRadiologyReports(isset($arguments['patientId']) ? (int) $arguments['patientId'] : null, $user);
                case 'getRadiologyRequests':
                case 'getPendingRadiologyRequests':
                    return $this->getPendingRadiologyRequests($user);
                case 'getMedicationOrders':
                    return $this->getMedicationOrders(isset($arguments['patientId']) ? (int) $arguments['patientId'] : null, $user);
                case 'getPendingPrescriptions':
                    return $this->getPendingPrescriptions($user);
                case 'getSurgerySchedule':
                    return $this->getSurgerySchedule($user);
                case 'getDietPlans':
                    return $this->getDietPlans(isset($arguments['patientId']) ? (int) $arguments['patientId'] : null, $user);
                case 'getCriticalAlerts':
                    return $this->getCriticalAlerts($user);
                case 'createLabRequest':
                    return $this->createLabRequest(
                        (int) ($arguments['patientId'] ?? 0),
                        $arguments['testName'] ?? 'General Lab Test',
                        $arguments['clinicalNotes'] ?? null,
                        $arguments['urgency'] ?? 'routine',
                        (bool) ($arguments['confirmed'] ?? false),
                        $user
                    );
                default:
                    return ['success' => false, 'error' => "Unknown function tool: {$functionName}"];
            }
        } catch (Throwable $e) {
            Log::error("HimsToolProvider Exception in {$functionName}: " . $e->getMessage());
            return ['success' => false, 'error' => "Error executing function {$functionName}: " . $e->getMessage()];
        }
    }

    public function getUserWorkloadSummary(User $user): array
    {
        $role = $user->primaryRole;

        $pendingLabs  = LabRequest::whereIn('status', ['pending', 'in_progress'])->count();
        $pendingRad   = RadiologyRequest::whereIn('status', ['pending', 'scheduled'])->count();
        $pendingRx    = Prescription::whereIn('status', ['pending', 'partially_dispensed'])->count();
        $pendingOr    = SurgerySchedule::where('status', 'scheduled')->count();

        $items = [];
        if (in_array($role, ['admin', 'med-tech', 'doctor'])) {
            $items[] = "Laboratory Queue (LIS): {$pendingLabs} pending test request(s)";
        }
        if (in_array($role, ['admin', 'rad-tech', 'radiologist', 'doctor'])) {
            $items[] = "Radiology Queue (RIS): {$pendingRad} pending imaging request(s)";
        }
        if (in_array($role, ['admin', 'pharmacist', 'doctor'])) {
            $items[] = "Pharmacy Queue (PMS): {$pendingRx} pending prescription(s)";
        }
        if (in_array($role, ['admin', 'or-coordinator', 'doctor'])) {
            $items[] = "Operating Room Queue (SORS): {$pendingOr} scheduled procedure(s)";
        }

        return [
            'success'          => true,
            'source'           => 'HIMS Database',
            'user_role'        => $user->roleName,
            'workload_summary' => $items,
            'metrics'          => [
                'pending_labs'       => $pendingLabs,
                'pending_radiology'  => $pendingRad,
                'pending_pharmacy'   => $pendingRx,
                'pending_surgeries'  => $pendingOr,
            ],
        ];
    }

    public function searchPatients(string $query, User $user): array
    {
        $patients = Patient::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('patient_no', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return [
            'success'  => true,
            'source'   => 'HIMS Database',
            'count'    => $patients->count(),
            'patients' => $patients->map(fn($p) => [
                'id'         => $p->id,
                'patient_no' => $p->patient_no,
                'full_name'  => $p->full_name,
                'gender'     => $p->gender,
                'dob'        => $p->date_of_birth,
                'blood_type' => $p->blood_type ?? 'N/A',
                'ward'       => $p->ward ?? 'Outpatient',
            ])->toArray(),
        ];
    }

    public function getPatientSummary(int $patientId, User $user): array
    {
        $patient = Patient::find($patientId);
        if (!$patient) {
            return ['success' => false, 'error' => "Patient ID {$patientId} not found."];
        }

        $labResults = LabResult::whereHas('labRequest', fn($q) => $q->where('patient_id', $patientId))
            ->latest()
            ->limit(5)
            ->get();

        $prescriptions = Prescription::where('patient_id', $patientId)
            ->latest()
            ->limit(5)
            ->get();

        return [
            'success' => true,
            'source'  => 'HIMS Database',
            'summary' => [
                'id'            => $patient->id,
                'patient'       => "{$patient->full_name} ({$patient->patient_no})",
                'gender'        => $patient->gender,
                'blood_type'    => $patient->blood_type,
                'lab_results'   => $labResults->map(fn($r) => [
                    'test_name'   => $r->labTest?->name ?? 'Lab Test',
                    'result'      => $r->result_value ?? 'Pending',
                    'unit'        => $r->unit,
                    'is_abnormal' => $r->is_abnormal ?? false,
                    'date'        => $r->created_at?->format('Y-m-d H:i'),
                ]),
                'prescriptions' => $prescriptions->map(fn($p) => [
                    'status' => $p->status,
                    'notes'  => $p->notes,
                    'date'   => $p->created_at?->format('Y-m-d'),
                ]),
            ],
        ];
    }

    public function getLabResults(?int $patientId, int $limit, User $user): array
    {
        $query = LabResult::with(['labTest', 'labRequest.patient'])->latest();
        if ($patientId) {
            $query->whereHas('labRequest', fn($q) => $q->where('patient_id', $patientId));
        }

        $results = $query->limit($limit)->get();

        return [
            'success'     => true,
            'source'      => 'HIMS Database',
            'count'       => $results->count(),
            'lab_results' => $results->map(fn($r) => [
                'id'          => $r->id,
                'patient'     => $r->labRequest?->patient?->full_name ?? 'N/A',
                'patient_no'  => $r->labRequest?->patient?->patient_no ?? 'N/A',
                'test'        => $r->labTest?->name ?? 'Lab Test',
                'value'       => $r->result_value,
                'unit'        => $r->unit,
                'reference'   => $r->reference_range,
                'is_abnormal' => $r->is_abnormal ?? false,
                'status'      => $r->status,
                'created_at'  => $r->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    public function getPendingLabRequests(User $user): array
    {
        $requests = LabRequest::with(['patient', 'doctor'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->limit(10)
            ->get();

        return [
            'success'          => true,
            'source'           => 'HIMS Database',
            'count'            => $requests->count(),
            'pending_requests' => $requests->map(fn($req) => [
                'request_no' => $req->request_no,
                'patient'    => $req->patient?->full_name ?? 'N/A',
                'patient_no' => $req->patient?->patient_no ?? 'N/A',
                'doctor'     => $req->doctor?->name ?? 'N/A',
                'urgency'    => $req->priority ?? 'routine',
                'status'     => $req->status,
                'created_at' => $req->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    public function getRadiologyReports(?int $patientId, User $user): array
    {
        $query = RadiologyReport::with(['radiologyRequest.patient'])->latest();
        if ($patientId) {
            $query->whereHas('radiologyRequest', fn($q) => $q->where('patient_id', $patientId));
        }

        $reports = $query->limit(5)->get();

        return [
            'success' => true,
            'source'  => 'HIMS Database',
            'reports' => $reports->map(fn($rep) => [
                'id'          => $rep->id,
                'patient'     => $rep->radiologyRequest?->patient?->full_name ?? 'N/A',
                'modality'    => $rep->radiologyRequest?->modality ?? 'Radiology',
                'impression'  => $rep->impression ?? $rep->findings,
                'status'      => $rep->status,
                'created_at'  => $rep->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    public function getPendingRadiologyRequests(User $user): array
    {
        $requests = RadiologyRequest::with(['patient'])
            ->whereIn('status', ['pending', 'scheduled'])
            ->latest()
            ->limit(10)
            ->get();

        return [
            'success'  => true,
            'source'   => 'HIMS Database',
            'count'    => $requests->count(),
            'requests' => $requests->map(fn($r) => [
                'id'         => $r->id,
                'patient'    => $r->patient?->full_name ?? 'N/A',
                'modality'   => $r->modality ?? 'Radiology',
                'status'     => $r->status,
                'created_at' => $r->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    public function getMedicationOrders(?int $patientId, User $user): array
    {
        $query = Prescription::with(['patient', 'doctor'])->latest();
        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        $prescriptions = $query->limit(5)->get();

        return [
            'success'       => true,
            'source'        => 'HIMS Database',
            'prescriptions' => $prescriptions->map(fn($p) => [
                'id'         => $p->id,
                'patient'    => $p->patient?->full_name ?? 'N/A',
                'doctor'     => $p->doctor?->name ?? 'N/A',
                'status'     => $p->status,
                'created_at' => $p->created_at?->format('Y-m-d'),
            ])->toArray(),
        ];
    }

    public function getPendingPrescriptions(User $user): array
    {
        $prescriptions = Prescription::with(['patient', 'doctor'])
            ->whereIn('status', ['pending', 'partially_dispensed'])
            ->latest()
            ->limit(10)
            ->get();

        return [
            'success'       => true,
            'source'        => 'HIMS Database',
            'count'         => $prescriptions->count(),
            'prescriptions' => $prescriptions->map(fn($p) => [
                'id'         => $p->id,
                'patient'    => $p->patient?->full_name ?? 'N/A',
                'patient_no' => $p->patient?->patient_no ?? 'N/A',
                'doctor'     => $p->doctor?->name ?? 'N/A',
                'status'     => $p->status,
                'created_at' => $p->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    public function getSurgerySchedule(User $user): array
    {
        $schedules = SurgerySchedule::with(['surgeryRequest.patient', 'operatingRoom'])
            ->latest()
            ->limit(10)
            ->get();

        return [
            'success'   => true,
            'source'    => 'HIMS Database',
            'schedules' => $schedules->map(fn($s) => [
                'id'         => $s->id,
                'patient'    => $s->surgeryRequest?->patient?->full_name ?? 'N/A',
                'procedure'  => $s->surgeryRequest?->procedure_name ?? 'Surgical Procedure',
                'room'       => $s->operatingRoom?->room_name ?? 'OR',
                'scheduled'  => $s->scheduled_start_time,
                'status'     => $s->status,
            ])->toArray(),
        ];
    }

    public function getDietPlans(?int $patientId, User $user): array
    {
        $query = DietPlan::with(['patient'])->latest();
        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        $plans = $query->limit(5)->get();

        return [
            'success' => true,
            'source'  => 'HIMS Database',
            'plans'   => $plans->map(fn($d) => [
                'id'        => $d->id,
                'patient'   => $d->patient?->full_name ?? 'N/A',
                'diet_type' => $d->diet_type,
                'notes'     => $d->special_instructions,
                'status'    => $d->status,
            ])->toArray(),
        ];
    }

    public function getCriticalAlerts(User $user): array
    {
        $criticalLabs = LabResult::with('requestItem')
            ->latest()
            ->limit(5)
            ->get();

        return [
            'success'         => true,
            'source'          => 'HIMS Database',
            'critical_alerts' => $criticalLabs->map(fn($r) => [
                'type'        => 'Recent Lab Result',
                'val'         => $r->result_value,
                'status'      => $r->status,
                'detected_at' => $r->created_at?->format('Y-m-d H:i'),
            ])->toArray(),
        ];
    }

    public function createLabRequest(
        int $patientId,
        string $testName,
        ?string $clinicalNotes,
        string $urgency,
        bool $confirmed,
        User $user
    ): array {
        $patient = Patient::find($patientId);
        if (!$patient) {
            return ['success' => false, 'error' => "Patient ID {$patientId} not found."];
        }

        if (!$confirmed) {
            return [
                'success'            => false,
                'requires_confirm'   => true,
                'action_details'     => [
                    'action'         => 'Create Laboratory Request',
                    'patient'        => "{$patient->full_name} ({$patient->patient_no})",
                    'patient_id'     => $patient->id,
                    'test_name'      => $testName,
                    'urgency'        => strtoupper($urgency),
                    'clinical_notes' => $clinicalNotes ?? 'None specified',
                    'requested_by'   => $user->name,
                ],
                'message' => "CONFIRMATION REQUIRED: Are you sure you want to create a {$urgency} laboratory request for {$testName} on patient {$patient->full_name}?",
            ];
        }

        $reqNo = 'LAB-REQ-' . strtoupper(uniqid());
        $labReq = LabRequest::create([
            'request_no'     => $reqNo,
            'patient_id'     => $patient->id,
            'doctor_id'      => $user->id,
            'priority'       => $urgency,
            'status'         => 'pending',
            'clinical_notes' => $clinicalNotes,
        ]);

        return [
            'success'    => true,
            'source'     => 'HIMS Database',
            'message'    => "Laboratory Request {$reqNo} successfully created for {$patient->full_name}.",
            'request_no' => $reqNo,
        ];
    }
}
