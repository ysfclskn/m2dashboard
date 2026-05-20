<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>📋 Quest Backlog — {{ $project->name }}</span>
            @can('manageTasks', $project)
                <a href="{{ route('projects.quests.create', $project) }}"
                   class="px-4 py-2 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + New Quest
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $pColors = ['critical'=>'text-red-400 bg-red-900/30','high'=>'text-orange-400 bg-orange-900/30','medium'=>'text-blue-400 bg-blue-900/30','low'=>'text-gray-400 bg-gray-800'];
        $sColors = ['backlog'=>'text-gray-400 bg-gray-800','todo'=>'text-blue-400 bg-blue-900/30','in_progress'=>'text-amber-400 bg-amber-900/30','review'=>'text-purple-400 bg-purple-900/30','done'=>'text-green-400 bg-green-900/30'];
    @endphp

    <div class="space-y-8">

        {{-- ── Section A: Backlog ──────────────────────────────────────── --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <h2 class="text-lg font-semibold text-gray-100">📋 Backlog</h2>
                <span class="text-xs bg-gray-800 text-gray-500 px-2 py-0.5 rounded-full">{{ $backlogTasks->count() }} quests</span>
            </div>

            @if ($backlogTasks->isEmpty())
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
                    <p class="text-gray-500 text-sm">No unassigned quests. All quests are assigned to a raid.</p>
                </div>
            @else
                <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                    <div class="divide-y divide-gray-800">
                        @foreach ($backlogTasks as $task)
                            <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-800/50 transition-colors">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <span class="text-sm shrink-0">{{ $task->type->icon() }}</span>
                                    <a href="{{ route('projects.quests.show', [$project, $task]) }}"
                                       class="text-sm text-gray-200 hover:text-amber-400 transition-colors truncate">
                                        {{ $task->title }}
                                    </a>
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $sColors[$task->status->value] ?? '' }} shrink-0">{{ $task->status->label() }}</span>
                                    <span class="text-xs px-1.5 py-0.5 rounded-full {{ $pColors[$task->priority->value] ?? '' }} shrink-0">{{ $task->priority->icon() }}</span>
                                </div>
                                <div class="flex items-center gap-3 shrink-0 ml-3">
                                    @if ($task->assignee)
                                        <div class="flex items-center gap-1.5">
                                            <img src="{{ $task->assignee->avatarUrl() }}" class="w-5 h-5 rounded-full" alt="">
                                            <span class="text-xs text-gray-500 hidden sm:inline">{{ $task->assignee->name }}</span>
                                        </div>
                                    @endif
                                    @can('manageTasks', $project)
                                        <form method="POST" action="{{ route('projects.backlog.tasks.sprint', [$project, $task]) }}">
                                            @csrf @method('PATCH')
                                            <select name="sprint_id" onchange="this.form.submit()"
                                                class="bg-gray-800 border border-gray-700 text-gray-300 rounded-lg px-2 py-1 text-xs focus:border-amber-500 focus:outline-none min-w-[130px]">
                                                <option value="" selected>📋 Backlog</option>
                                                @foreach ($assignableSprints as $sprint)
                                                    <option value="{{ $sprint->id }}">
                                                        {{ $sprint->status->value === 'active' ? '⚡' : '📅' }} {{ $sprint->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Section B: Active Raid ─────────────────────────────────── --}}
        <div>
            <div class="flex items-center gap-3 mb-4">
                <h2 class="text-lg font-semibold text-gray-100">⚡ Active Raid</h2>
                @if ($activeSprint)
                    <span class="text-xs px-2 py-0.5 rounded-full text-green-400 bg-green-900/30">{{ $activeSprint->status->label() }}</span>
                @endif
            </div>

            @if ($activeSprint)
                <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                    {{-- Sprint header --}}
                    <div class="px-5 py-4 border-b border-gray-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('projects.sprints.show', [$project, $activeSprint]) }}"
                                   class="font-semibold text-green-400 hover:text-green-300 transition-colors">
                                    {{ $activeSprint->name }}
                                </a>
                                @if ($activeSprint->goal)
                                    <p class="text-xs text-gray-500 mt-1">{{ $activeSprint->goal }}</p>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500">
                                @if ($activeSprint->start_date)
                                    {{ $activeSprint->start_date->format('M j') }}
                                    @if ($activeSprint->end_date) — {{ $activeSprint->end_date->format('M j') }} @endif
                                @endif
                                <span class="ml-2">{{ $activeSprint->tasks->count() }} quests</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sprint tasks --}}
                    @if ($activeSprint->tasks->isEmpty())
                        <div class="px-5 py-6 text-center text-sm text-gray-500">No quests assigned to this raid yet.</div>
                    @else
                        <div class="divide-y divide-gray-800">
                            @foreach ($activeSprint->tasks as $task)
                                <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-800/50 transition-colors">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        <span class="text-sm shrink-0">{{ $task->type->icon() }}</span>
                                        <a href="{{ route('projects.quests.show', [$project, $task]) }}"
                                           class="text-sm text-gray-200 hover:text-amber-400 transition-colors truncate">
                                            {{ $task->title }}
                                        </a>
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $sColors[$task->status->value] ?? '' }} shrink-0">{{ $task->status->label() }}</span>
                                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $pColors[$task->priority->value] ?? '' }} shrink-0">{{ $task->priority->icon() }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 ml-3">
                                        @if ($task->assignee)
                                            <div class="flex items-center gap-1.5">
                                                <img src="{{ $task->assignee->avatarUrl() }}" class="w-5 h-5 rounded-full" alt="">
                                                <span class="text-xs text-gray-500 hidden sm:inline">{{ $task->assignee->name }}</span>
                                            </div>
                                        @endif
                                        @can('manageTasks', $project)
                                            <form method="POST" action="{{ route('projects.backlog.tasks.sprint', [$project, $task]) }}">
                                                @csrf @method('PATCH')
                                                <select name="sprint_id" onchange="this.form.submit()"
                                                    class="bg-gray-800 border border-gray-700 text-gray-300 rounded-lg px-2 py-1 text-xs focus:border-amber-500 focus:outline-none min-w-[130px]">
                                                    <option value="">📋 Backlog</option>
                                                    @foreach ($assignableSprints as $sprint)
                                                        <option value="{{ $sprint->id }}" {{ $task->sprint_id == $sprint->id ? 'selected' : '' }}>
                                                            {{ $sprint->status->value === 'active' ? '⚡' : '📅' }} {{ $sprint->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
                    <p class="text-gray-500 text-sm">No active raid. <a href="{{ route('projects.sprints.create', $project) }}" class="text-amber-400 hover:text-amber-300">Create or activate a sprint</a> to plan work.</p>
                </div>
            @endif
        </div>

        {{-- ── Section C: Planned Raids ───────────────────────────────── --}}
        @if ($plannedSprints->isNotEmpty())
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <h2 class="text-lg font-semibold text-gray-100">📅 Planned Raids</h2>
                    <span class="text-xs bg-gray-800 text-gray-500 px-2 py-0.5 rounded-full">{{ $plannedSprints->count() }} raids</span>
                </div>

                <div class="space-y-4">
                    @foreach ($plannedSprints as $sprint)
                        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                            {{-- Sprint header --}}
                            <div class="px-5 py-4 border-b border-gray-800">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}"
                                           class="font-semibold text-gray-100 hover:text-amber-400 transition-colors">
                                            {{ $sprint->name }}
                                        </a>
                                        <span class="text-xs px-2 py-0.5 rounded-full text-gray-400 bg-gray-800 ml-2">Planned</span>
                                        @if ($sprint->goal)
                                            <p class="text-xs text-gray-500 mt-1">{{ $sprint->goal }}</p>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if ($sprint->start_date)
                                            {{ $sprint->start_date->format('M j') }}
                                            @if ($sprint->end_date) — {{ $sprint->end_date->format('M j') }} @endif
                                        @endif
                                        <span class="ml-2">{{ $sprint->tasks->count() }} quests</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Sprint tasks --}}
                            @if ($sprint->tasks->isEmpty())
                                <div class="px-5 py-4 text-center text-sm text-gray-600">No quests assigned yet.</div>
                            @else
                                <div class="divide-y divide-gray-800">
                                    @foreach ($sprint->tasks as $task)
                                        <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-800/50 transition-colors">
                                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                                <span class="text-sm shrink-0">{{ $task->type->icon() }}</span>
                                                <a href="{{ route('projects.quests.show', [$project, $task]) }}"
                                                   class="text-sm text-gray-200 hover:text-amber-400 transition-colors truncate">
                                                    {{ $task->title }}
                                                </a>
                                                <span class="text-xs px-2 py-0.5 rounded-full {{ $sColors[$task->status->value] ?? '' }} shrink-0">{{ $task->status->label() }}</span>
                                                <span class="text-xs px-1.5 py-0.5 rounded-full {{ $pColors[$task->priority->value] ?? '' }} shrink-0">{{ $task->priority->icon() }}</span>
                                            </div>
                                            <div class="flex items-center gap-3 shrink-0 ml-3">
                                                @if ($task->assignee)
                                                    <div class="flex items-center gap-1.5">
                                                        <img src="{{ $task->assignee->avatarUrl() }}" class="w-5 h-5 rounded-full" alt="">
                                                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $task->assignee->name }}</span>
                                                    </div>
                                                @endif
                                                @can('manageTasks', $project)
                                                    <form method="POST" action="{{ route('projects.backlog.tasks.sprint', [$project, $task]) }}">
                                                        @csrf @method('PATCH')
                                                        <select name="sprint_id" onchange="this.form.submit()"
                                                            class="bg-gray-800 border border-gray-700 text-gray-300 rounded-lg px-2 py-1 text-xs focus:border-amber-500 focus:outline-none min-w-[130px]">
                                                            <option value="">📋 Backlog</option>
                                                            @foreach ($assignableSprints as $s)
                                                                <option value="{{ $s->id }}" {{ $task->sprint_id == $s->id ? 'selected' : '' }}>
                                                                    {{ $s->status->value === 'active' ? '⚡' : '📅' }} {{ $s->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
