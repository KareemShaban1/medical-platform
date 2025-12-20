<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'affiliate_code_id')) {
                $table->foreignId('affiliate_code_id')
                    ->nullable()
                    ->constrained('affiliate_codes')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('subscriptions', 'discount_percent')) {
                $table->decimal('discount_percent', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'discount_amount')) {
                $table->decimal('discount_amount', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'commission_percent')) {
                $table->decimal('commission_percent', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'commission_amount')) {
                $table->decimal('commission_amount', 12, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'affiliate_code_id')) {
                $table->dropConstrainedForeignId('affiliate_code_id');
            }
            if (Schema::hasColumn('subscriptions', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
            if (Schema::hasColumn('subscriptions', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('subscriptions', 'commission_percent')) {
                $table->dropColumn('commission_percent');
            }
            if (Schema::hasColumn('subscriptions', 'commission_amount')) {
                $table->dropColumn('commission_amount');
            }
        });
    }
};
