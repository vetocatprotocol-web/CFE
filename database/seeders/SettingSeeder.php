<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'Haland PetCare'],
            ['key' => 'company_address', 'value' => 'Jl. Contoh No. 123, Jakarta Selatan'],
            ['key' => 'company_phone', 'value' => '021-12345678'],
            ['key' => 'company_email', 'value' => 'info@halandpetcare.com'],
            ['key' => 'tax_type', 'value' => 'percentage'],
            ['key' => 'tax_value', 'value' => '10'],
            ['key' => 'invoice_prefix', 'value' => 'INV-'],
            ['key' => 'receipt_prefix', 'value' => 'RCP-'],
            ['key' => 'visit_prefix', 'value' => 'VIS-'],
            ['key' => 'billing_prefix', 'value' => 'BIL-'],
            ['key' => 'payment_prefix', 'value' => 'PAY-'],
            ['key' => 'prescription_prefix', 'value' => 'RX-'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
