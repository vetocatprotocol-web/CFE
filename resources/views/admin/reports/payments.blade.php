@extends('layouts.app')

@section('title', 'Payment Report - Haland PetCare')
@section('header-title', 'Payment Report')

@section('content')
<div class="space-y-6">
    {{-- Date Range Picker --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.reports.payments') }}" class="flex items-end gap-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pembayaran</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">Rp {{ number_format($report['total_payments'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $report['total_transactions'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Rata-rata per Transaksi</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">Rp {{ number_format($report['average_payment'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Payments by Method --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Pembayaran berdasarkan Metode</h3>
            </div>
            <div class="p-6">
                @if(!empty($report['payments_by_method']))
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="pb-3">Metode</th>
                                <th class="pb-3 text-right">Transaksi</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($report['payments_by_method'] as $method)
                                <tr>
                                    <td class="py-3 font-medium text-gray-900">{{ ucfirst($method['payment_method'] ?? '-') }}</td>
                                    <td class="py-3 text-right text-gray-700">{{ $method['count'] ?? 0 }}</td>
                                    <td class="py-3 text-right font-medium text-gray-900">Rp {{ number_format($method['total'] ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada data pembayaran</p>
                @endif
            </div>
        </div>

        {{-- Payments by Status --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Status Pembayaran</h3>
            </div>
            <div class="p-6">
                @if(!empty($report['payments_by_status']))
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="pb-3">Status</th>
                                <th class="pb-3 text-right">Jumlah</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($report['payments_by_status'] as $status)
                                <tr>
                                    <td class="py-3">
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                            @if(($status['status'] ?? '') === 'PAID') bg-green-100 text-green-700
                                            @elseif(($status['status'] ?? '') === 'PENDING') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700 @endif">
                                            {{ ucfirst($status['status'] ?? 'unknown') }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right text-gray-700">{{ $status['count'] ?? 0 }}</td>
                                    <td class="py-3 text-right font-medium text-gray-900">Rp {{ number_format($status['total'] ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada data status</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Unpaid Invoices --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Invoice Tertunggak</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">No. Invoice</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Dibayar</th>
                        <th class="px-6 py-3 text-right">Sisa</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($unpaidInvoices ?? [] as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-blue-600">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $invoice->customer->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $invoice->invoice_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4 {{ ($invoice->due_date && $invoice->due_date->isPast()) ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                {{ $invoice->due_date?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-gray-700">Rp {{ number_format($invoice->paid_amount ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-red-600">
                                Rp {{ number_format($invoice->total - ($invoice->paid_amount ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                    @if($invoice->status === 'PARTIAL') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                Tidak ada invoice tertunggak
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
