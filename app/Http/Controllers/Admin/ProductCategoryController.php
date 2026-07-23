<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ProductCategory::withCount(['products' => function ($query) {
            $query->where('status', 'ACTIVE');
        }])->orderBy('name')->get();

        return view('admin.master-data.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()
                ->with('error', $validator->errors()->first());
        }

        try {
            ProductCategory::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'status' => 'ACTIVE',
            ]);

            return redirect()->route('admin.master-data.categories.index')
                ->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan kategori: '.$e->getMessage());
        }
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name,'.$category->id],
            'description' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()
                ->with('error', $validator->errors()->first());
        }

        try {
            $category->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
            ]);

            return redirect()->route('admin.master-data.categories.index')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui kategori: '.$e->getMessage());
        }
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        try {
            $activeProductCount = $category->products()->where('status', 'ACTIVE')->count();

            if ($activeProductCount > 0) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki produk aktif.');
            }

            $category->update(['status' => 'ARCHIVED']);

            return redirect()->route('admin.master-data.categories.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kategori: '.$e->getMessage());
        }
    }
}
