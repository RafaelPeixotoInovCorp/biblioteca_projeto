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
</head>
<body class="font-sans antialiased min-h-screen bg-base-200">
<!-- Navbar -->
<div class="bg-base-100 shadow-lg">
    <div class="flex justify-between items-center px-6 py-3 max-w-7xl mx-auto">
        <!-- Logo à esquerda -->
        <div>
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('images/livro.png') }}"
                     alt="Logo Biblioteca"
                     style="height: 32px; width: auto; max-width: 32px; object-fit: contain;">
                <span class="text-xl font-bold">Biblioteca</span>
            </a>
        </div>

        <!-- Botões à direita -->
        <div class="flex items-center gap-4">

            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Entrar</a>
                <a href="{{ route('register') }}" class="btn btn-outline btn-sm">Registar</a>
            @endauth
        </div>
    </div>
</div>

<!-- Espaço para centralizar o card -->
<div class="min-h-[calc(100vh-73px)] flex items-center justify-center">
    <!-- Card principal -->
    <div class="card w-96 bg-base-100 shadow-2xl rounded-3xl">
        <div class="card-body items-center text-center p-8">
            <!-- Logo no card -->
            <div class="mb-6">
                <img src="{{ asset('images/livro.png') }}"
                     alt="Logo Biblioteca"
                     style="height: 80px; width: auto; object-fit: contain;">
            </div>

            <h2 class="card-title text-3xl mb-4 leading-tight">Todos os seus livros favoritos num só lugar.</h2>

            <div class="card-actions mt-8 w-full flex justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-wide rounded-full">Ir para Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-wide rounded-full">Começar Agora</a>
                @endauth
            </div>

            @guest
                <p class="text-sm text-base-content/60 mt-6">
                    Já tem conta? <a href="{{ route('login') }}" class="link link-primary font-medium">Entrar</a>
                </p>
            @endguest
        </div>
    </div>
</div>

</body>
</html>
