<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add login-security columns for token auth and progressive lockout.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('login_token')->nullable()->after('remember_token');
            $table->unsignedTinyInteger('failed_attempts')->default(0)->after('login_token');
            $table->timestamp('locked_at')->nullable()->after('failed_attempts');
            $table->timestamp('lockout_until')->nullable()->after('locked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['login_token', 'failed_attempts', 'locked_at', 'lockout_until']);
        });
    }
};
