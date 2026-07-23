@extends('layouts.app')

@section('title', 'Dashboard - Haland PetCare')
@section('header-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Kunjungan Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $todayVisitsCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Total kunjungan tercatat hari ini</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Total pembayaran diterima</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pembayaran Tertunggak</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $pendingPaymentsCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Invoice belum/bayar sebagian</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Stok Rendah</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $lowStockProductsCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Produk di bawah titik reorder</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Charts / Revenue Chart --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Pendapatan Mingguan</h3>
                <span class="text-xs text-gray-400">{{ now()->format('F Y') }}</span>
            </div>
            <div x-data="{
                data: [{{ $dailyReport['chart_data'] ?? '0,0,0,0,0,0,0' }}],
                labels: {!! json_encode($dailyReport['chart_labels'] ?? ['Sen','Sel','Rab','Kam','Jum','Sab','Min']) !!},
                max: Math.max(...[{{ $dailyReport['chart_data'] ?? '0,0,0,0,0,0,0' }}]),
            }" class="space-y-3">
                <template x-for="(val, i) in data" :key="i">
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 w-8" x-text="labels[i]"></span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                            <div class="bg-primary-500 h-full rounded-full transition-all duration-500"
                                 :style="`width: ${max > 0 ? (val / max) * 100 : 0}%`"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-700 w-20 text-right" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(val)"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Pending Actions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Tertunda</h3>
            <div class="space-y-3">
                @if($pendingPaymentsCount > 0)
                    <a href="{{ route('invoices.index', ['status' => 'UNPAID']) }}" class="flex items-center gap-3 p-3 rounded-lg bg-amber-50 border border-amber-200 hover:bg-amber-100 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-amber-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $pendingPaymentsCount }} Invoice Tertunggak</p>
                            <p class="text-xs text-gray-500">Perlu ditindaklanjuti</p>
                        </div>
                    </a>
                @endif

                @if($lowStockProductsCount > 0)
                    <a href="{{ route('admin.stock.index', ['stock_status' => 'low']) }}" class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-200 hover:bg-red-100 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-red-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $lowStockProductsCount }} Stok Rendah</p>
                            <p class="text-xs text-gray-500">Perlu restock segera</p>
                        </div>
                    </a>
                @endif

                @if($pendingPaymentsCount == 0 && $lowStockProductsCount == 0)
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-green-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-gray-500 mt-3">Semua aksi sudah ditangani</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
            <a href="{{ route('invoices.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Jumlah</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentPayments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $payment->payable->customer->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                    @if(($payment->payable_type ?? '') === 'App\\Models\\Invoice') bg-blue-100 text-blue-700
                                    @else bg-purple-100 text-purple-700 @endif">
                                    {{ class_basename($payment->payable_type ?? 'Unknown') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                    @if($payment->status === 'PAID') bg-green-100 text-green-700
                                    @elseif($payment->status === 'PENDING') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst(strtolower($payment->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada transaksi hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
