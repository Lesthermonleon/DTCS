<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Lab results encoded by medical technologists. Tracks validation and release.
     */
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_request_item_id')
                  ->constrained('lab_request_items')
                  ->cascadeOnDelete();
            $table->foreignId('technologist_id')->nullable()->constrained('users');
            $table->string('result_value')->nullable(); // Can be numeric or text
            $table->text('remarks')->nullable();
            $table->enum('status', ['Pending', 'Encoded', 'Validated', 'Released'])->default('Pending');
            $table->foreignId('validated_by')->nullable()->constrained('users');
            $table->foreignId('released_by')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
