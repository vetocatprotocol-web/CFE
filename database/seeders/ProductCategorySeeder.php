<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Anjing', 'description' => 'Makanan untuk anjing', 'status' => 'active'],
            ['name' => 'Makanan Kucing', 'description' => 'Makanan untuk kucing', 'status' => 'active'],
            ['name' => 'Vitamin & Suplemen', 'description' => 'Vitamin dan suplemen untuk hewan', 'status' => 'active'],
            ['name' => 'Aksesoris', 'description' => 'Aksesoris hewan peliharaan', 'status' => 'active'],
            ['name' => 'Obat Luar', 'description' => 'Obat-obatan untuk penggunaan luar', 'status' => 'active'],
            ['name' => 'Mainan', 'description' => 'Mainan untuk hewan peliharaan', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
