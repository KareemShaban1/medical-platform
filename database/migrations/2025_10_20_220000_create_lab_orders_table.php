<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('clinic_user_id')->constrained('clinic_users')->onDelete('cascade');
            $table->foreignId('doctor_profile_id')->nullable()->constrained('doctor_profiles')->onDelete('set null');

            $table->string('test_name');
            $table->string('lab_name')->nullable();
            $table->enum('status', ['pending', 'received', 'completed'])->default('pending');
            $table->decimal('cost_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();

            $table->text('result_comment')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_orders');
    }
};
