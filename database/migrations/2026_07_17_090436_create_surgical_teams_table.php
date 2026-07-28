<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Surgical teams (lead surgeon + members).
     */
    public function up(): void
    {
        Schema::create('surgical_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');                   // e.g., General Surgery Team A
            $table->foreignId('surgeon_id')->constrained('users'); // Lead surgeon
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgical_teams');
    }
};
