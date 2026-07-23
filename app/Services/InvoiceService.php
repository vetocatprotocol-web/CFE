<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Setting;
use App\Models\Visit;

class InvoiceService
{
    protected NumberingService $numberingService;

    public function __construct(NumberingService $numberingService)
    {
        $this->numberingService = $numberingService;
    }

    /**
     * Generate an invoice from a visit.
     */
    public function generateFromVisit(Visit $visit): Invoice
    {
        $subtotal = 0.0;

        $invoice = Invoice::create([
            'invoice_number' => $this->numberingService->generateInvoiceNumber(),
            'customer_id' => $visit->customer_id,
            'pet_id' => $visit->pet_id,
            'source_type' => Visit::class,
            'source_id' => $visit->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => 0,
            'paid_amount' => 0,
            'status' => 'UNPAID',
        ]);

        foreach ($visit->items as $item) {
            $itemName = $item->service?->name ?? $item->drug?->name ?? 'Unknown Item';
            $itemCategory = strtoupper($item->item_type);
            $itemSubtotal = $item->quantity * $item->unit_price;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_name' => $itemName,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $itemSubtotal,
                'category' => $itemCategory,
            ]);

            $subtotal += $itemSubtotal;
        }

        $taxAmount = $this->calculateTax($subtotal);
        $total = $subtotal + $taxAmount;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);

        return $invoice->fresh();
    }

    /**
     * Generate an invoice from a billing.
     */
    public function generateFromBilling(Billing $billing): Invoice
    {
        $subtotal = 0.0;

        $invoice = Invoice::create([
            'invoice_number' => $this->numberingService->generateInvoiceNumber(),
            'customer_id' => $billing->customer_id,
            'pet_id' => $billing->pet_id,
            'source_type' => Billing::class,
            'source_id' => $billing->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'subtotal' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => 0,
            'paid_amount' => 0,
            'status' => 'UNPAID',
        ]);

        foreach ($billing->items as $item) {
            $itemType = strtoupper($item->item_type);
            $itemName = match ($itemType) {
                'SERVICE' => $item->service?->name ?? 'Unknown Service',
                'DRUG' => $item->drug?->name ?? 'Unknown Drug',
                'PRODUCT' => $item->product?->name ?? 'Unknown Product',
                default => 'Unknown Item',
            };

            $itemSubtotal = $item->quantity * $item->unit_price;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_name' => $itemName,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $itemSubtotal,
                'category' => $itemType,
            ]);

            $subtotal += $itemSubtotal;
        }

        $taxAmount = $this->calculateTax($subtotal);
        $total = $subtotal + $taxAmount;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ]);

        return $invoice->fresh();
    }

    /**
     * Calculate tax based on the configured tax rate.
     */
    public function calculateTax(float $subtotal): float
    {
        $taxRateSetting = Setting::where('key', 'tax_value')->first();
        $taxRate = $taxRateSetting ? (float) $taxRateSetting->value : 0;

        return round($subtotal * ($taxRate / 100), 2);
    }

    /**
     * Generate a unique invoice number.
     */
    public function generateInvoiceNumber(): string
    {
        return $this->numberingService->generateInvoiceNumber();
    }
}
