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
                       class="text-sm px-3 py-1.5 border border-amber-700/50 text-amber-500 rounded-lg hover:bg-amber-500/10 hover:border-amber-500 transition-colors">Edit</a>
                    <form method="POST" action="{{ route('projects.quests.destroy', [$project, $task]) }}"
                          onsubmit="return confirm('Delete this quest?')">
                        @csrf @method('DELETE')
                        <button class="text-sm px-3 py-1.5 border border-gray-700 text-gray-400 rounded-lg hover:border-red-700/50 hover:text-red-400 transition-colors">Delete</button>
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
                    @can('manageTasks', $project)
                        <form method="POST" action="{{ route('projects.quests.assignee', [$project, $task]) }}"
                              class="inline-flex items-center gap-2 ml-2">
                            @csrf @method('PATCH')
                            <select name="assignee_id" onchange="this.form.submit()"
                                class="bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-2 py-1 text-sm focus:border-amber-500 focus:outline-none">
                                <option value="">Unassigned</option>
                                @foreach ($members as $member)
                                    <option value="{{ $member->user_id }}" {{ $task->assignee_id == $member->user_id ? 'selected' : '' }}>
                                        {{ $member->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <span class="text-gray-200 ml-2">{{ $task->assignee?->name ?? '—' }}</span>
                    @endcan
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
                    <x-linkified-text :text="$task->description" class="text-sm text-gray-300" />
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
        {{-- Attachments --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-sm font-semibold text-gray-300 mb-4">📎 Attachments</h2>

            @if ($task->attachments->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
                    @foreach ($task->attachments as $attachment)
                        <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700">
                            <a href="{{ $attachment->url() }}" target="_blank">
                                <img src="{{ $attachment->url() }}"
                                     alt="{{ $attachment->original_name }}"
                                     class="w-full h-28 object-cover hover:opacity-80 transition-opacity">
                            </a>
                            <div class="px-2 py-1.5 flex items-center justify-between gap-1">
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-300 truncate" title="{{ $attachment->original_name }}">
                                        {{ $attachment->original_name }}
                                    </p>
                                    <p class="text-xs text-gray-600">{{ $attachment->formattedSize() }}</p>
                                </div>
                                @can('manageTasks', $project)
                                    <form method="POST"
                                          action="{{ route('projects.quests.attachments.destroy', [$project, $task, $attachment]) }}"
                                          onsubmit="return confirm('Delete this attachment?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500 hover:text-red-400 shrink-0">✕</button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-600 mb-4">No attachments yet.</p>
            @endif

            @can('manageTasks', $project)
                @error('file')
                    <p class="text-xs text-red-400 mb-2">⚠️ {{ $message }}</p>
                @enderror
                <form method="POST"
                      action="{{ route('projects.quests.attachments.store', [$project, $task]) }}"
                      enctype="multipart/form-data"
                      class="flex items-center gap-3">
                    @csrf
                    <input type="file" name="file" accept="image/jpeg,image/png,image/webp,image/gif"
                           class="text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                  file:text-xs file:font-medium file:bg-gray-700 file:text-gray-200
                                  hover:file:bg-gray-600 cursor-pointer">
                    <button type="submit"
                            class="px-3 py-1.5 bg-amber-500 text-gray-900 font-semibold text-xs rounded-lg hover:bg-amber-400 transition-colors shrink-0">
                        Upload
                    </button>
                </form>
                <p class="text-xs text-gray-600 mt-2">JPG, PNG, WebP, GIF · max 4 MB</p>
            @endcan
        </div>

    {{-- Comments --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <h3 class="font-semibold text-gray-100 mb-4">💬 Discussion</h3>

        @php
            $myMember    = $project->members()->where('user_id', auth()->id())->first();
            $isModeratorX = $myMember && in_array($myMember->role->value, ['owner', 'admin']);
        @endphp
        @forelse ($task->comments as $comment)
            <div class="flex gap-3 py-3 border-b border-gray-800 last:border-0">
                <img src="{{ $comment->user->avatarUrl() }}" class="w-7 h-7 rounded-full object-cover shrink-0 mt-0.5" alt="">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-medium text-gray-200">{{ $comment->user->name }}</span>
                        <span class="text-xs text-gray-600">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <x-linkified-text :text="$comment->body" class="text-sm text-gray-300" />
                </div>
                @if ($comment->user_id === auth()->id() || $isModeratorX)
                    <form method="POST" action="{{ route('projects.quests.comments.destroy', [$project, $task, $comment]) }}"
                          onsubmit="return confirm('Delete this comment?')" class="shrink-0">
                        @csrf @method('DELETE')
                        <button class="text-xs text-gray-600 hover:text-red-400 transition-colors mt-0.5">✕</button>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-600 italic">No comments yet. Be the first to add one.</p>
        @endforelse

        @if ($myMember && $myMember->role->value !== 'viewer')
            <form method="POST" action="{{ route('projects.quests.comments.store', [$project, $task]) }}"
                  class="mt-4">
                @csrf
                <textarea name="body" rows="3" maxlength="2000" required
                          placeholder="Add a comment…"
                          class="w-full bg-gray-800 border border-gray-700 text-gray-100 text-sm rounded-lg px-3 py-2 resize-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none placeholder-gray-600">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
                <div class="flex justify-end mt-2">
                    <button type="submit"
                            class="px-4 py-1.5 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                        Post Comment
                    </button>
                </div>
            </form>
        @else
            <p class="text-xs text-gray-600 mt-4 italic">Observers can read but cannot post comments.</p>
        @endif
    </div>

    </div>
</x-app-layout>
