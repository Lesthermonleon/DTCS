<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DietPlan;
use App\Models\DietRequest;
use App\Models\DispensingRecord;
use App\Models\LabRequest;
use App\Models\Permission;
use App\Models\Prescription;
use App\Models\RadiologyReport;
use App\Models\RadiologyRequest;
use App\Models\Role;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DashboardController — serves role-specific dashboard views.
 */
class DashboardController extends Controller
{
    /** System Administrator dashboard. */
    public function admin(): \Illuminate\View\View
    {
        return view('dashboard.admin', $this->getAdminDashboardData());
    }

    /** Helper to build admin dashboard payload. */
    private function getAdminDashboardData(): array
    {
        $todayStr = now()->toDateString();

        // ── 1. Summary Statistics ──
        $unassignedUsersCount = User::whereDoesntHave('roles')->count();
        $inactiveUsersCount   = User::where('is_active', false)->count();
        $lockedAccountsCount  = User::whereNotNull('locked_at')->count();
        $failedLoginsCount    = (int) User::where('failed_attempts', '>', 0)->sum('failed_attempts');
        $failedUsersCount     = User::where('failed_attempts', '>', 0)->count();

        $stats = [
            'total_users'          => User::count(),
            'active_users'         => User::where('is_active', true)->count(),
            'total_patients'       => \App\Models\Patient::count(),
            'pending_admin_tasks'  => $unassignedUsersCount + $inactiveUsersCount,
            'system_alerts_count'  => $failedUsersCount + $inactiveUsersCount + $unassignedUsersCount,
            'today_activity_count' => ActivityLog::whereDate('created_at', $todayStr)->count(),
            'inactive_users'       => $inactiveUsersCount,
            'locked_accounts'      => $lockedAccountsCount,
            'unassigned_users'     => $unassignedUsersCount,
            'failed_logins'        => $failedLoginsCount,
        ];

        // ── 2. Users & Roles Breakdown ──
        $usersByRole = Role::withCount('users')->get()->map(fn($r) => [
            'name'  => $r->name,
            'slug'  => $r->slug,
            'count' => $r->users_count,
        ])->values();

        // ── 3. Module Overview (Pending Counts) ──
        $moduleStats = [
            'lis'  => ['name' => 'Laboratory (LIS)',            'pending' => LabRequest::where('status', 'Pending')->count(),       'route' => 'lab.dashboard'],
            'ris'  => ['name' => 'Radiology (RIS)',             'pending' => RadiologyRequest::where('status', 'Pending')->count(), 'route' => 'radiology.dashboard'],
            'pms'  => ['name' => 'Pharmacy (PMS)',              'pending' => Prescription::where('status', 'Pending')->count(),    'route' => 'pharmacy.dashboard'],
            'sors' => ['name' => 'Surgery (SORS)',              'pending' => SurgeryRequest::where('status', 'Pending')->count(),   'route' => 'surgery.dashboard'],
            'dnms' => ['name' => 'Nutrition & Dietetics (DNMS)','pending' => DietRequest::where('status', 'Pending')->count(),      'route' => 'diet.dashboard'],
        ];

        // ── 4. System / Security Alerts ──
        $systemAlerts = $this->buildAdminAlerts($failedLoginsCount, $inactiveUsersCount, $lockedAccountsCount, $unassignedUsersCount);

        // ── 5. Recent System Activity ──
        $recentActivity = ActivityLog::with('user')->latest('created_at')->take(10)->get();

        // ── 6. Recent Patients Overview ──
        $recentPatients = \App\Models\Patient::latest('created_at')->take(5)->get();

        // ── 7. 7-day registration trend ──
        $newUsers7d = collect(range(6, 0))->map(fn($d) => [
            'date'  => now()->subDays($d)->format('M d'),
            'count' => User::whereDate('created_at', now()->subDays($d)->toDateString())->count(),
        ]);

        return compact(
            'stats',
            'usersByRole',
            'moduleStats',
            'systemAlerts',
            'recentActivity',
            'recentPatients',
            'newUsers7d'
        );
    }

