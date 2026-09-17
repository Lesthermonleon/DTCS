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

    /**
     * Helper to build admin dashboard payload.
     *
     * @return array<string, mixed>
     */
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
        $systemAlerts = $this->buildAdminAlerts();

        // ── 5. User Presence & System Activity ──
        ['userPresenceStats' => $userPresenceStats, 'userPresenceList' => $userPresenceList] = $this->getUserPresenceData();

        // ── 6. Recent Patients Overview ──
        $recentPatients = \App\Models\Patient::latest('created_at')->take(5)->get();

        // ── 7. System Activity (7 days) ──
        $adminActivity7d = collect(range(6, 0))->map(fn($d) => [
            'date'  => now()->subDays($d)->format('M d'),
            'count' => ActivityLog::whereDate('created_at', now()->subDays($d)->toDateString())->count(),
        ]);

        // ── 8. Monthly Service Request Trend (6m & 12m) ──
        $adminTrend6m  = $this->getAdminRequestTrend(6);
        $adminTrend12m = $this->getAdminRequestTrend(12);

        // ── 9. Module Volume (all-time totals for bar chart) ──
        $adminModuleVolume = [
            ['module' => 'Laboratory',  'total' => LabRequest::count()],
            ['module' => 'Radiology',   'total' => RadiologyRequest::count()],
            ['module' => 'Pharmacy',    'total' => Prescription::count()],
            ['module' => 'Surgery',     'total' => SurgeryRequest::count()],
            ['module' => 'Diet',        'total' => DietRequest::count()],
        ];

        // ── 10. Global Request Status Totals ──
        $allStatuses = [
            'Pending'   => LabRequest::where('status','Pending')->count()   + RadiologyRequest::where('status','Pending')->count()   + Prescription::where('status','Pending')->count()   + SurgeryRequest::where('status','Pending')->count()   + DietRequest::where('status','Pending')->count(),
            'Completed' => LabRequest::where('status','Completed')->count() + RadiologyRequest::where('status','Completed')->count() + Prescription::where('status','Dispensed')->count() + SurgeryRequest::where('status','Completed')->count() + DietRequest::where('status','Completed')->count(),
            'Cancelled' => LabRequest::where('status','Cancelled')->count() + RadiologyRequest::where('status','Cancelled')->count() + Prescription::where('status','Cancelled')->count() + SurgeryRequest::where('status','Cancelled')->count() + DietRequest::where('status','Cancelled')->count(),
        ];

        return compact(
            'stats',
            'usersByRole',
            'moduleStats',
            'systemAlerts',
            'userPresenceStats',
            'userPresenceList',
            'recentPatients',
            'adminActivity7d',
            'adminTrend6m',
            'adminTrend12m',
            'adminModuleVolume',
            'allStatuses'
        );
    }

    /**
     * Build user presence stats and list for the admin dashboard.
     *
     * @return array{userPresenceStats: array<string, int>, userPresenceList: \Illuminate\Support\Collection}
     */
    private function getUserPresenceData(): array
    {
        $onlineThreshold = now()->subMinutes(5);
        $recentThreshold = now()->subHours(3);

        // Fetch active users with roles and latest activity log for fallback
        $activeUsers = User::with(['roles', 'activityLogs' => fn($q) => $q->latest('created_at')])
            ->where('is_active', true)
            ->get();

        $onlineUsers         = collect();
        $recentlyActiveUsers = collect();
        $otherUsers          = collect();

        foreach ($activeUsers as $user) {
            // Determine effective last activity timestamp
            $lastActive = $user->last_activity_at;
            if (! $lastActive && $user->activityLogs->isNotEmpty()) {
                $lastActive = $user->activityLogs->first()->created_at;
                $user->updateQuietly(['last_activity_at' => $lastActive]);
            }

            $user->effective_last_active = $lastActive;

            // Online: active account AND active_session_id AND last activity >= 5 minutes ago
            $isOnline = $user->active_session_id !== null
                && $lastActive !== null
                && $lastActive->gte($onlineThreshold);

            if ($isOnline) {
                $onlineUsers->push($user);
            } elseif ($lastActive !== null && $lastActive->gte($recentThreshold)) {
                // Active Recently: NOT online AND last activity >= 3 hours ago
                $recentlyActiveUsers->push($user);
            } else {
                $otherUsers->push($user);
            }
        }

        $onlineUsers         = $onlineUsers->sortByDesc(fn($u) => $u->effective_last_active?->timestamp ?? 0)->values();
        $recentlyActiveUsers = $recentlyActiveUsers->sortByDesc(fn($u) => $u->effective_last_active?->timestamp ?? 0)->values();

        $userPresenceStats = [
            'online_count'          => $onlineUsers->count(),
            'recently_active_count' => $recentlyActiveUsers->count(),
            'total_users_count'     => $activeUsers->count(),
        ];

        $userPresenceList = collect();

        foreach ($onlineUsers as $user) {
            $userPresenceList->push([
                'id'                => $user->id,
                'name'              => $user->name,
                'role_name'         => $user->role_name,
                'is_online'         => true,
                'is_recently_active'=> false,
                'last_active_human' => 'Online',
            ]);
        }

        foreach ($recentlyActiveUsers as $user) {
            /** @var \Carbon\Carbon|null $lastActive */
            $lastActive = $user->effective_last_active;
            $userPresenceList->push([
                'id'                => $user->id,
                'name'              => $user->name,
                'role_name'         => $user->role_name,
                'is_online'         => false,
                'is_recently_active'=> true,
                'last_active_human' => $lastActive ? $lastActive->diffForHumans() : 'Recently',
            ]);
        }

        return [
            'userPresenceStats' => $userPresenceStats,
            'userPresenceList'  => $userPresenceList->take(8),
        ];
    }

    /** Build monthly service request totals for the admin trend chart. */
    private function getAdminRequestTrend(int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();
            $total = LabRequest::whereBetween('created_at', [$start, $end])->count()
                   + RadiologyRequest::whereBetween('created_at', [$start, $end])->count()
                   + Prescription::whereBetween('created_at', [$start, $end])->count()
                   + SurgeryRequest::whereBetween('created_at', [$start, $end])->count()
                   + DietRequest::whereBetween('created_at', [$start, $end])->count();
            $result[] = ['label' => $date->format('M Y'), 'total' => $total];
        }
        return $result;
    }

    /** Build monthly doctor clinical request totals. */
    private function getDoctorRequestTrend(int $doctorId, int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();
            $total = LabRequest::where('doctor_id', $doctorId)->whereBetween('created_at', [$start, $end])->count()
                   + RadiologyRequest::where('doctor_id', $doctorId)->whereBetween('created_at', [$start, $end])->count()
                   + Prescription::where('doctor_id', $doctorId)->whereBetween('created_at', [$start, $end])->count()
                   + SurgeryRequest::where('doctor_id', $doctorId)->whereBetween('created_at', [$start, $end])->count()
                   + DietRequest::where('doctor_id', $doctorId)->whereBetween('created_at', [$start, $end])->count();
            // If doctor has no data, fall back to system-wide totals
            if ($total === 0) {
                $total = LabRequest::whereBetween('created_at', [$start, $end])->count()
                       + RadiologyRequest::whereBetween('created_at', [$start, $end])->count()
                       + Prescription::whereBetween('created_at', [$start, $end])->count()
                       + SurgeryRequest::whereBetween('created_at', [$start, $end])->count()
                       + DietRequest::whereBetween('created_at', [$start, $end])->count();
            }
            $result[] = ['label' => $date->format('M Y'), 'total' => $total];
        }
        return $result;
    }

    /** Build monthly lab request trend. */
    private function getLabRequestTrend(int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();
            $result[] = ['label' => $date->format('M Y'), 'total' => LabRequest::whereBetween('created_at', [$start, $end])->count()];
        }
        return $result;
    }

    /** Build monthly radiology request trend. */
    private function getRadiologyRequestTrend(int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();
            $result[] = ['label' => $date->format('M Y'), 'total' => RadiologyRequest::whereBetween('created_at', [$start, $end])->count()];
        }
        return $result;
    }

    /** Build monthly pharmacy prescription trend. */
    private function getPharmacyTrend(int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();
            $result[] = ['label' => $date->format('M Y'), 'total' => Prescription::whereBetween('created_at', [$start, $end])->count()];
        }
        return $result;
    }

    /** Build monthly surgery request trend. */
    private function getSurgeryTrend(int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();
            $result[] = ['label' => $date->format('M Y'), 'total' => SurgeryRequest::whereBetween('created_at', [$start, $end])->count()];
        }
        return $result;
    }

    /** Build monthly diet request trend. */
    private function getDietTrend(int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();
            $result[] = ['label' => $date->format('M Y'), 'total' => DietRequest::whereBetween('created_at', [$start, $end])->count()];
        }
        return $result;
    }

    /**
     * Build system & security alerts for the admin dashboard.
     * Combines active account lockouts/failed logins with recent security-related audit logs.
     *
     * @return \Illuminate\Support\Collection
     */
    private function buildAdminAlerts(): \Illuminate\Support\Collection
    {
        $alerts = collect();

        // 1. CRITICAL: Account Locked users (where locked_at is set)
        $lockedUsers = User::with('roles')
            ->whereNotNull('locked_at')
            ->orderBy('locked_at', 'desc')
            ->get();

        $lockedUserIds = $lockedUsers->pluck('id')->all();

        foreach ($lockedUsers as $user) {
            $attemptsInfo = $user->failed_attempts > 0 ? " ({$user->failed_attempts} failed login attempts)" : '';
            $roleStr = $user->role_name ? " ({$user->role_name})" : '';

            $alerts->push([
                'id'           => 'user_locked_' . $user->id,
                'type'         => 'danger',
                'icon'         => 'bi-lock-fill',
                'title'        => 'Account Locked',
                'user_name'    => $user->name,
                'role_name'    => $user->role_name,
                'description'  => "{$user->name}{$roleStr} is currently locked due to failed login attempts{$attemptsInfo}.",
                'timestamp'    => $user->locked_at ? $user->locked_at->diffForHumans() : 'Recently',
                'action_label' => 'Review Account',
                'action_route' => route('admin.users.index'),
            ]);
        }

        // 2. CRITICAL: Excessive Failed Login Attempts (where failed_attempts > 0 or active lockout_until, excluding locked users)
        $failedUsers = User::with('roles')
            ->whereNotIn('id', $lockedUserIds)
            ->where(function ($q) {
                $q->where('failed_attempts', '>', 0)
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('lockout_until')
                         ->where('lockout_until', '>', now());
                  });
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($failedUsers as $user) {
            $roleStr = $user->role_name ? " ({$user->role_name})" : '';
            $attempts = $user->failed_attempts;

            $alerts->push([
                'id'           => 'user_failed_' . $user->id,
                'type'         => 'danger',
                'icon'         => 'bi-shield-exclamation',
                'title'        => 'Multiple Failed Login Attempts',
                'user_name'    => $user->name,
                'role_name'    => $user->role_name,
                'description'  => "{$user->name}{$roleStr} — {$attempts} failed login attempt(s).",
                'timestamp'    => $user->updated_at ? $user->updated_at->diffForHumans() : 'Recently',
                'action_label' => 'Review Account',
                'action_route' => route('admin.users.index'),
            ]);
        }

        // 3. WARNING: Recent Administrative Security Changes (last 24 hours from ActivityLog)
        $secChangeLogs = ActivityLog::with('user')
            ->whereIn('action', [
                'Role Assignment Changed',   // UserController emits this exact action string
                'Account Unlocked',
                'Password Reset',
            ])
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        foreach ($secChangeLogs as $log) {
            $userName = $log->user?->name ?? 'System';
            $alerts->push([
                'id'           => 'log_sec_change_' . $log->id,
                'type'         => 'warning',
                'icon'         => 'bi-shield-lock',
                'title'        => $log->action,
                'user_name'    => $userName,
                'role_name'    => $log->user?->role_name ?? null,
                'description'  => $log->description ?: "Security/Role modification logged for {$userName}.",
                'timestamp'    => $log->created_at ? $log->created_at->diffForHumans() : 'Recently',
                'action_label' => 'View System Audit Logs',
                'action_route' => route('admin.audit-logs.index'),
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

        // 6. Doctor monthly request trend (6m & 12m)
        $doctorTrend6m  = $this->getDoctorRequestTrend($doctorId, 6);
        $doctorTrend12m = $this->getDoctorRequestTrend($doctorId, 12);

        // 7. Doctor requests by service (all-time for bar chart)
        $doctorServiceBreakdown = [
            ['service' => 'Laboratory', 'total' => LabRequest::where('doctor_id', $doctorId)->count() ?: LabRequest::count()],
            ['service' => 'Radiology',  'total' => RadiologyRequest::where('doctor_id', $doctorId)->count() ?: RadiologyRequest::count()],
            ['service' => 'Pharmacy',   'total' => Prescription::where('doctor_id', $doctorId)->count() ?: Prescription::count()],
            ['service' => 'Surgery',    'total' => SurgeryRequest::where('doctor_id', $doctorId)->count() ?: SurgeryRequest::count()],
            ['service' => 'Diet',       'total' => DietRequest::where('doctor_id', $doctorId)->count() ?: DietRequest::count()],
        ];

        return compact(
            'stats',
            'pendingLabRequests',
            'pendingRadRequests',
            'pendingRx',
            'pendingSurgery',
            'pendingDiet',
            'releasedLabResults',
            'releasedRadReports',
            'recentPatients',
            'doctorTrend6m',
            'doctorTrend12m',
            'doctorServiceBreakdown'
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

        // Lab trend + priority breakdown for new charts
        $labTrend6m  = $this->getLabRequestTrend(6);
        $labTrend12m = $this->getLabRequestTrend(12);
        $labPriority = [
            ['label' => 'Routine', 'count' => LabRequest::where('priority', 'Routine')->count()],
            ['label' => 'Urgent',  'count' => LabRequest::where('priority', 'Urgent')->count()],
            ['label' => 'STAT',    'count' => LabRequest::where('priority', 'STAT')->count()],
        ];

        return view('dashboard.lab', compact('stats', 'recentRequests', 'labTrend6m', 'labTrend12m', 'labPriority'));
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

        // Radiology trend + examination type breakdown
        $radTrend6m  = $this->getRadiologyRequestTrend(6);
        $radTrend12m = $this->getRadiologyRequestTrend(12);
        $radStatusBreakdown = [
            ['label' => 'Pending',     'count' => $stats['pending']],
            ['label' => 'Scheduled',   'count' => $stats['scheduled']],
            ['label' => 'In Progress', 'count' => $stats['in_progress']],
            ['label' => 'Completed',   'count' => $stats['completed']],
        ];
        $radReportBreakdown = [
            ['label' => 'Draft',    'count' => RadiologyReport::where('status', 'Draft')->count()],
            ['label' => 'Approved', 'count' => RadiologyReport::where('status', 'Approved')->count()],
            ['label' => 'Released', 'count' => RadiologyReport::where('status', 'Released')->count()],
        ];

        return view('dashboard.radiology', compact(
            'stats',
            'recentRequests',
            'pendingReports',
            'completedStudiesAwaitingReport',
            'isRadiologist',
            'radTrend6m',
            'radTrend12m',
            'radStatusBreakdown',
            'radReportBreakdown'
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

        // Pharmacy trend and status breakdown
        $rxTrend6m  = $this->getPharmacyTrend(6);
        $rxTrend12m = $this->getPharmacyTrend(12);
        $rxStatusBreakdown = [
            ['label' => 'Pending',   'count' => $stats['pending_prescriptions']],
            ['label' => 'Verified',  'count' => $stats['verified']],
            ['label' => 'Dispensed', 'count' => $stats['dispensed_total']],
        ];

        return view('dashboard.pharmacy', compact('stats', 'pendingPrescriptionsList', 'recentDispensing', 'rxTrend6m', 'rxTrend12m', 'rxStatusBreakdown'));
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

        // Surgery trend and status breakdown
        $surgTrend6m  = $this->getSurgeryTrend(6);
        $surgTrend12m = $this->getSurgeryTrend(12);
        $surgStatusBreakdown = [
            ['label' => 'Pending',   'count' => $stats['pending']],
            ['label' => 'Scheduled', 'count' => $stats['scheduled']],
            ['label' => 'Completed', 'count' => $stats['completed']],
            ['label' => 'Cancelled', 'count' => $stats['cancelled']],
        ];

        return view('dashboard.surgery', compact('stats', 'recentRequests', 'upcomingSchedules', 'surgTrend6m', 'surgTrend12m', 'surgStatusBreakdown'));
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

        // Diet trend and status breakdown
        $dietTrend6m  = $this->getDietTrend(6);
        $dietTrend12m = $this->getDietTrend(12);
        $dietStatusBreakdown = [
            ['label' => 'Pending',     'count' => $stats['pending']],
            ['label' => 'In Progress', 'count' => $stats['in_progress']],
            ['label' => 'Completed',   'count' => $stats['completed_total']],
        ];

        return view('dashboard.diet', compact('stats', 'recentRequests', 'activePlansList', 'dietTrend6m', 'dietTrend12m', 'dietStatusBreakdown'));
    }
}
