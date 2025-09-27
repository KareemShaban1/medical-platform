<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Product::factory()->count(100)->create();

        foreach (Product::all() as $product) {
            $product->categories()->attach(Category::inRandomOrder()->first()->id);
            $product->approvement()->create([
                'module_type' => Product::class,
                'module_id' => $product->id,
                'action_by' => 1,
                'action' => 'approved',
                'notes' => 'Approved by admin',
            ]);
        }
    }
}
