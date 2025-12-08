<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            if (!Schema::hasColumn('clinics', 'slug')) {
                $table->string('slug')->unique()->nullable();
            }
        });
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'slug')) {
                $table->string('slug')->unique()->nullable();
            }
        });
        Schema::table('doctor_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_profiles', 'slug')) {
                $table->string('slug')->unique()->nullable();
            }
        });

        $this->backfillSlugs('clinics', 'name', function ($row, $nameColumn) {
            return  Str::slug($row->{$nameColumn} ?? 'clinic');
        });
        $this->backfillSlugs('suppliers', 'name', function ($row, $nameColumn) {
            return Str::slug($row->{$nameColumn} ?? 'supplier');
        });
        $this->backfillSlugs('doctor_profiles', 'name', function ($row, $nameColumn) {
            return Str::slug($row->{$nameColumn} ?? 'doctor');
        });

    }

    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            if (Schema::hasColumn('clinics', 'slug')) {
                $table->dropColumn('slug');
            }
        });
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'slug')) {
                $table->dropColumn('slug');
            }
        });
        Schema::table('doctor_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('doctor_profiles', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }

    private function backfillSlugs(string $table, string $nameColumn, callable $slugCallback): void
    {
        $rows = DB::table($table)->select('id', $nameColumn, 'slug')->get();
        foreach ($rows as $row) {
            if (!empty($row->slug)) {
                continue;
            }
            $baseSlug = $slugCallback($row, $nameColumn) ?: ('item-' . $row->id);
            $slug = $baseSlug;
            $suffix = 1;
            while (DB::table($table)->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }
            DB::table($table)->where('id', $row->id)->update(['slug' => $slug]);
        }
    }
};
