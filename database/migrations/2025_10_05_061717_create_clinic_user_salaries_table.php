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
        Schema::create('clinic_user_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_user_id')->constrained('clinic_users')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('salary_frequency', ['daily', 'weekly', 'monthly']);
            $table->decimal('amount_per_salary_frequency', 10, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('paid')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_user_salaries');
    }
};