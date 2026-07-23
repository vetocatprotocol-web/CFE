<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::query()->where('status', 'ACTIVE');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $services = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.master-data.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.master-data.services.create');
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        try {
            Service::create([
                'name' => $request->validated('name'),
                'description' => $request->validated('description'),
                'category' => $request->validated('category'),
                'price' => $request->validated('price'),
                'status' => 'ACTIVE',
            ]);

            return redirect()->route('admin.master-data.services.index')
                ->with('success', 'Layanan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan layanan: '.$e->getMessage());
        }
    }

    public function edit(Service $service): View
    {
        return view('admin.master-data.services.edit', compact('service'));
    }

    public function update(StoreServiceRequest $request, Service $service): RedirectResponse
    {
        try {
            $service->update($request->validated());

            return redirect()->route('admin.master-data.services.index')
                ->with('success', 'Layanan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui layanan: '.$e->getMessage());
        }
    }

    public function destroy(Service $service): RedirectResponse
    {
        try {
            $service->update(['status' => 'ARCHIVED']);

            return redirect()->route('admin.master-data.services.index')
                ->with('success', 'Layanan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus layanan: '.$e->getMessage());
        }
    }
}
