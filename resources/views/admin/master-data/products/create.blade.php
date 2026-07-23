@extends('layouts.app')

@section('header', 'Buat Produk Baru')

@section('content')
<div class="max-w-4xl" x-data="{ loading: false }">

    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Formulir Produk</h2>
            <p class="mt-1 text-sm text-gray-500">Isi data produk baru yang akan ditambahkan ke sistem.</p>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" @submit="loading = true">
            @csrf

            <div class="card-body space-y-8">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="form-input @error('name') error @enderror"
                                   placeholder="Contoh: Royal Canin Cat Food 3kg">
                            @error('name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id" class="form-label">Kategori <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id" required
                                    class="form-select @error('category_id') error @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Deskripsi</h3>
                    <div class="form-group">
                        <label for="description" class="form-label">Deskripsi Produk</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-textarea @error('description') error @enderror"
                                  placeholder="Deskripsi produk...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">Deskripsi opsional untuk menjelaskan detail produk.</p>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Harga & Barcode</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="price" class="form-label">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0" step="500"
                                   class="form-input @error('price') error @enderror"
                                   placeholder="0">
                            @error('price')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Harga jual produk dalam Rupiah.</p>
                        </div>

                        <div class="form-group">
                            <label for="barcode" class="form-label">Barcode</label>
                            <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                                   class="form-input @error('barcode') error @enderror"
                                   placeholder="Opsional">
                            @error('barcode')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Barcode produk (opsional).</p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Stok</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="current_stock" class="form-label">Stok Saat Ini <span class="text-red-500">*</span></label>
                            <input type="number" name="current_stock" id="current_stock" value="{{ old('current_stock', 0) }}" required min="0"
                                   class="form-input @error('current_stock') error @enderror"
                                   placeholder="0">
                            @error('current_stock')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Jumlah stok produk saat ini.</p>
                        </div>

                        <div class="form-group">
                            <label for="reorder_point" class="form-label">Titik Reorder <span class="text-red-500">*</span></label>
                            <input type="number" name="reorder_point" id="reorder_point" value="{{ old('reorder_point', 0) }}" required min="0"
                                   class="form-input @error('reorder_point') error @enderror"
                                   placeholder="0">
                            @error('reorder_point')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Batas minimum stok sebelum perlu restok.</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer flex items-center justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Produk'">Simpan Produk</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
