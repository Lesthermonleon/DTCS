<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Therapeutic diet plans created by dietitians for patient diet requests.
     */
    public function up(): void
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_request_id')
                  ->unique()
                  ->constrained('diet_requests')
                  ->cascadeOnDelete();
            $table->foreignId('dietitian_id')->constrained('users');
            $table->text('plan_details');          // Full nutritional plan description
            $table->integer('total_calories')->nullable();
            $table->decimal('protein_grams', 8, 2)->nullable();
            $table->decimal('carb_grams', 8, 2)->nullable();
            $table->decimal('fat_grams', 8, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['Active', 'Completed', 'Revised'])->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};
