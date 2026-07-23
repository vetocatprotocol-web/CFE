<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\NumberingService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected NumberingService $numberingService,
    ) {}

    public function index(Request $request): View
    {
        $query = Payment::with(['receiver', 'payable']);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(15);

        return view('kasir.payments.index', compact('payments'));
    }

    public function process(ProcessPaymentRequest $request): RedirectResponse
    {
        $payableClass = match ($request->payable_type) {
            'visit' => Invoice::class,
            'billing' => Invoice::class,
            'pos_order' => \App\Models\PosOrder::class,
            default => Invoice::class,
        };

        $payable = $payableClass::findOrFail($request->payable_id);

        if ($payable instanceof Invoice && $payable->status === 'PAID') {
            return back()->with('error', 'This invoice is already fully paid.');
        }

        $payment = new Payment([
            'payment_number' => $this->numberingService->generatePaymentNumber(),
            'payable_type' => get_class($payable),
            'payable_id' => $payable->id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'status' => 'PENDING',
            'received_by' => auth()->id(),
        ]);

        $payment->save();

        $processedPayment = $this->paymentService->processPayment($payment);

        return redirect()->route('kasir.payments.show', $processedPayment)
            ->with('success', 'Payment processed successfully.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['receiver', 'payable']);

        return view('kasir.payments.show', compact('payment'));
    }

    public function receipt(Payment $payment): View
    {
        $payment->load(['receiver', 'payable']);

        return view('kasir.payments.receipt', compact('payment'));
    }
}
