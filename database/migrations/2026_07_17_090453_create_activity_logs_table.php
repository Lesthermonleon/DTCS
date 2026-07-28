<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Activity logs for tracking user actions across all modules.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');      // e.g., "Created", "Updated", "Deleted", "Released"
            $table->string('module');      // e.g., "LIS", "RIS", "PMS"
            $table->text('description')->nullable();
            $table->string('loggable_type')->nullable();  // Polymorphic model class
            $table->unsignedBigInteger('loggable_id')->nullable(); // Polymorphic model id
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();

            $table->index(['loggable_type', 'loggable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
