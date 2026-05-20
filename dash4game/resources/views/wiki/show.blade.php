<x-app-layout :current-project="$project">
    @push('styles')
    <style>
    .wiki-prose h1{font-size:1.5rem;font-weight:700;color:#f1f5f9;margin:1.5rem 0 .75rem;padding-bottom:.5rem;border-bottom:1px solid #1f2937}
    .wiki-prose h2{font-size:1.25rem;font-weight:700;color:#f1f5f9;margin:1.25rem 0 .5rem}
    .wiki-prose h3{font-size:1.1rem;font-weight:600;color:#e2e8f0;margin:1rem 0 .4rem}
    .wiki-prose h4{font-size:1rem;font-weight:600;color:#e2e8f0;margin:.75rem 0 .3rem}
    .wiki-prose p{margin-bottom:.75rem;line-height:1.75;color:#d1d5db}
    .wiki-prose ul{list-style-type:disc;padding-left:1.5rem;margin-bottom:.75rem}
    .wiki-prose ol{list-style-type:decimal;padding-left:1.5rem;margin-bottom:.75rem}
    .wiki-prose li{margin-bottom:.25rem;color:#d1d5db}
    .wiki-prose a{color:#fbbf24;text-decoration:underline}
    .wiki-prose a:hover{color:#fcd34d}
    .wiki-prose code{background:#111827;color:#fcd34d;padding:.1rem .35rem;border-radius:.25rem;font-size:.8rem;font-family:monospace}
    .wiki-prose pre{background:#111827;border:1px solid #374151;border-radius:.5rem;padding:1rem;overflow-x:auto;margin-bottom:.75rem}
    .wiki-prose pre code{background:transparent;color:#d1d5db;padding:0}
    .wiki-prose blockquote{border-left:4px solid rgba(251,191,36,.35);padding-left:1rem;color:#9ca3af;font-style:italic;margin-bottom:.75rem}
    .wiki-prose hr{border-color:#374151;margin:1.25rem 0}
    .wiki-prose img{max-width:100%;border-radius:.5rem;margin:.75rem 0}
    .wiki-prose strong{font-weight:700;color:#f1f5f9}
    .wiki-prose em{font-style:italic;color:#d1d5db}
    .wiki-prose table{width:100%;border-collapse:collapse;margin-bottom:.75rem;font-size:.875rem}
    .wiki-prose th{border:1px solid #374151;padding:.4rem .75rem;text-align:left;background:#1f2937;color:#f1f5f9;font-weight:600}
    .wiki-prose td{border:1px solid #374151;padding:.4rem .75rem;color:#d1d5db}
    </style>
    @endpush

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
                       class="text-sm px-3 py-1.5 border border-amber-700/50 text-amber-500 rounded-lg hover:bg-amber-500/10 hover:border-amber-500 transition-colors">Edit</a>
                    <form method="POST" action="{{ route('projects.wiki.destroy', [$project, $wikiPage]) }}"
                          onsubmit="return confirm('Delete this wiki page?')">
                        @csrf @method('DELETE')
                        <button class="text-sm px-3 py-1.5 border border-gray-700 text-gray-400 rounded-lg hover:border-red-700/50 hover:text-red-400 transition-colors">Delete</button>
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
                <div id="wiki-content" class="wiki-prose text-sm">
                    <div id="wiki-content-fallback" class="whitespace-pre-wrap text-gray-300 leading-relaxed">{{ $wikiPage->content }}</div>
                </div>
            @else
                <p class="text-gray-500 italic text-sm">No content yet.
                    @can('manageWiki', $project)
                        <a href="{{ route('projects.wiki.edit', [$project, $wikiPage]) }}" class="text-amber-400 hover:text-amber-300">Add content →</a>
                    @endcan
                </p>
            @endif
        </div>
        @include('wiki._images')
    </div>

    @if ($wikiPage->content)
        @push('scripts')
        <script>window._wikiRaw = @json($wikiPage->content);</script>
        <script src="https://cdn.jsdelivr.net/npm/marked@9/marked.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('wiki-content');
            const fallback  = document.getElementById('wiki-content-fallback');
            if (!container || typeof marked === 'undefined') return;
            const html = typeof DOMPurify !== 'undefined'
                ? DOMPurify.sanitize(marked.parse(window._wikiRaw))
                : marked.parse(window._wikiRaw);
            if (fallback) fallback.remove();
            container.innerHTML = html;
        });
        </script>
        @endpush
    @endif

</x-app-layout>
