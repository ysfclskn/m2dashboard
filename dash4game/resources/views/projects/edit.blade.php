<x-app-layout :current-project="$project">
    <x-slot name="header">✏️ Edit — {{ $project->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name" value="Project Name *" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $project->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="genre" value="Genre" />
                        <x-text-input id="genre" name="genre" type="text" :value="old('genre', $project->genre)" />
                        <x-input-error :messages="$errors->get('genre')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status *" />
                        <select id="status" name="status"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ old('status', $project->status->value) === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea id="description" name="description" rows="4"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">{{ old('description', $project->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Save Changes</x-primary-button>
                    <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Cancel</a>

                    @can('delete', $project)
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" class="ml-auto"
                              onsubmit="return confirm('Delete this project? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:text-red-400 transition-colors">Delete Project</button>
                        </form>
                    @endcan
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
