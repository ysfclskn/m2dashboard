<x-app-layout :current-project="$project">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <span>⚡ Raid Sprints — {{ $project->name }}</span>
            @can('update', $project)
                <a href="{{ route('projects.sprints.create', $project) }}"
                   class="px-4 py-2 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + New Raid
                </a>
            @endcan
        </div>
    </x-slot>

    @php
        $hasAnySprint = $active->isNotEmpty() || $planned->isNotEmpty() || $completed->isNotEmpty() || $cancelled->isNotEmpty();
        $sColors = ['backlog'=>'bg-gray-800 text-gray-400','todo'=>'bg-blue-900/40 text-blue-400','in_progress'=>'bg-amber-900/40 text-amber-400','review'=>'bg-purple-900/40 text-purple-400','done'=>'bg-green-900/40 text-green-400'];
        $pColors = ['critical'=>'text-red-400','high'=>'text-orange-400','medium'=>'text-blue-400','low'=>'text-gray-500'];
    @endphp

    @if (!$hasAnySprint)
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center">
            <p class="text-4xl mb-4">⚡</p>
            <h2 class="text-lg font-semibold text-gray-100 mb-2">No raids yet</h2>
            <p class="text-gray-500 text-sm mb-6">Create a raid sprint to organize your quests into focused cycles.</p>
            @can('update', $project)
                <a href="{{ route('projects.sprints.create', $project) }}"
                   class="inline-flex items-center px-5 py-2.5 bg-amber-500 text-gray-900 font-semibold text-sm rounded-lg hover:bg-amber-400 transition-colors">
                    + Create First Raid
                </a>
            @endcan
        </div>
    @else
        <div class="space-y-8">

            {{-- ── Active Raid Sprint ─────────────────────────────────────── --}}
            @if ($active->isNotEmpty())
                @foreach ($active as $sprint)
                    @include('sprints._sprint-section', ['sprint' => $sprint, 'borderClass' => 'border-green-800', 'accentClass' => 'text-green-400'])
                @endforeach
            @else
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 text-center">
                    <p class="text-sm text-gray-500">No active raid. <a href="{{ route('projects.sprints.create', $project) }}" class="text-amber-400 hover:text-amber-300">Create or activate a sprint</a> to start the Quest Board.</p>
                </div>
            @endif

            {{-- ── Planned Raids ──────────────────────────────────────────── --}}
            @if ($planned->isNotEmpty())
                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">📅 Planned Raids</h2>
                    <div class="space-y-4">
                        @foreach ($planned as $sprint)
                            @include('sprints._sprint-section', ['sprint' => $sprint, 'borderClass' => 'border-gray-800', 'accentClass' => 'text-gray-300'])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Completed Raids ────────────────────────────────────────── --}}
            @if ($completed->isNotEmpty())
                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">✅ Completed Raids</h2>
                    <div class="space-y-4">
                        @foreach ($completed as $sprint)
                            @include('sprints._sprint-section', ['sprint' => $sprint, 'borderClass' => 'border-gray-800', 'accentClass' => 'text-blue-400'])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Cancelled Raids ────────────────────────────────────────── --}}
            @if ($cancelled->isNotEmpty())
                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">🚫 Cancelled Raids</h2>
                    <div class="space-y-4">
                        @foreach ($cancelled as $sprint)
                            @include('sprints._sprint-section', ['sprint' => $sprint, 'borderClass' => 'border-gray-800', 'accentClass' => 'text-red-400'])
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    @endif
</x-app-layout>
