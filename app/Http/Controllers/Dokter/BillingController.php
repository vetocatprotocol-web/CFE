<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\Customer;
use App\Models\Drug;
use App\Models\Product;
use App\Models\Service;
use App\Services\InvoiceService;
use App\Services\NumberingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(
        protected NumberingService $numberingService,
        protected InvoiceService $invoiceService,
    ) {}

    public function index(Request $request): View
    {
        $query = Billing::with(['customer', 'pet', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('billing_number', 'like', "%{$search}%");
            });
        }

        $billings = $query->latest('billing_start_date')->paginate(15);

        return view('dokter.billings.index', compact('billings'));
    }

    public function create(): View
    {
        $customers = Customer::with('pets')->where('status', 'active')->orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('name')->get();
        $drugs = Drug::where('status', 'active')->orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();

        return view('dokter.billings.create', compact('customers', 'services', 'drugs', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'pet_id' => ['required', 'exists:pets,id'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            $billing = Billing::create([
                'billing_number' => $this->numberingService->generateBillingNumber(),
                'customer_id' => $validated['customer_id'],
                'pet_id' => $validated['pet_id'],
                'billing_start_date' => now()->toDateString(),
                'status' => 'OPEN',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('dokter.billings.show', $billing)
                ->with('success', 'Billing created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create billing: ' . $e->getMessage());
        }
    }

    public function show(Billing $billing): View
    {
        $billing->load(['customer', 'pet', 'items.service', 'items.drug', 'items.product', 'creator', 'invoice']);

        $services = Service::where('status', 'active')->orderBy('name')->get();
        $drugs = Drug::where('status', 'active')->orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('name')->get();

        return view('dokter.billings.show', compact('billing', 'services', 'drugs', 'products'));
    }

    public function addItem(Request $request, Billing $billing): RedirectResponse
    {
        if ($billing->status !== 'OPEN') {
            return back()->with('error', 'Cannot add items to a non-open billing.');
        }

        $validated = $request->validate([
            'item_type' => ['required', 'string', 'in:service,drug,product'],
            'service_id' => ['required_if:item_type,service', 'nullable', 'exists:services,id'],
            'drug_id' => ['required_if:item_type,drug', 'nullable', 'exists:drugs,id'],
            'product_id' => ['required_if:item_type,product', 'nullable', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $unitPrice = 0;

        if ($validated['item_type'] === 'service') {
            $service = Service::findOrFail($validated['service_id']);
            $unitPrice = $service->price;
        } elseif ($validated['item_type'] === 'drug') {
            $drug = Drug::findOrFail($validated['drug_id']);
            $unitPrice = $drug->price_per_unit;
        } elseif ($validated['item_type'] === 'product') {
            $product = Product::findOrFail($validated['product_id']);
            $unitPrice = $product->price;
        }

        $quantity = $validated['quantity'];
        $subtotal = $quantity * $unitPrice;

        BillingItem::create([
            'billing_id' => $billing->id,
            'item_type' => $validated['item_type'],
            'service_id' => $validated['service_id'] ?? null,
            'drug_id' => $validated['drug_id'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Item added to billing successfully.');
    }

    public function removeItem(Billing $billing, BillingItem $item): RedirectResponse
    {
        if ($billing->status !== 'OPEN') {
            return back()->with('error', 'Cannot remove items from a non-open billing.');
        }

        if ($item->billing_id !== $billing->id) {
            return back()->with('error', 'Item does not belong to this billing.');
        }

        $item->delete();

        return back()->with('success', 'Item removed successfully.');
    }

    public function complete(Billing $billing): RedirectResponse
    {
        if ($billing->status !== 'OPEN') {
            return back()->with('error', 'Only open billings can be completed.');
        }

        if ($billing->items()->count() === 0) {
            return back()->with('error', 'Cannot complete a billing without items.');
        }

        DB::beginTransaction();

        try {
            $billing->update([
                'status' => 'COMPLETED',
                'billing_end_date' => now()->toDateString(),
            ]);

            $this->invoiceService->generateFromBilling($billing);

            DB::commit();

            return redirect()->route('dokter.billings.show', $billing)
                ->with('success', 'Billing completed and invoice generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete billing: ' . $e->getMessage());
        }
    }
}
