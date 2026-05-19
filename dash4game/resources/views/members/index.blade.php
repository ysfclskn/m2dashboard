<x-app-layout :current-project="$project">
    <x-slot name="header">⚔️ Party Members — {{ $project->name }}</x-slot>

    <div class="max-w-3xl space-y-6">

        {{-- Member List --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-800">
                <h2 class="font-semibold text-gray-100">Party Members ({{ $members->count() }})</h2>
            </div>
            <div class="divide-y divide-gray-800">
                @foreach ($members as $member)
                    <div class="flex items-center justify-between px-5 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->user->avatarUrl() }}" class="w-9 h-9 rounded-full object-cover" alt="">
                            <div>
                                <div class="text-sm font-medium text-gray-100">{{ $member->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $member->user->email }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($member->role === \App\Enums\ProjectRole::Owner)
                                <span class="text-xs px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-400 font-medium">
                                    {{ $member->role->label() }}
                                </span>
                            @else
                                @can('manageMembers', $project)
                                    <form method="POST" action="{{ route('projects.members.update', [$project, $member]) }}"
                                          class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <select name="role" onchange="this.form.submit()"
                                            class="text-xs bg-gray-800 border border-gray-700 text-gray-300 rounded-lg px-2 py-1.5 focus:border-amber-500 focus:outline-none">
                                            @foreach ($roles as $role)
                                                @if ($role !== \App\Enums\ProjectRole::Owner)
                                                    <option value="{{ $role->value }}" {{ $member->role === $role ? 'selected' : '' }}>
                                                        {{ $role->label() }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('projects.members.destroy', [$project, $member]) }}"
                                          onsubmit="return confirm('Remove {{ $member->user->name }} from the party?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-400 transition-colors px-2 py-1">
                                            Remove
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs px-2.5 py-1 rounded-full bg-gray-800 text-gray-400">
                                        {{ $member->role->label() }}
                                    </span>
                                @endcan
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Add Member --}}
        @can('manageMembers', $project)
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <h3 class="font-semibold text-gray-100 mb-4">Invite to Party</h3>
                <form method="POST" action="{{ route('projects.members.store', $project) }}" class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <x-input-label for="email" value="User Email" />
                        <x-text-input id="email" name="email" type="email" :value="old('email')"
                            placeholder="adventurer@example.com" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div class="w-40">
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100 rounded-lg px-3 py-2 text-sm focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">
                            @foreach ($roles as $role)
                                @if ($role !== \App\Enums\ProjectRole::Owner)
                                    <option value="{{ $role->value }}" {{ old('role', 'member') === $role->value ? 'selected' : '' }}>
                                        {{ $role->label() }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button type="submit">Add Member</x-primary-button>
                </form>
                <p class="text-xs text-gray-600 mt-2">The user must already have an account to be invited.</p>
            </div>
        @endcan

        {{-- Role Legend --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-gray-300 mb-3">Party Roles</h3>
            <div class="grid grid-cols-2 gap-2 text-xs">
                @foreach ($roles as $role)
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-amber-400">{{ $role->label() }}</span>
                        <span class="text-gray-500">({{ $role->value }})</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-3 space-y-1 text-xs text-gray-500">
                <p>🔑 <span class="text-amber-400">Guild Master / Raid Leader</span> — manage project, members, all content</p>
                <p>⚔️ <span class="text-gray-300">Adventurer</span> — create & edit quests, wiki pages</p>
                <p>👁️ <span class="text-gray-300">Observer</span> — read-only access</p>
            </div>
        </div>

    </div>
</x-app-layout>
