<x-app-layout :current-project="$project">
    <x-slot name="header">📖 New Wiki Page — {{ $project->name }}</x-slot>

    <div class="max-w-3xl">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('projects.wiki.store', $project) }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <x-input-label for="title" value="Page Title *" />
                        <x-text-input id="title" name="title" type="text" :value="old('title')" required autofocus placeholder="Combat System Overview" />
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="category" value="Category *" />
                        <select id="category" name="category"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->value }}" {{ old('category', 'other') === $cat->value ? 'selected' : '' }}>
                                    {{ $cat->label() }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </div>
                </div>

                <div>
                    @include('wiki._editor', ['initialContent' => old('content', '')])
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Create Page</x-primary-button>
                    <a href="{{ route('projects.wiki.index', $project) }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
