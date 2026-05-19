<x-app-layout :current-project="$project">
    <x-slot name="header">✏️ Edit Report — {{ $progressReport->title }}</x-slot>

    <div class="max-w-3xl">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('projects.reports.update', [$project, $progressReport]) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <x-input-label for="title" value="Report Title *" />
                    <x-text-input id="title" name="title" type="text" :value="old('title', $progressReport->title)" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="period_start_date" value="Period Start *" />
                        <x-text-input id="period_start_date" name="period_start_date" type="date"
                            :value="old('period_start_date', $progressReport->period_start_date->format('Y-m-d'))" required />
                    </div>
                    <div>
                        <x-input-label for="period_end_date" value="Period End *" />
                        <x-text-input id="period_end_date" name="period_end_date" type="date"
                            :value="old('period_end_date', $progressReport->period_end_date->format('Y-m-d'))" required />
                    </div>
                </div>

                @foreach ([
                    ['summary', 'Summary *', 3, true],
                    ['completed_work', 'Completed Work', 4, false],
                    ['current_blockers', 'Current Blockers', 3, false],
                    ['next_goals', 'Next Goals', 3, false],
                ] as [$field, $label, $rows, $required])
                <div>
                    <x-input-label :value="$label" />
                    <textarea name="{{ $field }}" rows="{{ $rows }}" {{ $required ? 'required' : '' }}
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">{{ old($field, $progressReport->$field) }}</textarea>
                    <x-input-error :messages="$errors->get($field)" class="mt-1" />
                </div>
                @endforeach

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Save Changes</x-primary-button>
                    <a href="{{ route('projects.reports.show', [$project, $progressReport]) }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
