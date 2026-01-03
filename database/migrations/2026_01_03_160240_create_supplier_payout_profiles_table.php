<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payout_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('payout_method'); // bank_transfer, instapay, vodafone_cash, etc.
            $table->text('payout_details'); // JSON or text with account details
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payout_profiles');
    }
};
