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

    @can('manageTasks', $project)
        <p class="text-xs text-gray-500 mb-3">Drag quests between columns to update status.</p>
    @endcan

    {{-- Flash message area --}}
    <div id="dnd-flash" class="hidden mb-3 px-3 py-2 rounded text-xs bg-green-900/40 text-green-400 border border-green-800">
        Board updated.
    </div>

    <div class="overflow-x-auto">
        <div class="flex gap-4 min-w-max pb-4">
            @foreach ($statuses as $status)
                @php $cols = $tasks->get($status->value, collect()) @endphp
                <div class="w-72 flex flex-col">
                    {{-- Column Header --}}
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-300">{{ $status->label() }}</span>
                            <span class="text-xs bg-gray-800 text-gray-500 px-1.5 py-0.5 rounded-full quest-count">{{ $cols->count() }}</span>
                        </div>
                    </div>

                    {{-- Cards --}}
                    <div class="quest-column flex flex-col gap-2 min-h-24"
                         data-status="{{ $status->value }}"
                         @can('manageTasks', $project) data-sortable="true" @endcan>

                        @foreach ($cols as $task)
                            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 hover:border-gray-700 transition-colors @can('manageTasks', $project) cursor-grab active:cursor-grabbing active:opacity-60 @endcan"
                                 data-task-id="{{ $task->id }}">
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
                        @endforeach

                        {{-- Always in DOM; JS shows/hides based on card count --}}
                        <div class="quest-empty border-2 border-dashed border-gray-800 rounded-xl p-4 text-center text-xs text-gray-600"
                             @if($cols->isNotEmpty()) style="display:none" @endif>
                            No quests
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @can('manageTasks', $project)
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
        (function () {
            const reorderUrl = '{{ route('projects.quests.reorder', $project) }}';
            const csrfToken  = '{{ csrf_token() }}';
            const flash      = document.getElementById('dnd-flash');
            let flashTimer;

            function showFlash() {
                flash.classList.remove('hidden');
                clearTimeout(flashTimer);
                flashTimer = setTimeout(() => flash.classList.add('hidden'), 3000);
            }

            function collectPayload() {
                const tasks = [];
                document.querySelectorAll('.quest-column').forEach(function (col) {
                    const status = col.dataset.status;
                    col.querySelectorAll('[data-task-id]').forEach(function (card, idx) {
                        tasks.push({
                            id: parseInt(card.dataset.taskId, 10),
                            status: status,
                            order_index: idx,
                        });
                    });
                });
                return tasks;
            }

            function updateColumnUI() {
                document.querySelectorAll('.quest-column').forEach(function (col) {
                    const cards = col.querySelectorAll('[data-task-id]').length;
                    const empty = col.querySelector('.quest-empty');
                    const count = col.closest('.flex-col').querySelector('.quest-count');
                    if (empty) empty.style.display = cards > 0 ? 'none' : '';
                    if (count) count.textContent = cards;
                });
            }

            function sendReorder() {
                const tasks = collectPayload();
                fetch(reorderUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ tasks }),
                }).then(function (res) {
                    if (res.ok) {
                        showFlash();
                    } else {
                        alert('Could not update board. Reloading…');
                        location.reload();
                    }
                }).catch(function () {
                    alert('Network error. Reloading…');
                    location.reload();
                });
            }

            document.querySelectorAll('.quest-column[data-sortable="true"]').forEach(function (col) {
                Sortable.create(col, {
                    group: 'quests',
                    animation: 120,
                    ghostClass: 'opacity-30',
                    onEnd: function () {
                        updateColumnUI();
                        sendReorder();
                    },
                });
            });
        })();
        </script>
    @endcan
</x-app-layout>
