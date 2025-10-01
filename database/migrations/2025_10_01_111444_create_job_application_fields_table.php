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
        Schema::create('job_application_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('clinic_jobs')->onDelete('cascade');
            $table->string('field_name');
            $table->string('field_label');
            $table->enum('field_type', ['text', 'email', 'phone', 'file', 'textarea']);
            $table->boolean('required')->default(false);
            $table->json('options')->nullable();
            $table->integer('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_application_fields');
    }
};