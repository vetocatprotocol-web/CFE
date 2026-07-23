@extends('layouts.portal')

@section('title', 'My Invoices')

@section('header')
    <h1 class="text-xl font-bold text-gray-900">Invoice Saya</h1>
@endsection

@section('content')
<div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap gap-2" x-data>
            <a href="{{ route('portal.invoices') }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                   {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'UNPAID']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                   {{ request('status') === 'UNPAID' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Belum Dibayar
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'PARTIAL']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                   {{ request('status') === 'PARTIAL' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Sebagian
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'PAID']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
                   {{ request('status') === 'PAID' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Lunas
            </a>
        </div>
    </div>

    {{-- Invoice List --}}
    @if($invoices->count() > 0)
        <div class="space-y-3">
            @foreach($invoices as $invoice)
                <div class="bg-white rounded-xl border border-gray-200 p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-semibold text-gray-900">#{{ $invoice->invoice_number }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($invoice->status === 'PAID') bg-green-100 text-green-800
                                    @elseif($invoice->status === 'PARTIAL') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @if($invoice->status === 'PAID') Lunas
                                    @elseif($invoice->status === 'PARTIAL') Sebagian
                                    @else Belum Dibayar @endif
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-sm text-gray-500">
                                <span>{{ $invoice->invoice_date->format('d M Y') }}</span>
                                @if($invoice->pet)
                                    <span>{{ $invoice->pet->name }}</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2">
                                <div>
                                    <span class="text-xs text-gray-400">Total</span>
                                    <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($invoice->total, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-400">Terbayar</span>
                                    <p class="text-sm text-green-600 font-medium">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</p>
                                </div>
                                @if($invoice->total - $invoice->paid_amount > 0)
                                    <div>
                                        <span class="text-xs text-gray-400">Sisa</span>
                                        <p class="text-sm text-red-600 font-medium">Rp {{ number_format($invoice->total - $invoice->paid_amount, 0, ',', '.') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('portal.invoices.show', $invoice) }}"
                               class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                Lihat
                            </a>
                            <a href="{{ route('portal.invoices.download', $invoice) }}"
                               class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                               title="Download PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $invoices->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
            </svg>
            <p class="mt-3 text-gray-500">Tidak ada invoice ditemukan.</p>
        </div>
    @endif

</div>
@endsection
