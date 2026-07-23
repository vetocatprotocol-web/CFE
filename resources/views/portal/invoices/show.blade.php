@extends('layouts.portal')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('portal.invoices') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">Invoice #{{ $invoice->invoice_number }}</h1>
            <p class="text-sm text-gray-500">{{ $invoice->invoice_date->format('d M Y') }}</p>
        </div>
        <a href="{{ route('portal.invoices.download', $invoice) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download PDF
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Invoice Card --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold">Haland PetCare</h2>
                    <p class="text-blue-100 text-sm mt-1">Klinik Hewan & Petshop</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-100">Invoice</p>
                    <p class="text-lg font-bold">#{{ $invoice->invoice_number }}</p>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Informasi Pelanggan</h3>
                    <p class="text-sm font-medium text-gray-900">{{ $invoice->customer->name ?? '-' }}</p>
                    @if($invoice->customer?->phone)
                        <p class="text-sm text-gray-600">{{ $invoice->customer->phone }}</p>
                    @endif
                    @if($invoice->customer?->address)
                        <p class="text-sm text-gray-600">{{ $invoice->customer->address }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Informasi Hewan</h3>
                    @if($invoice->pet)
                        <p class="text-sm font-medium text-gray-900">{{ $invoice->pet->name }}</p>
                        <p class="text-sm text-gray-600">{{ $invoice->pet->species }}{{ $invoice->pet->breed ? ' - ' . $invoice->pet->breed : '' }}</p>
                    @else
                        <p class="text-sm text-gray-400">-</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6">
                <div>
                    <p class="text-xs font-medium text-gray-500">Tanggal Invoice</p>
                    <p class="text-sm text-gray-900">{{ $invoice->invoice_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Jatuh Tempo</p>
                    <p class="text-sm text-gray-900">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($invoice->status === 'PAID') bg-green-100 text-green-800
                        @elseif($invoice->status === 'PARTIAL') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        @if($invoice->status === 'PAID') Lunas
                        @elseif($invoice->status === 'PARTIAL') Sebagian
                        @else Belum Dibayar @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        @if($invoice->items->count() > 0)
            <div class="px-6 pb-6">
                <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Rincian Item</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-2 text-xs font-medium text-gray-500 uppercase">Nama Item</th>
                                <th class="text-center py-2 text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="text-right py-2 text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="text-right py-2 text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="py-3 text-gray-900">{{ $item->item_name }}</td>
                                    <td class="py-3 text-center text-gray-700">{{ $item->quantity }}</td>
                                    <td class="py-3 text-right text-gray-700">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right text-gray-900 font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Totals --}}
        <div class="px-6 pb-6">
            <div class="bg-gray-50 rounded-lg p-4 max-w-xs ml-auto">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-gray-900">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($invoice->tax_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pajak</span>
                            <span class="text-gray-900">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($invoice->discount_amount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Diskon</span>
                            <span class="text-red-600">- Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-gray-200">
                        <span class="font-semibold text-gray-900">Total</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Status --}}
        <div class="px-6 pb-6">
            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Status Pembayaran</h3>
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-sm font-bold text-gray-900 mt-1">Rp {{ number_format($invoice->total, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Terbayar</p>
                    <p class="text-sm font-bold text-green-700 mt-1">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3 text-center">
                    <p class="text-xs text-gray-500">Sisa</p>
                    <p class="text-sm font-bold text-red-700 mt-1">Rp {{ number_format($invoice->total - $invoice->paid_amount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="px-6 pb-6">
                <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Catatan</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $invoice->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Back --}}
    <div>
        <a href="{{ route('portal.invoices') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Invoice
        </a>
    </div>

</div>
@endsection
