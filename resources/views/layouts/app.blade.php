<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mailvia') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-black">
<div class="min-h-screen flex bg-black text-white">

    {{-- Sidebar (dark) --}}
    @include('layouts.sidebar')

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Topbar (dark) --}}
        @include('layouts.navigation')

        {{-- Page heading (white area) --}}
        @isset($header)
            <div class="bg-gray-50 border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="text-gray-900">
                        {{ $header }}
                    </div>
                </div>
            </div>
        @endisset

        {{-- Page Content (white background) --}}
        <main class="flex-1 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>
</body>
</html>