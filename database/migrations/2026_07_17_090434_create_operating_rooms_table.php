<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Operating rooms available for surgeries.
     */
    public function up(): void
    {
        Schema::create('operating_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();        // e.g., OR-1, OR-2
            $table->string('location')->nullable();  // e.g., 3rd Floor
            $table->enum('status', ['Available', 'Occupied', 'Under Maintenance'])->default('Available');
            $table->text('equipment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operating_rooms');
    }
};
