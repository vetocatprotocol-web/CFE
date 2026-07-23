@extends('layouts.app')

@section('title', 'Dashboard - Haland PetCare')
@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center text-sm text-gray-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600 transition-colors">Dashboard</a>
        <svg class="w-4 h-4 mx-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-800 font-medium">Ringkasan Hari Ini</span>
    </nav>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Kunjungan Hari Ini --}}
        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-primary-200 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Kunjungan Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($todayVisitsCount) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Total kunjungan tercatat hari ini</p>
        </div>

        {{-- Pendapatan Hari Ini --}}
        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-green-200 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Total pembayaran diterima</p>
        </div>

        {{-- Pembayaran Tertunggak --}}
        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-amber-200 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pembayaran Tertunggak</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($pendingPaymentsCount) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
            </div>
            @if($pendingPaymentsCount > 0)
                <div class="flex items-center gap-1 mt-3">
                    <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-amber-600 font-medium">Perlu ditindaklanjuti</span>
                </div>
            @else
                <p class="text-xs text-gray-400 mt-3">Tidak ada yang tertunggak</p>
            @endif
        </div>

        {{-- Stok Rendah --}}
        <div class="stat-card group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md hover:border-red-200 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Stok Rendah</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($lowStockProductsCount) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            @if($lowStockProductsCount > 0)
                <div class="flex items-center gap-1 mt-3">
                    <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-red-600 font-medium">Perlu restock segera</span>
                </div>
            @else
                <p class="text-xs text-gray-400 mt-3">Stok aman semua</p>
            @endif
        </div>
    </div>

    {{-- Charts & Pending Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Weekly Revenue Chart --}}
        <div class="lg:col-span-2 card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Pendapatan Mingguan</h3>
                <span class="text-xs text-gray-400 font-medium">{{ now()->format('F Y') }}</span>
            </div>
            <div x-data="{
                data: [{{ $dailyReport['chart_data'] ?? '0,0,0,0,0,0,0' }}],
                labels: {!! json_encode($dailyReport['chart_labels'] ?? ['Sen','Sel','Rab','Kam','Jum','Sab','Min']) !!},
                max: Math.max(...[{{ $dailyReport['chart_data'] ?? '0,0,0,0,0,0,0' }}]),
                formatRupiah(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                }
            }" class="space-y-3">
                <template x-for="(val, i) in data" :key="i">
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 w-8 font-medium" x-text="labels[i]"></span>
                        <div class="flex-1 bg-gray-100 rounded-full h-7 overflow-hidden relative">
                            <div class="bg-gradient-to-r from-primary-400 to-primary-600 h-full rounded-full transition-all duration-700 ease-out flex items-center justify-end pr-2"
                                 x-transition:enter="transition ease-out duration-700"
                                 x-transition:enter-start="opacity-0 scale-x-0 origin-left"
                                 x-transition:enter-end="opacity-100 scale-x-100"
                                 :style="`width: ${max > 0 ? (val / max) * 100 : 0}%`">
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 w-24 text-right" x-text="formatRupiah(val)"></span>
                    </div>
                </template>
                <div x-show="max === 0" class="text-center py-6">
                    <svg class="w-10 h-10 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-sm text-gray-400 mt-2">Belum ada data pendapatan minggu ini</p>
                </div>
            </div>
        </div>

        {{-- Pending Actions --}}
        <div class="card bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Tertunda</h3>
            <div class="space-y-3">
                @if($pendingPaymentsCount > 0)
                    <a href="{{ route('invoices.index', ['status' => 'UNPAID']) }}" class="flex items-center gap-3 p-3 rounded-lg bg-amber-50 border border-amber-200 hover:bg-amber-100 hover:border-amber-300 transition-all duration-200 group">
                        <div class="w-10 h-10 rounded-full bg-amber-200 flex items-center justify-center group-hover:bg-amber-300 transition-colors">
                            <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ $pendingPaymentsCount }} Invoice Tertunggak</p>
                            <p class="text-xs text-gray-500">Perlu ditindaklanjuti</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif

                @if($lowStockProductsCount > 0)
                    <a href="{{ route('admin.stock.index', ['stock_status' => 'low']) }}" class="flex items-center gap-3 p-3 rounded-lg bg-red-50 border border-red-200 hover:bg-red-100 hover:border-red-300 transition-all duration-200 group">
                        <div class="w-10 h-10 rounded-full bg-red-200 flex items-center justify-center group-hover:bg-red-300 transition-colors">
                            <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">{{ $lowStockProductsCount }} Stok Rendah</p>
                            <p class="text-xs text-gray-500">Perlu restock segera</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif

                @if($pendingPaymentsCount == 0 && $lowStockProductsCount == 0)
                    <div class="text-center py-8">
                        <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-600 mt-4">Semua Aksi Ditangani</p>
                        <p class="text-xs text-gray-400 mt-1">Tidak ada yang perlu diperhatikan saat ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="data-table card bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
            <a href="{{ route('invoices.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1 transition-colors">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3 text-right">Jumlah</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentPayments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-primary-600">{{ strtoupper(substr($payment->payable->customer->name ?? '?', 0, 1)) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $payment->payable->customer->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                    @if(($payment->payable_type ?? '') === 'App\\Models\\Invoice') bg-blue-100 text-blue-700
                                    @else bg-purple-100 text-purple-700 @endif">
                                    {{ class_basename($payment->payable_type ?? 'Unknown') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right whitespace-nowrap">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if(($payment->payable ?? null) && method_exists($payment->payable, 'status'))
                                    <x-status-badge :status="$payment->payable->status" />
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                        @if($payment->status === 'PAID') bg-green-100 text-green-700
                                        @elseif($payment->status === 'PENDING') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst(strtolower($payment->status)) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm text-gray-500 mt-3">Belum ada transaksi terbaru</p>
                                <p class="text-xs text-gray-400 mt-1">Transaksi baru akan muncul di sini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
