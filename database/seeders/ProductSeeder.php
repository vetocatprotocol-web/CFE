<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Royal Canin Medium',
                'category' => 'Makanan Anjing',
                'price' => 250000,
                'description' => 'Makanan anjing ras menengah',
                'current_stock' => 50,
                'reorder_point' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Pro Plan Adult',
                'category' => 'Makanan Anjing',
                'price' => 180000,
                'description' => 'Makanan anjing dewasa',
                'current_stock' => 30,
                'reorder_point' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Whiskas Tuna',
                'category' => 'Makanan Kucing',
                'price' => 35000,
                'description' => 'Makanan kucing rasa tuna',
                'current_stock' => 100,
                'reorder_point' => 20,
                'status' => 'active',
            ],
            [
                'name' => 'Royal Canin Indoor',
                'category' => 'Makanan Kucing',
                'price' => 200000,
                'description' => 'Makanan kucing indoor',
                'current_stock' => 40,
                'reorder_point' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Nutri Plus Gel',
                'category' => 'Vitamin & Suplemen',
                'price' => 85000,
                'description' => 'Gel vitamin untuk hewan',
                'current_stock' => 25,
                'reorder_point' => 5,
                'status' => 'active',
            ],
            [
                'name' => 'Kalsium Tablet',
                'category' => 'Vitamin & Suplemen',
                'price' => 45000,
                'description' => 'Tablet kalsium untuk hewan',
                'current_stock' => 60,
                'reorder_point' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Collar Adjustable',
                'category' => 'Aksesoris',
                'price' => 35000,
                'description' => 'Kalung adjustable untuk hewan',
                'current_stock' => 40,
                'reorder_point' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Leash Nylon',
                'category' => 'Aksesoris',
                'price' => 55000,
                'description' => 'Tali hewan dari nylon',
                'current_stock' => 30,
                'reorder_point' => 10,
                'status' => 'active',
            ],
            [
                'name' => 'Shampoo Anti Kutu',
                'category' => 'Obat Luar',
                'price' => 42000,
                'description' => 'Shampoo anti kutu untuk hewan',
                'current_stock' => 35,
                'reorder_point' => 8,
                'status' => 'active',
            ],
            [
                'name' => 'Ball Rope',
                'category' => 'Mainan',
                'price' => 25000,
                'description' => 'Mainan bola tali untuk hewan',
                'current_stock' => 50,
                'reorder_point' => 10,
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            $category = ProductCategory::where('name', $product['category'])->first();
            if ($category) {
                Product::firstOrCreate(
                    ['name' => $product['name']],
                    [
                        'category_id' => $category->id,
                        'price' => $product['price'],
                        'description' => $product['description'],
                        'current_stock' => $product['current_stock'],
                        'reorder_point' => $product['reorder_point'],
                        'status' => $product['status'],
                    ]
                );
            }
        }
    }
}
