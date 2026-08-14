<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medisense_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_role', 50);
            $table->string('capability', 100);
            $table->string('module', 50)->nullable();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
            $table->text('user_prompt');
            $table->mediumText('ai_response')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->enum('status', ['success', 'error', 'timeout', 'unauthorized'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('capability');
            $table->index('module');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medisense_interactions');
    }
};
