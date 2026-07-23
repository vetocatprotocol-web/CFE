<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Pet;
use Illuminate\Database\Seeder;

class SampleCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customer1 = Customer::firstOrCreate(
            ['phone' => '081234567001'],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@email.com',
                'address' => 'Jl. Mawar No. 5',
                'status' => 'active',
            ]
        );

        Pet::firstOrCreate(
            ['name' => 'Doggo', 'customer_id' => $customer1->id],
            [
                'species' => 'Anjing',
                'breed' => 'Golden Retriever',
                'birth_date' => now()->subYears(5),
                'weight_kg' => 25.00,
                'status' => 'active',
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['phone' => '081234567002'],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti@email.com',
                'address' => 'Jl. Melati No. 10',
                'status' => 'active',
            ]
        );

        Pet::firstOrCreate(
            ['name' => 'Kitty', 'customer_id' => $customer2->id],
            [
                'species' => 'Kucing',
                'breed' => 'Persia',
                'birth_date' => now()->subYears(3),
                'weight_kg' => 4.50,
                'status' => 'active',
            ]
        );

        $customer3 = Customer::firstOrCreate(
            ['phone' => '081234567003'],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi@email.com',
                'address' => 'Jl. Kenanga No. 7',
                'status' => 'active',
            ]
        );

        Pet::firstOrCreate(
            ['name' => 'Max', 'customer_id' => $customer3->id],
            [
                'species' => 'Anjing',
                'breed' => 'Labrador',
                'birth_date' => now()->subYears(2),
                'weight_kg' => 30.00,
                'status' => 'active',
            ]
        );

        Pet::firstOrCreate(
            ['name' => 'Luna', 'customer_id' => $customer3->id],
            [
                'species' => 'Kucing',
                'breed' => 'Siamese',
                'birth_date' => now()->subYear(),
                'weight_kg' => 3.00,
                'status' => 'active',
            ]
        );
    }
}
