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
    public function admin()
    {
        // ── Statistics ──
        $stats = [
            'total_users'          => User::count(),
            'active_users_today'   => User::where('is_active', true)->count(),
            'new_user_requests'    => User::where('created_at', '>=', now()->subDays(7))->count(),
            'failed_login_attempts'=> User::where('failed_attempts', '>', 0)->sum('failed_attempts'),
            'locked_accounts'      => User::whereNotNull('locked_at')->count(),
            'inactive_accounts'    => User::where('is_active', false)->count(),
            'admins_count'         => User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->count(),
        ];

        // ── Users by Role (for donut chart) ──
        $roles = Role::withCount('users')->get();
        $usersByRole = $roles->map(fn($r) => [
            'name'  => $r->name,
            'count' => $r->users_count,
        ])->values();

        // ── 7-day new user registrations trend ──
        $newUsers7d = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->toDateString();
            return [
                'date'  => now()->subDays($daysAgo)->format('M d'),
                'count' => User::whereDate('created_at', $date)->count(),
            ];
        });

        // ── Notifications ──
        $notifications = collect();
        $recentUsers = User::where('created_at', '>=', now()->subHours(24))->count();
        if ($recentUsers > 0) {
            $notifications->push([
                'icon' => 'bi-person-plus', 'color' => 'signal',
                'text' => "{$recentUsers} new user registration" . ($recentUsers > 1 ? 's' : '') . " in the last 24h",
                'time' => 'Recent',
            ]);
        }
        $lockedAccounts = User::where('is_active', false)->count();
        if ($lockedAccounts > 0) {
            $notifications->push([
                'icon' => 'bi-lock', 'color' => 'coral',
                'text' => "{$lockedAccounts} account" . ($lockedAccounts > 1 ? 's' : '') . " currently locked/inactive",
                'time' => 'Active',
            ]);
        }
        $notifications->push([
            'icon' => 'bi-key', 'color' => 'amber',
            'text' => 'No pending password reset requests',
            'time' => '—',
        ]);
        $notifications->push([
            'icon' => 'bi-megaphone', 'color' => 'steel',
            'text' => 'System running normally — no announcements',
            'time' => '—',
        ]);

        // ── Task Queue ──
        $usersWithoutRoles = User::whereDoesntHave('roles')->count();
        $taskQueue = [
            ['label' => 'Pending user approvals',    'count' => $usersWithoutRoles, 'color' => $usersWithoutRoles > 0 ? 'coral' : 'signal'],
            ['label' => 'Pending role assignments',   'count' => $usersWithoutRoles, 'color' => $usersWithoutRoles > 0 ? 'amber' : 'signal'],
            ['label' => 'Pending permission updates', 'count' => 0,                  'color' => 'signal'],
        ];

        // ── Recent Activity ──
        $recentActivity = ActivityLog::with('user')
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('dashboard.admin', compact(
            'stats', 'notifications', 'taskQueue', 'recentActivity',
            'usersByRole', 'newUsers7d'
        ));
    }

    /** Doctor dashboard. */
    public function doctor()
    {
        /** @var User $doctor */
        $doctor    = Auth::user();
        $weekStart = now()->startOfWeek();

        $stats = [
            'my_lab_requests'  => LabRequest::where('doctor_id', $doctor->id)->count(),
            'pending_lab'      => LabRequest::where('doctor_id', $doctor->id)->where('status', 'Pending')->count(),
            'completed_lab'    => LabRequest::where('doctor_id', $doctor->id)->where('status', 'Completed')->count(),
            'my_radiology'     => RadiologyRequest::where('doctor_id', $doctor->id)->count(),
            'my_prescriptions' => Prescription::where('doctor_id', $doctor->id)->count(),
            'my_surgeries'     => SurgeryRequest::where('doctor_id', $doctor->id)->count(),
            'my_diet_requests' => DietRequest::where('doctor_id', $doctor->id)->count(),
            // This week
            'lab_this_week'    => LabRequest::where('doctor_id', $doctor->id)->where('created_at', '>=', $weekStart)->count(),
            'rx_this_week'     => Prescription::where('doctor_id', $doctor->id)->where('created_at', '>=', $weekStart)->count(),
            'rad_this_week'    => RadiologyRequest::where('doctor_id', $doctor->id)->where('created_at', '>=', $weekStart)->count(),
            'surg_this_week'   => SurgeryRequest::where('doctor_id', $doctor->id)->where('created_at', '>=', $weekStart)->count(),
            'diet_this_week'   => DietRequest::where('doctor_id', $doctor->id)->where('created_at', '>=', $weekStart)->count(),
        ];

        $recentLabRequests   = LabRequest::where('doctor_id', $doctor->id)->with('patient')->latest()->take(5)->get();
        $recentPrescriptions = Prescription::where('doctor_id', $doctor->id)->with('patient')->latest()->take(5)->get();

        return view('dashboard.doctor', compact('stats', 'recentLabRequests', 'recentPrescriptions'));
    }

    /** Lab / Medical Technologist dashboard. */
    public function lab()
    {
        $today = now()->toDateString();

        $stats = [
            'total_requests'  => LabRequest::count(),
            'pending'         => LabRequest::where('status', 'Pending')->count(),
            'in_progress'     => LabRequest::where('status', 'In Progress')->count(),
            'completed'       => LabRequest::where('status', 'Completed')->count(),
            'stat_count'      => LabRequest::where('priority', 'STAT')->count(),
            'today_received'  => LabRequest::whereDate('created_at', $today)->count(),
            'today_completed' => LabRequest::whereDate('updated_at', $today)->where('status', 'Completed')->count(),
            'stat_pending'    => LabRequest::where('priority', 'STAT')->where('status', 'Pending')->count(),
        ];

        $recentRequests = LabRequest::with('patient', 'doctor')
                            ->latest()
                            ->take(10)
                            ->get();

        return view('dashboard.lab', compact('stats', 'recentRequests'));
    }

    /** Radiology dashboard (shared by rad-tech and radiologist). */
    public function radiology()
    {
        $today = now()->toDateString();

        $stats = [
            'total_requests'  => RadiologyRequest::count(),
            'pending'         => RadiologyRequest::where('status', 'Pending')->count(),
            'scheduled'       => RadiologyRequest::where('status', 'Scheduled')->count(),
            'completed'       => RadiologyRequest::where('status', 'Completed')->count(),
            'reports_pending' => class_exists(RadiologyReport::class)
                                    ? RadiologyReport::where('status', 'Pending')->count()
                                    : 0,
            'today_scheduled' => RadiologyRequest::whereDate('updated_at', $today)->where('status', 'Scheduled')->count(),
            'today_completed' => RadiologyRequest::whereDate('updated_at', $today)->where('status', 'Completed')->count(),
            'reports_released'=> class_exists(RadiologyReport::class)
                                    ? RadiologyReport::where('status', 'Released')->count()
                                    : 0,
        ];

        $recentRequests = RadiologyRequest::with('patient', 'doctor')
                            ->latest()
                            ->take(10)
                            ->get();

        $pendingReports = class_exists(RadiologyReport::class)
            ? RadiologyReport::where('status', 'Pending')->with('imagingRequest.patient')->latest()->take(5)->get()
            : collect();

        return view('dashboard.radiology', compact('stats', 'recentRequests', 'pendingReports'));
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
                                        ->where('status', 'Pending')
                                        ->latest()->take(10)->get();

        $recentDispensing = class_exists(DispensingRecord::class)
            ? DispensingRecord::with('prescription.patient')->latest()->take(5)->get()
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
                            ->latest()->take(10)->get();

        $upcomingSchedules = class_exists(SurgerySchedule::class)
            ? SurgerySchedule::with('surgeryRequest.patient')
                ->whereIn('status', ['Scheduled', 'Confirmed'])
                ->orderBy('scheduled_at')
                ->take(5)->get()
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
                            ->latest()->take(10)->get();

        $activePlansList = class_exists(DietPlan::class)
            ? DietPlan::with('patient')->where('status', 'Active')->latest()->take(5)->get()
            : collect();

        return view('dashboard.diet', compact('stats', 'recentRequests', 'activePlansList'));
    }
}
