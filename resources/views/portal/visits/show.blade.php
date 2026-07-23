@extends('layouts.portal')

@section('title', 'Visit Detail')

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('portal.visits') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">Detail Kunjungan</h1>
            <p class="text-sm text-gray-500">{{ $visit->visit_number }}</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
            @if($visit->status === 'COMPLETED') bg-green-100 text-green-800
            @elseif($visit->status === 'IN_PROGRESS') bg-yellow-100 text-yellow-800
            @else bg-gray-100 text-gray-800 @endif">
            {{ ucfirst($visit->status) }}
        </span>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Visit Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kunjungan #</p>
                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $visit->visit_number }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal</p>
                <p class="mt-1 text-sm text-gray-900">{{ $visit->visit_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Hewan</p>
                <p class="mt-1 text-sm text-gray-900">{{ $visit->pet->name ?? '-' }}</p>
            </div>
            @if($visit->weight_kg)
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Berat</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->weight_kg }} kg</p>
                </div>
            @endif
            @if($visit->temperature)
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Suhu</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->temperature }}°C</p>
                </div>
            @endif
            @if($visit->heart_rate)
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Denyut Jantung</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->heart_rate }} bpm</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Chief Complaint --}}
    @if($visit->chief_complaint)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-2">Keluhan Utama</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $visit->chief_complaint }}</p>
        </div>
    @endif

    {{-- Diagnosis --}}
    @if($visit->diagnosis)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-2">Diagnosa</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $visit->diagnosis }}</p>
        </div>
    @endif

    {{-- Treatment Notes --}}
    @if($visit->treatment_notes)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-2">Catatan Perawatan</h2>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $visit->treatment_notes }}</p>
        </div>
    @endif

    {{-- Services & Drugs Table --}}
    @if($visit->items->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Layanan & Obat</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="text-left py-2 text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="text-center py-2 text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="text-right py-2 text-xs font-medium text-gray-500 uppercase">Harga</th>
                            <th class="text-right py-2 text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($visit->items as $item)
                            <tr>
                                <td class="py-3 text-gray-900">{{ $item->service?->name ?? $item->drug?->name ?? $item->notes }}</td>
                                <td class="py-3 text-gray-500 capitalize">{{ $item->item_type }}</td>
                                <td class="py-3 text-center text-gray-700">{{ $item->quantity }}</td>
                                <td class="py-3 text-right text-gray-700">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 text-right text-gray-900 font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-200">
                            <td colspan="4" class="py-3 text-right font-semibold text-gray-900">Total</td>
                            <td class="py-3 text-right font-bold text-gray-900">Rp {{ number_format($visit->items->sum('subtotal'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    {{-- Invoice --}}
    @if($visit->invoice)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Invoice</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($visit->invoice->status === 'PAID') bg-green-100 text-green-800
                    @elseif($visit->invoice->status === 'PARTIAL') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($visit->invoice->status) }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Invoice #</p>
                    <p class="text-gray-900 font-medium">{{ $visit->invoice->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Total</p>
                    <p class="text-gray-900 font-medium">Rp {{ number_format($visit->invoice->total, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Terbayar</p>
                    <p class="text-gray-900 font-medium">Rp {{ number_format($visit->invoice->paid_amount, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Sisa</p>
                    <p class="text-gray-900 font-medium">Rp {{ number_format($visit->invoice->total - $visit->invoice->paid_amount, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <a href="{{ route('portal.invoices.show', $visit->invoice) }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-800">Lihat Invoice &rarr;</a>
                <a href="{{ route('portal.invoices.download', $visit->invoice) }}"
                   class="text-sm font-medium text-gray-600 hover:text-gray-800">Download PDF</a>
            </div>
        </div>
    @endif

    {{-- Prescriptions --}}
    @if($visit->prescriptions->count() > 0)
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Resep</h2>
            <div class="space-y-3">
                @foreach($visit->prescriptions as $prescription)
                    <a href="{{ route('portal.prescriptions.show', $prescription) }}"
                       class="block p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $prescription->prescription_number }}</span>
                                <span class="text-xs text-gray-400 ml-2">{{ $prescription->prescription_date->format('d M Y') }}</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if($prescription->status === 'DISPENSED') bg-green-100 text-green-800
                                @elseif($prescription->status === 'PENDING') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($prescription->status) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Back Button --}}
    <div>
        <a href="{{ route('portal.visits') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Riwayat Kunjungan
        </a>
    </div>

</div>
@endsection
