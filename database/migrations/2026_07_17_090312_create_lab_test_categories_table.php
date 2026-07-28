<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Lab test categories (e.g. Hematology, Clinical Chemistry, Microbiology).
     */
    public function up(): void
    {
        Schema::create('lab_test_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();      // e.g., Hematology
            $table->string('code', 10)->unique();  // e.g., HEM
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test_categories');
    }
};
