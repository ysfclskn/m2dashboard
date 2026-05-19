<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>⚡ Raid Sprints — {{ $project->name }}</span>
            @can('update', $project)
                <a href="{{ route('projects.sprints.create', $project) }}"
                   class="px-4 py-2 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + New Raid
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $statusColors = ['planned'=>'text-gray-400 bg-gray-800','active'=>'text-green-400 bg-green-900/30','completed'=>'text-blue-400 bg-blue-900/30','cancelled'=>'text-red-400 bg-red-900/30'];
    @endphp

    @if ($sprints->isEmpty())
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
            <p class="text-4xl mb-4">⚡</p>
            <h2 class="text-lg font-semibold text-gray-100 mb-2">No raids yet</h2>
            <p class="text-gray-500 text-sm mb-6">Create a raid sprint to organize your quests into focused cycles.</p>
            @can('update', $project)
                <a href="{{ route('projects.sprints.create', $project) }}"
                   class="inline-flex items-center px-5 py-2.5 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + Create First Raid
                </a>
            @endcan
        </div>
    @else
        <div class="space-y-4">
            @foreach ($sprints->sortByDesc(fn($s) => $s->status->value === 'active' ? 1 : 0) as $sprint)
                @php $badge = $statusColors[$sprint->status->value] ?? $statusColors['planned']; @endphp
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-gray-700 transition-colors">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}"
                                   class="font-semibold text-gray-100 hover:text-amber-400 transition-colors">
                                    {{ $sprint->name }}
                                </a>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $badge }}">{{ $sprint->status->label() }}</span>
                            </div>
                            @if ($sprint->goal)
                                <p class="text-sm text-gray-400 mt-1">{{ $sprint->goal }}</p>
                            @endif
                        </div>
                        @can('update', $project)
                            <a href="{{ route('projects.sprints.edit', [$project, $sprint]) }}"
                               class="text-sm text-gray-500 hover:text-gray-300 transition-colors ml-4">Edit</a>
                        @endcan
                    </div>

                    <div class="flex items-center gap-6 text-sm">
                        <span class="text-gray-500">📜 {{ $sprint->tasks_count }} quests</span>
                        @if ($sprint->start_date)
                            <span class="text-gray-500">
                                {{ $sprint->start_date->format('M j') }}
                                @if ($sprint->end_date) — {{ $sprint->end_date->format('M j, Y') }} @endif
                            </span>
                        @endif
                    </div>

                    @if ($sprint->status->value === 'active')
                        @php $pct = $sprint->completionPercentage(); @endphp
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span><span>{{ $pct }}%</span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-1.5">
                                <div class="bg-green-500 h-1.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
