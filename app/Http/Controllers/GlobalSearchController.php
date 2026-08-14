<?php

namespace App\Http\Controllers;

use App\Models\DietRequest;
use App\Models\LabRequest;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\RadiologyRequest;
use App\Models\SurgeryRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalSearchController extends Controller
{
    /**
     * Perform global search across clinical and administrative models.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json([
                'query'   => $query,
                'results' => [],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $results = [];

        // 1. Patients (Restricted to System Administrator and Doctor)
        if ($user?->canAccessPatients()) {
            if ($patients = $this->searchPatients($query)) {
                $results['patients'] = $patients;
            }
        }

        // 2. Laboratory Requests
        if ($lab = $this->searchLabRequests($query)) {
            $results['lab'] = $lab;
        }

        // 3. Radiology Requests
        if ($radiology = $this->searchRadiologyRequests($query)) {
            $results['radiology'] = $radiology;
        }

        // 4. Prescriptions
        if ($pharmacy = $this->searchPrescriptions($query)) {
            $results['pharmacy'] = $pharmacy;
        }

        // 5. Surgery Requests
        if ($surgery = $this->searchSurgeryRequests($query)) {
            $results['surgery'] = $surgery;
        }

        // 6. Diet Requests
        if ($diet = $this->searchDietRequests($query)) {
            $results['diet'] = $diet;
        }

        return response()->json([
            'query'   => $query,
            'results' => $results,
        ]);
    }

    private function searchPatients(string $query): ?array
    {
        $patients = Patient::query()
            ->where(function ($q) use ($query) {
                $q->where('patient_no', 'like', "%{$query}%")
                  ->orWhere('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('middle_name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();

        if ($patients->isEmpty()) {
            return null;
        }

        return [
            'label' => 'Patients',
            'icon'  => 'bi-people',
            'items' => $patients->map(fn (Patient $p) => [
                'title'    => $p->full_name,
                'subtitle' => "ID: {$p->patient_no} | {$p->gender} | Type: " . ucfirst($p->patient_type ?? 'Outpatient'),
                'url'      => route('patients.show', $p->id),
                'badge'    => $p->blood_type ? "Blood: {$p->blood_type}" : null,
            ]),
        ];
    }

    private function searchLabRequests(string $query): ?array
    {
        $labRequests = LabRequest::with('patient')
            ->where(function ($q) use ($query) {
                $q->where('request_no', 'like', "%{$query}%")
                  ->orWhere('specimen_type', 'like', "%{$query}%")
                  ->orWhere('clinical_notes', 'like', "%{$query}%")
                  ->orWhereHas('patient', function ($pq) use ($query) {
                      $pq->where('first_name', 'like', "%{$query}%")
                         ->orWhere('last_name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get();

        if ($labRequests->isEmpty()) {
            return null;
        }

        return [
            'label' => 'Laboratory Requests',
            'icon'  => 'bi-flask',
            'items' => $labRequests->map(fn (LabRequest $lr) => [
                'title'    => "Lab Request {$lr->request_no}",
                'subtitle' => "Patient: " . ($lr->patient?->full_name ?? 'N/A') . " | Specimen: " . ($lr->specimen_type ?? 'Standard'),
                'url'      => route('lab.requests.show', $lr->id),
                'badge'    => ucfirst($lr->status),
            ]),
        ];
    }

    private function searchRadiologyRequests(string $query): ?array
    {
        $radRequests = RadiologyRequest::with('patient')
            ->where(function ($q) use ($query) {
                $q->where('request_no', 'like', "%{$query}%")
                  ->orWhere('modality', 'like', "%{$query}%")
                  ->orWhere('body_part', 'like', "%{$query}%")
                  ->orWhereHas('patient', function ($pq) use ($query) {
                      $pq->where('first_name', 'like', "%{$query}%")
                         ->orWhere('last_name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get();

        if ($radRequests->isEmpty()) {
            return null;
        }

        return [
            'label' => 'Radiology Requests',
            'icon'  => 'bi-activity',
            'items' => $radRequests->map(fn (RadiologyRequest $rr) => [
                'title'    => "Radiology Request {$rr->request_no}",
                'subtitle' => "Patient: " . ($rr->patient?->full_name ?? 'N/A') . " | " . strtoupper($rr->modality) . " ({$rr->body_part})",
                'url'      => route('radiology.requests.show', $rr->id),
                'badge'    => ucfirst($rr->status),
            ]),
        ];
    }

    private function searchPrescriptions(string $query): ?array
    {
        $prescriptions = Prescription::with('patient')
            ->where(function ($q) use ($query) {
                $q->where('prescription_no', 'like', "%{$query}%")
                  ->orWhere('diagnosis', 'like', "%{$query}%")
                  ->orWhereHas('patient', function ($pq) use ($query) {
                      $pq->where('first_name', 'like', "%{$query}%")
                         ->orWhere('last_name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get();

        if ($prescriptions->isEmpty()) {
            return null;
        }

        return [
            'label' => 'Prescriptions',
            'icon'  => 'bi-capsule',
            'items' => $prescriptions->map(fn (Prescription $pr) => [
                'title'    => "Prescription {$pr->prescription_no}",
                'subtitle' => "Patient: " . ($pr->patient?->full_name ?? 'N/A') . " | " . ($pr->diagnosis ?? 'General'),
                'url'      => route('pharmacy.prescriptions.show', $pr->id),
                'badge'    => ucfirst($pr->status),
            ]),
        ];
    }

    private function searchSurgeryRequests(string $query): ?array
    {
        $surgeries = SurgeryRequest::with('patient')
            ->where(function ($q) use ($query) {
                $q->where('request_no', 'like', "%{$query}%")
                  ->orWhere('procedure_name', 'like', "%{$query}%")
                  ->orWhere('diagnosis', 'like', "%{$query}%")
                  ->orWhereHas('patient', function ($pq) use ($query) {
                      $pq->where('first_name', 'like', "%{$query}%")
                         ->orWhere('last_name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get();

        if ($surgeries->isEmpty()) {
            return null;
        }

        return [
            'label' => 'Surgery Requests',
            'icon'  => 'bi-scissors',
            'items' => $surgeries->map(fn (SurgeryRequest $sr) => [
                'title'    => "Surgery: {$sr->procedure_name}",
                'subtitle' => "Patient: " . ($sr->patient?->full_name ?? 'N/A') . " | Req #: {$sr->request_no}",
                'url'      => route('surgery.requests.show', $sr->id),
                'badge'    => ucfirst($sr->status),
            ]),
        ];
    }

    private function searchDietRequests(string $query): ?array
    {
        $diets = DietRequest::with('patient')
            ->where(function ($q) use ($query) {
                $q->where('request_no', 'like', "%{$query}%")
                  ->orWhere('diet_type', 'like', "%{$query}%")
                  ->orWhereHas('patient', function ($pq) use ($query) {
                      $pq->where('first_name', 'like', "%{$query}%")
                         ->orWhere('last_name', 'like', "%{$query}%");
                  });
            })
            ->limit(5)
            ->get();

        if ($diets->isEmpty()) {
            return null;
        }

        return [
            'label' => 'Diet & Nutrition Requests',
            'icon'  => 'bi-apple',
            'items' => $diets->map(fn (DietRequest $dr) => [
                'title'    => "Diet Request: {$dr->diet_type}",
                'subtitle' => "Patient: " . ($dr->patient?->full_name ?? 'N/A') . " | Req #: {$dr->request_no}",
                'url'      => route('diet.requests.show', $dr->id),
                'badge'    => ucfirst($dr->status),
            ]),
        ];
    }
}
