<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ChronosEstudos') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>[x-cloak] { display: none !important; }</style>
    <script>
        (function(){
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.documentElement.style.setProperty('--sidebar-w', '4rem');
                document.documentElement.style.setProperty('--content-pl', '4rem');
            } else {
                document.documentElement.style.setProperty('--sidebar-w', '13rem');
                document.documentElement.style.setProperty('--content-pl', '13rem');
            }
        })();
    </script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="flex min-h-screen" x-data="{ mobileMenuOpen: false, sidebarCollapsed: JSON.parse(localStorage.getItem('sidebarCollapsed') || 'false') }">
        {{-- Sidebar desktop --}}
        @include('layouts.sidebar')

        {{-- Conteúdo principal --}}
        <div class="flex-1 flex flex-col pb-20 lg:pb-0"
             style="padding-left: var(--content-pl, 13rem)"
             x-bind:style="sidebarCollapsed ? 'padding-left: 4rem' : 'padding-left: 13rem'">
            {{-- Top bar --}}
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        @isset($header)
                            <h1 class="text-lg font-semibold text-slate-800">{{ $header }}</h1>
                        @else
                            <h1 class="text-lg font-semibold text-slate-800">{{ config('app.name') }}</h1>
                        @endisset
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-slate-500 hidden sm:inline">{{ Auth::user()->name }}</span>
                        <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Perfil</a>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Bottom nav mobile --}}
    @include('layouts.bottom-nav')

    @stack('scripts')
</body>
</html>
