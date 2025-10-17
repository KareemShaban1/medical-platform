<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Speciality;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name_en' => 'Anesthesiology', 'name_ar' => 'التخدير'],
            ['name_en' => 'Cardiology', 'name_ar' => 'أمراض القلب'],
            ['name_en' => 'Dermatology', 'name_ar' => 'الأمراض الجلدية'],
            ['name_en' => 'Emergency Medicine', 'name_ar' => 'طب الطوارئ'],
            ['name_en' => 'Endocrinology', 'name_ar' => 'الغدد الصماء'],
            ['name_en' => 'Family Medicine', 'name_ar' => 'طب الأسرة'],
            ['name_en' => 'Gastroenterology', 'name_ar' => 'أمراض الجهاز الهضمي'],
            ['name_en' => 'General Surgery', 'name_ar' => 'الجراحة العامة'],
            ['name_en' => 'Geriatrics', 'name_ar' => 'طب الشيخوخة'],
            ['name_en' => 'Hematology', 'name_ar' => 'أمراض الدم'],
            ['name_en' => 'Infectious Disease', 'name_ar' => 'الأمراض المعدية'],
            ['name_en' => 'Internal Medicine', 'name_ar' => 'الطب الباطني'],
            ['name_en' => 'Nephrology', 'name_ar' => 'أمراض الكلى'],
            ['name_en' => 'Neurology', 'name_ar' => 'طب الأعصاب'],
            ['name_en' => 'Neurosurgery', 'name_ar' => 'جراحة الأعصاب'],
            ['name_en' => 'Obstetrics and Gynecology', 'name_ar' => 'التوليد وأمراض النساء'],
            ['name_en' => 'Oncology', 'name_ar' => 'الأورام'],
            ['name_en' => 'Ophthalmology', 'name_ar' => 'طب العيون'],
            ['name_en' => 'Orthopedic Surgery', 'name_ar' => 'جراحة العظام'],
            ['name_en' => 'Otolaryngology (ENT)', 'name_ar' => 'الأنف والأذن والحنجرة'],
            ['name_en' => 'Pathology', 'name_ar' => 'الأمراض'],
            ['name_en' => 'Pediatrics', 'name_ar' => 'طب الأطفال'],
            ['name_en' => 'Physical Medicine and Rehabilitation', 'name_ar' => 'الطب الطبيعي وإعادة التأهيل'],
            ['name_en' => 'Plastic Surgery', 'name_ar' => 'الجراحة التجميلية'],
            ['name_en' => 'Psychiatry', 'name_ar' => 'الطب النفسي'],
            ['name_en' => 'Pulmonology', 'name_ar' => 'أمراض الرئة'],
            ['name_en' => 'Radiology', 'name_ar' => 'الأشعة'],
            ['name_en' => 'Rheumatology', 'name_ar' => 'أمراض الروماتيزم'],
            ['name_en' => 'Urology', 'name_ar' => 'جراحة المسالك البولية'],
            ['name_en' => 'Dentistry', 'name_ar' => 'طب الأسنان'],
            ['name_en' => 'Nutrition', 'name_ar' => 'التغذية'],
            ['name_en' => 'Physiotherapy', 'name_ar' => 'العلاج الطبيعي'],
        ];

        foreach ($specialties as $spec) {
            Speciality::firstOrCreate([
                'name_en' => $spec['name_en'],
            ], [
                'name_ar' => $spec['name_ar'],
            ]);
        }
    }
}

