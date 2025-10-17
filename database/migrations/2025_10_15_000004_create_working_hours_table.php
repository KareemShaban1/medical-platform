<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_user_id')->constrained('clinic_users')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sun .. 6=Sat
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_recurring')->default(true);
            $table->timestamps();

            $table->unique(['clinic_user_id','day_of_week','start_time','end_time'], 'wh_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};

