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
        Schema::create('supplier_category_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('supplier_specialized_category_id')->constrained('supplier_specialized_categories')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['supplier_id', 'supplier_specialized_category_id'], 'supplier_category_unique');
            $table->index(['supplier_id']);
            $table->index(['supplier_specialized_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_specialized_category_pivot');
    }
};
