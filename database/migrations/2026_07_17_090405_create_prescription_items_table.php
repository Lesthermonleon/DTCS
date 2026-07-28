<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Individual medication line items within a prescription.
     */
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')
                  ->constrained('prescriptions')
                  ->cascadeOnDelete();
            $table->string('medication_name');       // e.g., Amoxicillin 500mg
            $table->string('dosage');                // e.g., 500mg
            $table->string('route')->nullable();     // e.g., Oral, IV, IM
            $table->string('frequency');             // e.g., TID, OD, BID
            $table->string('duration');              // e.g., 7 days
            $table->integer('quantity');
            $table->text('instructions')->nullable();
            $table->enum('status', ['Pending', 'Dispensed'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
