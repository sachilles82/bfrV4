<div>
    <div>
        <div class="flex flex-wrap items-center gap-6 sm:flex-nowrap my-4 sm:my-2">
            <h1 class="dark:text-base text-base/7 font-semibold dark:text-white text-gray-900">
                {{ __('Employees') }}
            </h1>

            <x-pupi.actions.status-filter
                :statusFilter="$statusFilter"
            />

            <flux:modal.trigger name="create-employee">
                <div
                    class="ml-auto flex items-center gap-x-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-pointer">

                    <x-pupi.icon.create class="-ml-1.5 size-5"/>
                    {{ __('Create') }}
                </div>
            </flux:modal.trigger>

        </div>
    </div>

    <!-- Such- und Filterleiste -->
    <div
        class="xl:mt-5 mt-1 h-full flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4">
        <div class="w-full lg:w-1/3 md:w-1/2">
            <div class="mt-3 flex sm:mt-0">
                <x-pupi.actions.search/>
                <x-pupi.actions.reset-filters/>
            </div>
        </div>
        <div
            class="flex flex-col items-stretch justify-end shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
            <div class="flex items-center justify-end w-full space-x-1 md:w-auto">
                <!-- Bulk-Actions Dropdown wird hier eingebunden -->

                <div class="flex space-x-1" x-show="$wire.selectedIds.length > 0" x-cloak>
                    <div class="hidden sm:flex items-center justify-center">
                        <span class="text-indigo-600 dark:text-indigo-400">
                            <span x-text="$wire.selectedIds.length"
                                  class="pr-2 text-sm font-semibold text-indigo-600 border-r border-gray-200 dark:border-gray-700 dark:text-indigo-600"></span>
                            <span class="pl-2 pr-2">
                                {{ __('Selected') }}
                            </span>
                        </span>
                    </div>


                    <x-pupi.actions.bulkexport
                        :statusFilter="$statusFilter"
                    />
                    <x-pupi.actions.bulkstatus
                        :statusFilter="$statusFilter"
                    />
                </div>

                <div>
                    <flux:select
                        variant="listbox"
                        placeholder="{{ __('All Status') }}"
                        wire:model.live="employeeStatusFilter"
                        id="employeeStatusFilter">

                        <flux:option wire:click="setAllStatus" value="">{{ __('All Status') }}</flux:option>

                        @foreach($employeeStatuses as $empStatus)
                            <flux:option value="{{ $empStatus->value }}">
                                <div class="inline-flex items-center">
                                    <span class="mr-2">
                                        <x-dynamic-component
                                            :component="$empStatus->icon()"
                                            class="h-4 w-4 rounded-md {{ $empStatus->colors() ?? '' }}"/>
                                    </span>
                                    {{ $empStatus->label() }}
                                </div>
                            </flux:option>
                        @endforeach
                    </flux:select>
                </div>

                {{--                    <x-pupi.actions.reset-filters wire:click="resetFilters"/>--}}
                <x-pupi.actions.per-page/>
            </div>
        </div>
    </div>


    <!-- Tabelle -->
    <x-pupi.table.container>
        <div x-data="{ checked:false }">
            <x-pupi.table.main>
                <x-slot:head>
                    <x-pupi.table.th.check-all/>
                    <x-pupi.table.th.sort column="name" :$sortCol :$sortAsc class="pl-2">
                        {{ __('Name') }}
                    </x-pupi.table.th.sort>
                    <x-pupi.table.th.notsort>{{ __('Contact') }}</x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>{{ __('Team') }}</x-pupi.table.th.notsort>
                    <!-- Spalte Account Status -->
                    <x-pupi.table.th.notsort>{{ __('Role') }}</x-pupi.table.th.notsort>
                    <x-pupi.table.th.sort column="joined_at" :$sortCol :$sortAsc class="pl-2">
                        {{ __('Joined Date') }}
                    </x-pupi.table.th.sort>
                    <x-pupi.table.th.notsort>{{ __('Status') }}</x-pupi.table.th.notsort>
                    <x-pupi.table.th.sort column="created_at" :$sortCol :$sortAsc class="pl-2">
                        {{ __('Created') }}
                    </x-pupi.table.th.sort>
                    <x-pupi.table.th.actions/>
                </x-slot:head>
                <x-slot:body>
                    @if($statusFilter === 'trashed')
                        <tr>
                            <td colspan="8"
                                class="bg-yellow-50 dark:bg-yellow-400/10 dark:text-yellow-500 px-4 py-2 text-yellow-800">
                                <div class="flex items-start">
                                    <x-pupi.icon.danger class="h-6 w-6"/>
                                    <div class="ml-3 flex-1 pt-0.5">
                                        <p class="text-sm font-medium">{{ __('Attention!') }}
                                            <span
                                                class="ml-2 font-normal text-sm text-gray-600 dark:text-gray-400">{{ __('Trash will delete automatically all') }}</span>
                                            <span class="font-medium">{{ __('7 Days') }}</span>
                                            <span
                                                class="font-normal text-sm text-gray-600 dark:text-gray-400">{{ __('automatically permanently') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @forelse($users as $user)
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
                                    <div class="h-11 w-11 shrink-0">
                                        @if($user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                                 alt="{{ $user->name }}" class="h-11 w-11 rounded-full">
                                        @else
                                            @php
                                                $nameInitials = strtoupper(join('+', array_map(fn($name) => substr($name, 0, 1), explode(' ', $user->name . ' ' . $user->last_name))));
                                            @endphp
                                            <img
                                                src="https://ui-avatars.com/api/?name={{ $nameInitials }}&color=7F9CF5&background=EBF4FF"
                                                alt="{{ $user->name }}" class="h-11 w-11 rounded-full">
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <a wire:navigate.hover href="{{ route('employees.profile', $user) }}"
                                           class="font-medium text-gray-900 dark:text-gray-300 hover:text-indigo-700 decoration-1 hover:underline dark:hover:text-indigo-400">
                                            {{ $user->name }} {{ $user->last_name }}
                                        </a>
                                        <div class="mt-1 text-gray-500 dark:text-gray-400">
                                            @if($user->employee->profession)
                                                {{ $user->employee->profession->name }}
                                            @endif

                                            @if($user->employee->profession && $user->employee->stage)
                                                <span class="mx-1">•</span>
                                            @endif

                                            @if($user->employee->stage)
                                                {{ $user->employee->stage->name }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>

                                <x-pupi.table.tr.copiable-contact
                                    :value="$user->phone_1"
                                    linkPrefix="tel:"
                                    fallbackText="+41 44 401 11 42"
                                    id="phone-{{ $user->id }}"
                                />

                                <x-pupi.table.tr.copiable-contact
                                    :value="$user->email"
                                    linkPrefix="mailto:"
                                    id="email-{{ $user->id }}"
                                />

                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <div class="text-gray-900 dark:text-gray-300">
                                    @if($user->teams->count() > 0)
                                        {{ $user->teams->pluck('name')->join(', ') }}
                                    @else
                                        <span class="text-gray-400">No teams</span>
                                    @endif
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    @if($user->department)
                                        {{ $user->department->name }}
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400"> {{ __('No Department') }}</span>
                                    @endif
                                </div>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <div class="inline-flex items-center rounded-lg px-2 py-1 text-sm font-medium ring-1 ring-inset mr-2
                                bg-indigo-50 text-indigo-800 ring-indigo-700/10
                                dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-indigo-400/30">
                                            <x-pupi.icon.shield-check class="w-5 h-5 mr-1"/>
                                            {{$role->name}}
                                        </div>

                                    @endforeach
                                </div>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <flux:tooltip class="cursor-default"
                                              content="
                  {{ __('Hired on: ') }}
                  {{ $user->joined_at ? $user->joined_at->format('d.m.Y') : __('Not set') }}"
                                              position="top">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        {{ $user->joined_at ? $user->joined_at->diffForHumans() : __('Not available') }}
                                    </div>
                                </flux:tooltip>
                                @if($user->trashed())
                                    <div class="flex items-center mt-1 {{ $user->deletion_urgency_class }}">
                                        <x-pupi.icon.trash class="h-5 w-5 mr-1"/>
                                        {{ $user->deletion_message }}
                                        <span class="text-gray-500 dark:text-gray-400 text-xs ml-1">({{ $user->permanent_deletion_date_for_humans }})</span>
                                    </div>
                                @endif
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <flux:tooltip class="cursor-default" content="Settings">
                                    <div
                                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset gap-1
                                        {{  $user->employee->employee_status->colors() }}"
                                    >
                                        <x-dynamic-component class="h-5 w-5"
                                                             :component="$user->employee->employee_status->icon()"
                                        />
                                        <div>{{ $user->employee->employee_status->label() }}</div>
                                    </div>
                                </flux:tooltip>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <div class="text-gray-500 dark:text-gray-400">
                                    {{ $user->created_at->diffForHumans() }}
                                </div>
                            </x-pupi.table.tr.cell>
                            <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal" size="sm"
                                                 variant="ghost" inset="top bottom"/>
                                    <flux:menu class="min-w-32">
                                        @if($user->trashed())
                                            <!-- Options for users in trash -->
                                            <flux:menu.item wire:click="restore({{ $user->id }})"
                                                            icon="arrow-uturn-up">
                                                {{ __('Restore to Active') }}
                                            </flux:menu.item>

                                            <flux:menu.item wire:click="restoreToArchive({{ $user->id }})"
                                                            icon="archive-box">
                                                {{ __('Restore to Archive') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="forceDelete({{ $user->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to permanently delete this employee?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Delete Permanently') }}
                                            </flux:menu.item>
                                        @elseif($user->model_status === \App\Enums\Model\ModelStatus::ARCHIVED)
                                            <!-- Options for archived users -->
                                            <flux:modal.trigger name="edit-employee">
                                                <flux:menu.item wire:click="edit({{ $user->id }})" icon="pencil-square">
                                                    {{ __('Edit') }}
                                                </flux:menu.item>
                                            </flux:modal.trigger>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="activate({{ $user->id }})"
                                                            icon="check-circle">
                                                {{ __('Set Active') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="delete({{ $user->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to move this employee to trash?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Move to Trash') }}
                                            </flux:menu.item>
                                        @else
                                            <!-- Options for active users -->

                                            <flux:modal.trigger name="edit-employee">
                                                <flux:menu.item wire:click="edit({{ $user->id }})" icon="pencil-square">
                                                    {{ __('Edit') }}
                                                </flux:menu.item>
                                            </flux:modal.trigger>

                                            <flux:menu.item wire:click="archive({{ $user->id }})"
                                                            icon="archive-box">
                                                {{ __('Archive') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

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
                            <x-pupi.table.tr.empty-cell colspan="9"/>
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
