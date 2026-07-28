<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Records each dispensing event by a pharmacist for a prescription item.
     */
    public function up(): void
    {
        Schema::create('dispensing_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_item_id')
                  ->constrained('prescription_items')
                  ->cascadeOnDelete();
            $table->foreignId('pharmacist_id')->constrained('users');
            $table->integer('quantity_dispensed');
            $table->string('lot_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('dispensed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensing_records');
    }
};
