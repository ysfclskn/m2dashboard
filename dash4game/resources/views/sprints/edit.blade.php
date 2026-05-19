<x-app-layout :current-project="$project">
    <x-slot name="header">✏️ Edit Raid — {{ $sprint->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('projects.sprints.update', [$project, $sprint]) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <x-input-label for="name" value="Raid Name *" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $sprint->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="goal" value="Raid Goal" />
                    <x-text-input id="goal" name="goal" type="text" :value="old('goal', $sprint->goal)" />
                </div>

                <div>
                    <x-input-label for="status" value="Status *" />
                    <select id="status" name="status"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" {{ old('status', $sprint->status->value) === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-600 mt-1">Setting to "Active Raid" will pause any other active raid.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_date" value="Start Date" />
                        <x-text-input id="start_date" name="start_date" type="date" :value="old('start_date', $sprint->start_date?->format('Y-m-d'))" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="End Date" />
                        <x-text-input id="end_date" name="end_date" type="date" :value="old('end_date', $sprint->end_date?->format('Y-m-d'))" />
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Save Changes</x-primary-button>
                    <a href="{{ route('projects.sprints.show', [$project, $sprint]) }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Cancel</a>

                    @can('update', $project)
                        <form method="POST" action="{{ route('projects.sprints.destroy', [$project, $sprint]) }}" class="ml-auto"
                              onsubmit="return confirm('Delete this raid? Quests will return to backlog.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:text-red-400 transition-colors">Delete Raid</button>
                        </form>
                    @endcan
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