    /** Build administrative alert notifications. */
    private function buildAdminAlerts(int $failedLoginsCount, int $inactiveUsersCount, int $lockedAccountsCount, int $unassignedUsersCount): \Illuminate\Support\Collection
    {
        $alerts = collect();

        if ($failedLoginsCount > 0) {
            $alerts->push([
                'type'        => 'warning',
                'icon'        => 'bi-shield-exclamation',
                'title'       => 'Failed Login Attempts Detected',
                'description' => "{$failedLoginsCount} failed authentication attempt(s) recorded across user accounts.",
                'action_label'=> 'Manage Users',
                'action_route'=> route('admin.users.index'),
            ]);
        }

        if ($inactiveUsersCount > 0 || $lockedAccountsCount > 0) {
            $alerts->push([
                'type'        => 'danger',
                'icon'        => 'bi-lock-fill',
                'title'       => 'Locked / Inactive Accounts Require Attention',
                'description' => "{$inactiveUsersCount} account(s) inactive and {$lockedAccountsCount} locked due to failed login threshold.",
                'action_label'=> 'Review Accounts',
                'action_route'=> route('admin.users.index'),
            ]);
        }

        if ($unassignedUsersCount > 0) {
            $alerts->push([
                'type'        => 'info',
                'icon'        => 'bi-person-badge',
                'title'       => 'Pending Role Assignments',
                'description' => "{$unassignedUsersCount} user account(s) require role assignment or permission configuration.",
                'action_label'=> 'Assign Roles',
                'action_route'=> route('admin.users.index'),
            ]);
        }

        return $alerts;
    }

    /** Doctor dashboard. */
    public function doctor(): \Illuminate\View\View
    {
        /** @var User $doctor */
        $doctor = Auth::user();
        return view('dashboard.doctor', $this->getDoctorDashboardData($doctor ? $doctor->id : 0));
    }

