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
        Schema::create('payslip_items', function (Blueprint $table) {
            $table->id();
            // id, payslip_id, type enum[fixed|hours|percentage|bonus|deduction], label, amount
            $table->foreignId('payslip_id')->constrained('payslips')->onDelete('cascade');
            $table->enum('type', ['fixed', 'hours', 'percentage', 'bonus', 'deduction']);
            $table->string('notes');
            $table->decimal('amount', 10, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslip_items');
    }
};
