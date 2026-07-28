<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Scheduled surgery events linking requests, rooms, and teams.
     */
    public function up(): void
    {
        Schema::create('surgery_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_request_id')
                  ->constrained('surgery_requests')
                  ->cascadeOnDelete();
            $table->foreignId('operating_room_id')->constrained('operating_rooms');
            $table->foreignId('surgical_team_id')->constrained('surgical_teams');
            $table->foreignId('scheduled_by')->constrained('users'); // OR Coordinator
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['Scheduled', 'In Progress', 'Completed', 'Cancelled', 'Postponed'])->default('Scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_schedules');
    }
};
