<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Diet\DietPlanController;
use App\Http\Controllers\Diet\DietRequestController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\Lab\LabRequestController;
use App\Http\Controllers\Lab\LabResultController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MediSenseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Pharmacy\DispensingController;
use App\Http\Controllers\Pharmacy\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Radiology\RadiologyReportController;
use App\Http\Controllers\Radiology\RadiologyRequestController;
use App\Http\Controllers\Surgery\SurgeryRequestController;
use App\Http\Controllers\Surgery\SurgeryScheduleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — DITC Hospital Management System
|--------------------------------------------------------------------------
*/

// ── Auth Routes (Breeze) ─────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── Root redirect ────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ═══════════════════════════════════════════════════════════════════════
// AUTHENTICATED ROUTES (all roles must be logged in)
// ═══════════════════════════════════════════════════════════════════════
Route::middleware(['auth'])->group(function () {

    // ── Skeleton System Demo (admin-only developer reference) ─────────
    Route::get('/skeleton-demo', fn() => view('skeleton-demo'))
         ->middleware('role:admin')
         ->name('skeleton.demo');

    // ── Fallback/Generic Dashboard (For role-less users & Breeze tests) ──
    Route::get('/dashboard', function () {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $role = $user?->roles()->first();
        $dashboardRoute = $role?->dashboard_route;

        if ($dashboardRoute && Route::has($dashboardRoute)) {
            return redirect()->route($dashboardRoute);
        }

        return view('dashboard.default');
    })->name('dashboard');


    // ── Profile ───────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Global Search ──────────────────────────────────────────────────
    Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global-search');

    // ── Notifications ─────────────────────────────────────────────────
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // ── Internal Staff Messages ───────────────────────────────────────
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::get('/messages/recent', [MessageController::class, 'recent'])->name('messages.recent');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');

    // ── Settings ──────────────────────────────────────────────────────
    Route::get('/settings', fn() => view('settings.index'))->name('settings.index');

    // ── Patients ──────────────────────────────────────────────────────
    // Full Patient Information module — System Administrator & Doctor only
    Route::prefix('patients')
         ->name('patients.')
         ->middleware('role:admin,doctor')
         ->group(function () {
             Route::get('/',                  [PatientController::class, 'index'])->name('index');
             Route::get('/create',            [PatientController::class, 'create'])->name('create');
             Route::post('/',                 [PatientController::class, 'store'])->name('store');
             Route::get('/{patient}',         [PatientController::class, 'show'])->name('show');
             Route::get('/{patient}/edit',    [PatientController::class, 'edit'])->name('edit');
             Route::put('/{patient}',         [PatientController::class, 'update'])->name('update');
             Route::patch('/{patient}',       [PatientController::class, 'update']);
             Route::delete('/{patient}',      [PatientController::class, 'destroy'])->name('destroy');
         });

    // ═══════════════════════════════════════════════════════════════════
    // ADMIN ROUTES
    // ═══════════════════════════════════════════════════════════════════
    Route::prefix('admin')
         ->middleware('role:admin')
         ->name('admin.')
         ->group(function () {
             Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
             Route::resource('users', UserController::class);
             Route::resource('roles', RoleController::class);
             Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
             Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
             Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
             Route::post('users/{user}/unlock', [UserController::class, 'unlockAccount'])->name('users.unlock');
         });

    // ═══════════════════════════════════════════════════════════════════
    // DOCTOR DASHBOARD
    // ═══════════════════════════════════════════════════════════════════
    Route::get('/doctor/dashboard', [DashboardController::class, 'doctor'])
         ->middleware('role:admin,doctor')
         ->name('doctor.dashboard');

    // ═══════════════════════════════════════════════════════════════════
    // LIS — Laboratory Information System
    // ═══════════════════════════════════════════════════════════════════
    Route::prefix('lab')
         ->name('lab.')
         ->group(function () {

             // Dashboard for lab staff
             Route::get('/dashboard', [DashboardController::class, 'lab'])
                  ->middleware('role:admin,med-tech,doctor')
                  ->name('dashboard');

             // Lab Requests (Doctor creates, Med-Tech manages)
             Route::resource('requests', LabRequestController::class)
                  ->parameters(['requests' => 'labRequest'])
                  ->middleware('role:admin,doctor,med-tech');

             // Additional actions on lab requests
             Route::patch('requests/{labRequest}/receive', [LabRequestController::class, 'receive'])
                  ->middleware('role:admin,med-tech')
                  ->name('requests.receive');

             Route::get('requests/{labRequest}/print', [LabRequestController::class, 'print'])
                  ->middleware('role:admin,doctor,med-tech')
                  ->name('requests.print');

             // Lab Results (Med-Tech encodes)
             Route::resource('results', LabResultController::class)
                  ->middleware('role:admin,med-tech,doctor');

             Route::patch('results/{labResult}/validate', [LabResultController::class, 'validate'])
                  ->middleware('role:admin,med-tech')
                  ->name('results.validate');

             Route::patch('results/{labResult}/release', [LabResultController::class, 'release'])
                  ->middleware('role:admin,med-tech')
                  ->name('results.release');
         });

    // ═══════════════════════════════════════════════════════════════════
    // RIS — Radiology Information System
    // ═══════════════════════════════════════════════════════════════════
    Route::prefix('radiology')
         ->name('radiology.')
         ->group(function () {

             Route::get('/dashboard', [DashboardController::class, 'radiology'])
                  ->middleware('role:admin,doctor,rad-tech,radiologist')
                  ->name('dashboard');

             Route::resource('requests', RadiologyRequestController::class)
                  ->parameters(['requests' => 'radiologyRequest'])
                  ->middleware('role:admin,doctor,rad-tech,radiologist');

             Route::patch('requests/{radiologyRequest}/schedule', [RadiologyRequestController::class, 'schedule'])
                  ->middleware('role:admin,rad-tech')
                  ->name('requests.schedule');

             Route::patch('requests/{radiologyRequest}/start', [RadiologyRequestController::class, 'start'])
                  ->middleware('role:admin,rad-tech')
                  ->name('requests.start');

             Route::post('requests/{radiologyRequest}/upload', [RadiologyRequestController::class, 'uploadImage'])
                  ->middleware('role:rad-tech,admin')
                  ->name('requests.upload');

             Route::get('images/{image}/view', [RadiologyRequestController::class, 'viewImage'])
                  ->middleware('role:admin,doctor,rad-tech,radiologist')
                  ->name('images.view');

             Route::patch('requests/{radiologyRequest}/complete', [RadiologyRequestController::class, 'complete'])
                  ->middleware('role:admin,rad-tech')
                  ->name('requests.complete');

             Route::resource('reports', RadiologyReportController::class)
                  ->parameters(['reports' => 'radiologyReport'])
                  ->middleware('role:admin,radiologist,doctor,rad-tech');

             Route::patch('reports/{radiologyReport}/approve', [RadiologyReportController::class, 'approve'])
                  ->middleware('role:admin,radiologist')
                  ->name('reports.approve');

             Route::patch('reports/{radiologyReport}/release', [RadiologyReportController::class, 'release'])
                  ->middleware('role:admin,radiologist')
                  ->name('reports.release');
         });

    // ═══════════════════════════════════════════════════════════════════
    // PMS — Pharmacy Management System
    // ═══════════════════════════════════════════════════════════════════
    Route::prefix('pharmacy')
         ->name('pharmacy.')
         ->group(function () {

             Route::get('/dashboard', [DashboardController::class, 'pharmacy'])
                  ->middleware('role:admin,doctor,pharmacist')
                  ->name('dashboard');

             Route::resource('prescriptions', PrescriptionController::class)
                  ->middleware('role:admin,doctor,pharmacist');

             Route::patch('prescriptions/{prescription}/verify', [PrescriptionController::class, 'verify'])
                  ->middleware('role:pharmacist,admin')
                  ->name('prescriptions.verify');

             Route::resource('dispensing', DispensingController::class)
                  ->middleware('role:admin,pharmacist');
         });

    // ═══════════════════════════════════════════════════════════════════
    // SORS — Surgery & Operating Room Scheduler
    // ═══════════════════════════════════════════════════════════════════
    Route::prefix('surgery')
         ->name('surgery.')
         ->group(function () {

             Route::get('/dashboard', [DashboardController::class, 'surgery'])
                  ->middleware('role:admin,doctor,or-coordinator')
                  ->name('dashboard');

             Route::get('/calendar', [SurgeryScheduleController::class, 'calendar'])
                  ->middleware('role:admin,doctor,or-coordinator')
                  ->name('calendar');

             Route::get('/calendar/events', [SurgeryScheduleController::class, 'calendarEvents'])
                  ->middleware('role:admin,doctor,or-coordinator')
                  ->name('calendar.events');

             Route::resource('requests', SurgeryRequestController::class)
                  ->parameters(['requests' => 'surgeryRequest'])
                  ->middleware('role:admin,doctor,or-coordinator');

             Route::patch('requests/{surgeryRequest}/cancel', [SurgeryRequestController::class, 'cancel'])
                  ->middleware('role:admin,doctor,or-coordinator')
                  ->name('requests.cancel');

             Route::resource('schedules', SurgeryScheduleController::class)
                  ->parameters(['schedules' => 'surgerySchedule'])
                  ->middleware('role:admin,doctor,or-coordinator');

             Route::patch('schedules/{surgerySchedule}/start', [SurgeryScheduleController::class, 'start'])
                  ->middleware('role:admin,doctor,or-coordinator')
                  ->name('schedules.start');

             Route::patch('schedules/{surgerySchedule}/complete', [SurgeryScheduleController::class, 'complete'])
                  ->middleware('role:admin,doctor,or-coordinator')
                  ->name('schedules.complete');
         });

    // ═══════════════════════════════════════════════════════════════════
    // DNMS — Diet & Nutrition Management System
    // ═══════════════════════════════════════════════════════════════════
    Route::prefix('diet')
         ->name('diet.')
         ->group(function () {

             Route::get('/dashboard', [DashboardController::class, 'diet'])
                  ->middleware('role:admin,doctor,dietitian')
                  ->name('dashboard');

             Route::resource('requests', DietRequestController::class)
                  ->middleware('role:admin,doctor,dietitian');

             Route::resource('plans', DietPlanController::class)
                  ->middleware('role:admin,dietitian,doctor');

             Route::patch('plans/{dietPlan}/complete', [DietPlanController::class, 'complete'])
                  ->middleware('role:dietitian,admin')
                  ->name('plans.complete');
         });

    // ═══════════════════════════════════════════════════════════════════
    // REPORTS MODULE
    // ═══════════════════════════════════════════════════════════════════
    Route::prefix('reports')
         ->name('reports.')
         ->group(function () {

             Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');

             // ── Laboratory Reports ──
             Route::prefix('laboratory')->name('laboratory.')->middleware('role:admin,doctor,med-tech')->group(function () {
                 Route::get('/activity',  [\App\Http\Controllers\ReportController::class, 'labActivity'])->name('activity');
                 Route::get('/volume',    [\App\Http\Controllers\ReportController::class, 'labVolume'])->name('volume');
                 Route::get('/completed', [\App\Http\Controllers\ReportController::class, 'labCompleted'])->name('completed');
                 Route::get('/pending',   [\App\Http\Controllers\ReportController::class, 'labPending'])->name('pending');
             });

             // ── Radiology Reports ──
             Route::prefix('radiology')->name('radiology.')->middleware('role:admin,doctor,rad-tech,radiologist')->group(function () {
                 Route::get('/activity',  [\App\Http\Controllers\ReportController::class, 'radiologyActivity'])->name('activity');
                 Route::get('/volume',    [\App\Http\Controllers\ReportController::class, 'radiologyVolume'])->name('volume');
                 Route::get('/completed', [\App\Http\Controllers\ReportController::class, 'radiologyCompleted'])->name('completed');
                 Route::get('/pending',   [\App\Http\Controllers\ReportController::class, 'radiologyPending'])->name('pending');
             });

             // ── Pharmacy Reports ──
             Route::prefix('pharmacy')->name('pharmacy.')->middleware('role:admin,doctor,pharmacist')->group(function () {
                 Route::get('/activity',   [\App\Http\Controllers\ReportController::class, 'pharmacyActivity'])->name('activity');
                 Route::get('/dispensing', [\App\Http\Controllers\ReportController::class, 'pharmacyDispensing'])->name('dispensing');
                 Route::get('/pending',    [\App\Http\Controllers\ReportController::class, 'pharmacyPending'])->name('pending');
             });

             // ── Surgery Reports ──
             Route::prefix('surgery')->name('surgery.')->middleware('role:admin,doctor,or-coordinator')->group(function () {
                 Route::get('/activity',       [\App\Http\Controllers\ReportController::class, 'surgeryActivity'])->name('activity');
                 Route::get('/completed',      [\App\Http\Controllers\ReportController::class, 'surgeryCompleted'])->name('completed');
                 Route::get('/cancelled',      [\App\Http\Controllers\ReportController::class, 'surgeryCancelled'])->name('cancelled');
                 Route::get('/or-utilization', [\App\Http\Controllers\ReportController::class, 'surgeryOrUtilization'])->name('or-utilization');
             });

             // ── Diet & Nutrition Reports ──
             Route::prefix('diet')->name('diet.')->middleware('role:admin,doctor,dietitian')->group(function () {
                 Route::get('/activity',  [\App\Http\Controllers\ReportController::class, 'dietActivity'])->name('activity');
                 Route::get('/active',    [\App\Http\Controllers\ReportController::class, 'dietActive'])->name('active');
                 Route::get('/completed', [\App\Http\Controllers\ReportController::class, 'dietCompleted'])->name('completed');
             });

             // ── Clinical Summary Reports ──
             Route::prefix('clinical')->name('clinical.')->middleware('role:admin,doctor')->group(function () {
                 Route::get('/patient-activity',  [\App\Http\Controllers\ReportController::class, 'clinicalPatientActivity'])->name('patient-activity');
                 Route::get('/doctor-activity',   [\App\Http\Controllers\ReportController::class, 'clinicalDoctorActivity'])->name('doctor-activity');
                 Route::get('/services-summary',  [\App\Http\Controllers\ReportController::class, 'clinicalServicesSummary'])->name('services-summary');
             });
         });

    // ═══════════════════════════════════════════════════════════════════
    // VIRTUAL MEDISENSE AI
    // ═══════════════════════════════════════════════════════════════════
    Route::prefix('medisense')
         ->name('medisense.')
         ->middleware('role:admin,doctor,med-tech,rad-tech,radiologist,pharmacist,dietitian,or-coordinator')
         ->group(function () {
             Route::get('/',              [MediSenseController::class, 'index'])->name('index');
             Route::get('/capabilities',  [MediSenseController::class, 'capabilities'])->name('capabilities');
             Route::post('/chat',         [MediSenseController::class, 'chat'])->name('chat');
             Route::get('/history',       [MediSenseController::class, 'history'])->name('history');
             Route::post('/clear',        [MediSenseController::class, 'clear'])->name('clear');
         });
});