    /** Helper to build doctor dashboard payload. */
    private function getDoctorDashboardData(int $doctorId): array
    {
        $weekStart = now()->startOfWeek();

        // 1. My Patients Count
        $myPatientsCount = \App\Models\Patient::whereHas('labRequests', fn($q) => $q->where('doctor_id', $doctorId))
            ->orWhereHas('radiologyRequests', fn($q) => $q->where('doctor_id', $doctorId))
            ->orWhereHas('prescriptions', fn($q) => $q->where('doctor_id', $doctorId))
            ->orWhereHas('surgeryRequests', fn($q) => $q->where('doctor_id', $doctorId))
            ->orWhereHas('dietRequests', fn($q) => $q->where('doctor_id', $doctorId))
            ->distinct()
            ->count();

        // Fallback to total patients if doctor has no specific assignments yet
        if ($myPatientsCount === 0) {
            $myPatientsCount = \App\Models\Patient::count();
        }

        // 2. Pending Tasks
        $pendingLabRequests  = LabRequest::with('patient')->where('doctor_id', $doctorId)->where('status', 'Pending')->latest()->get();
        $pendingRadRequests  = RadiologyRequest::with('patient')->where('doctor_id', $doctorId)->where('status', 'Pending')->latest()->get();
        $pendingRx           = Prescription::with('patient')->where('doctor_id', $doctorId)->where('status', 'Pending')->latest()->get();
        $pendingSurgery      = SurgeryRequest::with('patient')->where('doctor_id', $doctorId)->where('status', 'Pending')->latest()->get();
        $pendingDiet         = DietRequest::with('patient')->where('doctor_id', $doctorId)->where('status', 'Pending')->latest()->get();

        // Fallback to general pending tasks if doctor-specific count is 0
        if ($pendingLabRequests->isEmpty() && $pendingRadRequests->isEmpty() && $pendingRx->isEmpty() && $pendingSurgery->isEmpty() && $pendingDiet->isEmpty()) {
            $pendingLabRequests = LabRequest::with('patient')->where('status', 'Pending')->latest()->take(3)->get();
            $pendingRadRequests = RadiologyRequest::with('patient')->where('status', 'Pending')->latest()->take(3)->get();
            $pendingRx          = Prescription::with('patient')->where('status', 'Pending')->latest()->take(3)->get();
            $pendingSurgery     = SurgeryRequest::with('patient')->where('status', 'Pending')->latest()->take(3)->get();
            $pendingDiet        = DietRequest::with('patient')->where('status', 'Pending')->latest()->take(3)->get();
        }

        $pendingTasksCount = $pendingLabRequests->count() + $pendingRadRequests->count() + $pendingRx->count() + $pendingSurgery->count() + $pendingDiet->count();

        // 3. Lab & Radiology Review / Critical Alerts
        $releasedLabResults = \App\Models\LabResult::with(['requestItem.labRequest.patient', 'requestItem.labRequest.doctor'])
            ->whereHas('requestItem.labRequest', fn($q) => $q->where('doctor_id', $doctorId))
            ->latest('released_at')
            ->get();

        if ($releasedLabResults->isEmpty()) {
            $releasedLabResults = \App\Models\LabResult::with(['requestItem.labRequest.patient', 'requestItem.labRequest.doctor'])
                ->latest()
                ->take(5)
                ->get();
        }

        $releasedRadReports = RadiologyReport::with(['radiologyRequest.patient', 'radiologyRequest.doctor'])
            ->whereHas('radiologyRequest', fn($q) => $q->where('doctor_id', $doctorId))
            ->latest()
            ->get();

        if ($releasedRadReports->isEmpty()) {
            $releasedRadReports = RadiologyReport::with(['radiologyRequest.patient', 'radiologyRequest.doctor'])
                ->latest()
                ->take(5)
                ->get();
        }

        $labResultsReviewCount = $releasedLabResults->count();
        $radReportsReviewCount = $releasedRadReports->count();
        $criticalAlertsCount   = $labResultsReviewCount + $radReportsReviewCount;

        // 4. Upcoming Surgeries
        $upcomingSurgeriesCount = SurgeryRequest::where('doctor_id', $doctorId)
            ->whereIn('status', ['Scheduled', 'Approved', 'Pending'])
            ->count();
        if ($upcomingSurgeriesCount === 0) {
            $upcomingSurgeriesCount = SurgerySchedule::whereIn('status', ['Scheduled', 'In Progress'])->count();
        }

        // Combined Stats Object
        $stats = [
            'my_patients'        => $myPatientsCount,
            'pending_tasks'      => $pendingTasksCount,
            'critical_alerts'    => $criticalAlertsCount,
            'lab_awaiting'       => $labResultsReviewCount,
            'rad_awaiting'       => $radReportsReviewCount,
            'upcoming_surgeries' => $upcomingSurgeriesCount,

            'my_lab_requests'    => LabRequest::where('doctor_id', $doctorId)->count() ?: LabRequest::count(),
            'my_radiology'       => RadiologyRequest::where('doctor_id', $doctorId)->count() ?: RadiologyRequest::count(),
            'my_prescriptions'   => Prescription::where('doctor_id', $doctorId)->count() ?: Prescription::count(),
            'my_surgeries'       => SurgeryRequest::where('doctor_id', $doctorId)->count() ?: SurgeryRequest::count(),
            'my_diet_requests'   => DietRequest::where('doctor_id', $doctorId)->count() ?: DietRequest::count(),

            'lab_this_week'      => LabRequest::where('created_at', '>=', $weekStart)->count(),
            'rx_this_week'       => Prescription::where('created_at', '>=', $weekStart)->count(),
            'rad_this_week'      => RadiologyRequest::where('created_at', '>=', $weekStart)->count(),
            'surg_this_week'     => SurgeryRequest::where('created_at', '>=', $weekStart)->count(),
            'diet_this_week'     => DietRequest::where('created_at', '>=', $weekStart)->count(),
        ];

        // 5. Recent Patients
        $recentPatients = \App\Models\Patient::latest('updated_at')->take(5)->get();

        return compact(
            'stats',
            'pendingLabRequests',
            'pendingRadRequests',
            'pendingRx',
            'pendingSurgery',
            'pendingDiet',
            'releasedLabResults',
            'releasedRadReports',
            'recentPatients'
        );
    }

