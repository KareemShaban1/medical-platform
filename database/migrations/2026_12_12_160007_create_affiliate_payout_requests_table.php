<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_code_id')->constrained('affiliate_codes')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('payout_method');
            $table->text('payout_details');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('paid_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['affiliate_code_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payout_requests');
    }
};
