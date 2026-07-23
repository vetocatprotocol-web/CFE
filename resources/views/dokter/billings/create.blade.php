@extends('layouts.app')

@section('title', 'Buat Billing Baru')
@section('header-title', 'Buat Billing Baru')

@section('content')
<div x-data="{
    searchQuery: '',
    searchResults: [],
    selectedCustomer: null,
    selectedPetId: '',
    isSearching: false,
    searchTimeout: null,

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
    }
}" x-init="$watch('searchQuery', () => searchCustomer())">

    <form method="POST" action="{{ route('dokter.billings.store') }}" class="space-y-6 max-w-2xl">
        @csrf

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

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm @error('notes') border-red-500 @enderror"
                          placeholder="Catatan billing (opsional)...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dokter.billings.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                Buat Billing
            </button>
        </div>
    </form>
</div>
@endsection
