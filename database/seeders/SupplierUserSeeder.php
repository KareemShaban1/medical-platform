<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SupplierUser;
use Illuminate\Support\Facades\Hash;


class SupplierUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        SupplierUser::create([
            'supplier_id' => 1,
            'name' => 'Supplier User 1',
            'email' => 'user@supplier1.com',
            'password' => Hash::make('password'),
            'status' => true,
        ]);
    }
}