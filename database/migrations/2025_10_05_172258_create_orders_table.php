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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_user_id')->constrained('clinic_users')->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->string('number')->unique();

            $table->enum('status', ['pending', 'processing', 'delivering', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('total', 10, 2)->default(0);
            $table->float('shipping')->default(0);
            $table->float('tax')->default(0);
            $table->float('discount')->default(0);

            $table->tinyInteger('payment_method')->default(0)->comment('0 -> cod , 1 -> online');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
