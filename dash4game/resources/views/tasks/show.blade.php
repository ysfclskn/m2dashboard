<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.quests.index', $project) }}" class="text-gray-500 hover:text-gray-300">Quest Board</a>
                <span class="text-gray-700">/</span>
                <span>{{ $task->title }}</span>
            </div>
            @can('manageTasks', $project)
                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.quests.edit', [$project, $task]) }}"
                       class="text-sm px-3 py-1.5 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">Edit</a>
                    <form method="POST" action="{{ route('projects.quests.destroy', [$project, $task]) }}"
                          onsubmit="return confirm('Delete this quest?')">
                        @csrf @method('DELETE')
                        <button class="text-sm px-3 py-1.5 text-red-500 hover:text-red-400 transition-colors">Delete</button>
                    </form>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-3xl space-y-4">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            @php
                $pColors = ['critical'=>'text-red-400 bg-red-900/30','high'=>'text-orange-400 bg-orange-900/30','medium'=>'text-blue-400 bg-blue-900/30','low'=>'text-gray-400 bg-gray-800'];
                $sColors = ['backlog'=>'text-gray-400 bg-gray-800','todo'=>'text-blue-400 bg-blue-900/30','in_progress'=>'text-amber-400 bg-amber-900/30','review'=>'text-purple-400 bg-purple-900/30','done'=>'text-green-400 bg-green-900/30'];
            @endphp

            <div class="flex items-start gap-4 mb-4">
                <span class="text-2xl">{{ $task->type->icon() }}</span>
                <div class="flex-1">
                    <h1 class="text-xl font-bold text-gray-100">{{ $task->title }}</h1>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $sColors[$task->status->value] }}">{{ $task->status->label() }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $pColors[$task->priority->value] }}">{{ $task->priority->icon() }} {{ $task->priority->label() }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 text-gray-400">{{ $task->type->label() }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                <div>
                    <span class="text-gray-500">Assigned to:</span>
                    <span class="text-gray-200 ml-2">{{ $task->assignee?->name ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Created by:</span>
                    <span class="text-gray-200 ml-2">{{ $task->creator->name }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Raid/Sprint:</span>
                    <span class="text-gray-200 ml-2">{{ $task->sprint?->name ?? 'Backlog' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Due date:</span>
                    <span class="{{ $task->isOverdue() ? 'text-red-400' : 'text-gray-200' }} ml-2">
                        {{ $task->due_date?->format('M j, Y') ?? '—' }}
                    </span>
                </div>
            </div>

            @if ($task->description)
                <div class="border-t border-gray-800 pt-4">
                    <p class="text-sm text-gray-300 whitespace-pre-wrap">{{ $task->description }}</p>
                </div>
            @endif

            @can('manageTasks', $project)
                <div class="border-t border-gray-800 pt-4 mt-4">
                    <form method="POST" action="{{ route('projects.quests.status', [$project, $task]) }}"
                          class="flex items-center gap-3">
                        @csrf @method('PATCH')
                        <span class="text-sm text-gray-500">Change status:</span>
                        <select name="status"
                            class="bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-1.5 text-sm focus:border-amber-500 focus:outline-none">
                            @foreach (\App\Enums\TaskStatus::cases() as $s)
                                <option value="{{ $s->value }}" {{ $task->status->value === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                            Update
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
