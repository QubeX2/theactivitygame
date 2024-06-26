<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,500;1,700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:pt-20 items-center pt-6 bg-gradient-to-b from-red-500 via-red-700 to-violet-800">
            <div class="mb-4">
                <a href="/" wire:navigate>
                    <x-application-logo class="w-20 h-20 fill-current" />
                </a>
            </div>
            <h1 class="w-full text-center font-bold text-2xl text-white">{{__('Welcome to The Activity Game')}}!</h1>
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden rounded-3xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
