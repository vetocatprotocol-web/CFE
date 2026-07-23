@extends('layouts.guest')

@section('content')
<div class="text-center py-8">
    <div class="relative inline-block mb-6">
        <svg class="w-20 h-20 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <div class="absolute -top-2 -right-2 w-5 h-5 bg-amber-400 rounded-full flex items-center justify-center">
            <span class="text-white text-xs font-bold">!</span>
        </div>
    </div>

    <h1 class="text-8xl font-extrabold text-gray-900 tracking-tight leading-none">
        40<span class="text-blue-600">4</span>
    </h1>

    <p class="text-xl font-semibold text-gray-800 mt-4">Halaman Tidak Ditemukan</p>
    <p class="text-sm text-gray-500 mt-2 max-w-sm mx-auto">
        Sepertinya halaman yang kamu cari sudah dipindahkan atau tidak tersedia. Mari kita kembali ke beranda.
    </p>

    <a href="{{ url('/') }}"
       class="inline-flex items-center gap-2 mt-8 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Kembali ke Beranda
    </a>

    <div class="mt-8 flex justify-center gap-1">
        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.504 1.132a1 1 0 01.992 0l7 4A1 1 0 0118 6v8a1 1 0 01-.504.868l-7 4a1 1 0 01-.992 0l-7-4A1 1 0 012 14V6a1 1 0 01.504-.868l7-4z"/>
        </svg>
        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.504 1.132a1 1 0 01.992 0l7 4A1 1 0 0118 6v8a1 1 0 01-.504.868l-7 4a1 1 0 01-.992 0l-7-4A1 1 0 012 14V6a1 1 0 01.504-.868l7-4z"/>
        </svg>
        <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.504 1.132a1 1 0 01.992 0l7 4A1 1 0 0118 6v8a1 1 0 01-.504.868l-7 4a1 1 0 01-.992 0l-7-4A1 1 0 012 14V6a1 1 0 01.504-.868l7-4z"/>
        </svg>
    </div>
</div>
@endsection
