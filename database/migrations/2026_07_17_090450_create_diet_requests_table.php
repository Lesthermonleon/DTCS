<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Diet requests from doctors for patients with special nutritional needs.
     */
    public function up(): void
    {
        Schema::create('diet_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique(); // e.g., DR-2026-0001
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users');
            $table->string('diet_type');          // e.g., Diabetic, Low-Sodium, Renal
            $table->text('allergies')->nullable();
            $table->text('food_restrictions')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->enum('status', ['Pending', 'Active', 'Completed', 'Cancelled'])->default('Pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_requests');
    }
};
