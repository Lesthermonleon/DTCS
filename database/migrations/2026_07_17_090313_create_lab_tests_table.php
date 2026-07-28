<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Individual lab tests with reference ranges and units.
     */
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_test_category_id')
                  ->constrained('lab_test_categories')
                  ->cascadeOnDelete();
            $table->string('name');             // e.g., Complete Blood Count
            $table->string('code', 20)->unique(); // e.g., CBC
            $table->string('normal_range')->nullable(); // e.g., 4.5-11.0
            $table->string('unit', 30)->nullable();     // e.g., x10³/µL
            $table->string('method')->nullable();       // e.g., Automated
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
