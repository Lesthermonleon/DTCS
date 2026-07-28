<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Surgery requests submitted by doctors.
     */
    public function up(): void
    {
        Schema::create('surgery_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique(); // e.g., SR-2026-0001
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users');
            $table->string('procedure_name');           // e.g., Appendectomy
            $table->string('diagnosis')->nullable();
            $table->enum('urgency', ['Elective', 'Urgent', 'Emergency'])->default('Elective');
            $table->enum('status', ['Pending', 'Scheduled', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->text('notes')->nullable();
            $table->string('anesthesia_type')->nullable(); // e.g., General, Spinal
            $table->integer('estimated_duration')->nullable(); // in minutes
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_requests');
    }
};
