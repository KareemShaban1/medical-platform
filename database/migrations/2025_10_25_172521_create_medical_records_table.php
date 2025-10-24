<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->unsignedBigInteger('appointment_id')->unique();
            $table->unsignedBigInteger('doctor_profile_id');
            $table->unsignedBigInteger('patient_id');
            $table->tinyInteger('visit_type')->default(0)->comment('0: Initial, 1: Follow-up, 2: Consultation');
            $table->text('chief_complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_shared_with_patient')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('clinic_id')->references('id')->on('clinics')->cascadeOnDelete();
            $table->foreign('appointment_id')->references('id')->on('appointments')->cascadeOnDelete();
            $table->foreign('doctor_profile_id')->references('id')->on('doctor_profiles')->cascadeOnDelete();
            $table->foreign('patient_id')->references('id')->on('patients')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
