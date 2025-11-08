<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Governorate;

class GovernorateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // egypt governorates
        $governorates = [
            'القاهرة',
            'الاسكندرية',
            'الجيزة',
            'المنصورة',
            'السويس',
            'اسيوط',
            'بني سويف',
            'الاسماعيلية',
            'بورسعيد',
            'القليوبية',
            'البحيرة',
            'الفيوم',
            'الغربية',
            'الدقهلية',
            'البحر الاحمر',
            'الشرقية',
            'الغربية',
            'المنوفية',
            'الأقصر',
            'الأسماعيلية',
            'السويس'
        ];
        foreach ($governorates as $governorate) {
            Governorate::create([
                'name' => $governorate,
            ]);
        }
    }
}