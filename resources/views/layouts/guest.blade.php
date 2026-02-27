<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="autumn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Biblioteca</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen flex flex-col items-center justify-center">

<div class="w-full max-w-md px-4">
    <!-- Logo da Biblioteca -->
    <div class="text-center mb-6">
        <a href="/" class="inline-flex flex-col items-center gap-2">
            <img src="{{ asset('images/livro.png') }}"
                 alt="Logo Biblioteca"
                 style="height: 64px; width: auto; object-fit: contain;">
            <span class="text-2xl font-bold text-base-content">Biblioteca</span>
        </a>
    </div>

    <!-- Card com tema DaisyUI -->
    <div class="card bg-base-100 shadow-2xl rounded-3xl">
        <div class="card-body p-8">
            {{ $slot }}
        </div>
    </div>
</div>

@livewireScripts

</body>
</html>
