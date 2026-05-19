<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>📜 Quest Board — {{ $project->name }}</span>
            @can('manageTasks', $project)
                <a href="{{ route('projects.quests.create', $project) }}"
                   class="px-4 py-2 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + New Quest
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $priorityColors = ['critical'=>'text-red-400','high'=>'text-orange-400','medium'=>'text-blue-400','low'=>'text-gray-500'];
        $statusColors   = ['backlog'=>'bg-gray-800 text-gray-400','todo'=>'bg-blue-900/40 text-blue-400','in_progress'=>'bg-amber-900/40 text-amber-400','review'=>'bg-purple-900/40 text-purple-400','done'=>'bg-green-900/40 text-green-400'];
    @endphp

    <div class="overflow-x-auto">
        <div class="flex gap-4 min-w-max pb-4">
            @foreach ($statuses as $status)
                @php $cols = $tasks->get($status->value, collect()) @endphp
                <div class="w-72 flex flex-col">
                    {{-- Column Header --}}
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-300">{{ $status->label() }}</span>
                            <span class="text-xs bg-gray-800 text-gray-500 px-1.5 py-0.5 rounded-full">{{ $cols->count() }}</span>
                        </div>
                    </div>

                    {{-- Cards --}}
                    <div class="flex flex-col gap-2 min-h-24">
                        @forelse ($cols as $task)
                            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 hover:border-gray-700 transition-colors">
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <a href="{{ route('projects.quests.show', [$project, $task]) }}"
                                       class="text-sm font-medium text-gray-100 hover:text-amber-400 transition-colors leading-snug flex-1">
                                        {{ $task->title }}
                                    </a>
                                    <span class="text-xs {{ $priorityColors[$task->priority->value] ?? '' }} shrink-0">
                                        {{ $task->priority->icon() }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap mb-3">
                                    <span class="text-xs px-1.5 py-0.5 rounded {{ $statusColors[$task->status->value] ?? '' }}">
                                        {{ $task->type->icon() }} {{ $task->type->label() }}
                                    </span>
                                    @if ($task->sprint)
                                        <span class="text-xs text-gray-500">⚡ {{ $task->sprint->name }}</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between">
                                    @if ($task->assignee)
                                        <img src="{{ $task->assignee->avatarUrl() }}" class="w-5 h-5 rounded-full" title="{{ $task->assignee->name }}" alt="">
                                    @else
                                        <span class="text-xs text-gray-600">Unassigned</span>
                                    @endif

                                    @can('manageTasks', $project)
                                        <form method="POST" action="{{ route('projects.quests.status', [$project, $task]) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                class="text-xs bg-gray-800 border border-gray-700 text-gray-400 rounded px-1.5 py-1 focus:outline-none">
                                                @foreach ($statuses as $s)
                                                    <option value="{{ $s->value }}" {{ $task->status->value === $s->value ? 'selected' : '' }}>
                                                        {{ $s->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @empty
                            <div class="border-2 border-dashed border-gray-800 rounded-xl p-4 text-center text-xs text-gray-600">
                                No quests
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
