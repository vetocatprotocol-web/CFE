@extends('layouts.app')

@section('title', 'Buat Kunjungan Baru')
@section('header-title', 'Buat Kunjungan Baru')

@section('content')
<div x-data="{
    searchQuery: '',
    searchResults: [],
    selectedCustomer: null,
    selectedPetId: '',
    isSearching: false,
    searchTimeout: null,

    services: @json($services),
    drugs: @json($drugs),
    items: [],
    serviceSearch: '',
    drugSearch: '',
    showServiceDropdown: false,
    showDrugDropdown: false,

    get filteredServices() {
        if (!this.serviceSearch) return this.services;
        const q = this.serviceSearch.toLowerCase();
        return this.services.filter(s => s.name.toLowerCase().includes(q));
    },
    get filteredDrugs() {
        if (!this.drugSearch) return this.drugs;
        const q = this.drugSearch.toLowerCase();
        return this.drugs.filter(d => d.name.toLowerCase().includes(q));
    },

    searchCustomer() {
        clearTimeout(this.searchTimeout);
        if (this.searchQuery.length < 2) {
            this.searchResults = [];
            return;
        }
        this.isSearching = true;
        this.searchTimeout = setTimeout(() => {
            fetch('{{ route('dokter.search-customer') }}?q=' + encodeURIComponent(this.searchQuery), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                this.searchResults = data.data || [];
                this.isSearching = false;
            })
            .catch(() => { this.isSearching = false; });
        }, 300);
    },
    selectCustomer(c) {
        this.selectedCustomer = c;
        this.selectedPetId = c.pets && c.pets.length > 0 ? c.pets[0].id : '';
        this.searchQuery = '';
        this.searchResults = [];
    },
    clearCustomer() {
        this.selectedCustomer = null;
        this.selectedPetId = '';
    },

    addService(service) {
        this.items.push({
            item_type: 'service',
            service_id: service.id,
            drug_id: null,
            item_name: service.name,
            unit_price: service.price,
            quantity: 1,
            subtotal: service.price,
        });
        this.serviceSearch = '';
        this.showServiceDropdown = false;
    },
    addDrug(drug) {
        this.items.push({
            item_type: 'drug',
            service_id: null,
            drug_id: drug.id,
            item_name: drug.name,
            unit_price: drug.price_per_unit,
            quantity: 1,
            subtotal: drug.price_per_unit,
        });
        this.drugSearch = '';
        this.showDrugDropdown = false;
    },
    updateItemQty(index, qty) {
        qty = parseInt(qty) || 1;
        this.items[index].quantity = qty;
        this.items[index].subtotal = qty * this.items[index].unit_price;
    },
    removeItem(index) {
        this.items.splice(index, 1);
    },
    get subtotal() {
        return this.items.reduce((sum, item) => sum + item.subtotal, 0);
    },
    get tax() {
        return Math.round(this.subtotal * 0.11);
    },
    get total() {
        return this.subtotal + this.tax;
    },

    formatCurrency(val) {
        return new Intl.NumberFormat('id-ID').format(val);
    }
}" x-init="$watch('searchQuery', () => searchCustomer())">

    <form method="POST" action="{{ route('dokter.visits.store') }}" class="space-y-6">
        @csrf

        {{-- Customer Selection --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pilih Pelanggan</h3>

            <div class="relative">
                <template x-if="!selectedCustomer">
                    <div>
                        <input type="text" x-model="searchQuery" placeholder="Cari pelanggan berdasarkan nama atau telepon..."
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <div x-show="searchResults.length > 0" @click.away="searchResults = []"
                             class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="c in searchResults" :key="c.id">
                                <div @click="selectCustomer(c)"
                                     class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0">
                                    <p class="text-sm font-medium text-gray-900" x-text="c.name"></p>
                                    <p class="text-xs text-gray-500" x-text="c.phone || '-'"></p>
                                    <p class="text-xs text-gray-400" x-text="c.pets.length + ' hewan terdaftar'"></p>
                                </div>
                            </template>
                        </div>
                        <div x-show="isSearching" class="mt-2 text-sm text-gray-500">Mencari...</div>
                    </div>
                </template>

                <template x-if="selectedCustomer">
                    <div class="flex items-center gap-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-700 font-bold" x-text="selectedCustomer.name.charAt(0).toUpperCase()"></div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900" x-text="selectedCustomer.name"></p>
                            <p class="text-xs text-gray-500" x-text="selectedCustomer.phone || '-'"></p>
                        </div>
                        <button type="button" @click="clearCustomer()" class="text-gray-400 hover:text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
                <input type="hidden" name="customer_id" :value="selectedCustomer ? selectedCustomer.id : ''" required>
                @error('customer_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4" x-show="selectedCustomer && selectedCustomer.pets && selectedCustomer.pets.length > 0">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Hewan <span class="text-red-500">*</span></label>
                <select name="pet_id" x-model="selectedPetId" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <template x-for="pet in (selectedCustomer ? selectedCustomer.pets : [])" :key="pet.id">
                        <option :value="pet.id" x-text="pet.name + ' (' + pet.species + (pet.breed ? ' - ' + pet.breed : '') + ')'"></option>
                    </template>
                </select>
                @error('pet_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Visit Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kunjungan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keluhan Utama <span class="text-red-500">*</span></label>
                    <textarea name="chief_complaint" rows="3" required
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('chief_complaint') border-red-500 @enderror"
                              placeholder="Keluhan utama pasien...">{{ old('chief_complaint') }}</textarea>
                    @error('chief_complaint')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="visit_date" value="{{ old('visit_date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
                        <input type="time" name="visit_time" value="{{ old('visit_time', now()->format('H:i')) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis</label>
                <textarea name="diagnosis" rows="2"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('diagnosis') border-red-500 @enderror"
                          placeholder="Diagnosis...">{{ old('diagnosis') }}</textarea>
                @error('diagnosis')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Perawatan</label>
                <textarea name="treatment_notes" rows="2"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('treatment_notes') border-red-500 @enderror"
                          placeholder="Catatan perawatan...">{{ old('treatment_notes') }}</textarea>
                @error('treatment_notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berat (kg)</label>
                    <input type="number" name="weight_kg" step="0.1" min="0" value="{{ old('weight_kg') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('weight_kg') border-red-500 @enderror"
                           placeholder="0.0">
                    @error('weight_kg')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Suhu (&deg;C)</label>
                    <input type="number" name="temperature" step="0.1" min="0" value="{{ old('temperature') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('temperature') border-red-500 @enderror"
                           placeholder="0.0">
                    @error('temperature')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detak Jantung</label>
                    <input type="number" name="heart_rate" min="0" value="{{ old('heart_rate') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('heart_rate') border-red-500 @enderror"
                           placeholder="0">
                    @error('heart_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Services & Drugs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Layanan & Obat</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tambah Layanan</label>
                    <input type="text" x-model="serviceSearch" @focus="showServiceDropdown = true"
                           @click.away="showServiceDropdown = false"
                           placeholder="Cari layanan..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <div x-show="showServiceDropdown && filteredServices.length > 0" x-transition
                         class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="s in filteredServices" :key="s.id">
                            <div @click="addService(s)" class="px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 flex items-center justify-between">
                                <span class="text-sm text-gray-900" x-text="s.name"></span>
                                <span class="text-sm text-gray-500" x-text="'Rp ' + formatCurrency(s.price)"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tambah Obat</label>
                    <input type="text" x-model="drugSearch" @focus="showDrugDropdown = true"
                           @click.away="showDrugDropdown = false"
                           placeholder="Cari obat..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <div x-show="showDrugDropdown && filteredDrugs.length > 0" x-transition
                         class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                        <template x-for="d in filteredDrugs" :key="d.id">
                            <div @click="addDrug(d)" class="px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 flex items-center justify-between">
                                <span class="text-sm text-gray-900" x-text="d.name"></span>
                                <span class="text-sm text-gray-500" x-text="'Rp ' + formatCurrency(d.price_per_unit)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div x-show="items.length > 0">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <th class="pb-2">Jenis</th>
                            <th class="pb-2">Nama</th>
                            <th class="pb-2 text-right">Harga</th>
                            <th class="pb-2 text-center w-24">Jumlah</th>
                            <th class="pb-2 text-right">Subtotal</th>
                            <th class="pb-2 text-center w-16"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-gray-100">
                                <td class="py-2">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full"
                                          :class="item.item_type === 'service' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700'"
                                          x-text="item.item_type === 'service' ? 'Layanan' : 'Obat'"></span>
                                </td>
                                <td class="py-2 text-sm text-gray-900" x-text="item.item_name"></td>
                                <td class="py-2 text-sm text-gray-700 text-right" x-text="'Rp ' + formatCurrency(item.unit_price)"></td>
                                <td class="py-2 text-center">
                                    <input type="number" min="1" :value="item.quantity" @input="updateItemQty(index, $event.target.value)"
                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="py-2 text-sm font-medium text-gray-900 text-right" x-text="'Rp ' + formatCurrency(item.subtotal)"></td>
                                <td class="py-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div x-show="items.length === 0" class="text-center py-8 text-sm text-gray-500">
                Belum ada layanan atau obat ditambahkan. Gunakan kolom pencarian di atas.
            </div>

            <div x-show="items.length > 0" class="mt-4 border-t border-gray-200 pt-4">
                <div class="flex justify-end space-y-1">
                    <div class="text-right w-48">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span x-text="'Rp ' + formatCurrency(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Pajak (11%)</span>
                            <span x-text="'Rp ' + formatCurrency(tax)"></span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-gray-900 border-t border-gray-200 pt-1 mt-1">
                            <span>Total</span>
                            <span x-text="'Rp ' + formatCurrency(total)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dokter.visits.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit" name="action" value="draft"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-lg transition-colors">
                Simpan Draft
            </button>
            <button type="submit" name="action" value="complete"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                Simpan & Selesaikan
            </button>
        </div>
    </form>
</div>
@endsection
