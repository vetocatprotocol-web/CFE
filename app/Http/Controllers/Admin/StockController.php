<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(
        private StockService $stockService,
    ) {}

    public function index(Request $request): View
    {
        $query = Product::query()->where('status', 'ACTIVE')->with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('stock_status')) {
            if ($request->input('stock_status') === 'low') {
                $query->whereColumn('current_stock', '<', 'reorder_point');
            } elseif ($request->input('stock_status') === 'normal') {
                $query->whereColumn('current_stock', '>=', 'reorder_point');
            }
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.stock.index', compact('products'));
    }

    public function adjust(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            $product = Product::findOrFail($validated['product_id']);

            $this->stockService->adjustStock(
                $product,
                $validated['quantity'],
                $validated['reason'],
                null,
                auth()->user(),
            );

            return redirect()->route('admin.stock.index')
                ->with('success', 'Stok berhasil disesuaikan.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyesuaikan stok: '.$e->getMessage());
        }
    }

    public function movements(Request $request): View
    {
        $query = StockAdjustment::with(['product', 'creator']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $movements = $query->latest()->paginate(20)->withQueryString();
        $products = Product::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.stock.movements', compact('movements', 'products'));
    }
}
