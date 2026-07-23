@extends('layouts.app')

@section('header', 'Tambah Pengguna')

@section('content')
<div class="max-w-4xl" x-data="{ loading: false }">

    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold text-gray-900">Formulir Pengguna</h2>
            <p class="mt-1 text-sm text-gray-500">Isi data pengguna baru yang akan ditambahkan ke sistem.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" @submit="loading = true">
            @csrf

            <div class="card-body space-y-8">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Informasi Akun</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="form-input @error('name') error @enderror"
                                   placeholder="Nama pengguna">
                            @error('name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="form-input @error('email') error @enderror"
                                   placeholder="email@contoh.com">
                            @error('email')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="form-input @error('phone') error @enderror"
                           placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Keamanan</h3>
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password"
                               class="form-input @error('password') error @enderror"
                               placeholder="Kosongkan untuk password sementara">
                        @error('password')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">Kosongkan jika ingin menggunakan password sementara otomatis.</p>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-4">Role & Akses</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="role_id" class="form-label">Role <span class="text-red-500">*</span></label>
                            <select name="role_id" id="role_id" required
                                    class="form-select @error('role_id') error @enderror">
                                <option value="">Pilih Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <p class="form-hint">Role menentukan hak akses pengguna di sistem.</p>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card-footer flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" :disabled="loading">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="loading ? 'Menyimpan...' : 'Simpan Pengguna'">Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
