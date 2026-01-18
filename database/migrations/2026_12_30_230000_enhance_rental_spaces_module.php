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
        // Enhance rental_spaces table
        Schema::table('rental_spaces', function (Blueprint $table) {
            $table->enum('listing_type', ['rent', 'sale'])->default('rent')->after('status');
            $table->decimal('sale_price', 10, 2)->nullable()->after('listing_type');
            $table->json('amenities')->nullable()->after('sale_price');
            $table->integer('capacity')->nullable()->after('amenities');
            $table->decimal('area_sqm', 8, 2)->nullable()->after('capacity');
        });

        // Enhance rental_pricings table
        Schema::table('rental_pricings', function (Blueprint $table) {
            $table->enum('pricing_type', ['hourly', 'daily', 'weekly', 'monthly'])->default('daily')->after('rental_space_id');
        });

        // Create rental_schedules table for recurring availability
        Schema::create('rental_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_space_id')->constrained('rental_spaces')->cascadeOnDelete();
            $table->enum('day_of_week', ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Index for faster queries
            $table->index(['rental_space_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_schedules');

        Schema::table('rental_pricings', function (Blueprint $table) {
            $table->dropColumn('pricing_type');
        });

        Schema::table('rental_spaces', function (Blueprint $table) {
            $table->dropColumn(['listing_type', 'sale_price', 'amenities', 'capacity', 'area_sqm']);
        });
    }
};
