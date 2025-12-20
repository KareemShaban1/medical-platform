<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_payout_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_code_id')->constrained('affiliate_codes')->cascadeOnDelete();
            $table->string('payout_method');
            $table->text('payout_details');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('affiliate_code_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payout_profiles');
    }
};
