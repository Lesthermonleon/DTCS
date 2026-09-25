<?php

namespace App\Services\MediSense;

use App\Models\Patient;
use Carbon\Carbon;

/**
 * ClinicalContextBuilder — Aggregates clinical information across HIMS modules.
 *
 * Gathers relevant patient data from:
 * - Patient Directory (Demographics, Doctor clinical findings)
 * - LIS (Released laboratory results & test values)
 * - RIS (Approved/released radiology reports & impressions)
 * - PMS (Active prescriptions & medications)
 * - SORS (Surgery requests & procedures)
 * - DNMS (Diet & nutrition requests)
 *
 * Does NOT duplicate clinical data — queries existing models on demand.
 */
class ClinicalContextBuilder
{
    /**
     * Build a structured clinical context array for a given patient.
     *
     * @param Patient $patient
     * @return array
     */
    public function build(Patient $patient): array
    {
        $patient->loadMissing([
            'labRequests.items.result',
            'radiologyRequests.report',
            'prescriptions.items',
            'surgeryRequests',
            'dietRequests',
        ]);

        // Calculate age
        $dob = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth) : null;
        $age = $dob ? $dob->age . ' yrs' : 'N/A';

        // 1. Patient Demographics & Doctor Findings
        $patientContext = [
            'id'                     => $patient->id,
            'patient_no'             => $patient->patient_no,
            'full_name'              => $patient->full_name,
            'gender'                 => $patient->gender,
            'date_of_birth'          => $dob?->format('Y-m-d'),
            'age'                    => $age,
            'blood_type'             => $patient->blood_type ?? 'Unspecified',
            'patient_type'           => $patient->patient_type ?? 'Outpatient',
            'ward'                   => $patient->ward,
            'bed_number'             => $patient->bed_number,
            'clinical_findings'      => $patient->clinical_findings,
        ];

        // 2. Gather Laboratory (LIS) Released Results
        $recentLabResults = [];
        $labSummaryText = [];
        foreach ($patient->labRequests as $req) {
            foreach ($req->items as $item) {
                $res = $item->result;
                if ($res && in_array($res->status, ['Released', 'Validated'])) {
                    $entry = [
                        'test_name'    => $item->test_name ?? ($item->labTest?->name ?? 'Lab Test'),
                        'result_value' => $res->result_value,
                        'remarks'      => $res->remarks,
                        'status'       => $res->status,
                        'released_at'  => $res->released_at?->format('Y-m-d H:i'),
                    ];
                    $recentLabResults[] = $entry;
                    $labSummaryText[] = "{$entry['test_name']}: {$entry['result_value']} ({$entry['status']})";
                }
            }
        }

        // 3. Gather Radiology (RIS) Approved/Released Reports
        $recentRadReports = [];
        $radSummaryText = [];
        foreach ($patient->radiologyRequests as $req) {
            if ($req->report && in_array($req->report->status, ['Approved', 'Released'])) {
                $report = $req->report;
                $entry = [
                    'modality'        => $req->modality,
                    'body_part'       => $req->body_part,
                    'findings'        => $report->findings,
                    'impression'      => $report->impression,
                    'recommendations' => $report->recommendations,
                    'status'          => $report->status,
                    'released_at'     => $report->released_at?->format('Y-m-d H:i') ?? $report->approved_at?->format('Y-m-d H:i'),
                ];
                $recentRadReports[] = $entry;
                $radSummaryText[] = "{$entry['modality']} ({$entry['body_part']}): Impression — {$entry['impression']}";
            }
        }

        // 4. Gather Pharmacy (PMS) Active Prescriptions
        $activePrescriptions = [];
        $rxSummaryText = [];
        foreach ($patient->prescriptions as $rx) {
            if (in_array($rx->status, ['Verified', 'Pending', 'Partially Dispensed', 'Dispensed'])) {
                $meds = [];
                foreach ($rx->items as $item) {
                    $meds[] = "{$item->medication_name} ({$item->dosage}, {$item->frequency})";
                }
                $entry = [
                    'prescription_no' => $rx->prescription_no,
                    'diagnosis'       => $rx->diagnosis,
                    'status'          => $rx->status,
                    'medications'     => $meds,
                ];
                $activePrescriptions[] = $entry;
                $rxSummaryText[] = "Rx #{$rx->prescription_no}: " . implode(', ', $meds);
            }
        }

        // 5. Gather Surgery (SORS) Requests
        $surgeryRequests = [];
        foreach ($patient->surgeryRequests as $surg) {
            $surgeryRequests[] = [
                'request_no'     => $surg->request_no,
                'procedure_name' => $surg->procedure_name,
                'urgency'        => $surg->urgency,
                'status'         => $surg->status,
            ];
        }

        // 6. Gather Diet (DNMS) Requests
        $dietRequests = [];
        $dietSummaryText = [];
        foreach ($patient->dietRequests as $diet) {
            $entry = [
                'request_no' => $diet->request_no,
                'diet_type'  => $diet->diet_type,
                'status'     => $diet->status,
                'allergies'  => $diet->allergies,
            ];
            $dietRequests[] = $entry;
            $dietSummaryText[] = "Diet: {$diet->diet_type} (Status: {$diet->status})";
        }

        return [
            'patient'                      => $patientContext,
            'symptoms_and_findings'        => $patient->clinical_findings ?: null,
            'recent_lab_results'           => $recentLabResults,
            'recent_lab_results_summary'   => !empty($labSummaryText) ? implode('; ', $labSummaryText) : 'No released lab results',
            'recent_radiology_reports'     => $recentRadReports,
            'recent_radiology_summary'     => !empty($radSummaryText) ? implode('; ', $radSummaryText) : 'No approved radiology reports',
            'active_prescriptions'         => $activePrescriptions,
            'active_prescriptions_summary' => !empty($rxSummaryText) ? implode('; ', $rxSummaryText) : 'No active prescriptions on file',
            'surgery_requests'             => $surgeryRequests,
            'diet_requests'                => $dietRequests,
            'active_diet_summary'          => !empty($dietSummaryText) ? implode('; ', $dietSummaryText) : 'No active diet plan requested',
            'generated_at'                 => now()->toIso8601String(),
        ];
    }
}
