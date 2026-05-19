<x-app-layout>
    <x-slot name="header">🎮 New Game Project</x-slot>

    <div class="max-w-2xl">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="name" value="Project Name *" />
                    <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus placeholder="Shadow Realm Online" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="genre" value="Genre" />
                        <x-text-input id="genre" name="genre" type="text" :value="old('genre')" placeholder="RPG, Action..." />
                        <x-input-error :messages="$errors->get('genre')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status *" />
                        <select id="status" name="status"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ old('status', 'idea') === $status->value ? 'selected' : '' }}>
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
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none"
                        placeholder="Describe your game project...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Create Project</x-primary-button>
                    <a href="{{ route('projects.index') }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
