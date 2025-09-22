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
        Schema::create('rental_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_space_id')->constrained('rental_spaces')->cascadeOnDelete();
            // if type is daily need [from_time, to_time]
            $table->enum('type', ['daily', 'weekly', 'monthly']);
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            // if type is weekly need [from_day, to_day] and [start_date, end_date] (optional)
            $table->enum('from_day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->enum('to_day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            // if type is monthly need [start_date, end_date] and [from_time, to_time] (optional)
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_availabilities');
    }
};
