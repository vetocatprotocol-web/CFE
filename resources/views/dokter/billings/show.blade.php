@extends('layouts.app')

@section('title', 'Detail Billing - ' . $billing->billing_number)
@section('header-title', 'Detail Billing')

@section('content')
<div x-data="{
    item_type: 'service',
    searchQuery: '',
    searchResults: [],
    quantity: 1,
    notes: '',
    isSearching: false,
    searchTimeout: null,
    showDropdown: false,

    allItems: {
        service: @json($services),
        drug: @json($drugs),
        product: @json($products),
    },

    get filteredItems() {
        const list = this.allItems[this.item_type] || [];
        if (!this.searchQuery) return list;
        const q = this.searchQuery.toLowerCase();
        return list.filter(i => i.name.toLowerCase().includes(q));
    },

    searchItem() {
        clearTimeout(this.searchTimeout);
        this.searchQuery = this.searchQuery;
        this.showDropdown = true;
    },

    get unitPriceField() {
        if (this.item_type === 'drug') return 'price_per_unit';
        return 'price';
    },

    formatCurrency(val) {
        return new Intl.NumberFormat('id-ID').format(val);
    }
}" x-init="$watch('item_type', () => { searchQuery = ''; searchResults = []; showDropdown = false; })">

    <div class="space-y-6">
        {{-- Billing Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $billing->billing_number }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $billing->billing_start_date?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <x-status-badge :status="$billing->status" size="md" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Pelanggan</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $billing->customer->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $billing->customer->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Hewan</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $billing->pet->name ?? '-' }}</p>
                    <p class="text-xs text-gray-500">{{ $billing->pet->species ?? '-' }} {{ $billing->pet->breed ? '- ' . $billing->pet->breed : '' }}</p>
                </div>
            </div>

            @if($billing->notes)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Catatan</p>
                    <p class="text-sm text-gray-900">{{ $billing->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Add Item --}}
        @if($billing->status === 'OPEN')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Tambah Item</h3>
                <form method="POST" action="{{ route('dokter.billings.items.add', $billing) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                            <select x-model="item_type" name="item_type"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="service">Layanan</option>
                                <option value="drug">Obat</option>
                                <option value="product">Produk</option>
                            </select>
                        </div>
                        <div class="md:col-span-5 relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Item</label>
                            <input type="text" x-model="searchQuery" @input="searchItem()" @focus="showDropdown = true"
                                   @click.away="showDropdown = false" placeholder="Ketik nama..."
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <div x-show="showDropdown && filteredItems.length > 0" x-transition
                                 class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <template x-for="item in filteredItems" :key="item.id">
                                    <div @click="searchQuery = item.name; showDropdown = false;" class="px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-900" x-text="item.name"></span>
                                            <span class="text-xs text-gray-500" x-text="'Rp ' + formatCurrency(item_type === 'drug' ? item.price_per_unit : item.price)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <input type="number" name="quantity" x-model="quantity" min="1" value="1"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <button type="submit"
                                    class="w-full px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Item
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        {{-- Items Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Item Billing</h3>

            @if($billing->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                <th class="pb-2">Jenis</th>
                                <th class="pb-2">Nama</th>
                                <th class="pb-2 text-center">Qty</th>
                                <th class="pb-2 text-right">Harga</th>
                                <th class="pb-2 text-right">Subtotal</th>
                                @if($billing->status === 'OPEN')
                                    <th class="pb-2 text-center w-16"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($billing->items as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                            {{ match($item->item_type) {
                                                'service' => 'bg-blue-100 text-blue-700',
                                                'drug' => 'bg-purple-100 text-purple-700',
                                                'product' => 'bg-green-100 text-green-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            } }}">
                                            {{ ucfirst($item->item_type) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-sm text-gray-900">{{ $item->service->name ?? $item->drug->name ?? $item->product->name ?? '-' }}</td>
                                    <td class="py-3 text-sm text-gray-700 text-center">{{ $item->quantity }}</td>
                                    <td class="py-3 text-sm text-gray-700 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-3 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    @if($billing->status === 'OPEN')
                                        <td class="py-3 text-center">
                                            <form method="POST" action="{{ route('dokter.billings.items.remove', [$billing, $item]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus item ini?')">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 border-t border-gray-200 pt-4 flex justify-end">
                    <div class="w-64 space-y-1">
                        @php
                            $subtotal = $billing->items->sum('subtotal');
                        @endphp
                        <div class="flex justify-between text-sm font-bold text-gray-900">
                            <span>Total</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-4">Belum ada item</p>
            @endif
        </div>

        {{-- Invoice --}}
        @if($billing->invoice)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Invoice</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Nomor Invoice</p>
                        <a href="{{ route('invoices.show', $billing->invoice) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">{{ $billing->invoice->invoice_number }}</a>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Jumlah</p>
                        <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($billing->invoice->total_amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Status</p>
                        <x-status-badge :status="$billing->invoice->status" size="md" />
                    </div>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('dokter.billings.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                Kembali
            </a>
            <div class="flex items-center gap-3">
                @if($billing->status === 'OPEN')
                    <form method="POST" action="{{ route('dokter.billings.complete', $billing) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors" onclick="return confirm('Selesaikan billing ini? Invoice akan dibuat otomatis.')">
                            Selesaikan Billing
                        </button>
                    </form>
                @endif

                @if($billing->status === 'COMPLETED' || $billing->status === 'PAID' || $billing->status === 'SETTLED')
                    @if($billing->invoice)
                        <a href="{{ route('invoices.show', $billing->invoice) }}" class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                            Lihat Invoice
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
