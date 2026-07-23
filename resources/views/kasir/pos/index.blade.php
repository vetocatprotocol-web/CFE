@extends('layouts.app')

@section('title', 'Point of Sale - Haland PetCare')

@section('content')
<div x-data="posApp()" x-init="init()" class="-mx-4 sm:-mx-6 lg:-mx-8 -mt-4 sm:-mt-6 lg:-mt-8">
    <div class="flex flex-col lg:flex-row h-[calc(100vh-4rem)]">

        {{-- Left: Product Catalog --}}
        <div class="flex-1 flex flex-col bg-gray-50 overflow-hidden">
            {{-- Search Bar --}}
            <div class="p-4 bg-white border-b border-gray-200 shadow-sm">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" @input="filterProducts()"
                           class="block w-full pl-10 pr-10 py-3 text-base border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Cari atau scan barcode produk...">
                    <div x-show="searchQuery" @click="searchQuery = ''; filterProducts()" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                        <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="px-4 py-3 bg-white border-b border-gray-200 overflow-x-auto">
                <div class="flex gap-2">
                    <button @click="selectedCategory = null; filterProducts()"
                            :class="selectedCategory === null ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                        Semua
                    </button>
                    @foreach($categories as $category)
                        <button @click="selectedCategory = {{ $category->id }}; filterProducts()"
                                :class="selectedCategory === {{ $category->id }} ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)"
                             class="bg-white rounded-xl border border-gray-200 p-3 cursor-pointer hover:border-blue-400 hover:shadow-md transition-all group">
                            <div class="aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                                <svg class="w-10 h-10 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-800 truncate" x-text="product.name"></h3>
                            <p class="text-sm font-bold text-blue-600 mt-1" x-text="'Rp ' + formatRupiah(product.price)"></p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs"
                                      :class="product.stock > 0 ? 'text-green-600' : 'text-red-500'"
                                      x-text="'Stok: ' + product.stock"></span>
                                <span class="w-7 h-7 rounded-full bg-blue-100 group-hover:bg-blue-600 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </template>
                    <template x-if="filteredProducts.length === 0">
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="text-gray-500 mt-3">Produk tidak ditemukan</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Right: Cart --}}
        <div class="w-full lg:w-[420px] xl:w-[460px] bg-white border-l border-gray-200 flex flex-col shadow-lg">
            {{-- Cart Header --}}
            <div class="p-4 border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-800">Keranjang</h2>
                    <span class="text-sm text-gray-500" x-text="cart.length + ' item'"></span>
                </div>

                {{-- Customer Selection --}}
                <div x-data="{ customerSearch: '', showCustomerDropdown: false, customers: [], searching: false }" class="relative">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 relative">
                            <input type="text" x-model="customerSearch"
                                   @input.debounce.300ms="searchCustomers(customerSearch, $data)"
                                   @focus="showCustomerDropdown = true"
                                   class="block w-full px-3 py-2 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Pelanggan (opsional)">
                            <template x-if="selectedCustomer">
                                <button @click="selectedCustomer = null; customerSearch = ''" class="absolute right-2 top-1/2 -translate-y-1/2">
                                    <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div x-show="showCustomerDropdown && customers.length > 0 && !selectedCustomer"
                         @click.away="showCustomerDropdown = false"
                         class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="c in customers" :key="c.id">
                            <button @click="selectedCustomer = c; customerSearch = c.name; showCustomerDropdown = false; customers = []"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 border-b border-gray-50 last:border-0">
                                <span class="font-medium text-gray-800" x-text="c.name"></span>
                                <span class="text-gray-500 ml-2" x-text="c.phone || ''"></span>
                            </button>
                        </template>
                    </div>
                    <template x-if="selectedCustomer">
                        <div class="mt-2 flex items-center gap-2 bg-blue-50 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium text-blue-700" x-text="selectedCustomer.name"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                <template x-if="cart.length === 0">
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                        <p class="text-gray-500 mt-3">Keranjang kosong</p>
                        <p class="text-gray-400 text-sm mt-1">Klik produk untuk menambahkan</p>
                    </div>
                </template>
                <template x-for="(item, index) in cart" :key="item.product_id">
                    <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-gray-800 truncate" x-text="item.name"></h4>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="'Rp ' + formatRupiah(item.price) + ' / pcs'"></p>
                            </div>
                            <button @click="removeFromCart(index)" class="text-red-400 hover:text-red-600 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center gap-2">
                                <button @click="updateQty(index, -1)"
                                        class="w-8 h-8 rounded-lg bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <input type="number" :value="item.quantity" @change="setQty(index, $event.target.value)"
                                       min="1" :max="item.stock"
                                       class="w-14 text-center text-sm font-medium border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1">
                                <button @click="updateQty(index, 1)"
                                        class="w-8 h-8 rounded-lg bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </button>
                            </div>
                            <span class="text-sm font-bold text-gray-800" x-text="'Rp ' + formatRupiah(item.price * item.quantity)"></span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Cart Summary & Payment --}}
            <div class="border-t border-gray-200 bg-white">
                {{-- Summary --}}
                <div class="p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-800" x-text="'Rp ' + formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm items-center gap-2">
                        <span class="text-gray-500">Diskon</span>
                        <div class="flex items-center gap-1">
                            <span class="text-gray-400 text-xs">Rp</span>
                            <input type="number" x-model.number="discount" min="0"
                                   class="w-28 text-right text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1">
                        </div>
                    </div>
                    <div class="border-t border-gray-200 pt-2 flex justify-between">
                        <span class="text-base font-bold text-gray-800">Total</span>
                        <span class="text-lg font-bold text-blue-600" x-text="'Rp ' + formatRupiah(total)"></span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="px-4 pb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="paymentMethod = 'cash'"
                                :class="paymentMethod === 'cash' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                class="px-3 py-2 rounded-lg border text-sm font-medium transition-colors text-center">
                            Tunai
                        </button>
                        <button @click="paymentMethod = 'card'"
                                :class="paymentMethod === 'card' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                class="px-3 py-2 rounded-lg border text-sm font-medium transition-colors text-center">
                            Kartu
                        </button>
                        <button @click="paymentMethod = 'transfer'"
                                :class="paymentMethod === 'transfer' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                class="px-3 py-2 rounded-lg border text-sm font-medium transition-colors text-center">
                            Transfer
                        </button>
                        <button @click="paymentMethod = 'qris'"
                                :class="paymentMethod === 'qris' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                class="px-3 py-2 rounded-lg border text-sm font-medium transition-colors text-center">
                            QRIS
                        </button>
                    </div>
                </div>

                {{-- Payment Amount --}}
                <div class="px-4 pb-3" x-show="paymentMethod === 'cash'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar</label>
                    <input type="number" x-model.number="paymentAmount" @input="calculateChange()"
                           :min="total"
                           class="block w-full px-3 py-3 text-lg font-bold border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                    <div class="flex justify-between text-sm mt-2">
                        <span class="text-gray-500">Kembalian</span>
                        <span class="font-bold"
                              :class="change >= 0 ? 'text-green-600' : 'text-red-600'"
                              x-text="'Rp ' + formatRupiah(change)"></span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="p-4 space-y-2">
                    <button @click="processPayment()"
                            :disabled="cart.length === 0 || processing"
                            class="w-full py-3 px-4 rounded-xl text-base font-bold transition-all
                                   disabled:opacity-50 disabled:cursor-not-allowed
                                   bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800
                                   flex items-center justify-center gap-2">
                        <svg x-show="processing" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="processing ? 'Memproses...' : 'Bayar'"></span>
                    </button>
                    <button @click="clearCart()"
                            :disabled="cart.length === 0"
                            class="w-full py-2 px-4 rounded-xl text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Receipt Modal --}}
    <div x-show="showReceipt" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4"
         @click.self="showReceipt = false">
        <div x-show="showReceipt" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 text-center border-b border-gray-200">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Pembayaran Berhasil!</h3>
                <p class="text-sm text-gray-500 mt-1">Transaksi telah diproses</p>
            </div>
            <div class="p-6 space-y-3" x-show="receiptData">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">No. Order</span>
                    <span class="font-medium text-gray-800" x-text="receiptData?.order_number"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total</span>
                    <span class="font-bold text-gray-800" x-text="'Rp ' + formatRupiah(receiptData?.total || 0)"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-medium text-gray-800 uppercase" x-text="receiptData?.payment_method"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Bayar</span>
                    <span class="font-medium text-gray-800" x-text="'Rp ' + formatRupiah(receiptData?.payment_amount || 0)"></span>
                </div>
                <template x-if="receiptData?.change_amount > 0">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Kembalian</span>
                        <span class="font-bold text-green-600" x-text="'Rp ' + formatRupiah(receiptData?.change_amount)"></span>
                    </div>
                </template>
            </div>
            <div class="p-6 border-t border-gray-200 flex gap-3">
                <a :href="receiptData ? '{{ url('/kasir/pos/receipt') }}/' + receiptData?.id : '#'"
                   class="flex-1 py-2.5 px-4 rounded-xl text-sm font-medium text-center border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                    Lihat Struk
                </a>
                <button @click="showReceipt = false; resetPOS()"
                        class="flex-1 py-2.5 px-4 rounded-xl text-sm font-bold text-center bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>

    {{-- Error Toast --}}
    <div x-show="errorMessage" x-transition
         class="fixed bottom-4 right-4 z-50 bg-red-600 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 max-w-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-sm" x-text="errorMessage"></span>
        <button @click="errorMessage = ''" class="ml-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
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
        processing: false,
        showReceipt: false,
        receiptData: null,
        errorMessage: '',
        orderId: null,

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

        async searchCustomers(query, data) {
            if (query.length < 2) {
                data.customers = [];
                return;
            }
            try {
                const response = await fetch(`/api/customers/search?q=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (response.ok) {
                    data.customers = await response.json();
                }
            } catch (e) {
                data.customers = [];
            }
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
            this.calculateChange();
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.calculateChange();
        },

        updateQty(index, delta) {
            const item = this.cart[index];
            const newQty = item.quantity + delta;
            if (newQty < 1) {
                this.removeFromCart(index);
                return;
            }
            if (newQty > item.stock) {
                this.showError('Stok tidak mencukupi');
                return;
            }
            item.quantity = newQty;
            this.calculateChange();
        },

        setQty(index, value) {
            const qty = parseInt(value);
            const item = this.cart[index];
            if (isNaN(qty) || qty < 1) {
                this.removeFromCart(index);
                return;
            }
            if (qty > item.stock) {
                this.showError('Stok tidak mencukupi');
                item.quantity = item.stock;
            } else {
                item.quantity = qty;
            }
            this.calculateChange();
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        get total() {
            return Math.max(0, this.subtotal - this.discount);
        },

        calculateChange() {
            if (this.paymentMethod === 'cash') {
                this.change = Math.max(0, (this.paymentAmount || 0) - this.total);
            }
        },

        clearCart() {
            if (this.cart.length === 0) return;
            if (confirm('Yakin ingin mengosongkan keranjang?')) {
                this.cart = [];
                this.discount = 0;
                this.paymentAmount = 0;
                this.change = 0;
                this.orderId = null;
            }
        },

        showError(msg) {
            this.errorMessage = msg;
            setTimeout(() => { this.errorMessage = ''; }, 4000);
        },

        async processPayment() {
            if (this.cart.length === 0) {
                this.showError('Keranjang kosong');
                return;
            }

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
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            customer_id: this.selectedCustomer?.id || null
                        })
                    });
                    const orderData = await orderRes.json();
                    if (!orderRes.ok || !orderData.success) {
                        throw new Error(orderData.message || 'Gagal membuat order');
                    }
                    this.orderId = orderData.order_id;
                }

                for (const item of this.cart) {
                    const itemRes = await fetch(`{{ url('/kasir/pos/order') }}/${this.orderId}/items`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            product_id: item.product_id,
                            quantity: item.quantity
                        })
                    });
                    const itemData = await itemRes.json();
                    if (!itemRes.ok || !itemData.success) {
                        throw new Error(itemData.message || 'Gagal menambah item');
                    }
                }

                const checkoutRes = await fetch(`{{ url('/kasir/pos/order') }}/${this.orderId}/checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        payment_method: this.paymentMethod,
                        payment_amount: this.paymentMethod === 'cash' ? this.paymentAmount : this.total,
                        discount_amount: this.discount
                    })
                });
                const checkoutData = await checkoutRes.json();
                if (!checkoutRes.ok || !checkoutData.success) {
                    throw new Error(checkoutData.message || 'Gagal checkout');
                }

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
