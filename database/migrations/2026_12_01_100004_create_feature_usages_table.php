<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('feature_id')->nullable()->constrained('features_master')->nullOnDelete();
            $table->string('feature_code');
            $table->integer('used_count')->default(0);
            $table->integer('limit_count')->nullable();
            $table->timestamp('last_reset_at')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id', 'feature_code']);
            $table->index('feature_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_usages');
    }
};

