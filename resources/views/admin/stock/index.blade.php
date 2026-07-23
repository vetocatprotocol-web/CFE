@extends('layouts.app')

@section('title', 'Stok')
@section('header-title', 'Manajemen Stok')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.stock.index') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select name="stock_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Semua Status</option>
                <option value="normal" {{ request('stock_status') === 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Rendah</option>
            </select>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Filter
            </button>
        </form>
        <div class="flex gap-3">
            <a href="{{ route('admin.stock.movements') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Riwayat Stok
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3">Produk</th>
                        <th class="px-6 py-3 text-right">Stok Saat Ini</th>
                        <th class="px-6 py-3 text-right">Titik Reorder</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                @if($product->barcode)
                                    <div class="text-xs text-gray-500 mt-0.5">SKU: {{ $product->barcode }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-right {{ $product->current_stock <= $product->reorder_point ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $product->current_stock }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 text-right">{{ $product->reorder_point }}</td>
                            <td class="px-6 py-4">
                                @if($product->current_stock <= $product->reorder_point)
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Rendah</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Normal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="$dispatch('open-adjust-modal', {
                                    id: {{ $product->id }},
                                    name: '{{ $product->name }}',
                                    stock: {{ $product->current_stock }},
                                })" class="bg-primary-50 hover:bg-primary-100 text-primary-700 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">
                                    Sesuaikan Stok
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada produk ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- Stock Adjustment Modal --}}
    <div x-data="{
        show: false,
        productId: '',
        productName: '',
        currentStock: '',
    }"
    @open-adjust-modal.window="show = true; productId = $event.detail.id; productName = $event.detail.name; currentStock = $event.detail.stock;"
    x-show="show" x-cloak x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6" @click.stop>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sesuaikan Stok</h3>
            <form method="POST" action="{{ route('admin.stock.adjust') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" :value="productId">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                    <input type="text" :value="productName" readonly
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-50 text-gray-700">
                    <p class="mt-1 text-xs text-gray-400">Stok saat ini: <span x-text="currentStock"></span></p>
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="quantity" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('quantity') border-red-500 @enderror"
                           placeholder="Gunakan angka positif untuk penambahan, negatif untuk pengurangan">
                    <p class="mt-1 text-xs text-gray-400">Positif = penambahan, Negatif = pengurangan</p>
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                    <select name="reason" id="reason" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('reason') border-red-500 @enderror">
                        <option value="">Pilih Alasan</option>
                        <option value="purchase">Pembelian</option>
                        <option value="sale">Penjualan</option>
                        <option value="damage">Kerusakan</option>
                        <option value="expired">Kedaluwarsa</option>
                        <option value="correction">Koreksi Stok</option>
                        <option value="return">Retur</option>
                        <option value="adjustment">Penyesuaian Manual</option>
                    </select>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="show = false" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
