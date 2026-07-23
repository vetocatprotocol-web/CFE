@extends('layouts.guest')

@section('title', 'Login - Haland PetCare')

@section('content')
<div x-data="{ loading: false, showPassword: false }" class="min-h-screen flex flex-col lg:flex-row">

    {{-- Left Side: Branding --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <g fill="white" fill-rule="evenodd">
                    <circle cx="60" cy="80" r="20"/><circle cx="100" cy="60" r="25"/><circle cx="140" cy="80" r="20"/>
                    <ellipse cx="60" cy="120" rx="25" ry="30" transform="rotate(-15 60 120)"/>
                    <ellipse cx="140" cy="120" rx="25" ry="30" transform="rotate(15 140 120)"/>
                    <ellipse cx="80" cy="150" rx="25" ry="30" transform="rotate(-5 80 150)"/>
                    <ellipse cx="120" cy="150" rx="25" ry="30" transform="rotate(5 120 150)"/>
                </g>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col items-center justify-center w-full px-12 text-white">
            <div class="mb-8">
                <svg class="w-32 h-32 drop-shadow-lg" fill="currentColor" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="35" cy="30" rx="12" ry="15" opacity="0.9"/>
                    <ellipse cx="65" cy="30" rx="12" ry="15" opacity="0.9"/>
                    <ellipse cx="20" cy="55" rx="15" ry="20" transform="rotate(-20 20 55)" opacity="0.85"/>
                    <ellipse cx="80" cy="55" rx="15" ry="20" transform="rotate(20 80 55)" opacity="0.85"/>
                    <ellipse cx="38" cy="75" rx="16" ry="20" transform="rotate(-8 38 75)" opacity="0.9"/>
                    <ellipse cx="62" cy="75" rx="16" ry="20" transform="rotate(8 62 75)" opacity="0.9"/>
                    <circle cx="50" cy="50" r="28" opacity="0.6"/>
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold mb-4 tracking-tight">Haland PetCare</h1>
            <p class="text-xl text-blue-100 mb-12 text-center max-w-md">Sistem Manajemen Klinik Hewan</p>

            <div class="space-y-5 max-w-sm w-full">
                <div class="flex items-start gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-5 py-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-blue-50 text-sm leading-relaxed">Manajemen kunjungan & rekam medis</p>
                </div>
                <div class="flex items-start gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-5 py-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <p class="text-blue-50 text-sm leading-relaxed">Sistem billing & pembayaran otomatis</p>
                </div>
                <div class="flex items-start gap-4 bg-white/10 backdrop-blur-sm rounded-xl px-5 py-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="text-blue-50 text-sm leading-relaxed">Portal pelanggan self-service</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Side: Form --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-gradient-to-br from-blue-50 via-indigo-50 to-slate-50 min-h-screen lg:min-h-0">
        <div class="w-full max-w-md">

            {{-- Mobile Header --}}
            <div class="text-center mb-8 lg:hidden">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-blue-600 mb-4 shadow-lg shadow-blue-600/25">
                    <svg class="w-9 h-9 text-white" fill="currentColor" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="35" cy="30" rx="12" ry="15"/>
                        <ellipse cx="65" cy="30" rx="12" ry="15"/>
                        <ellipse cx="20" cy="55" rx="15" ry="20" transform="rotate(-20 20 55)"/>
                        <ellipse cx="80" cy="55" rx="15" ry="20" transform="rotate(20 80 55)"/>
                        <ellipse cx="38" cy="75" rx="16" ry="20" transform="rotate(-8 38 75)"/>
                        <ellipse cx="62" cy="75" rx="16" ry="20" transform="rotate(8 62 75)"/>
                        <circle cx="50" cy="50" r="28" opacity="0.6"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Haland PetCare</h2>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/50 p-8 sm:p-10">
                <div class="text-center mb-8">
                    <div class="hidden lg:inline-flex items-center justify-center w-14 h-14 rounded-xl bg-blue-600 mb-4 shadow-lg shadow-blue-600/20">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="35" cy="30" rx="12" ry="15"/>
                            <ellipse cx="65" cy="30" rx="12" ry="15"/>
                            <ellipse cx="20" cy="55" rx="15" ry="20" transform="rotate(-20 20 55)"/>
                            <ellipse cx="80" cy="55" rx="15" ry="20" transform="rotate(20 80 55)"/>
                            <ellipse cx="38" cy="75" rx="16" ry="20" transform="rotate(-8 38 75)"/>
                            <ellipse cx="62" cy="75" rx="16" ry="20" transform="rotate(8 62 75)"/>
                            <circle cx="50" cy="50" r="28" opacity="0.6"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Selamat Datang</h2>
                    <p class="text-sm text-gray-500 mt-1">Masuk ke akun Anda</p>
                </div>

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm" role="alert">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <span class="font-medium">Terjadi kesalahan</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2" role="alert">
                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" @submit="loading = true" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                placeholder="nama@contoh.com"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                id="password"
                                required
                                class="w-full pl-11 pr-11 py-3 border border-gray-300 rounded-xl text-sm text-gray-900 placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                                placeholder="Masukkan password"
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                                tabindex="-1"
                            >
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center gap-2.5 cursor-pointer group">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember_me"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition-colors"
                            >
                            <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Ingat saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg shadow-blue-600/25 hover:shadow-blue-700/30 disabled:shadow-none"
                    >
                        <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        <span x-text="loading ? 'Masuk...' : 'Masuk'">Masuk</span>
                    </button>
                </form>
            </div>

            {{-- Footer --}}
            <p class="text-center text-xs text-gray-400 mt-8">&copy; {{ date('Y') }} Haland PetCare</p>
        </div>
    </div>
</div>
@endsection
