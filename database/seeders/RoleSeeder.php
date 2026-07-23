<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'owner', 'description' => 'Owner of the pet care business with full access'],
            ['name' => 'dokter', 'description' => 'Veterinarian who manages visits and consultations'],
            ['name' => 'kasir', 'description' => 'Cashier who handles payments and POS orders'],
            ['name' => 'admin', 'description' => 'Admin staff who assists with stock and master data'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
