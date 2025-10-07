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
        Schema::create('clinic_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_inventory_id')->constrained('clinic_inventories')->cascadeOnDelete();
            $table->integer('quantity');
            $table->enum('type', ['in', 'out']);
            $table->dateTime('movement_date');
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
        Schema::dropIfExists('clinic_inventories_movements');
    }
};