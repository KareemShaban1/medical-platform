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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_profile_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('period_id')->constrained('daily_periods')->onDelete('cascade');
            $table->integer('slot_number')->nullable(); // Queue position
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'expired', 'waiting', 'completed'])->default('pending');
            $table->string('confirmation_code', 10)->unique()->nullable();
            $table->timestamp('confirmation_code_expires_at')->nullable();
            $table->timestamp('booked_at')->nullable();
            $table->text('patient_notes')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_profile_id', 'status']);
            $table->index(['patient_id', 'status']);
            $table->index(['period_id', 'status']);
            $table->index('confirmation_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

