<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVisitRequest;
use App\Models\Customer;
use App\Models\Drug;
use App\Models\Service;
use App\Models\Visit;
use App\Models\VisitItem;
use App\Services\InvoiceService;
use App\Services\NumberingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VisitController extends Controller
{
    public function __construct(
        protected NumberingService $numberingService,
        protected InvoiceService $invoiceService,
    ) {}

    public function index(Request $request): View
    {
        $query = Visit::with(['customer', 'pet', 'creator'])
            ->where('created_by', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('visit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('visit_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhere('visit_number', 'like', "%{$search}%");
            });
        }

        $visits = $query->latest('visit_date')->paginate(15);

        return view('dokter.visits.index', compact('visits'));
    }

    public function create(): View
    {
        $customers = Customer::with('pets')->where('status', 'active')->orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('name')->get();
        $drugs = Drug::where('status', 'active')->orderBy('name')->get();

        return view('dokter.visits.create', compact('customers', 'services', 'drugs'));
    }

    public function store(StoreVisitRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $visit = Visit::create([
                'visit_number' => $this->numberingService->generateVisitNumber(),
                'customer_id' => $request->customer_id,
                'pet_id' => $request->pet_id,
                'visit_date' => now()->toDateString(),
                'visit_time' => now()->format('H:i:s'),
                'chief_complaint' => $request->chief_complaint,
                'diagnosis' => $request->diagnosis,
                'treatment_notes' => $request->treatment_notes,
                'weight_kg' => $request->weight_kg,
                'temperature' => $request->temperature,
                'heart_rate' => $request->heart_rate,
                'status' => 'DRAFT',
                'created_by' => auth()->id(),
            ]);

            if ($request->filled('items')) {
                foreach ($request->items as $item) {
                    $unitPrice = 0;
                    $itemName = $item['item_name'] ?? '';

                    if ($item['item_type'] === 'service' && !empty($item['service_id'])) {
                        $service = Service::findOrFail($item['service_id']);
                        $unitPrice = $service->price;
                    } elseif ($item['item_type'] === 'drug' && !empty($item['drug_id'])) {
                        $drug = Drug::findOrFail($item['drug_id']);
                        $unitPrice = $drug->price_per_unit;
                    }

                    $quantity = $item['quantity'] ?? 1;
                    $subtotal = $quantity * $unitPrice;

                    VisitItem::create([
                        'visit_id' => $visit->id,
                        'item_type' => $item['item_type'],
                        'service_id' => $item['service_id'] ?? null,
                        'drug_id' => $item['drug_id'] ?? null,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'notes' => $item['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('dokter.visits.show', $visit)
                ->with('success', 'Visit created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create visit: ' . $e->getMessage());
        }
    }

    public function show(Visit $visit): View
    {
        $visit->load(['customer', 'pet', 'items.service', 'items.drug', 'creator', 'invoice', 'prescriptions']);

        return view('dokter.visits.show', compact('visit'));
    }

    public function edit(Visit $visit): View
    {
        if ($visit->status !== 'DRAFT') {
            return back()->with('error', 'Only draft visits can be edited.');
        }

        $visit->load(['items.service', 'items.drug']);
        $customers = Customer::with('pets')->where('status', 'active')->orderBy('name')->get();
        $services = Service::where('status', 'active')->orderBy('name')->get();
        $drugs = Drug::where('status', 'active')->orderBy('name')->get();

        return view('dokter.visits.edit', compact('visit', 'customers', 'services', 'drugs'));
    }

    public function update(StoreVisitRequest $request, Visit $visit): RedirectResponse
    {
        if ($visit->status !== 'DRAFT') {
            return back()->with('error', 'Only draft visits can be updated.');
        }

        DB::beginTransaction();

        try {
            $visit->update([
                'customer_id' => $request->customer_id,
                'pet_id' => $request->pet_id,
                'chief_complaint' => $request->chief_complaint,
                'diagnosis' => $request->diagnosis,
                'treatment_notes' => $request->treatment_notes,
                'weight_kg' => $request->weight_kg,
                'temperature' => $request->temperature,
                'heart_rate' => $request->heart_rate,
            ]);

            if ($request->filled('items')) {
                $visit->items()->delete();

                foreach ($request->items as $item) {
                    $unitPrice = 0;

                    if ($item['item_type'] === 'service' && !empty($item['service_id'])) {
                        $service = Service::findOrFail($item['service_id']);
                        $unitPrice = $service->price;
                    } elseif ($item['item_type'] === 'drug' && !empty($item['drug_id'])) {
                        $drug = Drug::findOrFail($item['drug_id']);
                        $unitPrice = $drug->price_per_unit;
                    }

                    $quantity = $item['quantity'] ?? 1;
                    $subtotal = $quantity * $unitPrice;

                    VisitItem::create([
                        'visit_id' => $visit->id,
                        'item_type' => $item['item_type'],
                        'service_id' => $item['service_id'] ?? null,
                        'drug_id' => $item['drug_id'] ?? null,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'notes' => $item['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('dokter.visits.show', $visit)
                ->with('success', 'Visit updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update visit: ' . $e->getMessage());
        }
    }

    public function complete(Visit $visit): RedirectResponse
    {
        if ($visit->status !== 'DRAFT') {
            return back()->with('error', 'Only draft visits can be completed.');
        }

        if ($visit->items()->count() === 0) {
            return back()->with('error', 'Cannot complete a visit without items.');
        }

        DB::beginTransaction();

        try {
            $visit->update(['status' => 'COMPLETED']);

            $this->invoiceService->generateFromVisit($visit);

            DB::commit();

            return redirect()->route('dokter.visits.show', $visit)
                ->with('success', 'Visit completed and invoice generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete visit: ' . $e->getMessage());
        }
    }

    public function addItem(Request $request, Visit $visit): RedirectResponse
    {
        if ($visit->status !== 'DRAFT') {
            return back()->with('error', 'Cannot add items to a non-draft visit.');
        }

        $validated = $request->validate([
            'item_type' => ['required', 'string', 'in:service,drug'],
            'service_id' => ['required_if:item_type,service', 'nullable', 'exists:services,id'],
            'drug_id' => ['required_if:item_type,drug', 'nullable', 'exists:drugs,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $unitPrice = 0;

        if ($validated['item_type'] === 'service') {
            $service = Service::findOrFail($validated['service_id']);
            $unitPrice = $service->price;
        } else {
            $drug = Drug::findOrFail($validated['drug_id']);
            $unitPrice = $drug->price_per_unit;
        }

        $quantity = $validated['quantity'];
        $subtotal = $quantity * $unitPrice;

        VisitItem::create([
            'visit_id' => $visit->id,
            'item_type' => $validated['item_type'],
            'service_id' => $validated['service_id'] ?? null,
            'drug_id' => $validated['drug_id'] ?? null,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Item added successfully.');
    }

    public function removeItem(Visit $visit, VisitItem $item): RedirectResponse
    {
        if ($visit->status !== 'DRAFT') {
            return back()->with('error', 'Cannot remove items from a non-draft visit.');
        }

        if ($item->visit_id !== $visit->id) {
            return back()->with('error', 'Item does not belong to this visit.');
        }

        $item->delete();

        return back()->with('success', 'Item removed successfully.');
    }

    public function searchCustomer(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
        ]);

        $search = $request->q;

        $customers = Customer::with('pets')
            ->where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $customers->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone,
                'pets' => $c->pets->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'species' => $p->species,
                    'breed' => $p->breed,
                ]),
            ]),
        ]);
    }
}
