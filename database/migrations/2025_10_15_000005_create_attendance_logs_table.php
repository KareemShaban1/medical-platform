<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_user_id')->constrained('clinic_users')->cascadeOnDelete();
            $table->enum('check_type', ['check_in', 'check_out', 'absence_request']);
            $table->enum('source', ['mobile', 'web', 'admin'])->default('web');
            $table->foreignId('requested_by')->nullable()->constrained('clinic_users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('clinic_users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_user_id', 'at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};

