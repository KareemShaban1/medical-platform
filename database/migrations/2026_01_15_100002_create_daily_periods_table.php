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
        Schema::create('daily_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_profile_id')->constrained('doctor_profiles')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_open')->default(true);
            $table->integer('capacity')->default(10); // Max appointments per period
            $table->integer('booked_count')->default(0); // Current bookings
            $table->boolean('auto_queue')->default(false); // Allow queue when full
            $table->timestamps();

            $table->index(['doctor_profile_id', 'date']);
            $table->index(['date', 'is_open']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_periods');
    }
};

