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
        $tableNames = config('permission.table_names');
        
        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('group')->nullable()->after('guard_name');
            $table->index('group');
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');
        
        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropIndex(['group']);
            $table->dropColumn('group');
        });
    }
};
