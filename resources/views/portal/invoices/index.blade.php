@extends('layouts.portal')

@section('title', 'My Invoices')

@section('content')
<div>

    {{-- Filter Tabs --}}
    <div class="mb-5 overflow-x-auto scrollbar-hide -mx-1 px-1">
        <div class="inline-flex gap-2 min-w-full sm:min-w-0">
            <a href="{{ route('portal.invoices') }}"
               class="flex-shrink-0 px-4 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200
                   {{ !request('status') ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 active:bg-gray-300' }}">
                Semua
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'UNPAID']) }}"
               class="flex-shrink-0 px-4 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200
                   {{ request('status') === 'UNPAID' ? 'bg-red-600 text-white shadow-sm shadow-red-500/20' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 active:bg-gray-300' }}">
                Belum Dibayar
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'PARTIAL']) }}"
               class="flex-shrink-0 px-4 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200
                   {{ request('status') === 'PARTIAL' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/20' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 active:bg-gray-300' }}">
                Sebagian
            </a>
            <a href="{{ route('portal.invoices', ['status' => 'PAID']) }}"
               class="flex-shrink-0 px-4 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200
                   {{ request('status') === 'PAID' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 active:bg-gray-300' }}">
                Lunas
            </a>
        </div>
    </div>

    {{-- Invoice Cards --}}
    @if($invoices->count() > 0)
        <div class="space-y-3">
            @foreach($invoices as $invoice)
                <a href="{{ route('portal.invoices.show', $invoice) }}"
                   class="block rounded-2xl bg-white border border-gray-100 p-4 shadow-sm hover:shadow-md transition-all duration-200 active:scale-[0.99]">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-gray-900">#{{ $invoice->invoice_number }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold
                                    @if($invoice->status === 'PAID') bg-emerald-50 text-emerald-700
                                    @elseif($invoice->status === 'PARTIAL') bg-amber-50 text-amber-700
                                    @else bg-red-50 text-red-700 @endif">
                                    @if($invoice->status === 'PAID') Lunas
                                    @elseif($invoice->status === 'PARTIAL') Sebagian
                                    @else Belum Dibayar @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-xs text-gray-500">{{ $invoice->invoice_date->format('d M Y') }}</span>
                                @if($invoice->pet)
                                    <span class="text-xs text-gray-400">&bull;</span>
                                    <span class="text-xs text-gray-500 font-medium">{{ $invoice->pet->name }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-4 mt-3">
                                <div>
                                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Total</p>
                                    <p class="text-sm font-bold text-gray-900">Rp {{ number_format($invoice->total, 0, ',', '.') }}</p>
                                </div>
                                @if($invoice->paid_amount > 0)
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Dibayar</p>
                                        <p class="text-sm font-semibold text-emerald-600">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</p>
                                    </div>
                                @endif
                                @if($invoice->total - $invoice->paid_amount > 0)
                                    <div>
                                        <p class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">Sisa</p>
                                        <p class="text-sm font-semibold text-red-500">Rp {{ number_format($invoice->total - $invoice->paid_amount, 0, ',', '.') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            @if($invoice->total - $invoice->paid_amount > 0)
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm shadow-blue-500/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Bayar
                                </span>
                            @endif
                            <a href="{{ route('portal.invoices.download', $invoice) }}"
                               onclick="event.stopPropagation();"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition-colors"
                               title="Download PDF">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                PDF
                            </a>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-5">
            {{ $invoices->withQueryString()->links() }}
        </div>
    @else
        <div class="rounded-2xl bg-white border border-gray-100 p-12 text-center shadow-sm">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-gray-50">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-bold text-gray-900">Tidak ada invoice</h3>
            <p class="mt-1.5 text-sm text-gray-500">Invoice akan muncul di sini setelah kunjungan.</p>
        </div>
    @endif

</div>
@endsection
