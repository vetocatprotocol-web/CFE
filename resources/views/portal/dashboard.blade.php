@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-5">

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-blue-600 to-blue-500 p-6 text-white shadow-lg shadow-blue-500/20">
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-1">
                <span class="text-2xl">🐾</span>
                <h1 class="text-2xl font-bold tracking-tight">Halo, {{ Auth::user()->name }}!</h1>
            </div>
            <p class="text-sm text-blue-100/90 mt-1">Selamat datang di Haland PetCare Portal</p>
        </div>
    </div>

    {{-- Unpaid Invoices Alert --}}
    @if($unpaidInvoices->count() > 0)
        <div class="rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/60 p-4 shadow-sm" x-data="{ open: true }" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5 flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100">
                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-amber-900">Tagihan Belum Dibayar</h3>
                    <div class="mt-2 space-y-2">
                        @foreach($unpaidInvoices as $invoice)
                            <div class="flex items-center justify-between gap-3 rounded-lg bg-white/60 px-3 py-2">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">Invoice #{{ $invoice->invoice_number }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($invoice->pet) {{ $invoice->pet->name }} &mdash; @endif
                                        Rp {{ number_format($invoice->total, 0, ',', '.') }}
                                    </p>
                                </div>
                                <a href="{{ route('portal.invoices.show', $invoice) }}" class="flex-shrink-0 inline-flex items-center gap-1 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-amber-700 active:bg-amber-800 transition-colors">
                                    Bayar
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button @click="open = false" class="flex-shrink-0 rounded-lg p-1 text-amber-400 hover:text-amber-600 hover:bg-amber-100 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('portal.pets') }}" class="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 active:scale-[0.98]">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-100 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-900">Hewan Saya</p>
            <p class="text-xs text-gray-500">{{ $pets->count() }} terdaftar</p>
        </a>

        <a href="{{ route('portal.visits') }}" class="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 active:scale-[0.98]">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-100 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-900">Kunjungan</p>
            <p class="text-xs text-gray-500">{{ $recentVisits->count() }} terakhir</p>
        </a>

        <a href="{{ route('portal.invoices') }}" class="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 active:scale-[0.98]">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-900">Invoice</p>
            @if($unpaidInvoices->count() > 0)
                <p class="text-xs text-red-500 font-medium">{{ $unpaidInvoices->count() }} belum dibayar</p>
            @else
                <p class="text-xs text-gray-500">Semua lunas</p>
            @endif
        </a>

        <a href="{{ route('portal.profile.edit') }}" class="group relative overflow-hidden rounded-2xl bg-white p-4 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200 active:scale-[0.98]">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-gray-600 group-hover:bg-gray-200 transition-colors">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <p class="mt-3 text-sm font-semibold text-gray-900">Profil</p>
            <p class="text-xs text-gray-500">Akun saya</p>
        </a>
    </div>

    {{-- My Pets --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Hewan Peliharaan</h2>
            <a href="{{ route('portal.pets') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 active:text-blue-900 transition-colors">
                Lihat Semua
            </a>
        </div>

        @if($pets->count() > 0)
            <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 snap-x snap-mandatory scrollbar-hide">
                @foreach($pets as $pet)
                    <a href="{{ route('portal.pets.show', $pet) }}"
                       class="snap-start flex-shrink-0 w-40 rounded-2xl bg-white border border-gray-100 p-4 shadow-sm hover:shadow-md transition-all duration-200 active:scale-[0.98]">
                        <div class="relative h-14 w-14 mb-3">
                            @if($pet->species === 'Kucing')
                                <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-orange-100 to-amber-100 flex items-center justify-center text-2xl">🐱</div>
                            @elseif($pet->species === 'Anjing')
                                <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-blue-100 to-sky-100 flex items-center justify-center text-2xl">🐶</div>
                            @elseif($pet->species === 'Burung')
                                <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-yellow-100 to-amber-100 flex items-center justify-center text-2xl">🐦</div>
                            @elseif($pet->species === 'Kelinci')
                                <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-pink-100 to-rose-100 flex items-center justify-center text-2xl">🐰</div>
                            @elseif($pet->species === 'Hamster')
                                <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center text-2xl">🐹</div>
                            @elseif($pet->species === 'Iguana')
                                <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center text-2xl">🦎</div>
                            @else
                                <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-gray-100 to-slate-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $pet->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $pet->species }}</p>
                        @if($pet->breed)
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ $pet->breed }}</p>
                        @endif
                        @if($pet->weight_kg)
                            <p class="text-xs text-gray-400 mt-1">{{ $pet->weight_kg }} kg</p>
                        @endif
                    </a>
                @endforeach

                <a href="{{ route('portal.pets') }}"
                   class="snap-start flex-shrink-0 w-40 rounded-2xl border-2 border-dashed border-gray-200 p-4 hover:border-blue-300 hover:bg-blue-50/50 transition-all duration-200 flex flex-col items-center justify-center min-h-[160px]">
                    <div class="h-12 w-12 rounded-2xl bg-gray-100 flex items-center justify-center mb-2 group-hover:bg-blue-100 transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-500">Tambah Hewan</span>
                </a>
            </div>
        @else
            <div class="rounded-2xl bg-white border border-gray-100 p-8 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <p class="mt-3 text-sm font-medium text-gray-600">Belum ada hewan peliharaan</p>
                <a href="{{ route('portal.pets') }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800">
                    Tambah Sekarang
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>

    {{-- Recent Visits --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Kunjungan Terakhir</h2>
            <a href="{{ route('portal.visits') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 active:text-blue-900 transition-colors">
                Lihat Semua
            </a>
        </div>

        @if($recentVisits->count() > 0)
            <div class="space-y-3">
                @foreach($recentVisits as $visit)
                    <a href="{{ route('portal.visits.show', $visit) }}"
                       class="block rounded-2xl bg-white border border-gray-100 p-4 shadow-sm hover:shadow-md transition-all duration-200 active:scale-[0.99]">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900">{{ $visit->visit_number }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold
                                        @if($visit->status === 'COMPLETED') bg-emerald-50 text-emerald-700
                                        @elseif($visit->status === 'IN_PROGRESS') bg-amber-50 text-amber-700
                                        @else bg-gray-50 text-gray-600 @endif">
                                        @if($visit->status === 'COMPLETED') Selesai
                                        @elseif($visit->status === 'IN_PROGRESS') Dalam Proses
                                        @else {{ $visit->status }} @endif
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500">{{ $visit->visit_date->format('d M Y') }}</span>
                                    @if($visit->pet)
                                        <span class="text-xs text-gray-400">&bull;</span>
                                        <span class="text-xs text-gray-500 font-medium">{{ $visit->pet->name }}</span>
                                    @endif
                                </div>
                                @if($visit->diagnosis)
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2 leading-relaxed">{{ $visit->diagnosis }}</p>
                                @endif
                            </div>
                            <svg class="h-5 w-5 flex-shrink-0 text-gray-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl bg-white border border-gray-100 p-8 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="mt-3 text-sm font-medium text-gray-600">Belum ada kunjungan</p>
            </div>
        @endif
    </div>

</div>
@endsection
