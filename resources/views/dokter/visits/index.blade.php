@extends('layouts.app')

@section('title', 'Kunjungan')
@section('header-title', 'Kunjungan')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('dokter.visits.index') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kunjungan..."
                   class="w-full sm:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Status</option>
                <option value="DRAFT" {{ request('status') === 'DRAFT' ? 'selected' : '' }}>Draft</option>
                <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Selesai</option>
                <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>Dibayar</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Filter
            </button>
        </form>
        <a href="{{ route('dokter.visits.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Kunjungan Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3">Nomor</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Hewan</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Keluhan</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($visits as $visit)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('dokter.visits.show', $visit) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">{{ $visit->visit_number }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $visit->customer->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $visit->pet->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $visit->visit_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-[200px] truncate" title="{{ $visit->chief_complaint ?? '-' }}">{{ Str::limit($visit->chief_complaint ?? '-', 40) }}</td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$visit->status" />
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('dokter.visits.show', $visit) }}" class="text-blue-600 hover:text-blue-800 p-1" title="Lihat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if($visit->status === 'DRAFT')
                                        <a href="{{ route('dokter.visits.edit', $visit) }}" class="text-amber-600 hover:text-amber-800 p-1" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('dokter.visits.complete', $visit) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 p-1" title="Selesaikan" onclick="return confirm('Selesaikan kunjungan ini?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada kunjungan ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($visits->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $visits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
