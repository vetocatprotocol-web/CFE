@extends('layouts.portal')

@section('title', 'Visit History')

@section('header')
    <h1 class="text-xl font-bold text-gray-900">Riwayat Kunjungan</h1>
@endsection

@section('content')
<div x-data="{ expanded: null }">

    {{-- Filter Bar --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form action="{{ route('portal.visits') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label for="pet_id" class="block text-xs font-medium text-gray-500 mb-1">Hewan</label>
                <select name="pet_id" id="pet_id"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5">
                    <option value="">Semua Hewan</option>
                    @foreach($pets as $pet)
                        <option value="{{ $pet->id }}" {{ request('pet_id') == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" id="status"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2.5">
                    <option value="">Semua Status</option>
                    <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Selesai</option>
                    <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>Dalam Proses</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Filter
                </button>
                @if(request()->hasAny(['pet_id', 'status']))
                    <a href="{{ route('portal.visits') }}"
                       class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Visit List --}}
    @if($visits->count() > 0)
        <div class="space-y-3">
            @foreach($visits as $visit)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    {{-- Main Row --}}
                    <button @click="expanded = expanded === {{ $visit->id }} ? null : {{ $visit->id }}"
                            class="w-full text-left p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ $visit->visit_number }}</span>
                                    <span class="text-xs text-gray-400">&bull;</span>
                                    <span class="text-sm text-gray-600">{{ $visit->pet->name ?? '-' }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $visit->visit_date->format('d M Y') }}</p>
                                @if($visit->chief_complaint)
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-1">{{ $visit->chief_complaint }}</p>
                                @endif
                                @if($visit->diagnosis)
                                    <p class="text-sm text-gray-500 mt-0.5">Diagnosa: <span class="text-gray-700">{{ $visit->diagnosis }}</span></p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($visit->status === 'COMPLETED') bg-green-100 text-green-800
                                    @elseif($visit->status === 'IN_PROGRESS') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($visit->status) }}
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded === {{ $visit->id }} }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    {{-- Expanded Detail --}}
                    <div x-show="expanded === {{ $visit->id }}" x-collapse x-cloak
                         class="px-4 pb-4 border-t border-gray-100">
                        <div class="pt-4 space-y-4">
                            {{-- Services --}}
                            @if($visit->items->where('item_type', 'service')->count() > 0)
                                <div>
                                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Layanan</h4>
                                    <div class="space-y-1">
                                        @foreach($visit->items->where('item_type', 'service') as $item)
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-700">{{ $item->service?->name ?? $item->notes }}</span>
                                                <span class="text-gray-500">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Drugs --}}
                            @if($visit->items->where('item_type', 'drug')->count() > 0)
                                <div>
                                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Obat</h4>
                                    <div class="space-y-1">
                                        @foreach($visit->items->where('item_type', 'drug') as $item)
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-700">{{ $item->drug?->name ?? $item->notes }} x{{ $item->quantity }}</span>
                                                <span class="text-gray-500">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
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
                                <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-900">Total</span>
                                    <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($visitTotal, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="pt-2">
                                <a href="{{ route('portal.visits.show', $visit) }}"
                                   class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $visits->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="mt-3 text-gray-500">Tidak ada riwayat kunjungan ditemukan.</p>
        </div>
    @endif

</div>
@endsection
