<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->where('status', 'ACTIVE')->with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = ProductCategory::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.master-data.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = ProductCategory::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.master-data.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            Product::create([
                'name' => $request->validated('name'),
                'category_id' => $request->validated('category_id'),
                'price' => $request->validated('price'),
                'description' => $request->validated('description'),
                'current_stock' => $request->validated('current_stock'),
                'reorder_point' => $request->validated('reorder_point'),
                'barcode' => $request->validated('barcode'),
                'status' => 'ACTIVE',
            ]);

            return redirect()->route('admin.master-data.products.index')
                ->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan produk: '.$e->getMessage());
        }
    }

    public function edit(Product $product): View
    {
        $categories = ProductCategory::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('admin.master-data.products.edit', compact('product', 'categories'));
    }

    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        try {
            $product->update($request->validated());

            return redirect()->route('admin.master-data.products.index')
                ->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui produk: '.$e->getMessage());
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->update(['status' => 'ARCHIVED']);

            return redirect()->route('admin.master-data.products.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus produk: '.$e->getMessage());
        }
    }
}
