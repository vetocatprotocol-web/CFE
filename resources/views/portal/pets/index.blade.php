@extends('layouts.portal')

@section('title', 'My Pets')

@section('content')
<div x-data="{ showCreate: false }">

    {{-- Create Pet Modal --}}
    <div x-show="showCreate" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showCreate = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="showCreate" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="relative inline-block w-full max-w-lg p-6 text-left align-bottom bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl sm:align-middle" @click.away="showCreate = false">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Tambah Hewan Baru</h2>
                    <button @click="showCreate = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                   class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm border p-3 transition-colors"
                                   placeholder="Nama hewan">
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="species" class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis <span class="text-red-500">*</span></label>
                                <select name="species" id="species" required
                                        class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm border p-3 transition-colors">
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
                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="breed" class="block text-sm font-semibold text-gray-700 mb-1.5">Ras</label>
                                <input type="text" name="breed" id="breed"
                                       class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm border p-3 transition-colors"
                                       placeholder="Contoh: Persia">
                                @error('breed')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Lahir</label>
                                <input type="date" name="birth_date" id="birth_date"
                                       class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm border p-3 transition-colors">
                                @error('birth_date')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="weight_kg" class="block text-sm font-semibold text-gray-700 mb-1.5">Berat (kg)</label>
                                <input type="number" step="0.1" name="weight_kg" id="weight_kg"
                                       class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm border p-3 transition-colors"
                                       placeholder="0.0">
                                @error('weight_kg')
                                    <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="color_marking" class="block text-sm font-semibold text-gray-700 mb-1.5">Warna / Tanda Khusus</label>
                            <input type="text" name="color_marking" id="color_marking"
                                   class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm border p-3 transition-colors"
                                   placeholder="Contoh: Putih dengan bintik hitam">
                            @error('color_marking')
                                <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="medical_history_notes" class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan Riwayat Medis</label>
                            <textarea name="medical_history_notes" id="medical_history_notes" rows="3"
                                      class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm border p-3 transition-colors resize-none"
                                      placeholder="Riwayat alergi, penyakit, dll."></textarea>
                            @error('medical_history_notes')
                                <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="showCreate = false"
                                class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 active:bg-blue-800 shadow-sm shadow-blue-500/20 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Button --}}
    <div class="mb-4">
        <button @click="showCreate = true"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 active:bg-blue-800 shadow-sm shadow-blue-500/20 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Hewan
        </button>
    </div>

    {{-- Pet Grid --}}
    @if($pets->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($pets as $pet)
                <a href="{{ route('portal.pets.show', $pet) }}"
                   class="group rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden active:scale-[0.99]">
                    {{-- Photo Placeholder --}}
                    <div class="relative h-36 w-full">
                        @if($pet->species === 'Kucing')
                            <div class="h-full w-full bg-gradient-to-br from-orange-100 via-amber-50 to-orange-50 flex items-center justify-center">
                                <span class="text-5xl group-hover:scale-110 transition-transform duration-300">🐱</span>
                            </div>
                        @elseif($pet->species === 'Anjing')
                            <div class="h-full w-full bg-gradient-to-br from-blue-100 via-sky-50 to-blue-50 flex items-center justify-center">
                                <span class="text-5xl group-hover:scale-110 transition-transform duration-300">🐶</span>
                            </div>
                        @elseif($pet->species === 'Burung')
                            <div class="h-full w-full bg-gradient-to-br from-yellow-100 via-amber-50 to-yellow-50 flex items-center justify-center">
                                <span class="text-5xl group-hover:scale-110 transition-transform duration-300">🐦</span>
                            </div>
                        @elseif($pet->species === 'Kelinci')
                            <div class="h-full w-full bg-gradient-to-br from-pink-100 via-rose-50 to-pink-50 flex items-center justify-center">
                                <span class="text-5xl group-hover:scale-110 transition-transform duration-300">🐰</span>
                            </div>
                        @elseif($pet->species === 'Hamster')
                            <div class="h-full w-full bg-gradient-to-br from-amber-100 via-orange-50 to-amber-50 flex items-center justify-center">
                                <span class="text-5xl group-hover:scale-110 transition-transform duration-300">🐹</span>
                            </div>
                        @elseif($pet->species === 'Iguana')
                            <div class="h-full w-full bg-gradient-to-br from-green-100 via-emerald-50 to-green-50 flex items-center justify-center">
                                <span class="text-5xl group-hover:scale-110 transition-transform duration-300">🦎</span>
                            </div>
                        @else
                            <div class="h-full w-full bg-gradient-to-br from-gray-100 to-slate-50 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                        @endif
                        {{-- Species badge --}}
                        <div class="absolute top-3 right-3 rounded-lg bg-white/90 backdrop-blur-sm px-2.5 py-1 shadow-sm">
                            <span class="text-xs font-semibold text-gray-700">{{ $pet->species }}</span>
                        </div>
                    </div>
                    {{-- Info --}}
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="min-w-0">
                                <h3 class="font-bold text-gray-900 text-base">{{ $pet->name }}</h3>
                                @if($pet->breed)
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $pet->breed }}</p>
                                @endif
                            </div>
                            <svg class="h-5 w-5 flex-shrink-0 text-gray-300 group-hover:text-blue-500 transition-colors mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
                            @if($pet->birth_date)
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $pet->birth_date->age }} thn</span>
                                </div>
                            @endif
                            @if($pet->weight_kg)
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l3 9a5.002 5.002 0 01-6.001 0M18 7l-3 9m-6-2v6m6-12l3-1m-3 1l-3 9"/>
                                    </svg>
                                    <span>{{ $pet->weight_kg }} kg</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="rounded-2xl bg-white border border-gray-100 p-12 text-center shadow-sm">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-gray-50">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-bold text-gray-900">Belum ada hewan peliharaan</h3>
            <p class="mt-1.5 text-sm text-gray-500">Mulai dengan mendaftarkan hewan peliharaan Anda.</p>
            <button @click="showCreate = true"
                    class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 active:bg-blue-800 shadow-sm shadow-blue-500/20 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Hewan Pertama
            </button>
        </div>
    @endif
</div>
@endsection
