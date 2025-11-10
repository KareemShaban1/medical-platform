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
        Schema::table('clinic_users', function (Blueprint $table) {
            // Drop the existing foreign key constraint first
            $table->dropForeign(['clinic_id']);

            // Make clinic_id nullable
            $table->foreignId('clinic_id')->nullable()->change();

            // Re-add the foreign key constraint with nullable
            $table->foreign('clinic_id')->references('id')->on('clinics')->onDelete('cascade');

            // Add has_clinic column after clinic_id
            $table->boolean('has_clinic')->default(true)->after('clinic_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_users', function (Blueprint $table) {
            // Revert has_clinic column
            $table->dropColumn('has_clinic');

            // Revert clinic_id to not nullable (but we need to handle existing nulls first)
            // Note: This might fail if there are null values, so handle carefully
            $table->foreignId('clinic_id')->nullable(false)->change();
        });
    }
};
