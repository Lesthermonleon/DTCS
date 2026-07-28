<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Diet\DietPlanController;
use App\Http\Controllers\Diet\DietRequestController;
use App\Http\Controllers\Lab\LabRequestController;
use App\Http\Controllers\Lab\LabResultController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Pharmacy\DispensingController;
use App\Http\Controllers\Pharmacy\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Radiology\RadiologyReportController;
use App\Http\Controllers\Radiology\RadiologyRequestController;
use App\Http\Controllers\Surgery\SurgeryRequestController;
use App\Http\Controllers\Surgery\SurgeryScheduleController;
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
        $user = auth()->user();
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

    // ── Settings ──────────────────────────────────────────────────────
    Route::get('/settings', fn() => view('settings.index'))->name('settings.index');

    // ── Patients (accessible to doctor + admin) ───────────────────────
    Route::middleware('role:admin,doctor,med-tech,rad-tech,radiologist,pharmacist,dietitian,or-coordinator')
         ->resource('patients', PatientController::class);

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
                  ->middleware('role:admin,doctor,med-tech');

             // Additional actions on lab requests
             Route::patch('requests/{labRequest}/receive', [LabRequestController::class, 'receive'])
                  ->middleware('role:med-tech')
                  ->name('requests.receive');

             Route::get('requests/{labRequest}/print', [LabRequestController::class, 'print'])
                  ->middleware('role:admin,doctor,med-tech')
                  ->name('requests.print');

             // Lab Results (Med-Tech encodes)
             Route::resource('results', LabResultController::class)
                  ->middleware('role:admin,med-tech,doctor');

             Route::patch('results/{labResult}/validate', [LabResultController::class, 'validate'])
                  ->middleware('role:med-tech')
                  ->name('results.validate');

             Route::patch('results/{labResult}/release', [LabResultController::class, 'release'])
                  ->middleware('role:med-tech')
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
                  ->middleware('role:admin,doctor,rad-tech,radiologist');

             Route::patch('requests/{radiologyRequest}/schedule', [RadiologyRequestController::class, 'schedule'])
                  ->middleware('role:rad-tech')
                  ->name('requests.schedule');

             Route::post('requests/{radiologyRequest}/upload', [RadiologyRequestController::class, 'uploadImage'])
                  ->middleware('role:rad-tech,admin')
                  ->name('requests.upload');

             Route::resource('reports', RadiologyReportController::class)
                  ->middleware('role:admin,radiologist,doctor');

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
                  ->middleware('role:admin,doctor,or-coordinator');

             Route::patch('requests/{surgeryRequest}/cancel', [SurgeryRequestController::class, 'cancel'])
                  ->middleware('role:admin,doctor,or-coordinator')
                  ->name('requests.cancel');

             Route::resource('schedules', SurgeryScheduleController::class)
                  ->middleware('role:admin,or-coordinator');

             Route::patch('schedules/{surgerySchedule}/complete', [SurgeryScheduleController::class, 'complete'])
                  ->middleware('role:admin,or-coordinator')
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
});
