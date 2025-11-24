<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->enum('plan_type', ['doctor', 'clinic', 'supplier']);
            $table->enum('level', ['free', 'basic', 'advanced', 'vip']);
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration_in_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['plan_type', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

