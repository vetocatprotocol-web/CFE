@extends('layouts.portal')

@section('title', 'Visit History')

@section('content')
<div x-data="{ expanded: null }">

    {{-- Filter Bar --}}
    <div class="rounded-2xl bg-white border border-gray-100 p-4 shadow-sm mb-5">
        <form action="{{ route('portal.visits') }}" method="GET">
            <div class="flex flex-col gap-3">
                <div class="flex gap-3">
                    <div class="flex-1">
                        <select name="pet_id" id="pet_id"
                                class="block w-full rounded-xl border-gray-200 text-sm border p-3 focus:border-blue-500 focus:ring-blue-500 transition-colors">
                            <option value="">Semua Hewan</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ request('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <select name="status" id="status"
                                class="block w-full rounded-xl border-gray-200 text-sm border p-3 focus:border-blue-500 focus:ring-blue-500 transition-colors">
                            <option value="">Semua Status</option>
                            <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Selesai</option>
                            <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>Dalam Proses</option>
                            <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 px-4 py-3 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 active:bg-blue-800 shadow-sm shadow-blue-500/20 transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['pet_id', 'status']))
                        <a href="{{ route('portal.visits') }}"
                           class="flex-shrink-0 px-4 py-3 text-sm font-semibold text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Visit Cards --}}
    @if($visits->count() > 0)
        <div class="space-y-3">
            @foreach($visits as $visit)
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Main Card --}}
                    <button @click="expanded = expanded === {{ $visit->id }} ? null : {{ $visit->id }}"
                            class="w-full text-left p-4 active:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
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
                                <div class="flex items-center gap-2 mt-1.5">
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
                            <svg class="h-5 w-5 flex-shrink-0 text-gray-300 transition-transform duration-200" :class="{ 'rotate-180': expanded === {{ $visit->id }} }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    {{-- Expanded Detail --}}
                    <div x-show="expanded === {{ $visit->id }}" x-collapse x-cloak
                         class="border-t border-gray-100 bg-gray-50/50">
                        <div class="p-4 space-y-4">
                            {{-- Services --}}
                            @if($visit->items->where('item_type', 'service')->count() > 0)
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Layanan</h4>
                                    <div class="space-y-1.5">
                                        @foreach($visit->items->where('item_type', 'service') as $item)
                                            <div class="flex items-center justify-between text-sm rounded-xl bg-white px-3 py-2 border border-gray-100">
                                                <span class="text-gray-700">{{ $item->service?->name ?? $item->notes }}</span>
                                                <span class="text-gray-500 font-medium text-xs">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Drugs --}}
                            @if($visit->items->where('item_type', 'drug')->count() > 0)
                                <div>
                                    <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Obat</h4>
                                    <div class="space-y-1.5">
                                        @foreach($visit->items->where('item_type', 'drug') as $item)
                                            <div class="flex items-center justify-between text-sm rounded-xl bg-white px-3 py-2 border border-gray-100">
                                                <span class="text-gray-700">{{ $item->drug?->name ?? $item->notes }} <span class="text-gray-400">x{{ $item->quantity }}</span></span>
                                                <span class="text-gray-500 font-medium text-xs">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Total --}}
                            @php
                                $visitTotal = $visit->items->sum('subtotal');
                            @endphp
                            @if($visitTotal > 0)
                                <div class="pt-2 border-t border-gray-200 flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-900">Total</span>
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($visitTotal, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="flex gap-2 pt-1">
                                <a href="{{ route('portal.visits.show', $visit) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 active:bg-blue-200 transition-colors">
                                    Lihat Detail
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                                <a href="{{ route('portal.visits.show', $visit) }}"
                                   class="inline-flex items-center justify-center px-3 py-2.5 text-sm font-semibold text-gray-500 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 active:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-5">
            {{ $visits->withQueryString()->links() }}
        </div>
    @else
        <div class="rounded-2xl bg-white border border-gray-100 p-12 text-center shadow-sm">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-gray-50">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-bold text-gray-900">Tidak ada kunjungan</h3>
            <p class="mt-1.5 text-sm text-gray-500">Riwayat kunjungan akan muncul di sini.</p>
        </div>
    @endif

</div>
@endsection
