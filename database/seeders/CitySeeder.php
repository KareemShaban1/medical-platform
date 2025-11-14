<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Governorate;
use App\Models\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing governorates
        $governorates = Governorate::all();

        if ($governorates->isEmpty()) {
            $this->command->warn('No governorates found. Please run GovernorateSeeder first.');
            return;
        }

        // Map governorate names to their cities
        $governorateCities = [
            'القاهرة' => [
                'وسط البلد',
                'المعادي',
                'مدينة نصر',
                'الزمالك',
                'مصر الجديدة',
                'المنيل',
                'شبرا',
                'العباسية',
                'الزيتون',
                'حدائق القبة',
            ],
            'الاسكندرية' => [
                'سيدي بشر',
                'سموحة',
                'المنتزه',
                'العجمي',
                'المنشية',
                'الرمل',
                'كليوباترا',
                'سان ستيفانو',
                'المندرة',
                'برج العرب',
            ],
            'الجيزة' => [
                'الدقي',
                'المهندسين',
                'المنيل',
                'العجوزة',
                'الزمالك',
                'أكتوبر',
                'الشيخ زايد',
                '6 أكتوبر',
                'الهرم',
                'فيصل',
            ],
            'المنصورة' => [
                'المنصورة',
                'طلخا',
                'ميت غمر',
                'بلقاس',
                'أجا',
                'السنبلاوين',
                'شربين',
                'المنزلة',
                'ميت سلسيل',
                'نبروه',
            ],
            'السويس' => [
                'السويس',
                'الأربعين',
                'الجناين',
                'عتاقة',
                'فيصل',
                'المنشية',
                'العباسية',
            ],
            'اسيوط' => [
                'أسيوط',
                'أبوتيج',
                'أبنوب',
                'ديروط',
                'البداري',
                'ساحل سليم',
                'الغنايم',
                'صدفا',
                'منفلوط',
                'القوصية',
            ],
            'بني سويف' => [
                'بني سويف',
                'الواسطي',
                'ناصر',
                'إهناسيا',
                'ببا',
                'الفشن',
                'سمسطا',
                'الفيوم',
                'طامية',
            ],
            'الاسماعيلية' => [
                'الإسماعيلية',
                'القنطرة',
                'التل الكبير',
                'أبو صوير',
                'القصاصين',
                'فايد',
            ],
            'بورسعيد' => [
                'بورسعيد',
                'بورفؤاد',
                'الضاحية',
                'المناخ',
                'الشرق',
                'الغرب',
            ],
            'القليوبية' => [
                'بنها',
                'قليوب',
                'شبرا الخيمة',
                'الخانكة',
                'كفر شكر',
                'طوخ',
                'قها',
                'العبور',
                'الخصوص',
            ],
            'البحيرة' => [
                'دمنهور',
                'كفر الدوار',
                'رشيد',
                'إدكو',
                'أبو المطامير',
                'الدلنجات',
                'كوم حمادة',
                'حوش عيسى',
                'شبراخيت',
            ],
            'الفيوم' => [
                'الفيوم',
                'طامية',
                'سنورس',
                'إطسا',
                'يوسف الصديق',
                'أبشواي',
            ],
            'الغربية' => [
                'طنطا',
                'المحلة الكبرى',
                'زفتى',
                'كفر الزيات',
                'بسيون',
                'قطور',
                'سمنود',
                'شبرا بخوم',
            ],
            'الدقهلية' => [
                'المنصورة',
                'طلخا',
                'ميت غمر',
                'بلقاس',
                'أجا',
                'السنبلاوين',
                'شربين',
                'المنزلة',
                'ميت سلسيل',
                'نبروه',
            ],
            'البحر الاحمر' => [
                'الغردقة',
                'رأس غارب',
                'سفاجا',
                'القصير',
                'مرسى علم',
                'شلاتين',
            ],
            'الشرقية' => [
                'الزقازيق',
                'بلبيس',
                'أبو كبير',
                'فاقوس',
                'منيا القمح',
                'الحسينية',
                'ههيا',
                'أبو حماد',
                'كفر صقر',
            ],
            'المنوفية' => [
                'شبين الكوم',
                'منوف',
                'أشمون',
                'الباجور',
                'قويسنا',
                'بركة السبع',
                'تلا',
                'الشهداء',
            ],
            'الأقصر' => [
                'الأقصر',
                'إسنا',
                'الطود',
                'البياضية',
                'أرمنت',
                'الزينية',
            ],
        ];

        $createdCount = 0;

        foreach ($governorates as $governorate) {
            $cities = $governorateCities[$governorate->name] ?? [];

            // If no predefined cities, create a default city with the governorate name
            if (empty($cities)) {
                $cities = [$governorate->name];
            }

            foreach ($cities as $cityName) {
                // Check if city already exists for this governorate
                $existingCity = City::where('name', $cityName)
                    ->where('governorate_id', $governorate->id)
                    ->first();

                if (!$existingCity) {
                    City::create([
                        'name' => $cityName,
                        'governorate_id' => $governorate->id,
                    ]);
                    $createdCount++;
                }
            }
        }

        $this->command->info("Created {$createdCount} cities for existing governorates.");
    }
}