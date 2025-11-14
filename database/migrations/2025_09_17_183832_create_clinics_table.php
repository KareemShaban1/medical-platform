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
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->boolean('is_allowed')->default(true);
            $table->boolean('status')->default(true);
            $table->foreignId('governorate_id')->nullable()->constrained('governorates')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained('areas')->cascadeOnDelete();
            $table->string('clinic_email')->nullable()->unique();
            $table->string('clinic_website')->nullable()->unique();
            $table->text('about')->nullable();
            $table->json('services_offered')->nullable();
            $table->json('working_hours')->nullable();
            $table->boolean('has_emergency')->default(false);
            $table->float('patient_rating')->default(0);
            $table->integer('rating_reviews_count')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
