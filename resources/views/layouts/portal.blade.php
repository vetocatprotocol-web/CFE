<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Haland PetCare') }} - Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">

    {{-- Top Navbar --}}
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50 h-16 flex items-center">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                {{-- Left: Logo --}}
                <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.5 9.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5-5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM12 21c-4.97 0-9-2.69-9-6 0-2.39 1.6-4.73 4.5-6.34C8.86 7.63 10.37 7 12 7s3.14.63 4.5 1.66C19.4 10.27 21 12.61 21 15c0 3.31-4.03 6-9 6z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 hidden sm:block">Haland PetCare</span>
                </a>

                {{-- Right: Notifications + Profile --}}
                <div class="flex items-center gap-2 sm:gap-4">
                    {{-- Notification Bell --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="relative w-10 h-10 flex items-center justify-center rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @php
                                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 min-w-[1rem] flex items-center justify-center px-1 leading-none">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="px-4 py-3 text-sm font-semibold text-gray-800 border-b border-gray-100 bg-gray-50">Notifications</div>
                            <div class="px-4 py-8 text-sm text-gray-400 text-center">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                                </svg>
                                No new notifications
                            </div>
                        </div>
                    </div>

                    {{-- Profile Dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            <span class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold uppercase shadow-sm">
                                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500 hidden sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->email ?? '' }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('portal.profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    My Profile
                                </a>
                                <a href="{{ route('portal.profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                    Change Password
                                </a>
                            </div>
                            <div class="border-t border-gray-100 py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Mobile Bottom Navigation --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40 lg:hidden" style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="grid grid-cols-5 h-16">
            <a href="{{ route('portal.dashboard') }}" class="flex flex-col items-center justify-center gap-1 min-h-[44px] {{ request()->routeIs('portal.dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">Dashboard</span>
            </a>
            <a href="{{ route('portal.pets') }}" class="flex flex-col items-center justify-center gap-1 min-h-[44px] {{ request()->routeIs('portal.pets*') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">Pets</span>
            </a>
            <a href="{{ route('portal.visits') }}" class="flex flex-col items-center justify-center gap-1 min-h-[44px] {{ request()->routeIs('portal.visits*') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">Visits</span>
            </a>
            <a href="{{ route('portal.invoices') }}" class="flex flex-col items-center justify-center gap-1 min-h-[44px] {{ request()->routeIs('portal.invoices*') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">Invoices</span>
            </a>
            <a href="{{ route('portal.profile.edit') }}" class="flex flex-col items-center justify-center gap-1 min-h-[44px] {{ request()->routeIs('portal.profile*') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">Profile</span>
            </a>
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="flex flex-1">
        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:block w-64 bg-white border-r border-gray-200 min-h-[calc(100vh-4rem)] flex-shrink-0">
            <div class="px-5 py-6">
                <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.5 9.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5-5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM12 21c-4.97 0-9-2.69-9-6 0-2.39 1.6-4.73 4.5-6.34C8.86 7.63 10.37 7 12 7s3.14.63 4.5 1.66C19.4 10.27 21 12.61 21 15c0 3.31-4.03 6-9 6z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-lg font-bold text-gray-900 block leading-tight">Haland</span>
                        <span class="text-xs text-blue-600 font-medium leading-tight">PetCare</span>
                    </div>
                </a>

                <nav class="space-y-1">
                    <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('portal.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('portal.pets') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('portal.pets*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        My Pets
                    </a>
                    <a href="{{ route('portal.visits') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('portal.visits*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Visits
                    </a>
                    <a href="{{ route('portal.invoices') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('portal.invoices*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                        Invoices
                    </a>
                    <a href="{{ route('portal.prescriptions') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('portal.prescriptions*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Prescriptions
                    </a>

                    <div class="pt-4 mt-4 border-t border-gray-100">
                        <a href="{{ route('portal.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-150 {{ request()->routeIs('portal.profile*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            My Profile
                        </a>
                    </div>
                </nav>
            </div>
        </aside>

        {{-- Page Content --}}
        <main class="flex-1 min-w-0 pb-20 lg:pb-0">
            {{-- Flash Messages --}}
            <div class="px-4 sm:px-6 lg:px-8 pt-6">
                <x-flash-messages />
            </div>

            {{-- Page Header --}}
            @hasSection('header')
                <div class="px-4 sm:px-6 lg:px-8 pt-2 pb-4">
                    @yield('header')
                </div>
            @endif

            {{-- Content --}}
            <div class="px-4 sm:px-6 lg:px-8 pb-8">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Footer (hidden on mobile, bottom nav replaces it) --}}
    <footer class="hidden lg:block bg-white border-t border-gray-200 py-6 px-6 text-center text-sm text-gray-400">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-center gap-2 mb-2">
                <div class="w-5 h-5 bg-blue-600 rounded-md flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4.5 9.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5-5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 0a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm5 5a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM12 21c-4.97 0-9-2.69-9-6 0-2.39 1.6-4.73 4.5-6.34C8.86 7.63 10.37 7 12 7s3.14.63 4.5 1.66C19.4 10.27 21 12.61 21 15c0 3.31-4.03 6-9 6z"/>
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-500">Haland PetCare</span>
            </div>
            <p>&copy; {{ date('Y') }} Haland PetCare. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
                setTimeout(function () {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(function () { el.remove(); }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
