@extends('layouts.app')

@section('title', 'Point of Sale - Haland PetCare')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .cart-item-enter { animation: cartSlideIn .25s ease-out; }
    .cart-item-remove { animation: cartSlideOut .2s ease-in forwards; }
    @keyframes cartSlideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes cartSlideOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(-20px); max-height: 0; padding: 0; margin: 0; } }
    .product-card-pulse { animation: cardPulse .3s ease; }
    @keyframes cardPulse { 0% { transform: scale(1); } 50% { transform: scale(.95); } 100% { transform: scale(1); } }
    .qty-bounce { animation: qtyBounce .2s ease; }
    @keyframes qtyBounce { 0% { transform: scale(1); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
    .payment-success { animation: successPop .4s ease; }
    @keyframes successPop { 0% { transform: scale(0); opacity: 0; } 50% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
</style>
@endpush

@section('content')
<div x-data="posApp()" x-init="init()" x-cloak class="h-[calc(100vh-3.5rem)] sm:h-[calc(100vh-4rem)] flex flex-col bg-gray-100 overflow-hidden">

    {{-- Top Quick-Stats Bar --}}
    <div class="flex-shrink-0 flex items-center justify-between px-4 sm:px-6 py-2 bg-white border-b border-gray-200">
        <div class="flex items-center gap-4">
            <h1 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                POS
            </h1>
            <div class="hidden sm:flex items-center gap-3 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> <span x-text="products.length"></span> Produk</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> <span x-text="cart.length"></span> di Keranjang</span>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xs text-gray-400" x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })"></span>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">

        {{-- LEFT PANEL: Product Catalog --}}
        <div class="flex-1 flex flex-col overflow-hidden lg:w-[58%] xl:w-[60%] min-w-0">

            {{-- Search Bar --}}
            <div class="flex-shrink-0 px-4 sm:px-6 pt-4 pb-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.200ms="filterProducts()"
                           class="block w-full pl-12 pr-12 py-3.5 text-base sm:text-lg border-0 rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 transition-all"
                           placeholder="Cari produk, SKU, atau barcode...">
                    <button x-show="searchQuery.length > 0" @click="searchQuery = ''; filterProducts()"
                            x-transition
                            class="absolute inset-y-0 right-0 pr-4 flex items-center">
                        <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="flex-shrink-0 px-4 sm:px-6 pb-3">
                <div class="flex gap-2 overflow-x-auto scrollbar-hide pb-1">
                    <button @click="selectedCategory = null; filterProducts()"
                            :class="selectedCategory === null ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-white text-gray-600 hover:bg-gray-50 ring-1 ring-gray-200'"
                            class="flex-shrink-0 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                        Semua
                    </button>
                    @foreach($categories as $category)
                        <button @click="selectedCategory = {{ $category->id }}; filterProducts()"
                                :class="selectedCategory === {{ $category->id }} ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-white text-gray-600 hover:bg-gray-50 ring-1 ring-gray-200'"
                                class="flex-shrink-0 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="flex-1 overflow-y-auto px-4 sm:px-6 pb-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product); activeCard = product.id; setTimeout(() => activeCard = null, 300)"
                             :class="{ 'product-card-pulse': activeCard === product.id }"
                             class="bg-white rounded-2xl ring-1 ring-gray-100 shadow-sm hover:shadow-lg hover:ring-blue-200 cursor-pointer transition-all duration-200 group relative overflow-hidden flex flex-col">
                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="text-sm sm:text-base font-bold text-gray-800 line-clamp-2 leading-snug min-h-[2.5rem]" x-text="product.name"></h3>
                                <p class="text-lg sm:text-xl font-extrabold text-blue-600 mt-2" x-text="'Rp ' + formatRupiah(product.price)"></p>
                                <div class="mt-auto pt-3 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold rounded-full px-2.5 py-1"
                                          :class="product.current_stock > 5 ? 'bg-green-50 text-green-700' : (product.current_stock > 0 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700')">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="product.current_stock > 5 ? 'bg-green-500' : (product.current_stock > 0 ? 'bg-amber-500' : 'bg-red-500')"></span>
                                        <span x-text="'Stok: ' + product.current_stock"></span>
                                    </span>
                                    <button @click.stop="addToCart(product); activeCard = product.id; setTimeout(() => activeCard = null, 300)"
                                            :disabled="product.current_stock <= 0"
                                            class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-200 hover:bg-blue-700 active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty State --}}
                <template x-if="filteredProducts.length === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium text-lg">Produk tidak ditemukan</p>
                        <p class="text-gray-400 text-sm mt-1">Coba kata kunci atau kategori lain</p>
                        <button @click="searchQuery = ''; selectedCategory = null; filterProducts()"
                                class="mt-4 px-5 py-2.5 bg-white ring-1 ring-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                            Reset Pencarian
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- RIGHT PANEL: Cart --}}
        <div class="w-full lg:w-[42%] xl:w-[40%] flex flex-col bg-white border-t lg:border-t-0 lg:border-l border-gray-200 min-h-0"
             style="max-height: 100%;">

            {{-- Cart Header --}}
            <div class="flex-shrink-0 px-5 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2.5">
                        <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center text-sm font-bold"
                              x-text="cartTotalItems"></span>
                        Keranjang
                    </h2>
                    <button x-show="cart.length > 0"
                            @click="clearCart()"
                            x-transition
                            class="text-xs font-semibold text-red-500 hover:text-red-700 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                        Kosongkan
                    </button>
                </div>

                {{-- Customer Selection --}}
                <div x-data="{ customerSearch: '', showDropdown: false, customers: [], searching: false }" class="relative">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input type="text"
                               x-model="customerSearch"
                               @input.debounce.300ms="searchCustomers(customerSearch, $data)"
                               @focus="showDropdown = true"
                               class="block w-full pl-9 pr-8 py-2.5 text-sm bg-gray-50 ring-1 ring-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all"
                               placeholder="Pelanggan (opsional)">
                        <template x-if="selectedCustomer">
                            <button @click="selectedCustomer = null; customerSearch = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </template>
                    </div>
                    <div x-show="showDropdown && customers.length > 0 && !selectedCustomer"
                         @click.away="showDropdown = false"
                         class="absolute z-20 mt-1 w-full bg-white rounded-xl shadow-xl border border-gray-200 max-h-48 overflow-y-auto">
                        <template x-for="c in customers" :key="c.id">
                            <button @click="selectedCustomer = c; customerSearch = c.name; showDropdown = false; customers = []"
                                    class="w-full text-left px-4 py-3 hover:bg-blue-50 transition-colors border-b border-gray-50 last:border-0">
                                <span class="font-semibold text-gray-800 text-sm" x-text="c.name"></span>
                                <span class="text-gray-400 text-xs ml-2" x-text="c.phone || ''"></span>
                            </button>
                        </template>
                    </div>
                    <template x-if="selectedCustomer">
                        <div class="mt-2 flex items-center gap-2 bg-blue-50 rounded-xl px-3 py-2">
                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span class="text-sm font-semibold text-blue-700 truncate" x-text="selectedCustomer.name"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Cart Items List --}}
            <div class="flex-1 overflow-y-auto min-h-0">
                {{-- Empty Cart --}}
                <template x-if="cart.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">Keranjang kosong</p>
                        <p class="text-gray-400 text-sm mt-1">Pilih produk untuk mulai transaksi</p>
                    </div>
                </template>

                {{-- Items --}}
                <div class="divide-y divide-gray-50">
                    <template x-for="(item, index) in cart" :key="item.product_id">
                        <div class="cart-item-enter px-5 py-3.5 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-gray-800 truncate" x-text="item.name"></h4>
                                    <p class="text-xs text-gray-400 mt-0.5" x-text="'Rp ' + formatRupiah(item.price) + ' / pcs'"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-800 whitespace-nowrap" x-text="'Rp ' + formatRupiah(item.price * item.quantity)"></span>
                                    <button @click="removeFromCart(index)" class="w-7 h-7 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition-all flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-2.5">
                                <div class="flex items-center bg-gray-100 rounded-xl overflow-hidden">
                                    <button @click="updateQty(index, -1)"
                                            class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-200 active:bg-gray-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14"/></svg>
                                    </button>
                                    <input type="number" :value="item.quantity" @change="setQty(index, $event.target.value)"
                                           min="1" :max="item.stock"
                                           class="w-12 h-10 text-center text-sm font-bold border-0 bg-transparent focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button @click="updateQty(index, 1)"
                                            class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-200 active:bg-gray-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 6v12m6-6H6"/></svg>
                                    </button>
                                </div>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-lg"
                                      :class="item.stock <= 2 ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-500'"
                                      x-text="item.stock <= 2 ? 'Stok: ' + item.stock : ''"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Cart Summary & Payment (Sticky Bottom) --}}
            <div class="flex-shrink-0 border-t border-gray-200 bg-white">
                <div class="overflow-y-auto max-h-[55vh]">

                    {{-- Summary --}}
                    <div class="px-5 py-4 space-y-2.5">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-800" x-text="'Rp ' + formatRupiah(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pajak (10%)</span>
                            <span class="font-semibold text-gray-800" x-text="'Rp ' + formatRupiah(tax)"></span>
                        </div>
                        <div class="flex justify-between items-center gap-3 text-sm">
                            <span class="text-gray-500 flex-shrink-0">Diskon</span>
                            <div class="flex items-center gap-1 bg-gray-50 rounded-lg px-2 py-1 ring-1 ring-gray-200 focus-within:ring-2 focus-within:ring-blue-500 transition-all">
                                <span class="text-gray-400 text-xs font-medium">Rp</span>
                                <input type="number" x-model.number="discount" min="0"
                                       class="w-24 text-right text-sm font-semibold border-0 bg-transparent focus:ring-0 focus:outline-none p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            </div>
                        </div>
                        <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                            <span class="text-base font-bold text-gray-800">Total</span>
                            <span class="text-2xl font-extrabold text-blue-600" x-text="'Rp ' + formatRupiah(total)"></span>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="px-5 pb-3">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="method in paymentMethods" :key="method.value">
                                <button @click="paymentMethod = method.value; calculateChange()"
                                        :class="paymentMethod === method.value ? 'bg-blue-600 text-white shadow-md shadow-blue-200 ring-blue-600' : 'bg-gray-50 text-gray-600 ring-gray-200 hover:bg-gray-100'"
                                        class="flex flex-col items-center gap-1 py-3 px-2 rounded-xl ring-1 font-semibold text-xs transition-all duration-200">
                                    <span x-html="method.icon" class="w-5 h-5"></span>
                                    <span x-text="method.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Cash Amount Input --}}
                    <div class="px-5 pb-3" x-show="paymentMethod === 'cash'" x-transition>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Jumlah Bayar</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">Rp</span>
                            <input type="number"
                                   x-model.number="paymentAmount"
                                   @input="calculateChange()"
                                   :min="total"
                                   class="block w-full pl-12 pr-4 py-3.5 text-xl font-extrabold border-0 rounded-xl bg-gray-50 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                   placeholder="0">
                        </div>
                        {{-- Quick Amount Buttons --}}
                        <div class="flex gap-2 mt-2.5">
                            <button @click="paymentAmount = total; calculateChange()" class="flex-1 py-2 rounded-lg bg-blue-50 text-blue-600 text-xs font-bold hover:bg-blue-100 transition">Uang Pas</button>
                            <template x-for="quick in quickAmounts" :key="quick">
                                <button x-show="quick >= total"
                                        @click="paymentAmount = quick; calculateChange()"
                                        class="flex-1 py-2 rounded-lg bg-gray-50 text-gray-600 text-xs font-bold hover:bg-gray-100 ring-1 ring-gray-200 transition"
                                        x-text="'Rp' + (quick/1000) + 'K'"></button>
                            </template>
                        </div>
                        {{-- Change Display --}}
                        <div class="flex justify-between items-center mt-3 bg-gray-50 rounded-xl px-4 py-3 ring-1 ring-gray-200">
                            <span class="text-sm font-medium text-gray-500">Kembalian</span>
                            <span class="text-xl font-extrabold"
                                  :class="change >= 0 ? 'text-green-600' : 'text-red-600'"
                                  x-text="'Rp ' + formatRupiah(change)"></span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="px-5 pb-5 space-y-2.5">
                        <button @click="processPayment()"
                                :disabled="cart.length === 0 || processing"
                                class="w-full py-4 rounded-2xl text-base font-bold transition-all duration-200
                                       disabled:opacity-40 disabled:cursor-not-allowed
                                       bg-gradient-to-r from-blue-600 to-blue-700 text-white
                                       hover:from-blue-700 hover:to-blue-800
                                       active:from-blue-800 active:to-blue-900
                                       shadow-lg shadow-blue-200
                                       flex items-center justify-center gap-3">
                            <svg x-show="processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <svg x-show="!processing" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="processing ? 'Memproses Pembayaran...' : 'Bayar Sekarang'" class="text-base"></span>
                        </button>
                        <button @click="clearCart()"
                                :disabled="cart.length === 0"
                                class="w-full py-2.5 rounded-xl text-sm font-semibold text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                            Bersihkan Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PAYMENT SUCCESS MODAL ===== --}}
    <div x-show="showReceipt"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="showReceipt = false">
        <div x-show="showReceipt"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-3xl shadow-2xl max-w-sm w-full overflow-hidden">

            {{-- Success Header --}}
            <div class="relative bg-gradient-to-br from-green-500 to-emerald-600 px-6 py-8 text-center text-white">
                <div class="payment-success w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold">Pembayaran Berhasil!</h3>
                <p class="text-sm text-white/80 mt-1">Transaksi telah diproses dengan sukses</p>
            </div>

            {{-- Receipt Details --}}
            <div class="px-6 py-5 space-y-3" x-show="receiptData">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">No. Order</span>
                    <span class="text-sm font-bold text-gray-800 bg-gray-100 px-3 py-1 rounded-lg" x-text="receiptData?.order_number"></span>
                </div>
                <div class="h-px bg-gray-100"></div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Metode Bayar</span>
                    <span class="text-sm font-bold text-gray-800 uppercase" x-text="receiptData?.payment_method"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Total</span>
                    <span class="text-lg font-extrabold text-blue-600" x-text="'Rp ' + formatRupiah(receiptData?.total || 0)"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Dibayar</span>
                    <span class="text-sm font-bold text-gray-800" x-text="'Rp ' + formatRupiah(receiptData?.payment_amount || 0)"></span>
                </div>
                <template x-if="receiptData?.change_amount > 0">
                    <div class="flex justify-between items-center bg-green-50 -mx-6 px-6 py-3">
                        <span class="text-sm font-semibold text-green-700">Kembalian</span>
                        <span class="text-lg font-extrabold text-green-600" x-text="'Rp ' + formatRupiah(receiptData?.change_amount)"></span>
                    </div>
                </template>
            </div>

            {{-- Modal Actions --}}
            <div class="px-6 pb-6 space-y-2.5">
                <a :href="receiptData ? '{{ url('/kasir/pos/receipt') }}/' + receiptData?.id : '#'"
                   class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Lihat Struk
                </a>
                <button @click="showReceipt = false; resetPOS()"
                        class="w-full py-3.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 transition-colors shadow-lg shadow-blue-200">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>

    {{-- ===== ERROR TOAST ===== --}}
    <div x-show="errorMessage"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-gray-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 max-w-md">
        <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm font-medium" x-text="errorMessage"></span>
        <button @click="errorMessage = ''" class="ml-2 text-gray-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
function posApp() {
    return {
        products: {!! $products->toJson() !!},
        filteredProducts: [],
        searchQuery: '',
        selectedCategory: null,
        cart: [],
        selectedCustomer: null,
        paymentMethod: 'cash',
        paymentAmount: 0,
        discount: 0,
        change: 0,
        tax: 0,
        processing: false,
        showReceipt: false,
        receiptData: null,
        errorMessage: '',
        orderId: null,
        activeCard: null,
        paymentMethods: [
            { value: 'cash', label: 'Tunai', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>' },
            { value: 'card', label: 'Kartu', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>' },
            { value: 'transfer', label: 'Transfer', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 014-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>' },
            { value: 'qris', label: 'QRIS', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="3" height="3"/><rect x="18" y="18" width="3" height="3"/></svg>' },
        ],
        quickAmounts: [50000, 100000, 150000, 200000, 250000, 500000],

        init() {
            this.filteredProducts = [...this.products];
        },

        filterProducts() {
            let result = [...this.products];
            if (this.selectedCategory) {
                result = result.filter(p => p.category_id == this.selectedCategory);
            }
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(p =>
                    p.name.toLowerCase().includes(q) ||
                    (p.sku && p.sku.toLowerCase().includes(q)) ||
                    (p.barcode && p.barcode.toLowerCase().includes(q))
                );
            }
            this.filteredProducts = result;
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(Math.round(value || 0));
        },

        get cartTotalItems() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        get total() {
            return Math.max(0, this.subtotal + this.tax - this.discount);
        },

        calculateTax() {
            this.tax = Math.round(this.subtotal * 0.1);
            this.calculateChange();
        },

        calculateChange() {
            if (this.paymentMethod === 'cash') {
                this.change = Math.max(0, (this.paymentAmount || 0) - this.total);
            }
        },

        async searchCustomers(query, data) {
            if (query.length < 2) { data.customers = []; return; }
            try {
                const response = await fetch(`/api/customers/search?q=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (response.ok) { data.customers = await response.json(); }
            } catch (e) { data.customers = []; }
        },

        async addToCart(product) {
            if (product.current_stock <= 0) {
                this.showError('Stok produk habis');
                return;
            }
            const existing = this.cart.find(item => item.product_id === product.id);
            if (existing) {
                if (existing.quantity >= product.current_stock) {
                    this.showError('Stok tidak mencukupi');
                    return;
                }
                existing.quantity++;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    stock: product.current_stock
                });
            }
            this.calculateTax();
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.calculateTax();
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            const newQty = item.quantity + delta;
            if (newQty < 1) { this.removeFromCart(index); return; }
            if (newQty > item.stock) { this.showError('Stok tidak mencukupi'); return; }
            item.quantity = newQty;
            this.calculateTax();
        },

        setQty(index, value) {
            const qty = parseInt(value);
            const item = this.cart[index];
            if (isNaN(qty) || qty < 1) { this.removeFromCart(index); return; }
            if (qty > item.stock) { this.showError('Stok tidak mencukupi'); item.quantity = item.stock; }
            else { item.quantity = qty; }
            this.calculateTax();
        },

        clearCart() {
            if (this.cart.length === 0) return;
            if (confirm('Yakin ingin mengosongkan keranjang?')) {
                this.cart = [];
                this.discount = 0;
                this.paymentAmount = 0;
                this.change = 0;
                this.tax = 0;
                this.orderId = null;
            }
        },

        showError(msg) {
            this.errorMessage = msg;
            setTimeout(() => { this.errorMessage = ''; }, 4000);
        },

        async processPayment() {
            if (this.cart.length === 0) { this.showError('Keranjang kosong'); return; }
            if (this.paymentMethod === 'cash' && this.paymentAmount < this.total) {
                this.showError('Jumlah bayar kurang dari total');
                return;
            }
            this.processing = true;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
                if (!this.orderId) {
                    const orderRes = await fetch('{{ route("kasir.pos.create-order") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ customer_id: this.selectedCustomer?.id || null })
                    });
                    const orderData = await orderRes.json();
                    if (!orderRes.ok || !orderData.success) throw new Error(orderData.message || 'Gagal membuat order');
                    this.orderId = orderData.order_id;
                }

                for (const item of this.cart) {
                    const itemRes = await fetch(`{{ url('/kasir/pos/order') }}/${this.orderId}/items`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ product_id: item.product_id, quantity: item.quantity })
                    });
                    const itemData = await itemRes.json();
                    if (!itemRes.ok || !itemData.success) throw new Error(itemData.message || 'Gagal menambah item');
                }

                const checkoutRes = await fetch(`{{ url('/kasir/pos/order') }}/${this.orderId}/checkout`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({
                        payment_method: this.paymentMethod,
                        payment_amount: this.paymentMethod === 'cash' ? this.paymentAmount : this.total,
                        discount_amount: this.discount
                    })
                });
                const checkoutData = await checkoutRes.json();
                if (!checkoutRes.ok || !checkoutData.success) throw new Error(checkoutData.message || 'Gagal checkout');

                this.receiptData = checkoutData.order;
                this.showReceipt = true;

            } catch (error) {
                this.showError(error.message || 'Terjadi kesalahan');
            } finally {
                this.processing = false;
            }
        },

        resetPOS() {
            this.cart = [];
            this.discount = 0;
            this.paymentAmount = 0;
            this.change = 0;
            this.tax = 0;
            this.orderId = null;
            this.receiptData = null;
            this.selectedCustomer = null;
            this.paymentMethod = 'cash';
        }
    };
}
</script>
@endpush
@endsection
