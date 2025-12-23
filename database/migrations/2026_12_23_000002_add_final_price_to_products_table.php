<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('final_price', 10, 2)->nullable()->after('price_after');
        });

        $fixedFee = (float) config('payment_gateways.paymob.fee_fixed', 3);
        $percentFee = (float) config('payment_gateways.paymob.fee_percent', 5);

        DB::statement(sprintf(
            'UPDATE products SET final_price = price_after + %F + (price_after * %F / 100) WHERE final_price IS NULL',
            $fixedFee,
            $percentFee
        ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('final_price');
        });
    }
};
