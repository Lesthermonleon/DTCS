<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * AuditLogController — Read-only view of system activity logs.
 *
 * No create/edit/update/destroy — audit trail is append-only.
 */
class AuditLogController extends Controller
{
    /**
     * Display paginated audit log with filtering by user, role, module, severity, and date.
     *
     * All counts use SQL COUNT queries — no full-table PHP collection iteration.
     * This ensures the page remains fast even with millions of audit records.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with(['user.roles'])
            ->orderBy('created_at', 'desc');

        // --- Search by User Name ---
        if ($search = trim($request->input('search', ''))) {
            $query->whereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
        }

        // --- Role Filter ---
        if ($roleId = $request->input('role')) {
            $query->whereHas('user.roles', fn($r) => $r->where('roles.id', $roleId));
        }

        // --- Module Filter ---
        if ($module = $request->input('module')) {
            $query->where('module', $module);
        }

        // --- Severity Filter ---
        if ($severity = $request->input('severity')) {
            $query->where('severity', $severity);
        }

        // --- Date Filter ---
        if ($date = $request->input('date')) {
            match ($date) {
                'today'     => $query->where('created_at', '>=', now()->startOfDay()),
                'yesterday' => $query->whereBetween('created_at', [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()]),
                '7_days'    => $query->where('created_at', '>=', now()->subDays(7)->startOfDay()),
                '30_days'   => $query->where('created_at', '>=', now()->subDays(30)->startOfDay()),
                'this_year' => $query->where('created_at', '>=', now()->startOfYear()),
                default     => null,
            };
        }

        $logs = $query->paginate(25)->withQueryString();

        // ── Summary statistics (SQL COUNT — never loads full collections) ──
        $totalEvents       = ActivityLog::count();
        $authEvents        = ActivityLog::where('module', ActivityLog::MODULE_AUTH)->count();
        $clinicalEvents    = ActivityLog::whereIn('module', ActivityLog::CLINICAL_MODULES)->count();
        $adminSystemEvents = ActivityLog::whereIn('module', ActivityLog::ADMIN_MODULES)->count();

        // Any events that don't fit the defined categories
        $uncategorizedEvents = max(0, $totalEvents - $authEvents - $clinicalEvents - $adminSystemEvents);

        // ── Dropdown data ──
        $roles    = Role::orderBy('name')->get();
        $modules  = ActivityLog::ADMIN_MODULES + ActivityLog::CLINICAL_MODULES;

        // Build ordered module list for filter dropdown
        $moduleOptions = array_merge(
            [ActivityLog::MODULE_AUTH],
            ActivityLog::CLINICAL_MODULES,
            ActivityLog::ADMIN_MODULES,
        );

        return view('admin.audit-logs.index', compact(
            'logs',
            'totalEvents',
            'authEvents',
            'clinicalEvents',
            'adminSystemEvents',
            'uncategorizedEvents',
            'roles',
            'moduleOptions'
        ));
    }

    /**
     * Show a single audit log entry in detail.
     */
    public function show(ActivityLog $auditLog): View
    {
        $auditLog->load('user.roles');

        return view('admin.audit-logs.show', compact('auditLog'));
    }
}
