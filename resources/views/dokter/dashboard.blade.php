@extends('layouts.app')

@section('title', 'Dokter Dashboard')
@section('header-title', 'Dokter Dashboard')

@section('content')
<div class="space-y-6" x-data>
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center ring-2 ring-white/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h2>
                <p class="text-blue-100 text-sm mt-0.5">Kelola kunjungan pasien hari ini dengan mudah.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Kunjungan Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $todayVisits->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Draft Visits</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $draftVisits->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Selesai Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $todayVisits->where('status', 'COMPLETED')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Kunjungan Hari Ini</h3>
                <a href="{{ route('dokter.visits.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    Lihat Semua &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3">Nomor</th>
                            <th class="px-6 py-3">Pelanggan</th>
                            <th class="px-6 py-3">Hewan</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($todayVisits as $visit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('dokter.visits.show', $visit) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">{{ $visit->visit_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $visit->customer->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $visit->pet->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$visit->status" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm text-gray-500">Belum ada kunjungan hari ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('dokter.visits.create') }}" class="flex items-center gap-3 p-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition-colors shadow-sm">
                        <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Kunjungan Baru</p>
                            <p class="text-xs text-blue-100">Catat kunjungan pasien</p>
                        </div>
                    </a>

                    <a href="{{ route('dokter.billings.index') }}" class="flex items-center gap-3 p-3.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white transition-colors shadow-sm">
                        <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">Lihat Billings</p>
                            <p class="text-xs text-purple-100">Kelola billing pasien</p>
                        </div>
                    </a>
                </div>
            </div>

            @if($draftVisits->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                        <h3 class="text-sm font-semibold text-gray-800">Draft Menunggu</h3>
                    </div>
                    <div class="space-y-2">
                        @foreach($draftVisits->take(4) as $draft)
                            <a href="{{ route('dokter.visits.show', $draft) }}" class="flex items-center justify-between p-2.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 transition-colors">
                                <div>
                                    <p class="text-sm font-medium text-amber-900">{{ $draft->visit_number }}</p>
                                    <p class="text-xs text-amber-700">{{ $draft->customer->name ?? '-' }} &middot; {{ $draft->pet->name ?? '-' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Pasien Terbaru</h3>
            <a href="{{ route('dokter.visits.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                Lihat Semua &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Nomor</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Hewan</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentPatients as $visit)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $visit->visit_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('dokter.visits.show', $visit) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">{{ $visit->visit_number }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $visit->customer->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $visit->pet->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$visit->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">Belum ada data pasien</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
