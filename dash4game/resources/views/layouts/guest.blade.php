<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Dash4Game') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-950 text-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">

        <div class="mb-8 text-center">
            <a href="/" class="inline-flex items-center gap-2 text-amber-400 font-bold text-3xl">
                <span>⚔️</span> Dash4Game
            </a>
            <p class="text-gray-500 text-sm mt-1">Project management for indie game teams</p>
        </div>

        <div class="w-full max-w-md bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl px-8 py-8">
            {{ $slot }}
        </div>

    </div>
</body>

</html>