<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores imaging files uploaded by radiologic technologists.
     */
    public function up(): void
    {
        Schema::create('radiology_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radiology_request_id')
                  ->constrained('radiology_requests')
                  ->cascadeOnDelete();
            $table->string('file_path');                    // Relative path in storage
            $table->string('file_name');
            $table->string('file_type', 20)->nullable();    // e.g., DICOM, JPEG, PNG
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_images');
    }
};
