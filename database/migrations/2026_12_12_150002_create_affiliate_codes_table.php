<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_codes', function (Blueprint $table) {
            $table->id();
            $table->morphs('affiliateable');
            $table->string('code')->unique();
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_codes');
    }
};
