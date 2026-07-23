@extends('layouts.app')

@section('title', 'Detail Invoice - ' . ($invoice->number ?? ''))
@section('header-title', 'Detail Invoice')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-gray-900">Invoice {{ $invoice->number }}</h2>
        @php
            $statusClass = match($invoice->status) {
                'PAID' => 'bg-green-100 text-green-700',
                'PARTIAL' => 'bg-yellow-100 text-yellow-700',
                default => 'bg-red-100 text-red-700',
            };
            $statusText = match($invoice->status) {
                'PAID' => 'Lunas',
                'PARTIAL' => 'Sebagian',
                default => 'Belum Bayar',
            };
        @endphp
        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">{{ $statusText }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Invoice Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Item Invoice</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">Item</th>
                                <th class="px-6 py-3 text-right">Qty</th>
                                <th class="px-6 py-3 text-right">Harga</th>
                                <th class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($invoice->items ?? [] as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->name ?? $item->itemable->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 text-right">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada item</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="max-w-sm ml-auto space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="text-gray-900">Rp {{ number_format($invoice->subtotal ?? $invoice->total, 0, ',', '.') }}</span>
                        </div>
                        @if(($invoice->discount ?? 0) > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Diskon</span>
                                <span class="text-red-600">- Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if(($invoice->tax ?? 0) > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Pajak</span>
                                <span class="text-gray-900">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-base font-semibold border-t border-gray-200 pt-2">
                            <span class="text-gray-800">Total</span>
                            <span class="text-gray-900">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Dibayar</span>
                            <span class="text-green-600 font-medium">Rp {{ number_format($invoice->paid_amount ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @if($invoice->total > ($invoice->paid_amount ?? 0))
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Sisa Tagihan</span>
                                <span class="text-red-600 font-medium">Rp {{ number_format($invoice->total - ($invoice->paid_amount ?? 0), 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Customer Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3">Pelanggan</h3>
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-900">{{ $invoice->customer->name ?? '-' }}</p>
                    @if($invoice->customer?->phone)
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $invoice->customer->phone }}
                        </p>
                    @endif
                    @if($invoice->customer?->email)
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $invoice->customer->email }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Pet Info --}}
            @if($invoice->pet ?? null)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3">Hewan</h3>
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-900">{{ $invoice->pet->name }}</p>
                    @if($invoice->pet?->species)
                        <p class="text-sm text-gray-600">{{ $invoice->pet->species }} @if($invoice->pet?->breed) &middot; {{ $invoice->pet->breed }} @endif</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3">Aksi</h3>
                <div class="space-y-2">
                    <a href="{{ route('invoices.download', $invoice) }}" class="flex items-center gap-2 w-full px-4 py-2.5 bg-primary-50 hover:bg-primary-100 text-primary-700 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download PDF
                    </a>
                    <form method="POST" action="{{ route('invoices.email', $invoice) }}" x-data="{ sending: false }">
                        @csrf
                        <button type="submit" @click="sending = true" :disabled="sending"
                                class="flex items-center gap-2 w-full px-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg text-sm font-medium transition-colors disabled:opacity-50">
                            <svg x-show="!sending" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <svg x-show="sending" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="sending ? 'Mengirim...' : 'Kirim via Email'"></span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Payment Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3">Status Pembayaran</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tanggal Invoice</span>
                        <span class="text-gray-900">{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($invoice->due_date ?? null)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jatuh Tempo</span>
                            <span class="{{ $invoice->due_date->isPast() ? 'text-red-600' : 'text-gray-900' }}">{{ $invoice->due_date->format('d/m/Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
