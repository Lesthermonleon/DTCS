<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add `dashboard_route` to the roles table.
     *
     * This column decouples the post-login redirect from the application code.
     * When a new role is created, simply populate this column — no code changes needed.
     */
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('dashboard_route', 100)->nullable()->after('slug')
                  ->comment('Named route to redirect after login, e.g. admin.dashboard');
        });

        // Seed existing roles with their dashboard routes
        $routes = [
            'admin'          => 'admin.dashboard',
            'doctor'         => 'doctor.dashboard',
            'med-tech'       => 'lab.dashboard',
            'rad-tech'       => 'radiology.dashboard',
            'radiologist'    => 'radiology.dashboard',
            'pharmacist'     => 'pharmacy.dashboard',
            'dietitian'      => 'diet.dashboard',
            'or-coordinator' => 'surgery.dashboard',
        ];

        foreach ($routes as $slug => $route) {
            DB::table('roles')->where('slug', $slug)->update(['dashboard_route' => $route]);
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('dashboard_route');
        });
    }
};
