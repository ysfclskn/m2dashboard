<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-gray-500 text-sm">🎮</span>
                <span>{{ $project->name }}</span>
                @php
                    $colorMap = ['gray'=>'text-gray-400 bg-gray-800','blue'=>'text-blue-400 bg-blue-900/30','amber'=>'text-amber-400 bg-amber-900/30','purple'=>'text-purple-400 bg-purple-900/30','green'=>'text-green-400 bg-green-900/30','red'=>'text-red-400 bg-red-900/30'];
                    $badge = $colorMap[$project->status->color()] ?? $colorMap['gray'];
                @endphp
                <span class="text-xs px-2 py-0.5 rounded-full {{ $badge }}">{{ $project->status->label() }}</span>
            </div>
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="text-sm px-3 py-1.5 border border-amber-700/50 text-amber-500 rounded-lg hover:bg-amber-500/10 hover:border-amber-500 transition-colors">Edit</a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $statuses = ['backlog','todo','in_progress','review','done'];
                $labels   = ['Quest Log','Ready','In Progress','Review','Completed'];
                $colors   = ['gray','blue','amber','purple','green'];
                $total = $taskStats->sum();
            @endphp
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-gray-100">{{ $total }}</div>
                <div class="text-xs text-gray-500 mt-1">Total Quests</div>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-green-400">{{ $taskStats->get('done', 0) }}</div>
                <div class="text-xs text-gray-500 mt-1">Completed</div>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-amber-400">{{ $taskStats->get('in_progress', 0) }}</div>
                <div class="text-xs text-gray-500 mt-1">In Progress</div>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-gray-400">{{ $taskStats->get('backlog', 0) }}</div>
                <div class="text-xs text-gray-500 mt-1">Quest Log</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Active Raids --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-100">⚡ Active Raids</h3>
                    <a href="{{ route('projects.sprints.index', $project) }}" class="text-xs text-amber-400 hover:text-amber-300">View all →</a>
                </div>
                @forelse ($activeSprints as $sprint)
                    @php
                        $isActive = $sprint->status->value === 'active';
                        $pct = $sprint->completionPercentage();
                        $borderColor = $isActive ? 'border-green-800/50 bg-green-900/20' : 'border-gray-700 bg-gray-800/40';
                        $nameColor   = $isActive ? 'text-green-400' : 'text-gray-300';
                    @endphp
                    <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}"
                       class="block p-3 border {{ $borderColor }} rounded-lg hover:border-amber-700/50 transition-colors mb-2 last:mb-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs px-1.5 py-0.5 rounded {{ $isActive ? 'bg-green-900/50 text-green-400' : 'bg-gray-700 text-gray-400' }}">
                                {{ $sprint->status->label() }}
                            </span>
                            <span class="font-medium {{ $nameColor }} text-sm truncate">{{ $sprint->name }}</span>
                        </div>
                        @if ($sprint->goal)
                            <div class="text-xs text-gray-500 mb-2 truncate">{{ $sprint->goal }}</div>
                        @endif
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Progress</span><span>{{ $pct }}%</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1">
                            <div class="{{ $isActive ? 'bg-green-500' : 'bg-gray-600' }} h-1 rounded-full transition-all"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-500">No active raids.
                        <a href="{{ route('projects.sprints.create', $project) }}" class="text-amber-400 hover:text-amber-300">Start one →</a>
                    </p>
                @endforelse
            </div>

            {{-- Recent Quests --}}
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-100">📜 Recent Quests</h3>
                    <a href="{{ route('projects.quests.index', $project) }}" class="text-xs text-amber-400 hover:text-amber-300">View all →</a>
                </div>
                @forelse ($recentTasks as $task)
                    <a href="{{ route('projects.quests.show', [$project, $task]) }}"
                       class="flex items-center gap-2 py-2 border-b border-gray-800 last:border-0 hover:text-amber-400 transition-colors group">
                        <span class="text-sm">{{ $task->type->icon() }}</span>
                        <span class="text-sm text-gray-300 truncate group-hover:text-amber-400 transition-colors">{{ $task->title }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-500">No quests yet. <a href="{{ route('projects.quests.create', $project) }}" class="text-amber-400 hover:text-amber-300">Add one →</a></p>
                @endforelse
            </div>

            {{-- Latest Wiki & Report --}}
            <div class="space-y-4">
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-100">📖 Wiki</h3>
                        <a href="{{ route('projects.wiki.index', $project) }}" class="text-xs text-amber-400 hover:text-amber-300">View →</a>
                    </div>
                    @forelse ($latestWikiPages as $page)
                        <a href="{{ route('projects.wiki.show', [$project, $page]) }}"
                           class="block text-sm text-gray-400 hover:text-amber-400 transition-colors py-1">
                            {{ $page->title }}
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">No wiki pages yet.</p>
                    @endforelse
                </div>

                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-100">📊 Latest Report</h3>
                        <a href="{{ route('projects.reports.index', $project) }}" class="text-xs text-amber-400 hover:text-amber-300">View →</a>
                    </div>
                    @if ($latestReport)
                        <a href="{{ route('projects.reports.show', [$project, $latestReport]) }}"
                           class="block">
                            <div class="text-sm text-gray-300 hover:text-amber-400 transition-colors">{{ $latestReport->title }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $latestReport->period_start_date->format('M j') }} – {{ $latestReport->period_end_date->format('M j, Y') }}</div>
                        </a>
                    @else
                        <p class="text-sm text-gray-500">No reports yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
