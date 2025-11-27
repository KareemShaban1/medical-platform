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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->tinyInteger('payment_method')->default(0)->comment('0 -> cod, 1 -> online')->after('auto_renew');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('payment_method');
            $table->string('payment_gateway')->nullable()->after('payment_status');
            $table->string('transaction_id')->nullable()->after('payment_gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'payment_gateway', 'transaction_id']);
        });
    }
};
