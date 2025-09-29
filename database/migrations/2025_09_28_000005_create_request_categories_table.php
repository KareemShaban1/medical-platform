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
        Schema::create('request_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('supplier_specialized_category_id')->constrained('supplier_specialized_categories')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['request_id', 'supplier_specialized_category_id'], 'request_category_unique');
            $table->index(['request_id']);
            $table->index(['supplier_specialized_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_categories');
    }
};
