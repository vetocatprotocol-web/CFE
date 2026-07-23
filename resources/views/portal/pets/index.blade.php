@extends('layouts.portal')

@section('title', 'My Pets')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900">Hewan Peliharaan Saya</h1>
        <a href="{{ route('portal.pets') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Hewan
        </a>
    </div>
@endsection

@section('content')
<div x-data="{ showCreate: false }">

    {{-- Create Pet Modal --}}
    <div x-show="showCreate" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showCreate = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block w-full max-w-lg p-6 my-8 text-left align-bottom bg-white rounded-2xl shadow-xl sm:align-middle" @click.away="showCreate = false">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Tambah Hewan Baru</h2>
                    <button @click="showCreate = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                                   placeholder="Nama hewan">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="species" class="block text-sm font-medium text-gray-700">Jenis <span class="text-red-500">*</span></label>
                                <select name="species" id="species" required
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5">
                                    <option value="">Pilih Jenis</option>
                                    <option value="Anjing">Anjing</option>
                                    <option value="Kucing">Kucing</option>
                                    <option value="Burung">Burung</option>
                                    <option value="Kelinci">Kelinci</option>
                                    <option value="Hamster">Hamster</option>
                                    <option value="Iguana">Iguana</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                @error('species')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="breed" class="block text-sm font-medium text-gray-700">Ras</label>
                                <input type="text" name="breed" id="breed"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                                       placeholder="Contoh: Persia">
                                @error('breed')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                <input type="date" name="birth_date" id="birth_date"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5">
                                @error('birth_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="weight_kg" class="block text-sm font-medium text-gray-700">Berat (kg)</label>
                                <input type="number" step="0.1" name="weight_kg" id="weight_kg"
                                       class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                                       placeholder="0.0">
                                @error('weight_kg')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="color_marking" class="block text-sm font-medium text-gray-700">Warna / Tanda Khusus</label>
                            <input type="text" name="color_marking" id="color_marking"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                                   placeholder="Contoh: Putih dengan bintik hitam">
                            @error('color_marking')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="medical_history_notes" class="block text-sm font-medium text-gray-700">Catatan Riwayat Medis</label>
                            <textarea name="medical_history_notes" id="medical_history_notes" rows="3"
                                      class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5"
                                      placeholder="Riwayat alergi, penyakit, dll."></textarea>
                            @error('medical_history_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="showCreate = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Trigger for Modal --}}
    <div x-init="$watch('showCreate', v => { if(v) $el.querySelector('[x-ref=openBtn]')?.click() })"></div>

    {{-- Pet Grid --}}
    @if($pets->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($pets as $pet)
                <a href="{{ route('portal.pets.show', $pet) }}"
                   class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow block">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            @if($pet->species === 'Kucing')
                                <span class="text-2xl">🐱</span>
                            @elseif($pet->species === 'Anjing')
                                <span class="text-2xl">🐶</span>
                            @elseif($pet->species === 'Burung')
                                <span class="text-2xl">🐦</span>
                            @elseif($pet->species === 'Kelinci')
                                <span class="text-2xl">🐰</span>
                            @elseif($pet->species === 'Hamster')
                                <span class="text-2xl">🐹</span>
                            @elseif($pet->species === 'Iguana')
                                <span class="text-2xl">🦎</span>
                            @else
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900">{{ $pet->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $pet->species }}{{ $pet->breed ? ' - ' . $pet->breed : '' }}</p>
                            <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-400">
                                @if($pet->birth_date)
                                    <span>{{ $pet->birth_date->diffForHumans(['parts' => 2], true) }}</span>
                                @endif
                                @if($pet->weight_kg)
                                    <span>{{ $pet->weight_kg }} kg</span>
                                @endif
                                @if($pet->color_marking)
                                    <span class="truncate max-w-[120px]">{{ $pet->color_marking }}</span>
                                @endif
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada hewan peliharaan</h3>
            <p class="mt-1 text-sm text-gray-500">Mulai dengan mendaftarkan hewan peliharaan Anda.</p>
            <button @click="showCreate = true"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Hewan Pertama
            </button>
        </div>
    @endif
</div>
@endsection
