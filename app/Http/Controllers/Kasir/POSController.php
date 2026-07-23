<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Services\NumberingService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class POSController extends Controller
{
    public function __construct(
        protected NumberingService $numberingService,
        protected StockService $stockService,
    ) {}

    public function index(): View
    {
        $products = Product::with('category')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('kasir.pos.index', compact('products', 'categories'));
    }

    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
        ]);

        $order = PosOrder::create([
            'order_number' => $this->numberingService->generateReceiptNumber(),
            'customer_id' => $validated['customer_id'] ?? null,
            'subtotal' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => 0,
            'status' => 'DRAFT',
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'order_id' => $order->id,
        ]);
    }

    public function addItem(Request $request, PosOrder $order): JsonResponse
    {
        if ($order->status !== 'DRAFT') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add items to a non-pending order.',
            ], 422);
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->current_stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient stock for {$product->name}. Available: {$product->current_stock}.",
            ], 422);
        }

        $quantity = $validated['quantity'];
        $unitPrice = $product->price;
        $subtotal = $quantity * $unitPrice;

        $existingItem = PosOrderItem::where('pos_order_id', $order->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $quantity;

            if ($product->current_stock < $newQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$product->name}. Available: {$product->current_stock}, requested total: {$newQty}.",
                ], 422);
            }

            $existingItem->update([
                'quantity' => $newQty,
                'subtotal' => $newQty * $unitPrice,
            ]);
        } else {
            PosOrderItem::create([
                'pos_order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);
        }

        return $this->refreshOrder($order);
    }

    public function removeItem(PosOrder $order, PosOrderItem $item): JsonResponse
    {
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot remove items from a non-pending order.',
            ], 422);
        }

        if ($item->pos_order_id !== $order->id) {
            return response()->json([
                'success' => false,
                'message' => 'Item does not belong to this order.',
            ], 422);
        }

        $item->delete();

        return $this->refreshOrder($order);
    }

    public function checkout(Request $request, PosOrder $order): JsonResponse
    {
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order is not in pending status.',
            ], 422);
        }

        if ($order->items()->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot checkout an empty order.',
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:cash,card,qris,transfer'],
            'payment_amount' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order->load('items.product');

        $subtotal = $order->items->sum('subtotal');
        $discountAmount = $validated['discount_amount'] ?? 0;
        $total = $subtotal - $discountAmount;

        if ($validated['payment_amount'] < $total) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount is less than the total.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $changeAmount = $validated['payment_amount'] - $total;

            $order->update([
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_amount' => $validated['payment_amount'],
                'change_amount' => $changeAmount,
                'status' => 'COMPLETED',
            ]);

            foreach ($order->items as $item) {
                $this->stockService->deductStock(
                    $item->product,
                    $item->quantity,
                    $order->order_number,
                    auth()->user(),
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout completed successfully.',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'total' => $total,
                    'payment_method' => $validated['payment_method'],
                    'payment_amount' => $validated['payment_amount'],
                    'change_amount' => $changeAmount,
                    'status' => 'COMPLETED',
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function receipt(PosOrder $order): View
    {
        $order->load(['items.product', 'customer', 'creator']);

        return view('kasir.pos.receipt', compact('order'));
    }

    protected function refreshOrder(PosOrder $order): JsonResponse
    {
        $order->load('items.product');

        $subtotal = $order->items->sum('subtotal');

        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ]);

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => $order->status,
            ],
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
            ]),
        ]);
    }
}
