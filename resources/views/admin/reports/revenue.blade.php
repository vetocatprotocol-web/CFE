@extends('layouts.app')

@section('title', 'Revenue Report - Haland PetCare')
@section('header-title', 'Revenue Report')

@section('content')
<div class="space-y-6">
    {{-- Date Range Picker --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('admin.reports.revenue') }}" class="flex items-end gap-4">
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

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">Rp {{ number_format($report['total_revenue'] ?? 0, 0, ',', '.') }}</p>
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
                    <p class="text-sm font-medium text-gray-500">Rata-rata per Hari</p>
                    @php
                        $days = \Carbon\Carbon::parse($dateFrom)->diffInDays(\Carbon\Carbon::parse($dateTo)) + 1;
                        $avgPerDay = $days > 0 ? ($report['total_revenue'] ?? 0) / $days : 0;
                    @endphp
                    <p class="text-3xl font-bold text-gray-900 mt-1">Rp {{ number_format($avgPerDay, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue by Payment Method --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Pendapatan per Metode Bayar</h3>
            </div>
            <div class="p-6">
                @if(!empty($report['revenue_by_method']))
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="pb-3">Metode</th>
                                <th class="pb-3 text-right">Total</th>
                                <th class="pb-3 text-right">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($report['revenue_by_method'] as $method => $total)
                                <tr>
                                    <td class="py-3 font-medium text-gray-900">{{ ucfirst($method) }}</td>
                                    <td class="py-3 text-right font-medium text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                    <td class="py-3 text-right text-gray-600">
                                        {{ ($report['total_revenue'] ?? 0) > 0 ? number_format(($total / $report['total_revenue']) * 100, 1) : '0' }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada data pendapatan</p>
                @endif
            </div>
        </div>

        {{-- Revenue by Service Category --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Pendapatan per Kategori Layanan</h3>
            </div>
            <div class="p-6">
                @if(!empty($report['revenue_by_service']))
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="pb-3">Kategori</th>
                                <th class="pb-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($report['revenue_by_service'] as $category => $total)
                                <tr>
                                    <td class="py-3 font-medium text-gray-900">{{ ucfirst($category) }}</td>
                                    <td class="py-3 text-right font-medium text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada data kategori</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Daily Revenue Trend --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Tren Pendapatan Harian</h3>
        </div>
        <div class="p-6">
            @if(!empty($report['daily_revenue']))
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($report['daily_revenue'] as $day => $total)
                            <tr>
                                <td class="py-3 text-gray-900">{{ $day }}</td>
                                <td class="py-3 text-right font-medium text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-500 text-center py-4">Tidak ada data harian</p>
            @endif
            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 text-center">Chart visualization placeholder - integrate with Chart.js or similar library</p>
            </div>
        </div>
    </div>
</div>
@endsection
