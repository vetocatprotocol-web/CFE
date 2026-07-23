@extends('layouts.portal')

@section('title', 'Tambah Hewan Baru')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('portal.pets') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Tambah Hewan Baru</h1>
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form action="#" method="POST">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Hewan <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                           placeholder="Masukkan nama hewan">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="species" class="block text-sm font-medium text-gray-700">Jenis Hewan <span class="text-red-500">*</span></label>
                        <select name="species" id="species" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5">
                            <option value="">Pilih Jenis</option>
                            <option value="Anjing" {{ old('species') === 'Anjing' ? 'selected' : '' }}>Anjing</option>
                            <option value="Kucing" {{ old('species') === 'Kucing' ? 'selected' : '' }}>Kucing</option>
                            <option value="Burung" {{ old('species') === 'Burung' ? 'selected' : '' }}>Burung</option>
                            <option value="Kelinci" {{ old('species') === 'Kelinci' ? 'selected' : '' }}>Kelinci</option>
                            <option value="Hamster" {{ old('species') === 'Hamster' ? 'selected' : '' }}>Hamster</option>
                            <option value="Iguana" {{ old('species') === 'Iguana' ? 'selected' : '' }}>Iguana</option>
                            <option value="Lainnya" {{ old('species') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('species')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="breed" class="block text-sm font-medium text-gray-700">Ras</label>
                        <input type="text" name="breed" id="breed" value="{{ old('breed') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                               placeholder="Contoh: Persia, Golden Retriever">
                        @error('breed')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5">
                        @error('birth_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="weight_kg" class="block text-sm font-medium text-gray-700">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="weight_kg" id="weight_kg" value="{{ old('weight_kg') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                               placeholder="0.0">
                        @error('weight_kg')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="color_marking" class="block text-sm font-medium text-gray-700">Warna / Tanda Khusus</label>
                    <input type="text" name="color_marking" id="color_marking" value="{{ old('color_marking') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                           placeholder="Contoh: Putih dengan bintik hitam">
                    @error('color_marking')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="medical_history_notes" class="block text-sm font-medium text-gray-700">Catatan Riwayat Medis</label>
                    <textarea name="medical_history_notes" id="medical_history_notes" rows="4"
                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                              placeholder="Riwayat alergi, penyakit sebelumnya, vaksinasi, dll.">{{ old('medical_history_notes') }}</textarea>
                    @error('medical_history_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('portal.pets') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Simpan Hewan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
