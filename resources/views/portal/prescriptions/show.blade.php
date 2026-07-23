@extends('layouts.portal')

@section('title', 'Prescription ' . $prescription->prescription_number)

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('portal.prescriptions') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">Resep #{{ $prescription->prescription_number }}</h1>
            <p class="text-sm text-gray-500">{{ $prescription->prescription_date->format('d M Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="#" onclick="window.print(); return false;"
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6 print:max-w-none">

    {{-- Prescription Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 print:border-0">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Resep #</p>
                <p class="mt-1 text-sm text-gray-900 font-medium">{{ $prescription->prescription_number }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal</p>
                <p class="mt-1 text-sm text-gray-900">{{ $prescription->prescription_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                    @if($prescription->status === 'DISPENSED') bg-green-100 text-green-800
                    @elseif($prescription->status === 'PENDING') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    @if($prescription->status === 'DISPENSED') Sudah Diambil
                    @elseif($prescription->status === 'PENDING') Menunggu
                    @else {{ ucfirst($prescription->status) }} @endif
                </span>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Hewan</p>
                <p class="mt-1 text-sm text-gray-900">{{ $prescription->pet?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kunjungan #</p>
                <p class="mt-1 text-sm text-gray-900">{{ $prescription->visit?->visit_number ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Drug Table --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 print:border-0">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Daftar Obat</h2>

        @if($prescription->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="text-left py-2 text-xs font-medium text-gray-500 uppercase">Nama Obat</th>
                            <th class="text-center py-2 text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="text-left py-2 text-xs font-medium text-gray-500 uppercase">Dosis</th>
                            <th class="text-center py-2 text-xs font-medium text-gray-500 uppercase">Durasi</th>
                            <th class="text-left py-2 text-xs font-medium text-gray-500 uppercase">Instruksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($prescription->items as $index => $item)
                            <tr>
                                <td class="py-3 text-gray-500">{{ $index + 1 }}</td>
                                <td class="py-3 text-gray-900 font-medium">{{ $item->drug?->name ?? '-' }}</td>
                                <td class="py-3 text-center text-gray-700">{{ $item->quantity }}</td>
                                <td class="py-3 text-gray-700">{{ $item->dosage ?: '-' }}</td>
                                <td class="py-3 text-center text-gray-700">{{ $item->duration_days }} hari</td>
                                <td class="py-3 text-gray-700">{{ $item->instructions ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500">Tidak ada obat dalam resep ini.</p>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 print:hidden">
        <a href="{{ route('portal.prescriptions') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Resep
        </a>
    </div>

</div>

<style type="text/css" media="print">
    @media print {
        nav, footer, aside, .print\:hidden { display: none !important; }
        main { padding: 0 !important; }
    }
</style>
@endsection
