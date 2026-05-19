<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>📖 Game Wiki — {{ $project->name }}</span>
            @can('manageWiki', $project)
                <a href="{{ route('projects.wiki.create', $project) }}"
                   class="px-4 py-2 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + New Page
                </a>
            @endcan
        </div>
    </x-slot>

    @if ($pages->isEmpty())
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
            <p class="text-4xl mb-4">📖</p>
            <h2 class="text-lg font-semibold text-gray-100 mb-2">No wiki pages yet</h2>
            <p class="text-gray-500 text-sm mb-6">Start documenting your game design, lore, mechanics, and more.</p>
            @can('manageWiki', $project)
                <a href="{{ route('projects.wiki.create', $project) }}"
                   class="inline-flex items-center px-5 py-2.5 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + Create First Page
                </a>
            @endcan
        </div>
    @else
        <div class="space-y-6">
            @foreach ($categories as $cat)
                @php $catPages = $pages->get($cat->value, collect()) @endphp
                @if ($catPages->isNotEmpty())
                    <div>
                        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ $cat->label() }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                            @foreach ($catPages as $page)
                                <a href="{{ route('projects.wiki.show', [$project, $page]) }}"
                                   class="bg-gray-900 border border-gray-800 rounded-xl p-4 hover:border-gray-700 hover:bg-gray-800/50 transition-colors group">
                                    <div class="font-medium text-gray-100 group-hover:text-amber-400 transition-colors mb-1">
                                        {{ $page->title }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        by {{ $page->creator->name }} · {{ $page->updated_at->diffForHumans() }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</x-app-layout>
