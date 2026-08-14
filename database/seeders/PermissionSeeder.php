<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seeds realistic system permissions and assigns them to roles.
 *
 * Permissions are grouped by hospital module. The admin role
 * receives all permissions automatically.
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Define permissions by module ──────────────────────────────────
        $permissions = [
            // LIS — Laboratory Information System
            ['name' => 'View Lab Requests',     'slug' => 'view-lab-requests',     'module' => 'LIS'],
            ['name' => 'Create Lab Requests',   'slug' => 'create-lab-requests',   'module' => 'LIS'],
            ['name' => 'Process Lab Requests',  'slug' => 'process-lab-requests',  'module' => 'LIS'],
            ['name' => 'Encode Lab Results',    'slug' => 'encode-lab-results',    'module' => 'LIS'],
            ['name' => 'Validate Lab Results',  'slug' => 'validate-lab-results',  'module' => 'LIS'],
            ['name' => 'Release Lab Results',   'slug' => 'release-lab-results',   'module' => 'LIS'],

            // RIS — Radiology Information System
            ['name' => 'View Radiology Requests',     'slug' => 'view-radiology-requests',     'module' => 'RIS'],
            ['name' => 'Create Radiology Requests',   'slug' => 'create-radiology-requests',   'module' => 'RIS'],
            ['name' => 'Schedule Radiology Requests',  'slug' => 'schedule-radiology-requests', 'module' => 'RIS'],
            ['name' => 'Upload Radiology Scans',      'slug' => 'upload-radiology-scans',      'module' => 'RIS'],
            ['name' => 'Create Radiology Reports',    'slug' => 'create-radiology-reports',    'module' => 'RIS'],
            ['name' => 'Approve Radiology Reports',   'slug' => 'approve-radiology-reports',   'module' => 'RIS'],
            ['name' => 'Release Radiology Reports',   'slug' => 'release-radiology-reports',   'module' => 'RIS'],

            // PMS — Pharmacy Management System
            ['name' => 'View Prescriptions',    'slug' => 'view-prescriptions',    'module' => 'PMS'],
            ['name' => 'Create Prescriptions',  'slug' => 'create-prescriptions',  'module' => 'PMS'],
            ['name' => 'Verify Prescriptions',  'slug' => 'verify-prescriptions',  'module' => 'PMS'],
            ['name' => 'Dispense Medications',  'slug' => 'dispense-medications',  'module' => 'PMS'],

            // SORS — Surgical & Operating Room System
            ['name' => 'View Surgery Requests',   'slug' => 'view-surgery-requests',   'module' => 'SORS'],
            ['name' => 'Create Surgery Requests', 'slug' => 'create-surgery-requests', 'module' => 'SORS'],
            ['name' => 'Schedule Surgeries',      'slug' => 'schedule-surgeries',      'module' => 'SORS'],
            ['name' => 'Manage Operating Rooms',  'slug' => 'manage-operating-rooms',  'module' => 'SORS'],
            ['name' => 'Manage Surgical Teams',   'slug' => 'manage-surgical-teams',   'module' => 'SORS'],

            // DNMS — Diet & Nutrition Management System
            ['name' => 'View Diet Requests',      'slug' => 'view-diet-requests',      'module' => 'DNMS'],
            ['name' => 'Create Diet Requests',    'slug' => 'create-diet-requests',    'module' => 'DNMS'],
            ['name' => 'Manage Diet Plans',       'slug' => 'manage-diet-plans',       'module' => 'DNMS'],
            ['name' => 'Manage Meal Schedules',   'slug' => 'manage-meal-schedules',   'module' => 'DNMS'],

            // Administration
            ['name' => 'Manage Users',        'slug' => 'manage-users',        'module' => 'Admin'],
            ['name' => 'Manage Roles',        'slug' => 'manage-roles',        'module' => 'Admin'],
            ['name' => 'View Activity Logs',  'slug' => 'view-activity-logs',  'module' => 'Admin'],

            // Virtual MediSense AI Permissions
            ['name' => 'Access MediSense AI',          'slug' => 'medisense.access',                  'module' => 'MediSense'],
            ['name' => 'MediSense Symptom Assessment', 'slug' => 'medisense.symptom_assessment',      'module' => 'MediSense'],
            ['name' => 'MediSense Diagnostic Assist',   'slug' => 'medisense.diagnostic_assistance',  'module' => 'MediSense'],
            ['name' => 'MediSense Treatment Rec',       'slug' => 'medisense.treatment_recommendation','module' => 'MediSense'],
            ['name' => 'MediSense Clinical Summary',   'slug' => 'medisense.clinical_summary',        'module' => 'MediSense'],
            ['name' => 'MediSense Lab Analysis',       'slug' => 'medisense.laboratory_analysis',    'module' => 'MediSense'],
            ['name' => 'MediSense Imaging Summary',    'slug' => 'medisense.imaging_summary',         'module' => 'MediSense'],
            ['name' => 'MediSense Med Info',           'slug' => 'medisense.medication_information',  'module' => 'MediSense'],
            ['name' => 'MediSense Lab Assist',         'slug' => 'medisense.laboratory_assistance',   'module' => 'MediSense'],
            ['name' => 'MediSense Lab Test Info',      'slug' => 'medisense.test_information',        'module' => 'MediSense'],
            ['name' => 'MediSense Lab Batch Summary',  'slug' => 'medisense.laboratory_summary',      'module' => 'MediSense'],
            ['name' => 'MediSense Imaging Workflow',   'slug' => 'medisense.imaging_workflow',        'module' => 'MediSense'],
            ['name' => 'MediSense Radiology Assist',   'slug' => 'medisense.radiology_assistance',    'module' => 'MediSense'],
            ['name' => 'MediSense Radiology Findings', 'slug' => 'medisense.radiology_findings',      'module' => 'MediSense'],
            ['name' => 'MediSense Prescription Assist', 'slug' => 'medisense.prescription_assistance','module' => 'MediSense'],
            ['name' => 'MediSense Medication Review',   'slug' => 'medisense.medication_review',      'module' => 'MediSense'],
            ['name' => 'MediSense Nutrition Assessment','slug' => 'medisense.nutrition_assessment',   'module' => 'MediSense'],
            ['name' => 'MediSense Diet Plan Assist',   'slug' => 'medisense.diet_plan_assistance',   'module' => 'MediSense'],
            ['name' => 'MediSense Surgery Workflow',   'slug' => 'medisense.surgery_workflow',        'module' => 'MediSense'],
            ['name' => 'MediSense OR Scheduling',      'slug' => 'medisense.scheduling_assistance',   'module' => 'MediSense'],
            ['name' => 'MediSense Ops Analytics',      'slug' => 'medisense.operational_analytics',   'module' => 'MediSense'],
            ['name' => 'MediSense System Insights',    'slug' => 'medisense.system_insights',         'module' => 'MediSense'],
        ];

        // ── Create permission records ────────────────────────────────────
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        // ── Role → Permission assignments ────────────────────────────────
        $rolePermissions = [
            'admin' => Permission::pluck('id')->toArray(), // all permissions

            'doctor' => Permission::whereIn('slug', [
                'view-lab-requests', 'create-lab-requests',
                'view-radiology-requests', 'create-radiology-requests',
                'view-prescriptions', 'create-prescriptions',
                'view-surgery-requests', 'create-surgery-requests',
                'view-diet-requests', 'create-diet-requests',
                'medisense.access',
                'medisense.symptom_assessment',
                'medisense.diagnostic_assistance',
                'medisense.treatment_recommendation',
                'medisense.clinical_summary',
                'medisense.laboratory_analysis',
                'medisense.imaging_summary',
                'medisense.medication_information',
            ])->pluck('id')->toArray(),

            'med-tech' => Permission::whereIn('slug', [
                'view-lab-requests', 'process-lab-requests',
                'encode-lab-results', 'validate-lab-results', 'release-lab-results',
                'medisense.access',
                'medisense.laboratory_assistance',
                'medisense.test_information',
                'medisense.laboratory_summary',
            ])->pluck('id')->toArray(),

            'rad-tech' => Permission::whereIn('slug', [
                'view-radiology-requests', 'schedule-radiology-requests',
                'upload-radiology-scans',
                'medisense.access',
                'medisense.imaging_workflow',
            ])->pluck('id')->toArray(),

            'radiologist' => Permission::whereIn('slug', [
                'view-radiology-requests',
                'create-radiology-reports', 'approve-radiology-reports', 'release-radiology-reports',
                'medisense.access',
                'medisense.imaging_summary',
                'medisense.radiology_assistance',
                'medisense.radiology_findings',
            ])->pluck('id')->toArray(),

            'pharmacist' => Permission::whereIn('slug', [
                'view-prescriptions', 'verify-prescriptions', 'dispense-medications',
                'medisense.access',
                'medisense.medication_information',
                'medisense.prescription_assistance',
                'medisense.medication_review',
            ])->pluck('id')->toArray(),

            'dietitian' => Permission::whereIn('slug', [
                'view-diet-requests', 'manage-diet-plans', 'manage-meal-schedules',
                'medisense.access',
                'medisense.nutrition_assessment',
                'medisense.diet_plan_assistance',
            ])->pluck('id')->toArray(),

            'or-coordinator' => Permission::whereIn('slug', [
                'view-surgery-requests', 'schedule-surgeries',
                'manage-operating-rooms', 'manage-surgical-teams',
                'medisense.access',
                'medisense.surgery_workflow',
                'medisense.scheduling_assistance',
            ])->pluck('id')->toArray(),
        ];

        foreach ($rolePermissions as $slug => $permIds) {
            $role = Role::where('slug', $slug)->first();
            if ($role) {
                $role->permissions()->syncWithoutDetaching($permIds);
            }
        }
    }
}
