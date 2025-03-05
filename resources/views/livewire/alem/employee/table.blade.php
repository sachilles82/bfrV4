<div>
    <!-- Such- und Filterleiste -->
    <div class="xl:mt-2 mt-1 h-full flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4">
        <div class="w-full lg:w-1/3 md:w-1/2">
            <x-pupi.actions.search wire:model.live.debounce.400ms="search"/>
        </div>
        <div class="flex flex-col items-stretch justify-end flex-shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
            <div class="flex items-center justify-end w-full space-x-1 md:w-auto">
                <!-- Bulk-Actions Dropdown wird hier eingebunden -->
                <x-pupi.actions.bulkactions :statusFilter="$statusFilter"/>

                <x-pupi.actions.reset-filters wire:click="resetFilters"/>

                <flux:select wire:model.live="statusFilter" id="statusFilter" class="border rounded p-1">
                    <flux:option value="active">{{ __('Active') }}</flux:option>
                    <flux:option value="not_activated">{{ __('Not Activated') }}</flux:option>
                    <flux:option value="archived">{{ __('Archived') }}</flux:option>
                    <flux:option value="trash">{{ __('Trash') }}</flux:option>
                </flux::select>
                <x-pupi.actions.per-page/>
            </div>
        </div>
    </div>


    <!-- Tabelle -->
    <x-pupi.table.container>
        @json($selectedIds) @json($idsOnPage)
        <div x-data="{ checked:false }">
            <x-pupi.table.main>
                <x-slot:head>
                    <x-pupi.table.th.check-all/>
                    <x-pupi.table.th.sort column="name" :$sortCol :$sortAsc class="pl-2">
                        {{ __('Name') }}
                    </x-pupi.table.th.sort>
                    <x-pupi.table.th.notsort>{{ __('Team') }}</x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>{{ __('Roles') }}</x-pupi.table.th.notsort>
                    <!-- Spalte Account Status -->
                    <x-pupi.table.th.notsort>{{ __('Employee Status') }}</x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>{{ __('Account Status') }}</x-pupi.table.th.notsort>
                    <x-pupi.table.th.actions/>
                </x-slot:head>
                <x-slot:body>
                    @forelse($users as $user)
{{--                        <tr--}}
{{--                            wire:key="{{ $user->id }}"--}}
{{--                            x-on:check-all.window="checked = $event.detail"--}}
{{--                            x-on:update-table.window="checked = false"--}}
{{--                            x-data="{ checked: false }"--}}
{{--                            x-init="checked = $wire.selectedIds.includes('{{ $user->id }}')"--}}
{{--                            x-bind:class="{--}}
{{--                                        'bg-gray-100 dark:bg-gray-800/50': checked,--}}
{{--                                        'hover:bg-gray-100 dark:hover:bg-gray-800/50': !checked--}}
{{--                                      }"--}}
{{--                        >--}}

                        {{-- Update only the tr element in your table --}}
                        <tr
                            wire:key="{{ $user->id }}"
                            x-on:check-all.window="checked = $event.detail"
                            x-on:update-table.window="checked = false"
                            x-on:employee-updated.window="checked = false"
                            x-data="{ checked: false }"
                            x-init="checked = $wire.selectedIds.includes('{{ $user->id }}')"
                            x-bind:class="{
                'bg-gray-100 dark:bg-gray-800/50': checked,
                'hover:bg-gray-100 dark:hover:bg-gray-800/50': !checked
              }"
                        >
                            <td class="relative px-7 sm:w-12 sm:px-6">
                                <div x-show="checked" x-cloak
                                     class="absolute inset-y-0 left-0 w-0.5 dark:bg-indigo-500 bg-indigo-600"></div>
                                <x-pupi.table.tr.checkbox x-model="checked"
                                                          wire:model="selectedIds"
                                                          value="{{ $user->id}}"/>
                            </td>
                            <x-pupi.table.tr.cell>
                                <div class="flex items-center">
                                    <div class="h-11 w-11 flex-shrink-0">
                                        @if($user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-11 w-11 rounded-full">
                                        @else
                                            @php
                                                $nameInitials = strtoupper(join('+', array_map(fn($name) => substr($name, 0, 1), explode(' ', $user->name . ' ' . $user->last_name))));
                                            @endphp
                                            <img src="https://ui-avatars.com/api/?name={{ $nameInitials }}&color=7F9CF5&background=EBF4FF" alt="{{ $user->name }}" class="h-11 w-11 rounded-full">
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <a wire:navigate.hover href="{{ route('employees.profile', $user) }}" class="font-medium text-gray-900 dark:text-gray-300 hover:text-indigo-700 decoration-1 hover:underline dark:hover:text-indigo-300">
                                            {{ $user->name }} {{ $user->last_name }}
                                        </a>
                                        <div class="mt-1 text-gray-500 dark:text-gray-400">Malermeister</div>
                                    </div>
                                </div>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <div class="text-gray-900 dark:text-gray-300">+41 76 699 23 24</div>
                                <div class="text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <div class="text-gray-900 dark:text-gray-300">{{ optional($user->teams->first())->name }}</div>
                                <div class="text-gray-500 dark:text-gray-400">Departement</div>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                {{ $user->roles->pluck('name')->implode(', ') }}
                            </x-pupi.table.tr.cell>
                            <!-- Account Status Spalte: Mit Status Badge -->
                            <x-pupi.table.tr.cell>
                                <x-pupi.table.employee-badge :status="$user->account_status" class="cursor-pointer"
                                                         wire:click="setStatusFilter('{{ $user->account_status }}')"/>
                            </x-pupi.table.tr.cell>
                            <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal" size="sm" variant="ghost" inset="top bottom"/>
                                    <flux:menu class="min-w-32">
                                        @if($user->trashed())
                                            <!-- Options for users in trash -->
                                            <flux:menu.item wire:click="restore({{ $user->id }})" icon="arrow-uturn-up">
                                                {{ __('Restore to Active') }}
                                            </flux:menu.item>

                                            <flux:menu.item wire:click="restoreToArchive({{ $user->id }})" icon="archive-box">
                                                {{ __('Restore to Archive') }}
                                            </flux:menu.item>

                                            <flux:menu.item wire:click="forceDelete({{ $user->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to permanently delete this employee?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Delete Permanently') }}
                                            </flux:menu.item>
                                        @elseif($user->account_status === \App\Enums\User\AccountStatus::ARCHIVED)
                                            <!-- Options for archived users -->
                                            <flux:menu.item wire:click="edit({{ $user->id }})" icon="pencil-square">
                                                {{ __('Edit') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1" />

                                            <flux:menu.item wire:click="activate({{ $user->id }})" icon="check-circle">
                                                {{ __('Set Active') }}
                                            </flux:menu.item>
                                            <flux:menu.item wire:click="notActivate({{ $user->id }})" icon="x-mark">
                                                {{ __('Set Not Activated') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1" />

                                            <flux:menu.item wire:click="delete({{ $user->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to move this employee to trash?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Move to Trash') }}
                                            </flux:menu.item>
                                        @elseif($user->account_status === \App\Enums\User\AccountStatus::NOT_ACTIVATED)
                                            <!-- Options for not activated users -->
                                            <flux:menu.item wire:click="edit({{ $user->id }})" icon="pencil-square">
                                                {{ __('Edit') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1" />

                                            <flux:menu.item wire:click="activate({{ $user->id }})" icon="check-circle">
                                                {{ __('Set Active') }}
                                            </flux:menu.item>
                                            <flux:menu.item wire:click="archive({{ $user->id }})" icon="archive-box">
                                                {{ __('Archive') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1" />

                                            <flux:menu.item wire:click="delete({{ $user->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to add this employee to trash?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Move to Trash') }}
                                            </flux:menu.item>
                                        @else
                                            <!-- Options for active users -->
                                            <flux:menu.item wire:click="edit({{ $user->id }})" icon="pencil-square">
                                                {{ __('Edit') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1" />

                                            <flux:menu.item wire:click="notActivate({{ $user->id }})" icon="x-mark">
                                                {{ __('Set Not Activated') }}
                                            </flux:menu.item>
                                            <flux:menu.item wire:click="archive({{ $user->id }})" icon="archive-box">
                                                {{ __('Archive') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1" />

                                            <flux:menu.item wire:click="delete({{ $user->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to move this employee to trash?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Move to Trash') }}
                                            </flux:menu.item>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <x-pupi.table.tr.empty>
                            <x-pupi.table.tr.empty-cell colspan="7"/>
                        </x-pupi.table.tr.empty>
                    @endforelse
                </x-slot:body>
                <x-slot:pagination>
                    {{ $users->links() }}
                </x-slot:pagination>
            </x-pupi.table.main>
        </div>
    </x-pupi.table.container>
</div>
