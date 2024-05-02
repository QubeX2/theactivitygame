<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'The Activity Game') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data x-init="window.scrollTo(0, 0)" class="sm:flex sm:justify-center font-sans antialiased bg-indigo-700">
        <div class="min-h-screen">
            <livewire:layout.navigation />
            <!-- Page Content -->
            <main class="">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
