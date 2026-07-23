<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NumberingService
{
    /**
     * Generate a visit number: VIS-YYYY-MMDD-XXXXX
     */
    public function generateVisitNumber(): string
    {
        return $this->generate('VIS');
    }

    /**
     * Generate a billing number: BIL-YYYY-MMDD-XXXXX
     */
    public function generateBillingNumber(): string
    {
        return $this->generate('BIL');
    }

    /**
     * Generate an invoice number: INV-YYYY-MMDD-XXXXX
     */
    public function generateInvoiceNumber(): string
    {
        return $this->generate('INV');
    }

    /**
     * Generate a payment number: PAY-YYYY-MMDD-XXXXX
     */
    public function generatePaymentNumber(): string
    {
        return $this->generate('PAY');
    }

    /**
     * Generate a receipt number: RCP-YYYY-MMDD-XXXXX
     */
    public function generateReceiptNumber(): string
    {
        return $this->generate('RCP');
    }

    /**
     * Generate a prescription number: RX-YYYY-MMDD-XXXXX
     */
    public function generatePrescriptionNumber(): string
    {
        return $this->generate('RX');
    }

    /**
     * Generate a numbered sequence string.
     */
    protected function generate(string $prefix): string
    {
        $date = now()->format('Y-m-d');
        $cacheKey = "numbering_{$prefix}_{$date}";
        $sequence = $this->getNextSequence($prefix, $date);

        return sprintf('%s-%s-%05d', $prefix, $date, $sequence);
    }

    /**
     * Get the next sequence number for a prefix and date combination.
     */
    protected function getNextSequence(string $prefix, string $date): int
    {
        $lockKey = "numbering_lock_{$prefix}_{$date}";

        return DB::transaction(function () use ($prefix, $date, $lockKey) {
            $setting = Setting::where('key', $lockKey)->first();

            if (! $setting) {
                Setting::create([
                    'key' => $lockKey,
                    'value' => '1',
                ]);

                return 1;
            }

            $next = (int) $setting->value + 1;
            $setting->update(['value' => (string) $next]);

            return $next;
        });
    }
}
