<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Owner Haland',
                'email' => 'owner@halandpetcare.com',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'owner')->first()->id,
                'status' => 'active',
            ],
            [
                'name' => 'Dr. Ahmad',
                'email' => 'dokter@halandpetcare.com',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'dokter')->first()->id,
                'status' => 'active',
            ],
            [
                'name' => 'Kasir Budi',
                'email' => 'kasir@halandpetcare.com',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'kasir')->first()->id,
                'status' => 'active',
            ],
            [
                'name' => 'Admin Sari',
                'email' => 'admin@halandpetcare.com',
                'phone' => '081234567893',
                'password' => Hash::make('password'),
                'role_id' => Role::where('name', 'admin')->first()->id,
                'status' => 'active',
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
