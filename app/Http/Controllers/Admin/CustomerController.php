<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query()->where('status', 'ACTIVE');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        try {
            $tempPassword = Str::random(8);

            $customerRole = Role::where('name', 'customer')->first();

            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email') ?? $request->validated('phone').'@petcare.local',
                'phone' => $request->validated('phone'),
                'password' => Hash::make($tempPassword),
                'role_id' => $customerRole?->id,
                'status' => 'ACTIVE',
            ]);

            Customer::create([
                'name' => $request->validated('name'),
                'phone' => $request->validated('phone'),
                'email' => $request->validated('email'),
                'address' => $request->validated('address'),
                'city' => $request->validated('city'),
                'postal_code' => $request->validated('postal_code'),
                'user_id' => $user->id,
                'status' => 'ACTIVE',
            ]);

            return redirect()->route('admin.customers.index')
                ->with('success', 'Pelanggan berhasil ditambahkan. Password sementara: '.$tempPassword);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal menambahkan pelanggan: '.$e->getMessage());
        }
    }

    public function show(Customer $customer): View
    {
        $customer->load(['pets', 'visits' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        try {
            $customer->update($request->validated());

            if ($customer->user) {
                $customer->user->update([
                    'name' => $request->validated('name'),
                    'phone' => $request->validated('phone'),
                    'email' => $request->validated('email') ?? $customer->user->email,
                ]);
            }

            return redirect()->route('admin.customers.index')
                ->with('success', 'Pelanggan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui pelanggan: '.$e->getMessage());
        }
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $customer->update(['status' => 'ARCHIVED']);

            if ($customer->user) {
                $customer->user->update(['status' => 'INACTIVE']);
            }

            return redirect()->route('admin.customers.index')
                ->with('success', 'Pelanggan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus pelanggan: '.$e->getMessage());
        }
    }
}
