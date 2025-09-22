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
        Schema::create('rental_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_space_id')->constrained('rental_spaces')->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_pricings');
    }
};
