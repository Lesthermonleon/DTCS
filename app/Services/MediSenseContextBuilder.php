<?php

namespace App\Services;

use App\Models\DietPlan;
use App\Models\DietRequest;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\RadiologyReport;
use App\Models\RadiologyRequest;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * MediSenseContextBuilder — Builds role-authorized clinical context for Virtual MediSense AI.
 * Enforces strict data access boundaries based on user role and permissions.
 */
class MediSenseContextBuilder
{
    /**
     * Build contextual clinical payload for the AI prompt based on role and active context.
     *
     * @param  User  $user
     * @param  string  $capability
     * @param  int|null  $patientId
     * @param  array  $additionalContext
     * @return array Containing 'context_text', 'patient_info', and 'module_summary'
     */
    public function buildContext(User $user, string $capability, ?int $patientId = null, array $additionalContext = []): array
    {
        $roleSlug = $user->primaryRole;
        $contextLines = [];
        $patientSummary = null;

        // 1. Authenticated User Header
        $contextLines[] = "### Authorized User Context";
        $contextLines[] = "- User: {$user->name} (ID: {$user->id})";
        $contextLines[] = "- Role: {$user->roleName} (`{$roleSlug}`)";
        $contextLines[] = "- Department: " . ($user->department ?? 'Clinical Services');
        $contextLines[] = "";

        // 2. Patient Context (if authorized and patient_id supplied)
        if ($patientId && ($user->canAccessPatients() || in_array($roleSlug, ['med-tech', 'rad-tech', 'radiologist', 'pharmacist', 'dietitian', 'or-coordinator']))) {
            $patient = Patient::find($patientId);
            if ($patient) {
                $patientSummary = [
                    'id' => $patient->id,
                    'patient_no' => $patient->patient_no,
                    'name' => $patient->full_name,
                    'age' => $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : 'N/A',
                    'gender' => $patient->gender ?? 'Unspecified',
                    'blood_type' => $patient->blood_type ?? 'N/A',
                    'patient_type' => $patient->patient_type ?? 'Outpatient',
                    'ward' => $patient->ward ?? 'N/A',
                    'bed_number' => $patient->bed_number ?? 'N/A',
                ];

                $contextLines[] = "### Selected Patient Clinical Profile";
                $contextLines[] = "- Patient No: {$patient->patient_no}";
                $contextLines[] = "- Name: {$patient->full_name}";
                $contextLines[] = "- Age: {$patientSummary['age']} | Gender: {$patient->gender} | Blood Type: {$patient->blood_type}";
                $contextLines[] = "- Status: {$patient->patient_type} " . ($patient->ward ? "(Ward: {$patient->ward}, Bed: {$patient->bed_number})" : '');
                $contextLines[] = "";

                // Fetch role-specific authorized clinical history for this patient
                $this->appendPatientClinicalRecords($contextLines, $patient, $roleSlug);
            }
        }

        // 3. Operational Context (if no patient selected or for system/department role)
        if (! $patientId) {
            $this->appendOperationalContext($contextLines, $roleSlug);
        }

        // 4. Client-provided context (e.g. active form inputs, query text)
        if (! empty($additionalContext['custom_input'])) {
            $contextLines[] = "### User Session Input / Active Notes";
            $contextLines[] = $additionalContext['custom_input'];
            $contextLines[] = "";
        }

        return [
            'context_text' => implode("\n", $contextLines),
            'patient_info' => $patientSummary,
            'role_slug'    => $roleSlug,
        ];
    }

