@extends('layouts.portal')

@section('title', 'My Prescriptions')

@section('header')
    <h1 class="text-xl font-bold text-gray-900">Resep Saya</h1>
@endsection

@section('content')
<div x-data="{ expanded: null }">

    @if($prescriptions->count() > 0)
        <div class="space-y-3">
            @foreach($prescriptions as $prescription)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    {{-- Main Row --}}
                    <button @click="expanded = expanded === {{ $prescription->id }} ? null : {{ $prescription->id }}"
                            class="w-full text-left p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-semibold text-gray-900">#{{ $prescription->prescription_number }}</span>
                                    <span class="text-xs text-gray-400">&bull;</span>
                                    <span class="text-sm text-gray-600">{{ $prescription->prescription_date->format('d M Y') }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-sm text-gray-500">
                                    @if($prescription->pet)
                                        <span>{{ $prescription->pet->name }}</span>
                                    @endif
                                    @if($prescription->visit)
                                        <span>Kunjungan #{{ $prescription->visit->visit_number }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($prescription->status === 'DISPENSED') bg-green-100 text-green-800
                                    @elseif($prescription->status === 'PENDING') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if($prescription->status === 'DISPENSED') Sudah Diambil
                                    @elseif($prescription->status === 'PENDING') Menunggu
                                    @else {{ ucfirst($prescription->status) }} @endif
                                </span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded === {{ $prescription->id }} }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    {{-- Expanded Drug List --}}
                    <div x-show="expanded === {{ $prescription->id }}" x-collapse x-cloak
                         class="px-4 pb-4 border-t border-gray-100">
                        <div class="pt-4">
                            @if($prescription->items->count() > 0)
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-100">
                                            <th class="text-left py-2 text-xs font-medium text-gray-500 uppercase">Obat</th>
                                            <th class="text-center py-2 text-xs font-medium text-gray-500 uppercase">Qty</th>
                                            <th class="text-center py-2 text-xs font-medium text-gray-500 uppercase">Durasi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($prescription->items as $item)
                                            <tr>
                                                <td class="py-2 text-gray-900">{{ $item->drug?->name ?? '-' }}</td>
                                                <td class="py-2 text-center text-gray-700">{{ $item->quantity }}</td>
                                                <td class="py-2 text-center text-gray-700">{{ $item->duration_days }} hari</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-sm text-gray-500">Tidak ada obat dalam resep ini.</p>
                            @endif

                            <div class="mt-3">
                                <a href="{{ route('portal.prescriptions.show', $prescription) }}"
                                   class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $prescriptions->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-3 text-gray-500">Tidak ada resep ditemukan.</p>
        </div>
    @endif

</div>
@endsection
