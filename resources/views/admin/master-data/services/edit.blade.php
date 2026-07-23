@extends('layouts.app')

@section('header', 'Edit Layanan')

@section('content')
<div class="max-w-4xl" x-data="{ loading: false }">

    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Edit Layanan: {{ $service->name }}</h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui data layanan yang sudah ada.</p>
        </div>

        <form method="POST" action="{{ route('admin.services.update', $service) }}" @submit="loading = true">
            @csrf
            @method('PUT')

            <div class="card-body space-y-8">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Nama Layanan <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" required
                                   class="form-input @error('name') error @enderror"
                                   placeholder="Contoh: Konsultasi Umum">
                            @error('name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category" class="form-label">Kategori <span class="text-red-500">*</span></label>
                            <select name="category" id="category" required
                                    class="form-select @error('category') error @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach(['Konsultasi', 'Vaksin', 'Grooming', 'Operasi', 'Lab', 'Perawatan'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $service->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Deskripsi</h3>
                    <div class="form-group">
                        <label for="description" class="form-label">Deskripsi Layanan</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-textarea @error('description') error @enderror"
                                  placeholder="Deskripsi singkat layanan...">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">Deskripsi opsional untuk menjelaskan detail layanan.</p>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Harga</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="price" class="form-label">Harga (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="price" id="price" value="{{ old('price', $service->price) }}" required min="0" step="500"
                                   class="form-input @error('price') error @enderror"
                                   placeholder="0">
                            @error('price')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Masukkan harga dalam Rupiah, kelipatan Rp 500.</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer flex items-center justify-end gap-3">
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="loading ? 'Memperbarui...' : 'Perbarui Layanan'">Perbarui Layanan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
