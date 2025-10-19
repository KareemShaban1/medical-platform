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
        Schema::table('clinic_inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('clinic_inventories', 'min_quantity')) {
                $table->integer('min_quantity')->default(0)->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinic_inventories', function (Blueprint $table) {
            if (Schema::hasColumn('clinic_inventories', 'min_quantity')) {
                $table->dropColumn('min_quantity');
            }
        });
    }
};