    /**
     * Append authorized patient clinical records filtered by user role.
     */
    protected function appendPatientClinicalRecords(array &$lines, Patient $patient, string $roleSlug): void
    {
        // Doctor: Full clinical overview (LIS, RIS, PMS, SORS, DNMS)
        if ($roleSlug === 'doctor' || $roleSlug === 'admin') {
            $this->appendLabData($lines, $patient);
            $this->appendRadiologyData($lines, $patient);
            $this->appendPharmacyData($lines, $patient);
            $this->appendSurgeryData($lines, $patient);
            $this->appendDietData($lines, $patient);
            return;
        }

        // Med-Tech: Laboratory data only
        if ($roleSlug === 'med-tech') {
            $this->appendLabData($lines, $patient);
            return;
        }

        // Rad-Tech / Radiologist: Radiology data only
        if (in_array($roleSlug, ['rad-tech', 'radiologist'])) {
            $this->appendRadiologyData($lines, $patient);
            return;
        }

        // Pharmacist: Prescriptions and medication history only
        if ($roleSlug === 'pharmacist') {
            $this->appendPharmacyData($lines, $patient);
            return;
        }

        // Dietitian: Diet requests and nutrition plans only
        if ($roleSlug === 'dietitian') {
            $this->appendDietData($lines, $patient);
            return;
        }

        // OR Coordinator: Surgery schedules for this patient only
        if ($roleSlug === 'or-coordinator') {
            $this->appendSurgeryData($lines, $patient);
            return;
        }
    }

