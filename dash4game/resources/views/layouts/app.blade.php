@props(['currentProject' => null])
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
    <div class="flex min-h-screen">

        {{-- ── Sidebar ─────────────────────────────────────────────────────────── --}}
        <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col fixed inset-y-0 left-0 z-50">

            {{-- Logo --}}
            <div class="flex items-center gap-2 px-6 h-16 border-b border-gray-800">
                <span class="text-xl">⚔️</span>
                <a href="{{ route('dashboard') }}" class="text-amber-400 font-bold text-lg tracking-wide">Dash4Game</a>
            </div>

            {{-- Main Nav --}}
            <nav class="flex-1 px-3 py-4 overflow-y-auto">
                @php
                    $linkBase = 'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors';
                    $linkActive = 'bg-amber-500/10 text-amber-400 font-medium';
                    $linkIdle = 'text-gray-400 hover:bg-gray-800 hover:text-gray-100';
                @endphp

                <a href="{{ route('dashboard') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $linkActive : $linkIdle }}">
                    🏰 Dashboard
                </a>
                <a href="{{ route('projects.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('projects.index') ? $linkActive : $linkIdle }} mt-1">
                    🎮 Game Projects
                </a>

                {{-- Project-specific nav (pushed from project views) --}}
                @if ($currentProject)
                    <div class="mt-4 pt-4 border-t border-gray-800">
                        <p class="px-3 text-xs text-gray-600 uppercase tracking-wider mb-2 truncate">
                            {{ $currentProject->name }}
                        </p>
                        <a href="{{ route('projects.quests.index', $currentProject) }}"
                            class="{{ $linkBase }} {{ request()->routeIs('projects.quests.*') ? $linkActive : $linkIdle }}">
                            📜 Quest Board
                        </a>
                        <a href="{{ route('projects.sprints.index', $currentProject) }}"
                            class="{{ $linkBase }} {{ request()->routeIs('projects.sprints.*') ? $linkActive : $linkIdle }} mt-1">
                            ⚡ Raid Sprints
                        </a>
                        <a href="{{ route('projects.wiki.index', $currentProject) }}"
                            class="{{ $linkBase }} {{ request()->routeIs('projects.wiki.*') ? $linkActive : $linkIdle }} mt-1">
                            📖 Game Wiki
                        </a>
                        <a href="{{ route('projects.reports.index', $currentProject) }}"
                            class="{{ $linkBase }} {{ request()->routeIs('projects.reports.*') ? $linkActive : $linkIdle }} mt-1">
                            📊 Adventure Reports
                        </a>
                        <a href="{{ route('projects.members.index', $currentProject) }}"
                            class="{{ $linkBase }} {{ request()->routeIs('projects.members.*') ? $linkActive : $linkIdle }} mt-1">
                            ⚔️ Party Members
                        </a>
                    </div>
                @endif
            </nav>

            {{-- User --}}
            <div class="px-4 py-4 border-t border-gray-800">
                <div class="flex items-center gap-3 mb-3">
                    <img src="{{ auth()->user()->avatarUrl() }}" class="w-8 h-8 rounded-full object-cover" alt="">
                    <div class="min-w-0">
                        <div class="text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <a href="{{ route('profile.edit') }}"
                        class="text-gray-500 hover:text-gray-300 transition-colors">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors">Logout</button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ── Main Content ─────────────────────────────────────────────────────── --}}
        <div class="flex-1 ml-64 flex flex-col min-h-screen">

            {{-- Top Bar --}}
            @isset($header)
                <header class="h-16 bg-gray-900 border-b border-gray-800 px-6 flex items-center justify-between">
                    <div class="font-semibold text-gray-100">{{ $header }}</div>
                </header>
            @endisset

            {{-- Flash Messages --}}
            @if (session('success') || session('error'))
                <div class="px-6 pt-4">
                    @if (session('success'))
                        <div class="bg-green-900/40 border border-green-700 text-green-300 px-4 py-3 rounded-lg text-sm mb-2">
                            ✅ {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-900/40 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm mb-2">
                            ❌ {{ session('error') }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- Page Content --}}
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>