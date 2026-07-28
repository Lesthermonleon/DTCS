<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Radiology diagnostic reports created and approved by radiologists.
     */
    public function up(): void
    {
        Schema::create('radiology_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radiology_request_id')
                  ->unique()            // one report per request
                  ->constrained('radiology_requests')
                  ->cascadeOnDelete();
            $table->foreignId('radiologist_id')->constrained('users');
            $table->text('findings');
            $table->text('impression');
            $table->text('recommendations')->nullable();
            $table->enum('status', ['Draft', 'Approved', 'Released'])->default('Draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('released_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_reports');
    }
};
