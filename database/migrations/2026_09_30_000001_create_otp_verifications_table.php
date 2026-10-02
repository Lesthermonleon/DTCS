<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the otp_verifications table for Email OTP Two-Factor Authentication.
     * This table stores hashed OTP records — plaintext OTPs are NEVER stored.
     *
     * One active (non-verified) record per user per purpose at a time.
     * Previous records are deleted when a new OTP is generated.
     */
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->string('purpose', 50)->default('login')
                  ->comment('Intended use of this OTP (e.g. login, password_reset)');
            $table->string('otp_hash')
                  ->comment('Bcrypt hash of the plaintext OTP — never store the OTP itself');
            $table->tinyInteger('attempts')->unsigned()->default(0)
                  ->comment('Number of failed verification attempts against this OTP');
            $table->timestamp('last_sent_at')->nullable()
                  ->comment('Timestamp of last OTP generation/resend for this verification record');
            $table->timestamp('expires_at')
                  ->comment('Absolute expiry of this OTP — expired OTPs must never be accepted');
            $table->timestamp('verified_at')->nullable()
                  ->comment('Set when OTP is successfully verified — prevents replay');
            $table->timestamps();

            // Index for fast user+purpose lookups during verification
            $table->index(['user_id', 'purpose'], 'idx_otp_user_purpose');
            // Index for cleanup of expired records
            $table->index('expires_at', 'idx_otp_expires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
