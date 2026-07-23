@extends('layouts.portal')

@section('title', 'Pet Detail - ' . $pet->name)

@section('header')
    <div class="flex items-center gap-3">
        <a href="{{ route('portal.pets') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-bold text-gray-900">{{ $pet->name }}</h1>
            <p class="text-sm text-gray-500">{{ $pet->species }}{{ $pet->breed ? ' - ' . $pet->breed : '' }}</p>
        </div>
        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Profil
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Pet Info Card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                @if($pet->species === 'Kucing')
                    <span class="text-4xl">🐱</span>
                @elseif($pet->species === 'Anjing')
                    <span class="text-4xl">🐶</span>
                @elseif($pet->species === 'Burung')
                    <span class="text-4xl">🐦</span>
                @elseif($pet->species === 'Kelinci')
                    <span class="text-4xl">🐰</span>
                @elseif($pet->species === 'Hamster')
                    <span class="text-4xl">🐹</span>
                @elseif($pet->species === 'Iguana')
                    <span class="text-4xl">🦎</span>
                @else
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-900">{{ $pet->name }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($pet->status === 'ACTIVE') bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($pet->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jenis</p>
                <p class="mt-1 text-sm text-gray-900">{{ $pet->species }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ras</p>
                <p class="mt-1 text-sm text-gray-900">{{ $pet->breed ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tanggal Lahir</p>
                <p class="mt-1 text-sm text-gray-900">{{ $pet->birth_date ? $pet->birth_date->format('d M Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Usia</p>
                <p class="mt-1 text-sm text-gray-900">{{ $pet->birth_date ? $pet->birth_date->diffForHumans(['parts' => 2], true) : '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Berat</p>
                <p class="mt-1 text-sm text-gray-900">{{ $pet->weight_kg ? $pet->weight_kg . ' kg' : '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Warna / Tanda</p>
                <p class="mt-1 text-sm text-gray-900">{{ $pet->color_marking ?: '-' }}</p>
            </div>
        </div>

        @if($pet->medical_history_notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Catatan Riwayat Medis</p>
                <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $pet->medical_history_notes }}</p>
            </div>
        @endif
    </div>

    {{-- Visit History --}}
    <div>
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Riwayat Kunjungan</h2>

        @if($pet->visits->count() > 0)
            <div class="space-y-3">
                @foreach($pet->visits as $visit)
                    <a href="{{ route('portal.visits.show', $visit) }}"
                       class="block bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $visit->visit_number }}</span>
                                    <span class="text-xs text-gray-400">&bull;</span>
                                    <span class="text-sm text-gray-600">{{ $visit->visit_date->format('d M Y') }}</span>
                                </div>
                                @if($visit->chief_complaint)
                                    <p class="text-sm text-gray-600 mt-1">{{ $visit->chief_complaint }}</p>
                                @endif
                                @if($visit->diagnosis)
                                    <p class="text-sm text-gray-500 mt-0.5">Diagnosa: <span class="text-gray-700">{{ $visit->diagnosis }}</span></p>
                                @endif
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($visit->status === 'COMPLETED') bg-green-100 text-green-800
                                @elseif($visit->status === 'IN_PROGRESS') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($visit->status) }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="mt-3 text-gray-500">Belum ada riwayat kunjungan.</p>
            </div>
        @endif
    </div>

</div>
@endsection
