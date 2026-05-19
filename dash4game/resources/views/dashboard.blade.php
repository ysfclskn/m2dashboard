<x-app-layout>
    <x-slot name="header">🏰 Dashboard</x-slot>

    <div class="space-y-6">

        {{-- Welcome --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h1 class="text-xl font-bold text-amber-400">Welcome back, {{ auth()->user()->name }}! ⚔️</h1>
            <p class="text-gray-400 text-sm mt-1">Here's what's happening across your game projects.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- My Projects --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-100">🎮 My Game Projects</h2>
                    <a href="{{ route('projects.index') }}" class="text-xs text-amber-400 hover:text-amber-300">View all →</a>
                </div>
                @forelse ($myProjects as $project)
                    <a href="{{ route('projects.show', $project) }}"
                       class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-800 transition-colors group">
                        <div>
                            <div class="text-sm font-medium text-gray-100 group-hover:text-amber-400 transition-colors">
                                {{ $project->name }}
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $project->genre }} · {{ $project->tasks_count }} quests</div>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 text-gray-400 border border-gray-700">
                            {{ $project->status->label() }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-gray-500 py-4 text-center">No projects yet.
                        <a href="{{ route('projects.create') }}" class="text-amber-400 hover:text-amber-300">Create one →</a>
                    </p>
                @endforelse
            </div>

            {{-- My Quests --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-100">📜 My Active Quests</h2>
                </div>
                @forelse ($myTasks as $task)
                    <a href="{{ route('projects.quests.show', [$task->project, $task]) }}"
                       class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-800 transition-colors group">
                        <span class="text-base">{{ $task->type->icon() }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-gray-100 truncate group-hover:text-amber-400 transition-colors">
                                {{ $task->title }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $task->project->name }}</div>
                        </div>
                        @php
                            $pColors = ['critical'=>'text-red-400','high'=>'text-orange-400','medium'=>'text-blue-400','low'=>'text-gray-500'];
                        @endphp
                        <span class="text-xs font-medium {{ $pColors[$task->priority->value] ?? 'text-gray-500' }}">
                            {{ $task->priority->icon() }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-gray-500 py-4 text-center">No active quests assigned to you.</p>
                @endforelse
            </div>

        </div>

        {{-- CTA if no projects --}}
        @if ($myProjects->isEmpty())
            <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-6 text-center">
                <p class="text-amber-400 font-semibold mb-2">Begin your quest! 🗡️</p>
                <p class="text-gray-400 text-sm mb-4">Create your first game project to get started.</p>
                <a href="{{ route('projects.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + Create Game Project
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
