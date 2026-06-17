@php
    $statusBadge = ['planned'=>'text-gray-400 bg-gray-800','active'=>'text-green-400 bg-green-900/30','completed'=>'text-blue-400 bg-blue-900/30','cancelled'=>'text-red-400 bg-red-900/30'];
    $sColors     = ['backlog'=>'bg-gray-800 text-gray-400','todo'=>'bg-blue-900/40 text-blue-400','in_progress'=>'bg-amber-900/40 text-amber-400','review'=>'bg-purple-900/40 text-purple-400','done'=>'bg-green-900/40 text-green-400'];
    $pColors     = ['critical'=>'text-red-400','high'=>'text-orange-400','medium'=>'text-blue-400','low'=>'text-gray-500'];
    $pct         = $sprint->completionPercentage();
    $questCount  = $sprint->tasks->count();
    $doneCount   = $sprint->tasks->where('status.value', 'done')->count();
@endphp

<div x-data="{ open: true }" class="bg-gray-900 border {{ $borderClass }} rounded-xl overflow-hidden">

    {{-- Sprint Header --}}
    <div class="px-5 py-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center flex-wrap gap-2 mb-1">
                    <button @click="open = !open" class="text-xs text-gray-600 hover:text-gray-400 transition-colors shrink-0" title="Toggle quests">
                        <span x-show="open">▼</span><span x-show="!open">▶</span>
                    </button>
                    <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}"
                       class="font-semibold {{ $accentClass }} hover:text-amber-400 transition-colors">
                        {{ $sprint->name }}
                    </a>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $statusBadge[$sprint->status->value] ?? $statusBadge['planned'] }}">
                        {{ $sprint->status->label() }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $questCount }} quests · {{ $doneCount }} done</span>
                </div>

                @if ($sprint->goal)
                    <p class="text-sm text-gray-400 ml-5">{{ $sprint->goal }}</p>
                @endif

                @if ($sprint->start_date || $sprint->end_date)
                    <p class="text-xs text-gray-600 ml-5 mt-1">
                        @if ($sprint->start_date) {{ $sprint->start_date->format('M j, Y') }} @endif
                        @if ($sprint->start_date && $sprint->end_date) — @endif
                        @if ($sprint->end_date) {{ $sprint->end_date->format('M j, Y') }} @endif
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if ($sprint->status->value === 'active')
                    <a href="{{ route('projects.quests.index', $project) }}"
                       class="text-xs px-3 py-1.5 bg-amber-500 text-gray-900 font-semibold rounded-lg hover:bg-amber-400 transition-colors">
                        Open Board
                    </a>
                @endif
                @can('update', $project)
                    @if (in_array($sprint->status->value, ['planned', 'active']))
                        <form method="POST" action="{{ route('projects.sprints.complete', [$project, $sprint]) }}"
                              onsubmit="return confirm('Complete this raid?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-2.5 py-1.5 border border-green-700/50 text-green-400 rounded-lg hover:bg-green-500/10 hover:border-green-500 transition-colors">✓ Complete</button>
                        </form>
                    @endif
                    <a href="{{ route('projects.sprints.edit', [$project, $sprint]) }}"
                       class="text-xs text-gray-500 hover:text-gray-300 transition-colors px-2 py-1.5">Edit</a>
                @endcan
            </div>
        </div>

        @if ($sprint->status->value === 'active' && $questCount > 0)
            <div class="ml-5 mt-3">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>Progress</span><span>{{ $pct }}%</span>
                </div>
                <div class="w-full bg-gray-800 rounded-full h-1.5">
                    <div class="bg-green-500 h-1.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        @endif
    </div>

    {{-- Quest List --}}
    <div x-show="open" x-transition>
        @if ($sprint->tasks->isEmpty())
            <div class="px-5 pb-4 text-xs text-gray-600 italic">No quests assigned to this raid yet.</div>
        @else
            <div class="border-t border-gray-800 divide-y divide-gray-800/60">
                @foreach ($sprint->tasks as $task)
                    <div class="flex items-center gap-3 px-5 py-2.5 hover:bg-gray-800/40 transition-colors">
                        <span class="text-sm shrink-0">{{ $task->type->icon() }}</span>

                        <a href="{{ route('projects.quests.show', [$project, $task]) }}"
                           class="text-sm text-gray-200 hover:text-amber-400 transition-colors flex-1 min-w-0 truncate">
                            {{ $task->title }}
                        </a>

                        <div class="flex items-center gap-2 shrink-0 ml-2">
                            <span class="text-xs px-1.5 py-0.5 rounded {{ $sColors[$task->status->value] ?? '' }}">
                                {{ $task->status->label() }}
                            </span>
                            <span class="text-xs {{ $pColors[$task->priority->value] ?? '' }}" title="{{ $task->priority->label() }}">
                                {{ $task->priority->icon() }}
                            </span>
                            @if ($task->due_date)
                                <span class="text-xs {{ $task->isOverdue() ? 'text-red-400' : 'text-gray-600' }}">
                                    {{ $task->due_date->format('M j') }}
                                </span>
                            @endif
                            @if ($task->assignee)
                                <img src="{{ $task->assignee->avatarUrl() }}" class="w-5 h-5 rounded-full shrink-0" title="{{ $task->assignee->name }}" alt="">
                            @else
                                <span class="w-5 h-5 rounded-full bg-gray-800 shrink-0"></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
