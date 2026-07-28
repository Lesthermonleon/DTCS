<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Individual meal schedules within a diet plan.
     */
    public function up(): void
    {
        Schema::create('meal_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diet_plan_id')
                  ->constrained('diet_plans')
                  ->cascadeOnDelete();
            $table->enum('meal_type', ['Breakfast', 'Mid-Morning Snack', 'Lunch', 'Afternoon Snack', 'Dinner', 'Bedtime Snack']);
            $table->date('meal_date');
            $table->text('menu');             // Description of the meal
            $table->integer('calories')->nullable();
            $table->boolean('is_served')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_schedules');
    }
};
