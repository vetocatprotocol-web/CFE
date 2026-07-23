<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['customer', 'pet', 'source', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('invoice_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        $invoices = $query->latest('invoice_date')->paginate(15);

        return view('shared.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['customer', 'pet', 'items', 'payments.receiver']);

        return view('shared.invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $invoice->load(['customer', 'pet', 'items']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('shared.invoices.pdf', compact('invoice'));

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function email(Invoice $invoice): RedirectResponse
    {
        $invoice->load(['customer', 'pet', 'items']);

        if (empty($invoice->customer->email)) {
            return back()->with('error', 'Customer does not have an email address.');
        }

        try {
            \Mail::to($invoice->customer->email)->send(
                new \App\Mail\InvoiceMail($invoice)
            );

            return back()->with('success', 'Invoice has been sent to ' . $invoice->customer->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
