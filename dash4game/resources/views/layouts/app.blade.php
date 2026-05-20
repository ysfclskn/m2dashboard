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
    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-950 text-gray-100">
<div class="flex min-h-screen" x-data="{
    collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
    toggle() { this.collapsed = !this.collapsed; localStorage.setItem('sidebar_collapsed', this.collapsed); }
}">

    {{-- ── Sidebar ─────────────────────────────────────────────────────────── --}}
    <aside :class="collapsed ? 'w-16' : 'w-64'"
           class="bg-gray-900 border-r border-gray-800 flex flex-col fixed inset-y-0 left-0 z-50 transition-all duration-200">

        {{-- Logo + Toggle --}}
        <div class="flex items-center h-16 border-b border-gray-800 px-3 overflow-hidden">
            <a href="{{ route('dashboard') }}"
               :title="collapsed ? 'Dash4Game' : ''"
               class="flex items-center gap-2 min-w-0 flex-1">
                <span class="text-xl shrink-0">⚔️</span>
                <span x-show="!collapsed" x-cloak class="text-amber-400 font-bold text-lg tracking-wide whitespace-nowrap">Dash4Game</span>
            </a>
            <button @click="toggle()"
                    class="ml-auto shrink-0 p-1.5 rounded text-gray-500 hover:text-gray-200 hover:bg-gray-800 transition-colors"
                    :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <svg x-show="!collapsed" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7M18 19l-7-7 7-7" />
                </svg>
                <svg x-show="collapsed" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M6 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- Main Nav --}}
        <nav class="flex-1 px-2 py-4 overflow-y-auto overflow-x-hidden">
            @php
                $linkBase   = 'flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition-colors w-full';
                $linkActive = 'bg-amber-500/10 text-amber-400 font-medium';
                $linkIdle   = 'text-gray-400 hover:bg-gray-800 hover:text-gray-100';
            @endphp

            <a href="{{ route('dashboard') }}"
               :title="collapsed ? 'Dashboard' : ''"
               class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $linkActive : $linkIdle }}">
                <span class="shrink-0 text-base">🏰</span>
                <span x-show="!collapsed" x-cloak class="whitespace-nowrap">Dashboard</span>
            </a>
            <a href="{{ route('projects.index') }}"
               :title="collapsed ? 'Game Projects' : ''"
               class="{{ $linkBase }} {{ request()->routeIs('projects.index') ? $linkActive : $linkIdle }} mt-1">
                <span class="shrink-0 text-base">🎮</span>
                <span x-show="!collapsed" x-cloak class="whitespace-nowrap">Game Projects</span>
            </a>

            @if ($currentProject)
                <div class="mt-4 pt-4 border-t border-gray-800">

                    {{-- Tıklanabilir proje adı --}}
                    <a href="{{ route('projects.show', $currentProject) }}"
                       :title="collapsed ? '{{ $currentProject->name }}' : ''"
                       class="{{ $linkBase }} {{ request()->routeIs('projects.show') && request()->route('project')?->id == $currentProject->id ? $linkActive : $linkIdle }} mb-1">
                        <span class="shrink-0 text-base">🎯</span>
                        <span x-show="!collapsed" x-cloak class="whitespace-nowrap font-semibold truncate">{{ $currentProject->name }}</span>
                    </a>

                    {{-- Treeview: collapsed'da gizle, expanded'da göster --}}
                    <div x-show="!collapsed" x-cloak
                         class="ml-3 pl-3 border-l border-gray-700/60 mt-1 space-y-0.5">
                        @php
                            $treeLink = 'flex items-center gap-2 px-2 py-1.5 rounded-lg text-sm transition-colors';
                        @endphp
                        <a href="{{ route('projects.quests.index', $currentProject) }}"
                           class="{{ $treeLink }} {{ request()->routeIs('projects.quests.*') ? $linkActive : $linkIdle }}">
                            <span class="shrink-0">📜</span>
                            <span class="whitespace-nowrap">Quest Board</span>
                        </a>
                        <a href="{{ route('projects.backlog', $currentProject) }}"
                           class="{{ $treeLink }} {{ request()->routeIs('projects.backlog*') ? $linkActive : $linkIdle }}">
                            <span class="shrink-0">📋</span>
                            <span class="whitespace-nowrap">Quest Backlog</span>
                        </a>
                        <a href="{{ route('projects.sprints.index', $currentProject) }}"
                           class="{{ $treeLink }} {{ request()->routeIs('projects.sprints.*') ? $linkActive : $linkIdle }}">
                            <span class="shrink-0">⚡</span>
                            <span class="whitespace-nowrap">Raid Sprints</span>
                        </a>
                        <a href="{{ route('projects.wiki.index', $currentProject) }}"
                           class="{{ $treeLink }} {{ request()->routeIs('projects.wiki.*') ? $linkActive : $linkIdle }}">
                            <span class="shrink-0">📖</span>
                            <span class="whitespace-nowrap">Game Wiki</span>
                        </a>
                        <a href="{{ route('projects.reports.index', $currentProject) }}"
                           class="{{ $treeLink }} {{ request()->routeIs('projects.reports.*') ? $linkActive : $linkIdle }}">
                            <span class="shrink-0">📊</span>
                            <span class="whitespace-nowrap">Adventure Reports</span>
                        </a>
                        <a href="{{ route('projects.members.index', $currentProject) }}"
                           class="{{ $treeLink }} {{ request()->routeIs('projects.members.*') ? $linkActive : $linkIdle }}">
                            <span class="shrink-0">⚔️</span>
                            <span class="whitespace-nowrap">Party Members</span>
                        </a>
                    </div>

                    {{-- Collapsed modunda emoji-only linkler --}}
                    <div x-show="collapsed" x-cloak class="mt-1 space-y-0.5">
                        @foreach([
                            ['📜', route('projects.quests.index', $currentProject), 'Quest Board'],
                            ['📋', route('projects.backlog', $currentProject), 'Quest Backlog'],
                            ['⚡', route('projects.sprints.index', $currentProject), 'Raid Sprints'],
                            ['📖', route('projects.wiki.index', $currentProject), 'Game Wiki'],
                            ['📊', route('projects.reports.index', $currentProject), 'Adventure Reports'],
                            ['⚔️', route('projects.members.index', $currentProject), 'Party Members'],
                        ] as [$icon, $href, $title])
                            <a href="{{ $href }}" title="{{ $title }}"
                               class="flex items-center justify-center px-2 py-1.5 rounded-lg text-sm transition-colors text-gray-400 hover:bg-gray-800 hover:text-gray-100">
                                {{ $icon }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </nav>

        {{-- User --}}
        <div class="px-2 py-4 border-t border-gray-800">
            <div class="flex items-center gap-3 mb-2 overflow-hidden">
                <img src="{{ auth()->user()->avatarUrl() }}"
                     class="w-8 h-8 rounded-full object-cover shrink-0"
                     :title="collapsed ? '{{ auth()->user()->name }}' : ''"
                     alt="">
                <div x-show="!collapsed" x-cloak class="min-w-0">
                    <div class="text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div x-show="!collapsed" x-cloak class="flex items-center gap-3 text-xs pl-1">
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
    <div :class="collapsed ? 'ml-16' : 'ml-64'"
         class="flex-1 flex flex-col min-h-screen transition-all duration-200">

        {{-- Top Bar --}}
        @isset($header)
            <header class="h-16 bg-gray-900 border-b border-gray-800 px-6 flex items-center justify-between">
                <div class="font-semibold text-gray-100 w-full">{{ $header }}</div>
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
    @stack('scripts')
</body>

</html>
