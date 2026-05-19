<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>📊 Adventure Reports — {{ $project->name }}</span>
            @can('update', $project)
                <a href="{{ route('projects.reports.create', $project) }}"
                   class="px-4 py-2 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + New Report
                </a>
            @endcan
        </div>
    </x-slot>

    @if ($reports->isEmpty())
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
            <p class="text-4xl mb-4">📊</p>
            <h2 class="text-lg font-semibold text-gray-100 mb-2">No adventure reports yet</h2>
            <p class="text-gray-500 text-sm mb-6">Document your progress, blockers, and goals for the party.</p>
            @can('update', $project)
                <a href="{{ route('projects.reports.create', $project) }}"
                   class="inline-flex items-center px-5 py-2.5 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + Write First Report
                </a>
            @endcan
        </div>
    @else
        <div class="space-y-3">
            @foreach ($reports as $report)
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-gray-700 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <a href="{{ route('projects.reports.show', [$project, $report]) }}"
                               class="font-semibold text-gray-100 hover:text-amber-400 transition-colors">
                                {{ $report->title }}
                            </a>
                            <p class="text-sm text-gray-400 mt-1 line-clamp-2">{{ $report->summary }}</p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                <span>{{ $report->period_start_date->format('M j') }} – {{ $report->period_end_date->format('M j, Y') }}</span>
                                <span>by {{ $report->creator->name }}</span>
                            </div>
                        </div>
                        @can('update', $project)
                            <div class="flex items-center gap-2 ml-4 shrink-0">
                                <a href="{{ route('projects.reports.edit', [$project, $report]) }}"
                                   class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Edit</a>
                            </div>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
