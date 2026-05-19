<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.reports.index', $project) }}" class="text-gray-500 hover:text-gray-300">Reports</a>
                <span class="text-gray-700">/</span>
                <span>{{ $progressReport->title }}</span>
            </div>
            @can('update', $project)
                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.reports.edit', [$project, $progressReport]) }}"
                       class="text-sm px-3 py-1.5 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 transition-colors">Edit</a>
                    <form method="POST" action="{{ route('projects.reports.destroy', [$project, $progressReport]) }}"
                          onsubmit="return confirm('Delete this report?')">
                        @csrf @method('DELETE')
                        <button class="text-sm px-3 py-1.5 text-red-500 hover:text-red-400 transition-colors">Delete</button>
                    </form>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-3xl space-y-4">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h1 class="text-2xl font-bold text-gray-100 mb-2">{{ $progressReport->title }}</h1>
            <div class="flex items-center gap-4 text-xs text-gray-500 mb-6">
                <span>📅 {{ $progressReport->period_start_date->format('M j') }} – {{ $progressReport->period_end_date->format('M j, Y') }}</span>
                <span>by {{ $progressReport->creator->name }}</span>
                <span>{{ $progressReport->created_at->diffForHumans() }}</span>
            </div>

            <div class="space-y-5 text-sm">
                <div>
                    <h3 class="text-gray-400 font-semibold text-xs uppercase tracking-wider mb-2">📋 Summary</h3>
                    <p class="text-gray-200 whitespace-pre-wrap">{{ $progressReport->summary }}</p>
                </div>

                @if ($progressReport->completed_work)
                    <div class="border-t border-gray-800 pt-5">
                        <h3 class="text-green-400 font-semibold text-xs uppercase tracking-wider mb-2">✅ Completed Work</h3>
                        <p class="text-gray-300 whitespace-pre-wrap">{{ $progressReport->completed_work }}</p>
                    </div>
                @endif

                @if ($progressReport->current_blockers)
                    <div class="border-t border-gray-800 pt-5">
                        <h3 class="text-red-400 font-semibold text-xs uppercase tracking-wider mb-2">🚧 Current Blockers</h3>
                        <p class="text-gray-300 whitespace-pre-wrap">{{ $progressReport->current_blockers }}</p>
                    </div>
                @endif

                @if ($progressReport->next_goals)
                    <div class="border-t border-gray-800 pt-5">
                        <h3 class="text-amber-400 font-semibold text-xs uppercase tracking-wider mb-2">🎯 Next Goals</h3>
                        <p class="text-gray-300 whitespace-pre-wrap">{{ $progressReport->next_goals }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
