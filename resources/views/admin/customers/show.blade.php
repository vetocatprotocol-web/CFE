@extends('layouts.app')

@section('title', 'Detail Pelanggan - ' . $customer->name)
@section('header-title', 'Detail Pelanggan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.customers.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h2 class="text-xl font-bold text-gray-900">{{ $customer->name }}</h2>
        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $customer->status === 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
            {{ $customer->status }}
        </span>
        <a href="{{ route('admin.customers.edit', $customer) }}" class="ml-auto text-primary-600 hover:text-primary-800 text-sm font-medium">Edit</a>
    </div>

    {{-- Customer Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pelanggan</h3>
            <dl class="space-y-3">
                <div class="flex items-center gap-3">
                    <dt class="text-sm text-gray-500 w-24">Telepon</dt>
                    <dd class="text-sm text-gray-900 font-medium">{{ $customer->phone }}</dd>
                </div>
                <div class="flex items-center gap-3">
                    <dt class="text-sm text-gray-500 w-24">Email</dt>
                    <dd class="text-sm text-gray-900">{{ $customer->email ?? '-' }}</dd>
                </div>
                <div class="flex items-start gap-3">
                    <dt class="text-sm text-gray-500 w-24">Alamat</dt>
                    <dd class="text-sm text-gray-900">{{ $customer->address ?? '-' }}</dd>
                </div>
                <div class="flex items-center gap-3">
                    <dt class="text-sm text-gray-500 w-24">Kota</dt>
                    <dd class="text-sm text-gray-900">{{ $customer->city ?? '-' }}</dd>
                </div>
                <div class="flex items-center gap-3">
                    <dt class="text-sm text-gray-500 w-24">Kode Pos</dt>
                    <dd class="text-sm text-gray-900">{{ $customer->postal_code ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Hewan Peliharaan</h3>
                <span class="text-sm text-gray-500">{{ $customer->pets->count() }} hewan</span>
            </div>
            @forelse($customer->pets as $pet)
                <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 {{ !$loop->last ? 'mb-2' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-bold">
                        {{ substr($pet->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $pet->name }}</p>
                        <p class="text-xs text-gray-500">{{ $pet->species ?? '-' }} @if($pet->breed) &middot; {{ $pet->breed }} @endif @if($pet->age) &middot; {{ $pet->age }} thn @endif</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-6">
                    <svg class="w-10 h-10 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <p class="text-sm text-gray-500 mt-2">Belum ada hewan terdaftar</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Visit History --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Riwayat Kunjungan</h3>
            <span class="text-sm text-gray-500">{{ $customer->visits->count() }} kunjungan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Hewan</th>
                        <th class="px-6 py-3">Dokter</th>
                        <th class="px-6 py-3">Catatan</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($customer->visits as $visit)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $visit->visit_date->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $visit->pet->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $visit->creator->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($visit->notes ?? '-', 40) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                    @if(($visit->status ?? '') === 'COMPLETED') bg-green-100 text-green-700
                                    @elseif(($visit->status ?? '') === 'IN_PROGRESS') bg-blue-100 text-blue-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $visit->status ?? 'pending')) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada riwayat kunjungan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
