<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Radiology imaging requests ordered by doctors.
     */
    public function up(): void
    {
        Schema::create('radiology_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique(); // e.g., RR-2026-0001
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users');
            $table->string('modality');          // e.g., X-Ray, CT Scan, MRI, Ultrasound
            $table->string('body_part');         // e.g., Chest, Abdomen, Brain
            $table->text('clinical_information')->nullable();
            $table->enum('priority', ['Routine', 'Urgent', 'STAT'])->default('Routine');
            $table->enum('status', ['Pending', 'Scheduled', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_requests');
    }
};
