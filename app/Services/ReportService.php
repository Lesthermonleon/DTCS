<?php

namespace App\Services;

use App\Models\DietPlan;
use App\Models\DietRequest;
use App\Models\DispensingRecord;
use App\Models\LabRequest;
use App\Models\LabRequestItem;
use App\Models\LabResult;
use App\Models\Prescription;
use App\Models\RadiologyReport;
use App\Models\RadiologyRequest;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportService
{
    /**
     * Role slugs allowed per report category.
     */
    public const ROLE_ACCESS = [
        'laboratory'  => ['admin', 'doctor', 'med-tech'],
        'radiology'   => ['admin', 'doctor', 'rad-tech', 'radiologist'],
        'pharmacy'    => ['admin', 'doctor', 'pharmacist'],
        'surgery'     => ['admin', 'doctor', 'or-coordinator'],
        'diet'        => ['admin', 'doctor', 'dietitian'],
        'clinical'    => ['admin', 'doctor'],
    ];

    /**
     * Parse and validate date range from request.
     * Defaults to current month if not provided.
     */
    public function parseDateRange(Request $request): array
    {
        $from = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        // Ensure from <= to
        if ($from->gt($to)) {
            $temp = $from;
            $from = $to->startOfDay();
            $to = $temp->endOfDay();
        }

        return [$from, $to];
    }

    /**
     * Check if user role can access a report category.
     */
    public function canAccess(string $category, ?string $roleSlug): bool
    {
        $allowed = self::ROLE_ACCESS[$category] ?? [];
        return in_array($roleSlug, $allowed, true);
    }

    /**
     * Get categories accessible by a role.
     */
    public function accessibleCategories(?string $roleSlug): array
    {
        $accessible = [];
        foreach (self::ROLE_ACCESS as $category => $roles) {
            if (in_array($roleSlug, $roles, true)) {
                $accessible[] = $category;
            }
        }
        return $accessible;
    }

    // ─── Laboratory ──────────────────────────────────────────────────

    public function labActivitySummary(Carbon $from, Carbon $to, ?string $status = null): array
    {
        $query = LabRequest::whereBetween('requested_at', [$from, $to]);
        $total = (clone $query)->count();

        return [
            'total'     => $total,
            'pending'   => (clone $query)->where('status', 'Pending')->count(),
            'received'  => (clone $query)->where('status', 'Received')->count(),
            'completed' => (clone $query)->where('status', 'Completed')->count(),
            'cancelled' => (clone $query)->where('status', 'Cancelled')->count(),
            'completion_rate' => $total > 0
                ? round((clone $query)->where('status', 'Completed')->count() / $total * 100, 1)
                : 0,
        ];
    }

    public function labActivityDetails(Carbon $from, Carbon $to, Request $request)
    {
        $query = LabRequest::with(['patient', 'doctor', 'items.labTest'])
            ->whereBetween('requested_at', [$from, $to]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        return $query->orderBy('requested_at', 'desc')->paginate(25)->withQueryString();
    }

    public function labTestVolume(Carbon $from, Carbon $to)
    {
        return LabRequestItem::select('lab_test_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN lab_request_items.status = 'Completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN lab_request_items.status = 'Pending' THEN 1 ELSE 0 END) as pending")
            ->join('lab_requests', 'lab_requests.id', '=', 'lab_request_items.lab_request_id')
            ->whereBetween('lab_requests.requested_at', [$from, $to])
            ->groupBy('lab_test_id')
            ->with('labTest')
            ->orderByDesc('total')
            ->get();
    }

    // ─── Radiology ───────────────────────────────────────────────────

    public function radiologyActivitySummary(Carbon $from, Carbon $to): array
    {
        $query = RadiologyRequest::whereBetween('requested_at', [$from, $to]);
        $total = (clone $query)->count();

        return [
            'total'     => $total,
            'pending'   => (clone $query)->where('status', 'Pending')->count(),
            'scheduled' => (clone $query)->where('status', 'Scheduled')->count(),
            'in_progress' => (clone $query)->where('status', 'In Progress')->count(),
            'completed' => (clone $query)->where('status', 'Completed')->count(),
            'cancelled' => (clone $query)->where('status', 'Cancelled')->count(),
            'completion_rate' => $total > 0
                ? round((clone $query)->where('status', 'Completed')->count() / $total * 100, 1)
                : 0,
        ];
    }

    public function radiologyActivityDetails(Carbon $from, Carbon $to, Request $request)
    {
        $query = RadiologyRequest::with(['patient', 'doctor', 'report'])
            ->whereBetween('requested_at', [$from, $to]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('modality')) {
            $query->where('modality', $request->modality);
        }

        return $query->orderBy('requested_at', 'desc')->paginate(25)->withQueryString();
    }

    public function radiologyVolumeByModality(Carbon $from, Carbon $to)
    {
        return RadiologyRequest::select('modality')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending")
            ->whereBetween('requested_at', [$from, $to])
            ->groupBy('modality')
            ->orderByDesc('total')
            ->get();
    }

    public function radiologyPendingInterpretations(Carbon $from, Carbon $to, Request $request)
    {
        $query = RadiologyRequest::with(['patient', 'doctor', 'report'])
            ->whereBetween('requested_at', [$from, $to])
            ->where('status', 'Completed')
            ->whereDoesntHave('report', fn($q) => $q->where('status', 'Released'));

        if ($request->filled('modality')) {
            $query->where('modality', $request->modality);
        }

        return $query->orderBy('requested_at', 'desc')->paginate(25)->withQueryString();
    }

    // ─── Pharmacy ────────────────────────────────────────────────────

    public function pharmacyActivitySummary(Carbon $from, Carbon $to): array
    {
        $query = Prescription::whereBetween('prescribed_at', [$from, $to]);
        $total = (clone $query)->count();

        return [
            'total'     => $total,
            'pending'   => (clone $query)->where('status', 'Pending')->count(),
            'verified'  => (clone $query)->where('status', 'Verified')->count(),
            'dispensed' => (clone $query)->where('status', 'Dispensed')->count(),
            'cancelled' => (clone $query)->where('status', 'Cancelled')->count(),
            'completion_rate' => $total > 0
                ? round((clone $query)->where('status', 'Dispensed')->count() / $total * 100, 1)
                : 0,
        ];
    }

    public function pharmacyActivityDetails(Carbon $from, Carbon $to, Request $request)
    {
        $query = Prescription::with(['patient', 'doctor', 'items'])
            ->whereBetween('prescribed_at', [$from, $to]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        return $query->orderBy('prescribed_at', 'desc')->paginate(25)->withQueryString();
    }

    public function dispensingDetails(Carbon $from, Carbon $to, Request $request)
    {
        $query = DispensingRecord::with(['prescriptionItem.prescription.patient', 'prescriptionItem.prescription.doctor', 'pharmacist'])
            ->whereBetween('dispensed_at', [$from, $to]);

        if ($request->filled('pharmacist_id')) {
            $query->where('pharmacist_id', $request->pharmacist_id);
        }

        return $query->orderBy('dispensed_at', 'desc')->paginate(25)->withQueryString();
    }

    // ─── Surgery ─────────────────────────────────────────────────────

    public function surgeryActivitySummary(Carbon $from, Carbon $to): array
    {
        $query = SurgeryRequest::whereBetween('requested_at', [$from, $to]);
        $total = (clone $query)->count();

        return [
            'total'     => $total,
            'pending'   => (clone $query)->where('status', 'Pending')->count(),
            'scheduled' => (clone $query)->where('status', 'Scheduled')->count(),
            'completed' => (clone $query)->where('status', 'Completed')->count(),
            'cancelled' => (clone $query)->where('status', 'Cancelled')->count(),
            'in_progress' => (clone $query)->where('status', 'In Progress')->count(),
            'completion_rate' => $total > 0
                ? round((clone $query)->where('status', 'Completed')->count() / $total * 100, 1)
                : 0,
        ];
    }

    public function surgeryActivityDetails(Carbon $from, Carbon $to, Request $request)
    {
        $query = SurgeryRequest::with(['patient', 'doctor', 'schedule.operatingRoom'])
            ->whereBetween('requested_at', [$from, $to]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        return $query->orderBy('requested_at', 'desc')->paginate(25)->withQueryString();
    }

    public function orUtilization(Carbon $from, Carbon $to)
    {
        return SurgerySchedule::select('operating_room_id')
            ->selectRaw('COUNT(*) as total_surgeries')
            ->selectRaw("SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw('COALESCE(SUM(duration_minutes), 0) as total_minutes')
            ->whereBetween('scheduled_at', [$from, $to])
            ->groupBy('operating_room_id')
            ->with('operatingRoom')
            ->orderByDesc('total_surgeries')
            ->get();
    }

    // ─── Diet & Nutrition ────────────────────────────────────────────

    public function dietActivitySummary(Carbon $from, Carbon $to): array
    {
        $reqQuery = DietRequest::whereBetween('requested_at', [$from, $to]);
        $planQuery = DietPlan::whereHas('dietRequest', fn($q) => $q->whereBetween('requested_at', [$from, $to]));

        return [
            'total_requests' => (clone $reqQuery)->count(),
            'total_plans'    => (clone $planQuery)->count(),
            'active_plans'   => (clone $planQuery)->where('status', 'Active')->count(),
            'completed_plans'=> (clone $planQuery)->where('status', 'Completed')->count(),
            'pending_requests' => (clone $reqQuery)->where('status', 'Pending')->count(),
        ];
    }

    public function dietActivityDetails(Carbon $from, Carbon $to, Request $request)
    {
        $query = DietRequest::with(['patient', 'doctor', 'dietPlan.dietitian'])
            ->whereBetween('requested_at', [$from, $to]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('diet_type')) {
            $query->where('diet_type', $request->diet_type);
        }

        return $query->orderBy('requested_at', 'desc')->paginate(25)->withQueryString();
    }

    public function dietPlansByStatus(Carbon $from, Carbon $to, string $status, Request $request)
    {
        $query = DietPlan::with(['dietRequest.patient', 'dietRequest.doctor', 'dietitian'])
            ->whereHas('dietRequest', fn($q) => $q->whereBetween('requested_at', [$from, $to]))
            ->where('status', $status);

        if ($request->filled('dietitian_id')) {
            $query->where('dietitian_id', $request->dietitian_id);
        }

        return $query->orderBy('start_date', 'desc')->paginate(25)->withQueryString();
    }

    // ─── Clinical Summary ────────────────────────────────────────────

    public function clinicalServicesSummary(Carbon $from, Carbon $to): array
    {
        return [
            'lab'       => $this->labActivitySummary($from, $to),
            'radiology' => $this->radiologyActivitySummary($from, $to),
            'pharmacy'  => $this->pharmacyActivitySummary($from, $to),
            'surgery'   => $this->surgeryActivitySummary($from, $to),
            'diet'      => $this->dietActivitySummary($from, $to),
        ];
    }
}
