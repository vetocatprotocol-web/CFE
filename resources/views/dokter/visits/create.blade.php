@extends('layouts.app')

@section('title', 'Buat Kunjungan Baru')
@section('header', 'Buat Kunjungan Baru')

@section('content')
<div x-data="{
    /* ─── Customer Search ─── */
    searchQuery: '',
    searchResults: [],
    selectedCustomer: null,
    selectedPetId: '',
    isSearching: false,
    searchTimeout: null,

    /* ─── Visit Data ─── */
    visitDate: '{{ old('visit_date', now()->format('Y-m-d')) }}',
    visitTime: '{{ old('visit_time', now()->format('H:i')) }}',
    chiefComplaint: '{{ old('chief_complaint') }}',
    diagnosis: '{{ old('diagnosis') }}',
    treatmentNotes: '{{ old('treatment_notes') }}',
    weightKg: '{{ old('weight_kg') }}',
    temperature: '{{ old('temperature') }}',
    heartRate: '{{ old('heart_rate') }}',

    /* ─── Services & Drugs ─── */
    allServices: @json($services),
    allDrugs: @json($drugs),
    activeTab: 'services',
    serviceSearch: '',
    drugSearch: '',
    selectedServices: [],
    addedDrugs: [],

    /* ─── Customer Search ─── */
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

    get selectedPet() {
        if (!this.selectedCustomer || !this.selectedPetId) return null;
        return this.selectedCustomer.pets.find(p => p.id == this.selectedPetId) || null;
    },

    /* ─── Services ─── */
    get filteredServices() {
        const q = this.serviceSearch.toLowerCase();
        if (!q) return this.allServices;
        return this.allServices.filter(s =>
            s.name.toLowerCase().includes(q) ||
            (s.category && s.category.toLowerCase().includes(q))
        );
    },

    toggleService(service) {
        const idx = this.selectedServices.findIndex(s => s.id === service.id);
        if (idx >= 0) {
            this.selectedServices.splice(idx, 1);
        } else {
            this.selectedServices.push({ ...service, quantity: 1 });
        }
    },

    isServiceSelected(service) {
        return this.selectedServices.some(s => s.id === service.id);
    },

    updateServiceQty(service, qty) {
        const s = this.selectedServices.find(s => s.id === service.id);
        if (s) {
            s.quantity = Math.max(1, parseInt(qty) || 1);
        }
    },

    removeService(service) {
        this.selectedServices = this.selectedServices.filter(s => s.id !== service.id);
    },

    /* ─── Drugs ─── */
    get filteredDrugs() {
        const q = this.drugSearch.toLowerCase();
        if (!q) return this.allDrugs;
        return this.allDrugs.filter(d =>
            d.name.toLowerCase().includes(q) ||
            (d.unit && d.unit.toLowerCase().includes(q))
        );
    },

    addDrug(drug) {
        const existing = this.addedDrugs.find(d => d.id === drug.id);
        if (!existing) {
            this.addedDrugs.push({ ...drug, quantity: 1 });
        }
    },

    isDrugAdded(drug) {
        return this.addedDrugs.some(d => d.id === drug.id);
    },

    updateDrugQty(drug, qty) {
        const d = this.addedDrugs.find(d => d.id === drug.id);
        if (d) {
            d.quantity = Math.max(1, parseInt(qty) || 1);
        }
    },

    removeDrug(drug) {
        this.addedDrugs = this.addedDrugs.filter(d => d.id !== drug.id);
    },

    /* ─── All Items Combined ─── */
    get allItems() {
        const svcItems = this.selectedServices.map(s => ({
            item_type: 'service',
            service_id: s.id,
            drug_id: null,
            item_name: s.name,
            unit_price: s.price,
            quantity: s.quantity,
            subtotal: s.price * s.quantity,
        }));
        const drugItems = this.addedDrugs.map(d => ({
            item_type: 'drug',
            service_id: null,
            drug_id: d.id,
            item_name: d.name,
            unit_price: d.price_per_unit,
            quantity: d.quantity,
            subtotal: d.price_per_unit * d.quantity,
        }));
        return [...svcItems, ...drugItems];
    },

    get itemCount() {
        return this.allItems.length;
    },

    get subtotal() {
        return this.allItems.reduce((sum, item) => sum + item.subtotal, 0);
    },

    get tax() {
        return Math.round(this.subtotal * 0.10);
    },

    get total() {
        return this.subtotal + this.tax;
    },

    /* ─── Helpers ─── */
    formatCurrency(val) {
        return new Intl.NumberFormat('id-ID').format(val);
    },

    /* ─── Form Submit ─── */
    get canSubmit() {
        return this.selectedCustomer && this.selectedPetId && this.chiefComplaint.trim().length > 0;
    },

    submitForm(action) {
        const form = this.$refs.visitForm;
        const itemsInput = form.querySelector('[name=\"items\"]');
        itemsInput.value = JSON.stringify(this.allItems);
        const actionInput = form.querySelector('[name=\"action\"]');
        actionInput.value = action;
        form.submit();
    }
}" x-init="$watch('searchQuery', () => searchCustomer())">

    <form x-ref="visitForm" method="POST" action="{{ route('dokter.visits.store') }}">
        @csrf
        <input type="hidden" name="items" value="[]">
        <input type="hidden" name="action" value="">

        <div class="space-y-6">

            {{-- ═══════════════════════════════════════════════════ --}}
            {{-- SECTION 1: Customer & Pet Selection               --}}
            {{-- ═══════════════════════════════════════════════════ --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Data Pelanggan & Hewan</h3>
                            <p class="text-xs text-gray-500">Pilih pelanggan dan hewan yang akan diperiksa</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Customer Search --}}
                    <div class="relative" x-show="!selectedCustomer">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Cari Pelanggan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   x-model="searchQuery"
                                   placeholder="Ketik nama atau nomor telepon pelanggan..."
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                                   autocomplete="off">
                            <div x-show="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="w-4 h-4 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Search Results Dropdown --}}
                        <div x-show="searchResults.length > 0" @click.away="searchResults = []"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute z-30 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto">
                            <template x-for="c in searchResults" :key="c.id">
                                <div @click="selectCustomer(c)"
                                     class="px-4 py-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                                             x-text="c.name.charAt(0).toUpperCase()"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate" x-text="c.name"></p>
                                            <p class="text-xs text-gray-500" x-text="c.phone || 'Tidak ada telepon'"></p>
                                        </div>
                                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full flex-shrink-0"
                                              x-text="c.pets.length + ' hewan'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Selected Customer Display --}}
                    <div x-show="selectedCustomer" x-cloak>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pelanggan Terpilih</label>
                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl p-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold flex-shrink-0"
                                     x-text="selectedCustomer ? selectedCustomer.name.charAt(0).toUpperCase() : ''"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-sm font-bold text-gray-900" x-text="selectedCustomer ? selectedCustomer.name : ''"></p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">AKTIF</span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs text-gray-600">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span x-text="selectedCustomer ? selectedCustomer.phone || '-' : '-'"></span>
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span x-text="selectedCustomer ? (selectedCustomer.email || '-') : '-'"></span>
                                        </span>
                                    </div>
                                </div>
                                <button type="button" @click="clearCustomer()"
                                        class="text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg p-1.5 transition-colors flex-shrink-0"
                                        title="Ganti pelanggan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="customer_id" :value="selectedCustomer ? selectedCustomer.id : ''">
                        @error('customer_id')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Pet Selection --}}
                    <div x-show="selectedCustomer && selectedCustomer.pets && selectedCustomer.pets.length > 0" x-cloak>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pilih Hewan <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <template x-for="pet in (selectedCustomer ? selectedCustomer.pets : [])" :key="pet.id">
                                <label @click="selectedPetId = pet.id"
                                       class="relative cursor-pointer">
                                    <input type="radio" name="pet_id" :value="pet.id" x-model="selectedPetId" class="sr-only peer">
                                    <div class="border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-3 transition-all hover:border-gray-300">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-300 to-orange-400 flex items-center justify-center text-white flex-shrink-0"
                                                 :class="pet.species === 'Kucing' ? 'from-pink-300 to-rose-400' : (pet.species === 'Anjing' ? 'from-amber-300 to-orange-400' : 'from-emerald-300 to-teal-400')">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4.5 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm5-4a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm5 0a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm5 4a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zM12 22c-4.97 0-9-2.69-9-6 0-2.22 1.53-4.15 3.75-5.16C7.97 9.62 10 8.5 12 8.5s4.03 1.12 5.25 2.34C19.47 11.85 21 13.78 21 16c0 3.31-4.03 6-9 6z"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate" x-text="pet.name"></p>
                                                <p class="text-xs text-gray-500" x-text="pet.species + (pet.breed ? ' - ' + pet.breed : '')"></p>
                                            </div>
                                            <div class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 flex items-center justify-center transition-colors">
                                                <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                        @error('pet_id')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- No Customer Selected Hint --}}
                    <div x-show="!selectedCustomer" class="text-center py-4">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm text-gray-400">Gunakan kolom pencarian di atas untuk memilih pelanggan</p>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════ --}}
            {{-- SECTION 2: Visit Information                      --}}
            {{-- ═══════════════════════════════════════════════════ --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Informasi Kunjungan</h3>
                            <p class="text-xs text-gray-500">Detail keluhan, diagnosis, dan vital signs</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Date & Time Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                            <input type="date" name="visit_date" x-model="visitDate"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Waktu</label>
                            <input type="time" name="visit_time" x-model="visitTime"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                    </div>

                    {{-- Two Column: Complaint & Diagnosis --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Keluhan Utama <span class="text-red-500">*</span></label>
                            <textarea name="chief_complaint" rows="3" x-model="chiefComplaint" required
                                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none @error('chief_complaint') border-red-400 @enderror"
                                      placeholder="Deskripsikan keluhan utama pasien..."></textarea>
                            @error('chief_complaint')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Diagnosis</label>
                            <textarea name="diagnosis" rows="3" x-model="diagnosis"
                                      class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none @error('diagnosis') border-red-400 @enderror"
                                      placeholder="Diagnosis awal..."></textarea>
                            @error('diagnosis')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Vital Signs --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Vital Signs</label>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <div class="relative">
                                    <input type="number" name="weight_kg" step="0.1" min="0" x-model="weightKg"
                                           class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('weight_kg') border-red-400 @enderror"
                                           placeholder="0.0">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 pointer-events-none">kg</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Berat</p>
                                @error('weight_kg')
                                    <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <div class="relative">
                                    <input type="number" name="temperature" step="0.1" min="0" x-model="temperature"
                                           class="w-full px-3 py-2.5 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('temperature') border-red-400 @enderror"
                                           placeholder="0.0">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 pointer-events-none">&deg;C</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Suhu</p>
                                @error('temperature')
                                    <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <div class="relative">
                                    <input type="number" name="heart_rate" min="0" x-model="heartRate"
                                           class="w-full px-3 py-2.5 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('heart_rate') border-red-400 @enderror"
                                           placeholder="0">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 pointer-events-none">bpm</span>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Detak Jantung</p>
                                @error('heart_rate')
                                    <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Treatment Notes --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Catatan Perawatan</label>
                        <textarea name="treatment_notes" rows="2" x-model="treatmentNotes"
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-none @error('treatment_notes') border-red-400 @enderror"
                                  placeholder="Catatan perawatan, rencana tindak lanjut..."></textarea>
                        @error('treatment_notes')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════ --}}
            {{-- SECTION 3: Services & Drugs (Tabs)               --}}
            {{-- ═══════════════════════════════════════════════════ --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Tindakan & Obat</h3>
                            <p class="text-xs text-gray-500">Pilih layanan tindakan dan obat yang diberikan</p>
                        </div>
                    </div>
                </div>

                {{-- Tabs --}}
                <div class="border-b border-gray-200">
                    <div class="flex">
                        <button type="button" @click="activeTab = 'services'"
                                class="flex-1 px-6 py-3 text-sm font-medium transition-colors relative"
                                :class="activeTab === 'services' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700'">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Tindakan
                                <span x-show="selectedServices.length > 0"
                                      class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700"
                                      x-text="selectedServices.length"></span>
                            </span>
                            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 transition-all" x-show="activeTab === 'services'"></div>
                        </button>
                        <button type="button" @click="activeTab = 'drugs'"
                                class="flex-1 px-6 py-3 text-sm font-medium transition-colors relative"
                                :class="activeTab === 'drugs' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700'">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                Obat
                                <span x-show="addedDrugs.length > 0"
                                      class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700"
                                      x-text="addedDrugs.length"></span>
                            </span>
                            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-indigo-600 transition-all" x-show="activeTab === 'drugs'"></div>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    {{-- ═══ Services Tab ═══ --}}
                    <div x-show="activeTab === 'services'" x-cloak>
                        {{-- Search --}}
                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" x-model="serviceSearch"
                                   placeholder="Cari tindakan berdasarkan nama atau kategori..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        {{-- Service List --}}
                        <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100 mb-4">
                            <template x-for="s in filteredServices" :key="s.id">
                                <div @click="toggleService(s)"
                                     class="flex items-center justify-between px-4 py-3 cursor-pointer transition-colors"
                                     :class="isServiceSelected(s) ? 'bg-indigo-50 hover:bg-indigo-100' : 'hover:bg-gray-50'">
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors flex-shrink-0"
                                             :class="isServiceSelected(s) ? 'bg-indigo-500 border-indigo-500' : 'border-gray-300'">
                                            <svg x-show="isServiceSelected(s)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900" x-text="s.name"></p>
                                            <span x-show="s.category"
                                                  class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 mt-0.5"
                                                  x-text="s.category"></span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 flex-shrink-0" x-text="'Rp ' + formatCurrency(s.price)"></span>
                                </div>
                            </template>
                            <div x-show="filteredServices.length === 0" class="px-4 py-8 text-center">
                                <p class="text-sm text-gray-400">Tidak ada tindakan ditemukan</p>
                            </div>
                        </div>

                        {{-- Selected Services Summary --}}
                        <div x-show="selectedServices.length > 0">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tindakan Terpilih</h4>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                                            <th class="px-4 py-2">Nama</th>
                                            <th class="px-4 py-2 text-center w-20">Qty</th>
                                            <th class="px-4 py-2 text-right w-28">Harga</th>
                                            <th class="px-4 py-2 text-right w-28">Subtotal</th>
                                            <th class="px-4 py-2 w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="(s, i) in selectedServices" :key="s.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2.5 text-sm font-medium text-gray-900" x-text="s.name"></td>
                                                <td class="px-4 py-2.5 text-center">
                                                    <input type="number" min="1" :value="s.quantity"
                                                           @input="updateServiceQty(s, $event.target.value)"
                                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:ring-2 focus:ring-indigo-500">
                                                </td>
                                                <td class="px-4 py-2.5 text-sm text-gray-600 text-right" x-text="'Rp ' + formatCurrency(s.price)"></td>
                                                <td class="px-4 py-2.5 text-sm font-semibold text-gray-900 text-right" x-text="'Rp ' + formatCurrency(s.price * s.quantity)"></td>
                                                <td class="px-4 py-2.5 text-center">
                                                    <button type="button" @click="removeService(s)" class="text-gray-400 hover:text-red-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div x-show="selectedServices.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm text-gray-400">Klik tindakan dari daftar di atas untuk menambahkannya</p>
                        </div>
                    </div>

                    {{-- ═══ Drugs Tab ═══ --}}
                    <div x-show="activeTab === 'drugs'" x-cloak>
                        {{-- Search --}}
                        <div class="relative mb-4">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" x-model="drugSearch"
                                   placeholder="Cari obat berdasarkan nama atau satuan..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        {{-- Drug List --}}
                        <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-100 mb-4">
                            <template x-for="d in filteredDrugs" :key="d.id">
                                <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900" x-text="d.name"></p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span x-show="d.unit" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-700" x-text="d.unit"></span>
                                            <span class="text-xs text-gray-500" x-text="'Rp ' + formatCurrency(d.price_per_unit) + '/unit'"></span>
                                        </div>
                                    </div>
                                    <button type="button" @click="addDrug(d)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                                            :class="isDrugAdded(d) ? 'bg-green-100 text-green-700 cursor-default' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'">
                                        <template x-if="!isDrugAdded(d)">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Tambah
                                            </span>
                                        </template>
                                        <template x-if="isDrugAdded(d)">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Ditambahkan
                                            </span>
                                        </template>
                                    </button>
                                </div>
                            </template>
                            <div x-show="filteredDrugs.length === 0" class="px-4 py-8 text-center">
                                <p class="text-sm text-gray-400">Tidak ada obat ditemukan</p>
                            </div>
                        </div>

                        {{-- Added Drugs Summary --}}
                        <div x-show="addedDrugs.length > 0">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Obat Ditambahkan</h4>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                                            <th class="px-4 py-2">Nama</th>
                                            <th class="px-4 py-2 text-center w-20">Qty</th>
                                            <th class="px-4 py-2 text-right w-28">Harga/Unit</th>
                                            <th class="px-4 py-2 text-right w-28">Subtotal</th>
                                            <th class="px-4 py-2 w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-for="(d, i) in addedDrugs" :key="d.id">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2.5 text-sm font-medium text-gray-900" x-text="d.name"></td>
                                                <td class="px-4 py-2.5 text-center">
                                                    <input type="number" min="1" :value="d.quantity"
                                                           @input="updateDrugQty(d, $event.target.value)"
                                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-sm text-center focus:ring-2 focus:ring-indigo-500">
                                                </td>
                                                <td class="px-4 py-2.5 text-sm text-gray-600 text-right" x-text="'Rp ' + formatCurrency(d.price_per_unit)"></td>
                                                <td class="px-4 py-2.5 text-sm font-semibold text-gray-900 text-right" x-text="'Rp ' + formatCurrency(d.price_per_unit * d.quantity)"></td>
                                                <td class="px-4 py-2.5 text-center">
                                                    <button type="button" @click="removeDrug(d)" class="text-gray-400 hover:text-red-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div x-show="addedDrugs.length === 0" class="text-center py-6 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            <p class="text-sm text-gray-400">Klik "Tambah" pada obat di daftar untuk menambahkannya</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════ --}}
            {{-- SUMMARY CARD                                       --}}
            {{-- ═══════════════════════════════════════════════════ --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Ringkasan</h3>
                            <p class="text-xs text-gray-500">Total item dan rincian biaya</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex flex-col sm:flex-row justify-between gap-6">
                        {{-- Left: Stats --}}
                        <div class="flex gap-6">
                            <div class="text-center">
                                <div class="w-14 h-14 rounded-xl bg-indigo-100 flex items-center justify-center mx-auto mb-1.5">
                                    <span class="text-lg font-bold text-indigo-600" x-text="itemCount"></span>
                                </div>
                                <p class="text-[11px] text-gray-500 font-medium">Total Item</p>
                            </div>
                            <div class="text-center">
                                <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center mx-auto mb-1.5">
                                    <span class="text-lg font-bold text-blue-600" x-text="selectedServices.length"></span>
                                </div>
                                <p class="text-[11px] text-gray-500 font-medium">Tindakan</p>
                            </div>
                            <div class="text-center">
                                <div class="w-14 h-14 rounded-xl bg-purple-100 flex items-center justify-center mx-auto mb-1.5">
                                    <span class="text-lg font-bold text-purple-600" x-text="addedDrugs.length"></span>
                                </div>
                                <p class="text-[11px] text-gray-500 font-medium">Obat</p>
                            </div>
                        </div>

                        {{-- Right: Totals --}}
                        <div class="sm:text-right min-w-[200px]">
                            <div class="flex justify-between sm:justify-end sm:gap-8 text-sm text-gray-600 mb-1">
                                <span>Subtotal</span>
                                <span class="font-medium" x-text="'Rp ' + formatCurrency(subtotal)"></span>
                            </div>
                            <div class="flex justify-between sm:justify-end sm:gap-8 text-sm text-gray-600 mb-2">
                                <span>Pajak (10%)</span>
                                <span class="font-medium" x-text="'Rp ' + formatCurrency(tax)"></span>
                            </div>
                            <div class="flex justify-between sm:justify-end sm:gap-8 text-base font-bold text-gray-900 border-t border-gray-200 pt-2">
                                <span>Total</span>
                                <span class="text-indigo-600" x-text="'Rp ' + formatCurrency(total)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-100">
                        <a href="{{ route('dokter.visits.index') }}"
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Batal
                        </a>
                        <button type="button" @click="submitForm('draft')"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Simpan Draft
                        </button>
                        <button type="button" @click="submitForm('complete')"
                                :disabled="!canSubmit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Selesai & Buat Invoice
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection
