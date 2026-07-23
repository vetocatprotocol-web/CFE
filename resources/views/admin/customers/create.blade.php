@extends('layouts.app')

@section('header', 'Tambah Pelanggan')

@section('content')
<div class="max-w-4xl" x-data="{ loading: false }">

    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Formulir Pelanggan</h2>
            <p class="mt-1 text-sm text-gray-500">Isi data pelanggan baru yang akan ditambahkan ke sistem.</p>
        </div>

        <form method="POST" action="{{ route('admin.customers.store') }}" @submit="loading = true">
            @csrf

            <div class="card-body space-y-8">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Informasi Kontak</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="form-input @error('name') error @enderror"
                                   placeholder="Nama pelanggan">
                            @error('name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                                   class="form-input @error('phone') error @enderror"
                                   placeholder="08xxxxxxxxxx">
                            @error('phone')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-input @error('email') error @enderror"
                           placeholder="email@contoh.com">
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <p class="form-hint">Email untuk pengiriman notifikasi (opsional).</p>
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Alamat</h3>
                    <div class="form-group">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="2"
                                  class="form-textarea @error('address') error @enderror"
                                  placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Kota & Kode Pos</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="city" class="form-label">Kota</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                   class="form-input @error('city') error @enderror"
                                   placeholder="Kota">
                            @error('city')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="postal_code" class="form-label">Kode Pos</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                   class="form-input @error('postal_code') error @enderror"
                                   placeholder="xxxxxx">
                            @error('postal_code')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer flex items-center justify-end gap-3">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Pelanggan'">Simpan Pelanggan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
