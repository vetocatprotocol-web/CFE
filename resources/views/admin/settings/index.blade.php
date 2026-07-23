@extends('layouts.app')

@section('title', 'Pengaturan')
@section('header-title', 'Pengaturan Sistem')

@section('content')
<div x-data="{ activeTab: 'company' }" class="space-y-6">
    {{-- Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto px-6 -mb-px" aria-label="Tabs">
                <button @click="activeTab = 'company'" :class="activeTab === 'company' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-4 text-sm font-medium border-b-2 transition-colors">
                    Informasi Perusahaan
                </button>
                <button @click="activeTab = 'tax'" :class="activeTab === 'tax' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-4 text-sm font-medium border-b-2 transition-colors">
                    Pajak
                </button>
                <button @click="activeTab = 'payment'" :class="activeTab === 'payment' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-4 text-sm font-medium border-b-2 transition-colors">
                    Metode Pembayaran
                </button>
                <button @click="activeTab = 'numbering'" :class="activeTab === 'numbering' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-4 text-sm font-medium border-b-2 transition-colors">
                    Penomoran
                </button>
            </nav>
        </div>

        {{-- Company Info Tab --}}
        <div x-show="activeTab === 'company'" x-cloak class="p-6">
            <form method="POST" action="{{ route('admin.settings.company') }}" class="space-y-5 max-w-2xl">
                @csrf

                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" name="company_name" id="company_name" value="{{ $settings['company_name'] ?? '' }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('company_name') border-red-500 @enderror">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="company_address" id="company_address" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('company_address') border-red-500 @enderror">{{ $settings['company_address'] ?? '' }}</textarea>
                    @error('company_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="company_phone" id="company_phone" value="{{ $settings['company_phone'] ?? '' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('company_phone') border-red-500 @enderror">
                        @error('company_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="company_email" id="company_email" value="{{ $settings['company_email'] ?? '' }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('company_email') border-red-500 @enderror">
                        @error('company_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Perusahaan</label>
                    <div class="flex items-center gap-4">
                        <input type="file" name="company_logo" accept="image/*"
                               class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Format: PNG, JPG, SVG. Maks 2MB.</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        Simpan Informasi Perusahaan
                    </button>
                </div>
            </form>
        </div>

        {{-- Tax Tab --}}
        <div x-show="activeTab === 'tax'" x-cloak class="p-6">
            <form method="POST" action="{{ route('admin.settings.tax') }}" class="space-y-5 max-w-2xl">
                @csrf

                <div x-data="{ enabled: {{ ($settings['tax_enabled'] ?? '0') === '1' ? 'true' : 'false' }} }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pajak Aktif</label>
                    <input type="hidden" name="tax_enabled" :value="enabled ? '1' : '0'">
                    <button type="button" @click="enabled = !enabled"
                            :class="enabled ? 'bg-primary-600' : 'bg-gray-200'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                        <span :class="enabled ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                    </button>
                    <span class="ml-2 text-sm text-gray-600" x-text="enabled ? 'Aktif' : 'Nonaktif'"></span>
                </div>

                <div>
                    <label for="tax_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Pajak <span class="text-red-500">*</span></label>
                    <input type="text" name="tax_name" id="tax_name" value="{{ $settings['tax_name'] ?? 'PPN' }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('tax_name') border-red-500 @enderror"
                           placeholder="Contoh: PPN">
                    @error('tax_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-1">Tarif Pajak (%) <span class="text-red-500">*</span></label>
                    <input type="number" name="tax_rate" id="tax_rate" value="{{ $settings['tax_rate'] ?? '11' }}" required min="0" max="100" step="0.1"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('tax_rate') border-red-500 @enderror"
                           placeholder="11">
                    <p class="mt-1 text-xs text-gray-400">Masukkan angka tanpa simbol %. Contoh: 11 untuk PPN 11%</p>
                    @error('tax_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        Simpan Pengaturan Pajak
                    </button>
                </div>
            </form>
        </div>

        {{-- Payment Methods Tab --}}
        <div x-show="activeTab === 'payment'" x-cloak class="p-6">
            <form method="POST" action="{{ route('admin.settings.payment-methods') }}" class="space-y-5 max-w-2xl">
                @csrf
                @php
                    $paymentMethods = json_decode($settings['payment_methods'] ?? '["cash"]', true);
                @endphp

                <p class="text-sm text-gray-600 mb-4">Pilih metode pembayaran yang tersedia di klinik Anda:</p>

                <div class="space-y-3">
                    @foreach(['cash' => 'Tunai (Cash)', 'bank_transfer' => 'Transfer Bank', 'card' => 'Kartu Debit/Kredit', 'ewallet' => 'e-Wallet', 'custom' => 'Lainnya'] as $key => $label)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="payment_methods[]" value="{{ $key }}"
                                   {{ in_array($key, $paymentMethods) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                @error('payment_methods')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        Simpan Metode Pembayaran
                    </button>
                </div>
            </form>
        </div>

        {{-- Numbering Tab --}}
        <div x-show="activeTab === 'numbering'" x-cloak class="p-6">
            <form method="POST" action="{{ route('admin.settings.numbering') }}" class="space-y-5 max-w-2xl">
                @csrf

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-700">
                        Gunakan format berikut: <code class="bg-blue-100 px-1 py-0.5 rounded">{YEAR}</code> untuk tahun,
                        <code class="bg-blue-100 px-1 py-0.5 rounded">{MONTH}</code> untuk bulan,
                        <code class="bg-blue-100 px-1 py-0.5 rounded">{SEQ}</code> untuk nomor urut.
                    </p>
                </div>

                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider pt-2">Invoice</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="invoice_prefix" class="block text-sm font-medium text-gray-700 mb-1">Prefix <span class="text-red-500">*</span></label>
                        <input type="text" name="invoice_prefix" id="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV' }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('invoice_prefix') border-red-500 @enderror">
                        @error('invoice_prefix')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="invoice_format" class="block text-sm font-medium text-gray-700 mb-1">Format <span class="text-red-500">*</span></label>
                        <input type="text" name="invoice_format" id="invoice_format" value="{{ $settings['invoice_format'] ?? '{PREFIX}/{YEAR}/{SEQ}' }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('invoice_format') border-red-500 @enderror">
                        @error('invoice_format')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider pt-2">Billing</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="billing_prefix" class="block text-sm font-medium text-gray-700 mb-1">Prefix <span class="text-red-500">*</span></label>
                        <input type="text" name="billing_prefix" id="billing_prefix" value="{{ $settings['billing_prefix'] ?? 'BIL' }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('billing_prefix') border-red-500 @enderror">
                        @error('billing_prefix')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="billing_format" class="block text-sm font-medium text-gray-700 mb-1">Format <span class="text-red-500">*</span></label>
                        <input type="text" name="billing_format" id="billing_format" value="{{ $settings['billing_format'] ?? '{PREFIX}/{YEAR}/{SEQ}' }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('billing_format') border-red-500 @enderror">
                        @error('billing_format')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider pt-2">Kunjungan</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="visit_prefix" class="block text-sm font-medium text-gray-700 mb-1">Prefix <span class="text-red-500">*</span></label>
                        <input type="text" name="visit_prefix" id="visit_prefix" value="{{ $settings['visit_prefix'] ?? 'VST' }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('visit_prefix') border-red-500 @enderror">
                        @error('visit_prefix')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="visit_format" class="block text-sm font-medium text-gray-700 mb-1">Format <span class="text-red-500">*</span></label>
                        <input type="text" name="visit_format" id="visit_format" value="{{ $settings['visit_format'] ?? '{PREFIX}/{YEAR}/{SEQ}' }}" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm @error('visit_format') border-red-500 @enderror">
                        @error('visit_format')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        Simpan Pengaturan Penomoran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
