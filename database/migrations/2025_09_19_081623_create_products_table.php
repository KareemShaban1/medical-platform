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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');

            $table->string('name_en', 255);
            $table->string('name_ar', 255);
            $table->string('slug_en', 255)->unique();
            $table->string('slug_ar', 255)->unique();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();

            $table->string('sku', 100)->unique();
            $table->decimal('price_before', 10, 2);
            $table->decimal('price_after', 10, 2);
            $table->decimal('discount_value', 10, 2);
            $table->integer('stock');

            // $table->boolean('approved')->default(false);
            $table->string('reason')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 = inactive, 1 = active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
