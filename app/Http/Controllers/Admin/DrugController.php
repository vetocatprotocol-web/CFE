<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrugRequest;
use App\Models\Drug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DrugController extends Controller
{
    public function index(Request $request): View
    {
        $query = Drug::query()->where('status', 'ACTIVE');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $drugs = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.master-data.drugs.index', compact('drugs'));
    }

    public function create(): View
    {
        return view('admin.master-data.drugs.create');
    }

    public function store(StoreDrugRequest $request): RedirectResponse
    {
        try {
            Drug::create([
                'name' => $request->validated('name'),
                'description' => $request->validated('description'),
                'unit' => $request->validated('unit'),
                'price_per_unit' => $request->validated('price_per_unit'),
                'status' => 'ACTIVE',
            ]);

            return redirect()->route('admin.master-data.drugs.index')
                ->with('success', 'Obat berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan obat: '.$e->getMessage());
        }
    }

    public function edit(Drug $drug): View
    {
        return view('admin.master-data.drugs.edit', compact('drug'));
    }

    public function update(StoreDrugRequest $request, Drug $drug): RedirectResponse
    {
        try {
            $drug->update($request->validated());

            return redirect()->route('admin.master-data.drugs.index')
                ->with('success', 'Obat berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui obat: '.$e->getMessage());
        }
    }

    public function destroy(Drug $drug): RedirectResponse
    {
        try {
            $drug->update(['status' => 'ARCHIVED']);

            return redirect()->route('admin.master-data.drugs.index')
                ->with('success', 'Obat berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus obat: '.$e->getMessage());
        }
    }
}