    /** Lab / Medical Technologist dashboard. */
    public function lab()
    {
        $today = now()->toDateString();

        $stats = [
            'total_requests'   => LabRequest::count(),
            'pending'          => LabRequest::where('status', 'Pending')->count(),
            'in_progress'      => LabRequest::where('status', 'In Progress')->count(),
            'completed'        => LabRequest::where('status', 'Completed')->count(),
            'stat_count'       => LabRequest::where('priority', 'STAT')->count(),
            'today_received'   => LabRequest::whereDate('created_at', $today)->count(),
            'today_completed'  => LabRequest::whereDate('updated_at', $today)->where('status', 'Completed')->count(),
            'stat_pending'     => LabRequest::where('priority', 'STAT')->where('status', 'Pending')->count(),
            'pending_release'  => \App\Models\LabResult::where('status', 'Draft')->count(),
        ];

        $recentRequests = LabRequest::with('patient', 'doctor')
                            ->latest()
                            ->take(15)
                            ->get();

        return view('dashboard.lab', compact('stats', 'recentRequests'));
    }

    /** Radiology dashboard (differentiating Radiologic Technologist & Radiologist). */
    public function radiology()
    {
        $today = now()->toDateString();
        /** @var User $user */
        $user = Auth::user();
        $isRadiologist = $user ? $user->hasRole('radiologist') : false;

        $stats = [
            'total_requests'   => RadiologyRequest::count(),
            'pending'          => RadiologyRequest::where('status', 'Pending')->count(),
            'scheduled'        => RadiologyRequest::where('status', 'Scheduled')->count(),
            'in_progress'      => RadiologyRequest::where('status', 'In Progress')->count(),
            'completed'        => RadiologyRequest::where('status', 'Completed')->count(),
            'reports_pending'  => RadiologyRequest::where('status', 'Completed')
                                    ->whereDoesntHave('report', fn($q) => $q->where('status', 'Released'))
                                    ->count() + RadiologyReport::whereIn('status', ['Draft', 'Approved'])->count(),
            'today_scheduled'  => RadiologyRequest::whereDate('updated_at', $today)->where('status', 'Scheduled')->count(),
            'today_completed'  => RadiologyRequest::whereDate('updated_at', $today)->where('status', 'Completed')->count(),
            'reports_released' => RadiologyReport::where('status', 'Released')->count(),
            'reports_today'    => RadiologyReport::whereDate('created_at', $today)->count(),
        ];

        // Technologist procedure queue (Pending, Scheduled, In Progress, Completed)
        $recentRequests = RadiologyRequest::with('patient', 'doctor', 'images')
                            ->latest()
                            ->take(15)
                            ->get();

        // Radiologist interpretation queue (Completed studies & Draft/Approved reports)
        $pendingReports = RadiologyReport::whereIn('status', ['Draft', 'Approved'])
                            ->with('radiologyRequest.patient', 'radiologyRequest.doctor', 'radiologyRequest.images')
                            ->latest()
                            ->take(15)
                            ->get();

        $completedStudiesAwaitingReport = RadiologyRequest::where('status', 'Completed')
                            ->whereDoesntHave('report')
                            ->with('patient', 'doctor', 'images')
                            ->latest()
                            ->take(15)
                            ->get();

        return view('dashboard.radiology', compact(
            'stats',
            'recentRequests',
            'pendingReports',
            'completedStudiesAwaitingReport',
            'isRadiologist'
        ));
    }

