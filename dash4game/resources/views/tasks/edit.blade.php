<x-app-layout :current-project="$project">
    <x-slot name="header">✏️ Edit Quest — {{ $task->title }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('projects.quests.update', [$project, $task]) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <x-input-label for="title" value="Quest Title *" />
                    <x-text-input id="title" name="title" type="text" :value="old('title', $task->title)" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="grid grid-cols-3 gap-4">
                    @foreach ([['type','Type','types'],['status','Status','statuses'],['priority','Priority','priorities']] as [$field,$label,$var])
                    <div>
                        <x-input-label :value="$label . ' *'" />
                        <select name="{{ $field }}"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            @foreach ($$var as $opt)
                                <option value="{{ $opt->value }}" {{ old($field, $task->$field->value) === $opt->value ? 'selected' : '' }}>
                                    {{ $opt->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="assignee_id" value="Assign To" />
                        <select id="assignee_id" name="assignee_id"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">Unassigned</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->user_id }}" {{ old('assignee_id', $task->assignee_id) == $member->user_id ? 'selected' : '' }}>
                                    {{ $member->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="sprint_id" value="Assign to Raid" />
                        <select id="sprint_id" name="sprint_id"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">Backlog (no raid)</option>
                            @foreach ($sprints as $sprint)
                                <option value="{{ $sprint->id }}" {{ old('sprint_id', $task->sprint_id) == $sprint->id ? 'selected' : '' }}>
                                    {{ $sprint->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="due_date" value="Due Date" />
                        <x-text-input id="due_date" name="due_date" type="date" :value="old('due_date', $task->due_date?->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('due_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="wiki_page_id" value="Linked Wiki Page" />
                        <select id="wiki_page_id" name="wiki_page_id"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            <option value="">None (no wiki page)</option>
                            @foreach ($wikiPages as $wp)
                                <option value="{{ $wp->id }}" {{ old('wiki_page_id', $task->wiki_page_id) == $wp->id ? 'selected' : '' }}>
                                    {{ $wp->title }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('wiki_page_id')" class="mt-1" />
                    </div>
                </div>

                <div>
                    @include('wiki._editor', [
                        'fieldName' => 'description',
                        'fieldLabel' => 'Description',
                        'initialContent' => old('description', $task->description ?? ''),
                        'rows' => 10
                    ])
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Save Changes</x-primary-button>
                    <a href="{{ route('projects.quests.show', [$project, $task]) }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
