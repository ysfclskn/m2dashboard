<x-app-layout :current-project="$project">
    <x-slot name="header">📊 New Adventure Report — {{ $project->name }}</x-slot>

    <div class="max-w-3xl">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('projects.reports.store', $project) }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="title" value="Report Title *" />
                    <x-text-input id="title" name="title" type="text" :value="old('title')" required autofocus placeholder="Sprint 2 Adventure Report" />
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="period_start_date" value="Period Start *" />
                        <x-text-input id="period_start_date" name="period_start_date" type="date" :value="old('period_start_date')" required />
                        <x-input-error :messages="$errors->get('period_start_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="period_end_date" value="Period End *" />
                        <x-text-input id="period_end_date" name="period_end_date" type="date" :value="old('period_end_date')" required />
                        <x-input-error :messages="$errors->get('period_end_date')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <x-input-label for="summary" value="Summary *" />
                    <textarea id="summary" name="summary" rows="3" required
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none"
                        placeholder="High-level overview of what happened...">{{ old('summary') }}</textarea>
                    <x-input-error :messages="$errors->get('summary')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="completed_work" value="Completed Work" />
                    <textarea id="completed_work" name="completed_work" rows="4"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none"
                        placeholder="- Implemented combat system&#10;- Fixed enemy AI bug&#10;- ...">{{ old('completed_work') }}</textarea>
                </div>

                <div>
                    <x-input-label for="current_blockers" value="Current Blockers" />
                    <textarea id="current_blockers" name="current_blockers" rows="3"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none"
                        placeholder="Any issues blocking progress...">{{ old('current_blockers') }}</textarea>
                </div>

                <div>
                    <x-input-label for="next_goals" value="Next Goals" />
                    <textarea id="next_goals" name="next_goals" rows="3"
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100 placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none"
                        placeholder="What's planned next...">{{ old('next_goals') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button>Submit Report</x-primary-button>
                    <a href="{{ route('projects.reports.index', $project) }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
