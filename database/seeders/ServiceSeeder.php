<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Konsultasi Umum',
                'description' => 'Konsultasi umum dengan dokter hewan',
                'category' => 'Konsultasi',
                'price' => 100000,
                'status' => 'active',
            ],
            [
                'name' => 'Vaksin Rabies',
                'description' => 'Vaksinasi rabies untuk hewan peliharaan',
                'category' => 'Vaksin',
                'price' => 150000,
                'status' => 'active',
            ],
            [
                'name' => 'Vaksin F4',
                'description' => 'Vaksinasi F4 untuk kucing',
                'category' => 'Vaksin',
                'price' => 200000,
                'status' => 'active',
            ],
            [
                'name' => 'Sterilisasi/Jamin',
                'description' => 'Prosedur sterilisasi hewan',
                'category' => 'Operasi',
                'price' => 500000,
                'status' => 'active',
            ],
            [
                'name' => 'Grooming Basic',
                'description' => 'Perawatan dasar hewan peliharaan',
                'category' => 'Grooming',
                'price' => 80000,
                'status' => 'active',
            ],
            [
                'name' => 'Grooming Premium',
                'description' => 'Perawatan premium hewan peliharaan',
                'category' => 'Grooming',
                'price' => 150000,
                'status' => 'active',
            ],
            [
                'name' => 'Cacingan Treatment',
                'description' => 'Pengobatan cacingan pada hewan',
                'category' => 'Perawatan',
                'price' => 120000,
                'status' => 'active',
            ],
            [
                'name' => 'Surgery Minor',
                'description' => 'Operasi minor pada hewan',
                'category' => 'Operasi',
                'price' => 300000,
                'status' => 'active',
            ],
            [
                'name' => 'Lab Darah',
                'description' => 'Pemeriksaan laboratorium darah',
                'category' => 'Lab',
                'price' => 180000,
                'status' => 'active',
            ],
            [
                'name' => 'Rontgen',
                'description' => 'Pemeriksaan rontgen hewan',
                'category' => 'Lab',
                'price' => 250000,
                'status' => 'active',
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
