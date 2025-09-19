<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $categories = [
        [
            'name_ar' => 'Category 1',
            'name_en' => 'Category 1',
            'slug_ar' => 'category-1',
            'slug_en' => 'category-1',
            'admin_id' => 1,
            'status' => 1,
        ],
        [
            'name_ar' => 'Category 2',
            'name_en' => 'Category 2',
            'slug_ar' => 'category-2',
            'slug_en' => 'category-2',
            'admin_id' => 1,
            'status' => 1,
        ],
        [
            'name_ar' => 'Category 3',
            'name_en' => 'Category 3',
            'slug_ar' => 'category-3',
            'slug_en' => 'category-3',
            'admin_id' => 1,
            'status' => 1,
        ],
    ];
        Category::insert($categories);
    }
}