    /** Pharmacy dashboard. */
    public function pharmacy()
    {
        $today = now()->toDateString();

        $totalPrescriptions    = Prescription::count();
        $pendingPrescriptions  = Prescription::where('status', 'Pending')->count();
        $verifiedPrescriptions = Prescription::where('status', 'Verified')->count();
        $dispensedTotal        = Prescription::where('status', 'Dispensed')->count();

        $dispensedToday = class_exists(DispensingRecord::class)
            ? DispensingRecord::whereDate('created_at', $today)->count()
            : Prescription::where('status', 'Dispensed')->whereDate('updated_at', $today)->count();

        $verifiedToday = Prescription::where('status', 'Verified')->whereDate('updated_at', $today)->count();

        $stats = [
            'total_prescriptions'   => $totalPrescriptions,
            'pending_prescriptions' => $pendingPrescriptions,
            'verified'              => $verifiedPrescriptions,
            'dispensed_today'       => $dispensedToday,
            'dispensed_total'       => $dispensedTotal,
            'verified_today'        => $verifiedToday,
            'low_stock'             => 0,
            'pending_rate'          => $totalPrescriptions > 0
                                        ? round(($pendingPrescriptions / $totalPrescriptions) * 100)
                                        : 0,
        ];

        $pendingPrescriptionsList = Prescription::with('patient', 'doctor')
                                        ->whereIn('status', ['Pending', 'Verified'])
                                        ->latest()->take(15)->get();

        $recentDispensing = class_exists(DispensingRecord::class)
            ? DispensingRecord::with('prescriptionItem.prescription.patient', 'pharmacist')->latest()->take(5)->get()
            : collect();

        return view('dashboard.pharmacy', compact('stats', 'pendingPrescriptionsList', 'recentDispensing'));
    }

    /** Surgery / OR Coordinator dashboard. */
    public function surgery()
    {
        $today    = now()->toDateString();
        $next7end = now()->addDays(7)->toDateString();

        $stats = [
            'total_requests'  => SurgeryRequest::count(),
            'pending'         => SurgeryRequest::where('status', 'Pending')->count(),
            'scheduled'       => SurgeryRequest::where('status', 'Scheduled')->count(),
            'completed'       => SurgeryRequest::where('status', 'Completed')->count(),
            'cancelled'       => SurgeryRequest::where('status', 'Cancelled')->count(),
            'upcoming_7d'     => class_exists(SurgerySchedule::class)
                                    ? SurgerySchedule::whereBetween('scheduled_at', [$today, $next7end])
                                        ->whereIn('status', ['Scheduled', 'Confirmed'])->count()
                                    : 0,
            'today_scheduled' => class_exists(SurgerySchedule::class)
                                    ? SurgerySchedule::whereDate('scheduled_at', $today)->count()
                                    : 0,
        ];

        $recentRequests = SurgeryRequest::with('patient', 'doctor')
                            ->latest()->take(15)->get();

        $upcomingSchedules = class_exists(SurgerySchedule::class)
            ? SurgerySchedule::with('surgeryRequest.patient')
                ->whereIn('status', ['Scheduled', 'Confirmed'])
                ->orderBy('scheduled_at')
                ->take(10)->get()
            : collect();

        return view('dashboard.surgery', compact('stats', 'recentRequests', 'upcomingSchedules'));
    }

    /** Diet / Nutrition dashboard. */
    public function diet()
    {
        $today = now()->toDateString();

        $totalRequests  = DietRequest::count();
        $pendingCount   = DietRequest::where('status', 'Pending')->count();
        $activePlans    = class_exists(DietPlan::class)
                            ? DietPlan::where('status', 'Active')->count()
                            : DietRequest::where('status', 'Active')->count();
        $completedToday = DietRequest::where('status', 'Completed')
                            ->whereDate('updated_at', $today)->count();
        $completedTotal = DietRequest::where('status', 'Completed')->count();

        $stats = [
            'total_requests'  => $totalRequests,
            'pending'         => $pendingCount,
            'active_plans'    => $activePlans,
            'completed_today' => $completedToday,
            'completed_total' => $completedTotal,
            'in_progress'     => DietRequest::where('status', 'Active')->count(),
        ];

        $recentRequests  = DietRequest::with('patient', 'doctor')
                            ->latest()->take(15)->get();

        $activePlansList = class_exists(DietPlan::class)
            ? DietPlan::with('dietRequest.patient')->where('status', 'Active')->latest()->take(10)->get()
            : collect();

        return view('dashboard.diet', compact('stats', 'recentRequests', 'activePlansList'));
    }
}
