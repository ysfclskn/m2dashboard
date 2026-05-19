<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.wiki.index', $project) }}" class="text-gray-500 hover:text-gray-300">Wiki</a>
                <span class="text-gray-700">/</span>
                <span>{{ $wikiPage->title }}</span>
            </div>
            @can('manageWiki', $project)
                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.wiki.edit', [$project, $wikiPage]) }}"
                       class="text-sm px-3 py-1.5 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">Edit</a>
                    <form method="POST" action="{{ route('projects.wiki.destroy', [$project, $wikiPage]) }}"
                          onsubmit="return confirm('Delete this wiki page?')">
                        @csrf @method('DELETE')
                        <button class="text-sm px-3 py-1.5 text-red-500 hover:text-red-400 transition-colors">Delete</button>
                    </form>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-6">
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-800 text-gray-400">{{ $wikiPage->category->label() }}</span>
                <span class="text-xs text-gray-600">
                    by {{ $wikiPage->creator->name }}
                    @if ($wikiPage->updater) · updated by {{ $wikiPage->updater->name }} @endif
                    · {{ $wikiPage->updated_at->diffForHumans() }}
                </span>
            </div>

            <h1 class="text-2xl font-bold text-gray-100 mb-6">{{ $wikiPage->title }}</h1>

            @if ($wikiPage->content)
                <div class="text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $wikiPage->content }}</div>
            @else
                <p class="text-gray-500 italic text-sm">No content yet.
                    @can('manageWiki', $project)
                        <a href="{{ route('projects.wiki.edit', [$project, $wikiPage]) }}" class="text-amber-400 hover:text-amber-300">Add content →</a>
                    @endcan
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
