@extends('layouts.app')

@section('title', 'Detail Kunjungan - ' . $visit->visit_number)
@section('header-title', 'Detail Kunjungan')

@section('content')
<div class="space-y-6">
    {{-- Visit Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $visit->visit_number }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $visit->visit_date?->format('d/m/Y H:i') ?? '-' }}</p>
            </div>
            <x-status-badge :status="$visit->status" size="md" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Pelanggan</p>
                <p class="text-sm font-semibold text-gray-900">{{ $visit->customer->name ?? '-' }}</p>
                <p class="text-xs text-gray-500">{{ $visit->customer->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Hewan</p>
                <p class="text-sm font-semibold text-gray-900">{{ $visit->pet->name ?? '-' }}</p>
                <p class="text-xs text-gray-500">{{ $visit->pet->species ?? '-' }} {{ $visit->pet->breed ? '- ' . $visit->pet->breed : '' }}</p>
            </div>
        </div>
    </div>

    {{-- Clinical Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Klinis</h3>

        <div class="space-y-4">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase mb-1">Keluhan Utama</p>
                <p class="text-sm text-gray-900">{{ $visit->chief_complaint ?? '-' }}</p>
            </div>

            @if($visit->diagnosis)
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Diagnosis</p>
                    <p class="text-sm text-gray-900">{{ $visit->diagnosis }}</p>
                </div>
            @endif

            @if($visit->treatment_notes)
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase mb-1">Catatan Perawatan</p>
                    <p class="text-sm text-gray-900">{{ $visit->treatment_notes }}</p>
                </div>
            @endif
        </div>

        @if($visit->weight_kg || $visit->temperature || $visit->heart_rate)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-xs font-medium text-gray-500 uppercase mb-3">Tanda Vital</p>
                <div class="grid grid-cols-3 gap-4">
                    @if($visit->weight_kg)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-900">{{ $visit->weight_kg }}</p>
                            <p class="text-xs text-gray-500">kg</p>
                        </div>
                    @endif
                    @if($visit->temperature)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-900">{{ $visit->temperature }}</p>
                            <p class="text-xs text-gray-500">&deg;C</p>
                        </div>
                    @endif
                    @if($visit->heart_rate)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <p class="text-lg font-bold text-gray-900">{{ $visit->heart_rate }}</p>
                            <p class="text-xs text-gray-500">bpm</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Item Kunjungan</h3>

        @if($visit->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <th class="pb-2">Jenis</th>
                            <th class="pb-2">Nama</th>
                            <th class="pb-2 text-center">Qty</th>
                            <th class="pb-2 text-right">Harga</th>
                            <th class="pb-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($visit->items as $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-3">
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                        {{ $item->item_type === 'service' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ ucfirst($item->item_type) }}
                                    </span>
                                </td>
                                <td class="py-3 text-sm text-gray-900">{{ $item->service->name ?? $item->drug->name ?? '-' }}</td>
                                <td class="py-3 text-sm text-gray-700 text-center">{{ $item->quantity }}</td>
                                <td class="py-3 text-sm text-gray-700 text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 border-t border-gray-200 pt-4 flex justify-end">
                <div class="w-64 space-y-1">
                    @php
                        $subtotal = $visit->items->sum('subtotal');
                        $tax = round($subtotal * 0.11);
                        $total = $subtotal + $tax;
                    @endphp
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Pajak (11%)</span>
                        <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-bold text-gray-900 border-t border-gray-200 pt-1">
                        <span>Total</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 text-center py-4">Belum ada item</p>
        @endif
    </div>

    {{-- Invoice --}}
    @if($visit->invoice)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Invoice</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Nomor Invoice</p>
                    <a href="{{ route('invoices.show', $visit->invoice) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">{{ $visit->invoice->invoice_number }}</a>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Jumlah</p>
                    <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($visit->invoice->total_amount, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Status</p>
                    <x-status-badge :status="$visit->invoice->status" size="md" />
                </div>
            </div>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('dokter.visits.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
            Kembali
        </a>
        <div class="flex items-center gap-3">
            @if($visit->status === 'DRAFT')
                <a href="{{ route('dokter.visits.edit', $visit) }}" class="px-4 py-2.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                    Edit
                </a>
                <form method="POST" action="{{ route('dokter.visits.complete', $visit) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors" onclick="return confirm('Selesaikan kunjungan ini? Invoice akan dibuat otomatis.')">
                        Selesaikan Kunjungan
                    </button>
                </form>
            @endif

            @if($visit->status === 'COMPLETED' || $visit->status === 'PAID')
                @if($visit->invoice)
                    <a href="{{ route('invoices.show', $visit->invoice) }}" class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        Lihat Invoice
                    </a>
                @endif
                <button type="button" onclick="window.print()" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
            @endif
        </div>
    </div>
</div>
@endsection
