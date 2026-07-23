@extends('layouts.app')

@section('title', 'Detail Pembayaran - Haland PetCare')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('kasir.payments.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pembayaran</h1>
            </div>
            <p class="text-sm text-gray-500 mt-1 ml-8">{{ $payment->payment_number }}</p>
        </div>
        <div class="flex gap-2 ml-8 sm:ml-0">
            <a href="{{ route('kasir.payments.index') }}"
               class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                Kembali
            </a>
            <button onclick="window.print()"
                    class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Payment Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pembayaran</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $payment->payment_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</p>
                        <div class="mt-1">
                            @if($payment->status === 'COMPLETED')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Selesai</span>
                            @elseif($payment->status === 'PENDING')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Diterima Oleh</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $payment->receiver->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment Breakdown --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Rincian Pembayaran</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Jumlah Bayar</span>
                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Metode Pembayaran</span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 uppercase">{{ $payment->payment_method }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Invoice/Billing --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Invoice Terkait</h3>
                @if($payment->payable)
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Nomor</span>
                            <span class="font-medium text-gray-800">{{ $payment->payable->invoice_number ?? $payment->payable->order_number ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pelanggan</span>
                            <span class="font-medium text-gray-800">{{ $payment->payable->customer->name ?? '-' }}</span>
                        </div>
                        @if($payment->payable->pet ?? null)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Hewan</span>
                                <span class="font-medium text-gray-800">{{ $payment->payable->pet->name }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Total Tagihan</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($payment->payable->total ?? 0, 0, ',', '.') }}</span>
                        </div>
                        @if(($payment->payable->status ?? null) === 'PAID')
                            <div class="mt-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Lunas</span>
                            </div>
                        @elseif(($payment->payable->status ?? null) === 'PARTIAL')
                            <div class="mt-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Bayar Sebagian</span>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada invoice terkait</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi</h3>
                <div class="space-y-2">
                    <button onclick="window.print()" class="w-full py-2.5 px-4 rounded-xl text-sm font-medium text-center border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Struk
                    </button>
                    <a href="{{ route('kasir.pos.index') }}" class="w-full py-2.5 px-4 rounded-xl text-sm font-medium text-center bg-blue-600 text-white hover:bg-blue-700 transition-colors block">
                        Buka POS
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
