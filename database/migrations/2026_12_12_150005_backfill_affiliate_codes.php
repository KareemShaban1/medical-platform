<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $existingCodes = DB::table('affiliate_codes')->pluck('code')->toArray();

        $clinicUsers = DB::table('clinic_users')->select('id', 'name')->get();
        foreach ($clinicUsers as $user) {
            $hasCode = DB::table('affiliate_codes')
                ->where('affiliateable_type', 'App\\Models\\ClinicUser')
                ->where('affiliateable_id', $user->id)
                ->exists();
            if ($hasCode) {
                continue;
            }

            $base = strtoupper(Str::slug($user->name ?? 'CLINIC', ''));
            if ($base === '') {
                $base = 'CLINIC';
            }
            $base = substr($base, 0, 8);
            $code = $base . '-' . strtoupper(Str::random(4));
            while (in_array($code, $existingCodes, true)) {
                $code = $base . '-' . strtoupper(Str::random(4));
            }
            $existingCodes[] = $code;

            DB::table('affiliate_codes')->insert([
                'affiliateable_type' => 'App\\Models\\ClinicUser',
                'affiliateable_id' => $user->id,
                'code' => $code,
                'balance' => 0,
                'total_earned' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('affiliate_codes')
            ->where('affiliateable_type', 'App\\Models\\ClinicUser')
            ->delete();
    }
};
