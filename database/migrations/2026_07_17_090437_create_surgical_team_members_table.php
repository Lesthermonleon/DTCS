<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Members of a surgical team with their roles.
     */
    public function up(): void
    {
        Schema::create('surgical_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgical_team_id')
                  ->constrained('surgical_teams')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('role_in_team'); // e.g., Anesthesiologist, Scrub Nurse, Assistant Surgeon
            $table->timestamps();

            $table->unique(['surgical_team_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgical_team_members');
    }
};
