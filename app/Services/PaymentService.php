<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PosOrder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected NumberingService $numberingService;

    public function __construct(NumberingService $numberingService)
    {
        $this->numberingService = $numberingService;
    }

    /**
     * Process a payment and update related records.
     */
    public function processPayment(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment) {
            $payment->update([
                'payment_number' => $this->generatePaymentNumber(),
                'status' => 'PAID',
            ]);

            $payable = $payment->payable;

            if ($payable) {
                $newPaidAmount = $payable->paid_amount + $payment->amount;

                if ($payable instanceof Invoice) {
                    $newStatus = $newPaidAmount >= $payable->total ? 'PAID' : 'PARTIAL';
                    $payable->update([
                        'paid_amount' => $newPaidAmount,
                        'status' => $newStatus,
                    ]);
                } elseif ($payable instanceof Billing) {
                    $payable->update(['status' => 'PAID']);
                } elseif ($payable instanceof PosOrder) {
                    $payable->update([
                        'payment_method' => $payment->payment_method,
                        'payment_amount' => $payment->amount,
                        'change_amount' => max(0, $payment->amount - $payable->total),
                        'status' => 'COMPLETED',
                    ]);
                }
            }

            return $payment->fresh();
        });
    }

    /**
     * Generate a unique payment number.
     */
    public function generatePaymentNumber(): string
    {
        return $this->numberingService->generatePaymentNumber();
    }

    /**
     * Handle a partial payment for an invoice.
     */
    public function handlePartialPayment(Invoice $invoice, float $amount): Payment
    {
        $payment = new Payment([
            'payment_number' => $this->generatePaymentNumber(),
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->id,
            'payment_method' => 'cash',
            'amount' => $amount,
            'status' => 'PENDING',
            'received_by' => auth()->id(),
        ]);

        $payment->save();

        return $this->processPayment($payment);
    }
}
