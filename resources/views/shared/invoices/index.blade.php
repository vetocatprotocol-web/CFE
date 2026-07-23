@extends('layouts.app')

@section('title', 'Invoice')
@section('header-title', 'Daftar Invoice')

@section('content')
<div class="space-y-4">
    <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1 sm:w-64">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice..."
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">Semua Status</option>
            <option value="UNPAID" {{ request('status') === 'UNPAID' ? 'selected' : '' }}>Belum Bayar</option>
            <option value="PARTIAL" {{ request('status') === 'PARTIAL' ? 'selected' : '' }}>Bayar Sebagian</option>
            <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>Lunas</option>
        </select>
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            Filter
        </button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3">Invoice #</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Dibayar</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices ?? [] as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-medium text-primary-600 hover:text-primary-800">{{ $invoice->number }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $invoice->customer->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $invoice->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 text-right">Rp {{ number_format($invoice->paid_amount ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
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
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-800 p-1 inline-flex" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada invoice ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($invoices) && $invoices->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
