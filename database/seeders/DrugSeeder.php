<?php

namespace Database\Seeders;

use App\Models\Drug;
use Illuminate\Database\Seeder;

class DrugSeeder extends Seeder
{
    public function run(): void
    {
        $drugs = [
            [
                'name' => 'Amoxicillin 500mg',
                'description' => 'Antibiotik untuk infeksi bakteri',
                'unit' => 'tablet',
                'price_per_unit' => 5000,
                'status' => 'active',
            ],
            [
                'name' => 'Paracetamol 500mg',
                'description' => 'Obat penurun demam dan pereda nyeri',
                'unit' => 'tablet',
                'price_per_unit' => 2000,
                'status' => 'active',
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'description' => 'Obat anti inflamasi non steroid',
                'unit' => 'tablet',
                'price_per_unit' => 3000,
                'status' => 'active',
            ],
            [
                'name' => 'Betadine 60ml',
                'description' => 'Antiseptik untuk luka luar',
                'unit' => 'botol',
                'price_per_unit' => 15000,
                'status' => 'active',
            ],
            [
                'name' => 'Vitamin B Complex',
                'description' => 'Suplemen vitamin B untuk hewan',
                'unit' => 'botol',
                'price_per_unit' => 8000,
                'status' => 'active',
            ],
            [
                'name' => 'Rabies Vaccine',
                'description' => 'Vaksin rabies untuk hewan peliharaan',
                'unit' => 'vial',
                'price_per_unit' => 75000,
                'status' => 'active',
            ],
            [
                'name' => 'F4 Vaccine',
                'description' => 'Vaksin F4 untuk kucing',
                'unit' => 'vial',
                'price_per_unit' => 90000,
                'status' => 'active',
            ],
            [
                'name' => 'Antihistamine',
                'description' => 'Obat anti alergi',
                'unit' => 'tablet',
                'price_per_unit' => 4000,
                'status' => 'active',
            ],
            [
                'name' => 'Anti Muntah',
                'description' => 'Obat anti muntah untuk hewan',
                'unit' => 'tablet',
                'price_per_unit' => 6000,
                'status' => 'active',
            ],
            [
                'name' => 'Ointment Skin',
                'description' => 'Salep untuk masalah kulit hewan',
                'unit' => 'tube',
                'price_per_unit' => 12000,
                'status' => 'active',
            ],
        ];

        foreach ($drugs as $drug) {
            Drug::firstOrCreate(
                ['name' => $drug['name']],
                $drug
            );
        }
    }
}
