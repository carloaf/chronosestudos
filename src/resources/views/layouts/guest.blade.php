<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ChronosEstudos') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col items-center justify-center px-4 py-8" style="background: linear-gradient(135deg, #312e81 0%, #1e1b4b 50%, #172033 100%);">

        <!-- Card -->
        <div class="animate-fade-in-up bg-white rounded-2xl" style="max-width: 400px; width: 100%; padding: 48px 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.35);">
            {{ $slot }}
        </div>

        <!-- Fixed Footer -->
        <footer class="fixed bottom-0 left-0 w-full text-center pointer-events-none z-50 pb-4">
            <span class="text-xs italic tracking-wide" style="color: rgba(255,255,255,0.40);">
                &copy; {{ date('Y') }} ChronosEstudos &mdash; Cronograma com Repetição Espaçada
            </span>
        </footer>
    </body>
</html>
