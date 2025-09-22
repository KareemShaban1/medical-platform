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
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_user_id')->constrained('clinic_users')->onDelete('cascade');
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->text('research_links')->nullable(); // JSON field for multiple links
            $table->integer('years_experience')->nullable();
            $table->text('specialties')->nullable(); // JSON field for multiple specialties
            $table->text('services_offered')->nullable(); // JSON field for multiple services
            $table->text('education')->nullable(); // JSON field for education details
            $table->text('experience')->nullable(); // JSON field for experience details
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained(table: 'admins')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();

            // is featured
            $table->boolean('is_featured')->default(false);
            $table->foreignId('featured_by')->nullable()->constrained(table: 'admins')->onDelete('set null');

            $table->timestamps();
            $table->index(['clinic_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