    protected function appendLabData(array &$lines, Patient $patient): void
    {
        $labRequests = LabRequest::with(['items.labTest', 'items.result', 'doctor'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->take(5)
            ->get();

        if ($labRequests->isNotEmpty()) {
            $lines[] = "#### Laboratory History (LIS)";
            foreach ($labRequests as $req) {
                $lines[] = "- Request #{$req->request_no} | Status: {$req->status} | Priority: {$req->priority} | Date: " . $req->created_at->format('Y-m-d H:i');
                foreach ($req->items as $item) {
                    $testName = $item->labTest->test_name ?? 'Laboratory Test';
                    $resValue = $item->result->result_value ?? 'Pending';
                    $remarks = $item->result->remarks ?? '';
                    $lines[] = "  * Test: {$testName} | Result: {$resValue} | Status: {$item->status}" . ($remarks ? " (Remarks: {$remarks})" : '');
                }
            }
            $lines[] = "";
        }
    }

    protected function appendRadiologyData(array &$lines, Patient $patient): void
    {
        $radRequests = RadiologyRequest::with(['report', 'doctor'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->take(5)
            ->get();

        if ($radRequests->isNotEmpty()) {
            $lines[] = "#### Radiology History (RIS)";
            foreach ($radRequests as $req) {
                $lines[] = "- Request #{$req->request_no} | Modality: {$req->modality} | Body Part: {$req->body_part} | Status: {$req->status}";
                if ($req->report) {
                    $rep = $req->report;
                    $lines[] = "  * Report #{$rep->id} | Status: {$rep->status}";
                    $lines[] = "    Findings: " . \Illuminate\Support\Str::limit($rep->findings ?? 'Pending', 200);
                    $lines[] = "    Impression: " . \Illuminate\Support\Str::limit($rep->impression ?? 'Pending', 200);
                }
            }
            $lines[] = "";
        }
    }

    protected function appendPharmacyData(array &$lines, Patient $patient): void
    {
        $prescriptions = Prescription::with('items')
            ->where('patient_id', $patient->id)
            ->latest()
            ->take(5)
            ->get();

        if ($prescriptions->isNotEmpty()) {
            $lines[] = "#### Medication & Prescription History (PMS)";
            foreach ($prescriptions as $rx) {
                $lines[] = "- Prescription #{$rx->prescription_no} | Status: {$rx->status} | Doctor Notes: {$rx->notes}";
                foreach ($rx->items as $item) {
                    $lines[] = "  * Drug: {$item->medication_name} | Dosage: {$item->dosage} | Frequency: {$item->frequency} | Duration: {$item->duration} days";
                }
            }
            $lines[] = "";
        }
    }

    protected function appendSurgeryData(array &$lines, Patient $patient): void
    {
        $surgeries = SurgeryRequest::with('surgerySchedule')
            ->where('patient_id', $patient->id)
            ->latest()
            ->take(3)
            ->get();

        if ($surgeries->isNotEmpty()) {
            $lines[] = "#### Surgical Record (SORS)";
            foreach ($surgeries as $surg) {
                $schedInfo = $surg->surgerySchedule ? "Scheduled: {$surg->surgerySchedule->scheduled_at} in OR {$surg->surgerySchedule->operating_room_id}" : "Unscheduled";
                $lines[] = "- Procedure: {$surg->procedure_name} | Urgency: {$surg->urgency} | Status: {$surg->status} | {$schedInfo}";
            }
            $lines[] = "";
        }
    }

    protected function appendDietData(array &$lines, Patient $patient): void
    {
        $dietPlans = DietPlan::with('dietRequest')
            ->whereHas('dietRequest', fn ($q) => $q->where('patient_id', $patient->id))
            ->latest()
            ->take(3)
            ->get();

        if ($dietPlans->isNotEmpty()) {
            $lines[] = "#### Diet & Nutrition Record (DNMS)";
            foreach ($dietPlans as $plan) {
                $lines[] = "- Diet Type: " . ($plan->dietRequest->diet_type ?? 'Therapeutic') . " | Status: {$plan->status} | Calories: {$plan->total_calories} kcal | Details: {$plan->plan_details}";
            }
            $lines[] = "";
        }
    }

    /**
     * Append department operational metrics when no specific patient is in context.
     */
    protected function appendOperationalContext(array &$lines, string $roleSlug): void
    {
        $lines[] = "### System & Service Operational Context";

        switch ($roleSlug) {
            case 'admin':
                $lines[] = "- System Statistics:";
                $lines[] = "  * Total Active Patients: " . Patient::count();
                $lines[] = "  * Pending Lab Requests: " . LabRequest::where('status', 'pending')->count();
                $lines[] = "  * Pending Radiology Requests: " . RadiologyRequest::where('status', 'pending')->count();
                $lines[] = "  * Active Prescriptions: " . Prescription::where('status', 'pending')->count();
                $lines[] = "  * Scheduled Surgeries Today: " . SurgerySchedule::whereDate('scheduled_at', today())->count();
                break;

            case 'med-tech':
                $lines[] = "- Laboratory Operational Summary:";
                $lines[] = "  * Pending Lab Requests: " . LabRequest::where('status', 'pending')->count();
                $lines[] = "  * In-Process Tests: " . LabRequest::where('status', 'in_process')->count();
                $lines[] = "  * Completed (Awaiting Release): " . LabResult::where('status', 'draft')->count();
                break;

            case 'rad-tech':
            case 'radiologist':
                $lines[] = "- Radiology Department Operational Summary:";
                $lines[] = "  * Pending Imaging Requests: " . RadiologyRequest::where('status', 'pending')->count();
                $lines[] = "  * Scheduled Scans Today: " . RadiologyRequest::where('status', 'scheduled')->count();
                $lines[] = "  * Pending Radiology Reports: " . RadiologyReport::where('status', 'draft')->count();
                break;

            case 'pharmacist':
                $lines[] = "- Pharmacy Operational Summary:";
                $lines[] = "  * Unverified Prescriptions: " . Prescription::where('status', 'pending')->count();
                $lines[] = "  * Ready for Dispensing: " . Prescription::where('status', 'verified')->count();
                break;

            case 'dietitian':
                $lines[] = "- Diet & Nutrition Summary:";
                $lines[] = "  * Pending Diet Requests: " . DietRequest::where('status', 'pending')->count();
                $lines[] = "  * Active Diet Plans: " . DietPlan::where('status', 'active')->count();
                break;

            case 'or-coordinator':
                $lines[] = "- Operating Room Operational Summary:";
                $lines[] = "  * Unscheduled Surgery Requests: " . SurgeryRequest::where('status', 'pending')->count();
                $lines[] = "  * Surgeries Scheduled Today: " . SurgerySchedule::whereDate('scheduled_at', today())->count();
                break;

            case 'doctor':
                $lines[] = "- Doctor Clinical Quick Summary:";
                $lines[] = "  * Patients Managed Today: " . Patient::count();
                break;
        }
        $lines[] = "";
    }
}
