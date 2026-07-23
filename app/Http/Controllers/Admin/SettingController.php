<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function updateCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'company_phone' => ['nullable', 'string', 'max:20'],
            'company_email' => ['nullable', 'email'],
        ]);

        try {
            foreach ($validated as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value],
                );
            }

            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengusahaan perusahaan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui pengusahaan perusahaan: '.$e->getMessage());
        }
    }

    public function updateTax(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tax_enabled' => ['required', 'boolean'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'tax_name' => ['required', 'string', 'max:50'],
        ]);

        try {
            foreach ($validated as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value],
                );
            }

            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengaturan pajak berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui pengaturan pajak: '.$e->getMessage());
        }
    }

    public function updatePaymentMethods(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_methods' => ['required', 'array'],
            'payment_methods.*' => ['required', 'string', 'max:100'],
        ]);

        try {
            Setting::updateOrCreate(
                ['key' => 'payment_methods'],
                ['value' => json_encode($validated['payment_methods'])],
            );

            return redirect()->route('admin.settings.index')
                ->with('success', 'Metode pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui metode pembayaran: '.$e->getMessage());
        }
    }

    public function updateNumbering(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_prefix' => ['required', 'string', 'max:10'],
            'invoice_format' => ['required', 'string', 'max:50'],
            'billing_prefix' => ['required', 'string', 'max:10'],
            'billing_format' => ['required', 'string', 'max:50'],
            'visit_prefix' => ['required', 'string', 'max:10'],
            'visit_format' => ['required', 'string', 'max:50'],
        ]);

        try {
            foreach ($validated as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value],
                );
            }

            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengaturan penomoran berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal memperbarui pengaturan penomoran: '.$e->getMessage());
        }
    }
}
