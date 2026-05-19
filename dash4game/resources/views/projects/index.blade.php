<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>🎮 Game Projects</span>
            <a href="{{ route('projects.create') }}"
               class="px-4 py-2 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                + New Project
            </a>
        </div>
    </x-slot>

    @if ($projects->isEmpty())
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
            <p class="text-4xl mb-4">🎮</p>
            <h2 class="text-lg font-semibold text-gray-100 mb-2">No game projects yet</h2>
            <p class="text-gray-500 text-sm mb-6">Create your first game project to start tracking quests and raids.</p>
            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center px-5 py-2.5 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                + Create Game Project
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($projects as $project)
                @php
                    $color = $project->status->color();
                    $colorMap = ['gray'=>'text-gray-400 bg-gray-800','blue'=>'text-blue-400 bg-blue-900/30','amber'=>'text-amber-400 bg-amber-900/30','purple'=>'text-purple-400 bg-purple-900/30','green'=>'text-green-400 bg-green-900/30','red'=>'text-red-400 bg-red-900/30'];
                    $badge = $colorMap[$color] ?? $colorMap['gray'];
                @endphp
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-gray-700 transition-colors flex flex-col">
                    <div class="flex items-start justify-between mb-3">
                        <h3 class="font-semibold text-gray-100 leading-tight">{{ $project->name }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full ml-2 shrink-0 {{ $badge }}">
                            {{ $project->status->label() }}
                        </span>
                    </div>
                    @if ($project->genre)
                        <p class="text-xs text-gray-500 mb-2">{{ $project->genre }}</p>
                    @endif
                    @if ($project->description)
                        <p class="text-sm text-gray-400 line-clamp-2 mb-4">{{ $project->description }}</p>
                    @endif
                    <div class="flex items-center gap-4 text-xs text-gray-500 mt-auto mb-4">
                        <span>📜 {{ $project->tasks_count }} quests</span>
                        <span>👥 {{ $project->members_count }} members</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('projects.show', $project) }}"
                           class="flex-1 text-center px-3 py-1.5 bg-gray-800 text-gray-300 text-sm rounded-lg hover:bg-gray-700 hover:text-gray-100 transition-colors">
                            Open Project
                        </a>
                        @can('update', $project)
                            <a href="{{ route('projects.edit', $project) }}"
                               class="px-3 py-1.5 text-gray-500 text-sm rounded-lg hover:bg-gray-800 hover:text-gray-300 transition-colors">
                                Edit
                            </a>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
