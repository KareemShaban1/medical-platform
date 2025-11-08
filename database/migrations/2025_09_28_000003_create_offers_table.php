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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            // shipping
            $table->float('shipping')->default(0);
            $table->date('delivery_time');
            $table->text('terms');
            $table->decimal('discount', 8, 2)->nullable();
            $table->decimal('tax', 8, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            // add payment information
            $table->tinyInteger('payment_method')->default(0)->comment('0 -> cod , 1 -> online');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_gateway')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();

            $table->index(['request_id', 'status']);
            $table->index(['supplier_id', 'status']);
            $table->unique(['request_id', 'supplier_id']); // Prevent duplicate offers from same supplier
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
