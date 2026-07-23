@extends('layouts.portal')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-500 rounded-xl p-6 text-white">
        <h1 class="text-2xl font-bold">Halo, {{ Auth::user()->name }}!</h1>
        <p class="mt-1 text-blue-100">Selamat datang di Customer Portal Haland PetCare.</p>
    </div>

    {{-- Unpaid Invoices Alert --}}
    @if($unpaidInvoices->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4" x-data="{ open: true }" x-show="open">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-red-800">Tagihan Belum Dibayar</h3>
                    <div class="mt-2 space-y-1">
                        @foreach($unpaidInvoices as $invoice)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-red-700">
                                    Invoice #{{ $invoice->invoice_number }}
                                    @if($invoice->pet) - {{ $invoice->pet->name }} @endif
                                    &mdash; Rp {{ number_format($invoice->total, 0, ',', '.') }}
                                </span>
                                <a href="{{ route('portal.invoices.show', $invoice) }}" class="font-medium text-red-600 hover:text-red-800 underline">Lihat</a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button @click="open = false" class="flex-shrink-0 text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- My Pets --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-900">Hewan Peliharaan Saya</h2>
            <a href="{{ route('portal.pets') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Lihat Semua</a>
        </div>

        @if($pets->count() > 0)
            <div class="flex gap-3 overflow-x-auto pb-2 -mx-1 px-1 scrollbar-hide">
                @foreach($pets as $pet)
                    <a href="{{ route('portal.pets.show', $pet) }}"
                       class="flex-shrink-0 w-44 bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-3">
                            @if($pet->species === 'Kucing')
                                <span class="text-xl">🐱</span>
                            @elseif($pet->species === 'Anjing')
                                <span class="text-xl">🐶</span>
                            @elseif($pet->species === 'Burung')
                                <span class="text-xl">🐦</span>
                            @elseif($pet->species === 'Kelinci')
                                <span class="text-xl">🐰</span>
                            @elseif($pet->species === 'Hamster')
                                <span class="text-xl">🐹</span>
                            @elseif($pet->species === 'Iguana')
                                <span class="text-xl">🦎</span>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-900 truncate">{{ $pet->name }}</h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $pet->species }}</p>
                        @if($pet->breed)
                            <p class="text-xs text-gray-400 truncate">{{ $pet->breed }}</p>
                        @endif
                    </a>
                @endforeach

                {{-- Add Pet --}}
                <a href="{{ route('portal.pets') }}"
                   class="flex-shrink-0 w-44 bg-white rounded-xl border-2 border-dashed border-gray-300 p-4 hover:border-blue-400 hover:bg-blue-50 transition-colors flex flex-col items-center justify-center min-h-[140px]">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="text-sm font-medium text-gray-500 mt-2">Tambah Hewan</span>
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <p class="mt-3 text-gray-500">Belum ada hewan peliharaan terdaftar.</p>
            </div>
        @endif
    </div>

    {{-- Recent Visits --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-900">Kunjungan Terakhir</h2>
            <a href="{{ route('portal.visits') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Lihat Semua</a>
        </div>

        @if($recentVisits->count() > 0)
            <div class="space-y-3">
                @foreach($recentVisits as $visit)
                    <a href="{{ route('portal.visits.show', $visit) }}"
                       class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $visit->visit_number }}</span>
                                    <span class="text-xs text-gray-400">&bull;</span>
                                    <span class="text-sm text-gray-600">{{ $visit->pet->name ?? '-' }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $visit->visit_date->format('d M Y') }}</p>
                                @if($visit->diagnosis)
                                    <p class="text-sm text-gray-700 mt-1 line-clamp-1">{{ $visit->diagnosis }}</p>
                                @endif
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($visit->status === 'COMPLETED') bg-green-100 text-green-800
                                @elseif($visit->status === 'IN_PROGRESS') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($visit->status) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                <p class="text-gray-500">Belum ada kunjungan.</p>
            </div>
        @endif
    </div>

    {{-- Notifications --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Notifikasi</h2>
        @if(isset($notifications) && $notifications->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
                @foreach($notifications as $notification)
                    <div class="p-4 {{ $notification->is_read ? 'opacity-60' : '' }}">
                        <div class="flex items-start gap-3">
                            @unless($notification->is_read)
                                <span class="flex-shrink-2 w-2 h-2 bg-blue-500 rounded-full mt-1.5"></span>
                            @endunless
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                <p class="text-sm text-gray-500 mt-0.5">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="mt-3 text-gray-500">Tidak ada notifikasi.</p>
            </div>
        @endif
    </div>

</div>
@endsection
