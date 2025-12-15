<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-900 to-gray-800">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072&auto=format&fit=crop')] bg-cover bg-center opacity-20"></div>
            
            <div class="relative z-10 flex flex-col items-center">
                <a href="/" class="mb-8 transition transform hover:scale-105">
                    <x-application-logo class="w-24 h-24 fill-current text-white drop-shadow-lg" />
                </a>

                <div class="w-full sm:max-w-md px-8 py-8 bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl overflow-hidden sm:rounded-2xl">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
