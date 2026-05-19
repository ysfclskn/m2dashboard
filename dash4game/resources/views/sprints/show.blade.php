<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.sprints.index', $project) }}" class="text-gray-500 hover:text-gray-300">Raid Sprints</a>
                <span class="text-gray-700">/</span>
                <span>{{ $sprint->name }}</span>
                @php $badge = ['planned'=>'text-gray-400 bg-gray-800','active'=>'text-green-400 bg-green-900/30','completed'=>'text-blue-400 bg-blue-900/30','cancelled'=>'text-red-400 bg-red-900/30']; @endphp
                <span class="text-xs px-2 py-0.5 rounded-full {{ $badge[$sprint->status->value] ?? '' }}">{{ $sprint->status->label() }}</span>
            </div>
            @can('update', $project)
                <a href="{{ route('projects.sprints.edit', [$project, $sprint]) }}"
                   class="text-sm px-3 py-1.5 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">Edit Raid</a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Raid Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 bg-gray-900 border border-gray-800 rounded-xl p-5">
                @if ($sprint->goal)
                    <p class="text-sm text-gray-300 mb-3"><span class="text-gray-500">Goal:</span> {{ $sprint->goal }}</p>
                @endif
                <div class="flex items-center gap-6 text-sm text-gray-500">
                    @if ($sprint->start_date)
                        <span>Start: {{ $sprint->start_date->format('M j, Y') }}</span>
                    @endif
                    @if ($sprint->end_date)
                        <span>End: {{ $sprint->end_date->format('M j, Y') }}</span>
                    @endif
                </div>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 text-center">
                @php $pct = $sprint->completionPercentage(); @endphp
                <div class="text-3xl font-bold text-amber-400 mb-1">{{ $pct }}%</div>
                <div class="text-xs text-gray-500 mb-3">Complete</div>
                <div class="w-full bg-gray-800 rounded-full h-2">
                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>

        {{-- Quest Board --}}
        @php
            $statusColors = ['backlog'=>'bg-gray-800 text-gray-400','todo'=>'bg-blue-900/40 text-blue-400','in_progress'=>'bg-amber-900/40 text-amber-400','review'=>'bg-purple-900/40 text-purple-400','done'=>'bg-green-900/40 text-green-400'];
            $pColors      = ['critical'=>'text-red-400','high'=>'text-orange-400','medium'=>'text-blue-400','low'=>'text-gray-500'];
        @endphp

        <div class="overflow-x-auto">
            <div class="flex gap-4 min-w-max pb-2">
                @foreach ($statuses as $status)
                    @php $cols = $tasks->get($status->value, collect()) @endphp
                    <div class="w-64">
                        <div class="flex items-center gap-2 mb-2 px-1">
                            <span class="text-sm font-semibold text-gray-300">{{ $status->label() }}</span>
                            <span class="text-xs bg-gray-800 text-gray-500 px-1.5 py-0.5 rounded-full">{{ $cols->count() }}</span>
                        </div>
                        <div class="flex flex-col gap-2 min-h-16">
                            @forelse ($cols as $task)
                                <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
                                    <div class="flex items-start justify-between gap-1">
                                        <a href="{{ route('projects.quests.show', [$project, $task]) }}"
                                           class="text-sm text-gray-200 hover:text-amber-400 transition-colors leading-snug flex-1">
                                            {{ $task->type->icon() }} {{ $task->title }}
                                        </a>
                                        <span class="text-xs {{ $pColors[$task->priority->value] ?? '' }} shrink-0">{{ $task->priority->icon() }}</span>
                                    </div>
                                    @if ($task->assignee)
                                        <div class="flex items-center gap-1.5 mt-2">
                                            <img src="{{ $task->assignee->avatarUrl() }}" class="w-4 h-4 rounded-full" alt="">
                                            <span class="text-xs text-gray-500">{{ $task->assignee->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="border-2 border-dashed border-gray-800 rounded-xl p-3 text-center text-xs text-gray-600">Empty</div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-app-layout>
