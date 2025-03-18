<div>
    <div
        class="xl:mt-2 mt-1 h-full flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4">
        <div class="w-full lg:w-1/3 md:w-1/2">
            <x-pupi.actions.search wire:model.live.debounce.400ms="search"/>
        </div>
        <div
            class="flex flex-col items-stretch justify-end shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
            <div class="flex items-center justify-end w-full space-x-1 md:w-auto">
                <x-pupi.actions.bulkstatus :statusFilter="$statusFilter"/>
                <x-pupi.actions.reset-filters wire:click="resetFilters"/>
                <x-pupi.actions.per-page/>
                <div>
                    <flux:select variant="listbox" wire:model.live="statusFilter" id="statusFilter">
                        @foreach($statuses as $status)
                            <flux:option value="{{ $status->value }}">{{ __($status->label()) }}</flux:option>
                        @endforeach
                    </flux::select>
                </div>
            </div>
        </div>
    </div>
@json($selectedIds) @json($idsOnPage)
    <x-pupi.table.container>
        <div x-data="{checked:false}">
            <x-pupi.table.main>
                <x-slot:head>
                    <x-pupi.table.th.check-all/>
                    <x-pupi.table.th.notsort>
                        {{ __('Department') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>
                        {{ __('Team') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>
                        {{ __('Created By') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.actions/>
                </x-slot:head>
                <x-slot:body>
                    @forelse($departments as $department)
                        <tr
                            wire:key="{{ $department->id }}"
                            x-on:check-all.window="checked = $event.detail"
                            x-on:update-table.window="checked = false"
                            x-data="{ checked: false }"
                            x-init="checked = $wire.selectedIds.includes('{{ $department->id }}')"
                            x-bind:class="{
                            'bg-gray-100 dark:bg-gray-800/50': checked,
                            'hover:bg-gray-100 dark:hover:bg-gray-800/50': !checked
                        }"
                        >
                            <td class="relative px-7 sm:w-12 sm:px-6">
                                <div x-show="checked" x-cloak
                                     class="absolute inset-y-0 left-0 w-0.5 dark:bg-indigo-500 bg-indigo-600"></div>
                                <x-pupi.table.tr.checkbox x-model="checked" wire:model.live="selectedIds"
                                                          value="{{ $department->id }}"/>
                            </td>
                            <x-pupi.table.tr.cell>
                                <div x-show="checked" x-cloak>
                                    <div
                                        class="font-medium text-gray-500 dark:text-gray-400">
                                        {{$department->name}}
                                    </div>
                                </div>
                                <div x-show="!checked">
                                    <a wire:navigate.hover
                                       href="{{ route('settings.departments.show', $department->id) }}"
                                       class="font-medium text-gray-900 dark:text-gray-300
                                                hover:text-indigo-700 decoration-1 hover:underline
                                                dark:hover:text-indigo-300">
                                        {{$department->name}}
                                    </a>
                                </div>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>{{ $department->team->name }}</x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>{{ $department->creator->name }}<div
                                    class="flex items-center mt-1 {{ $department->days_until_permanent_delete <= 2 ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    @if($department->trashed())
                                        <div class="flex items-center mt-1 {{ $department->deletion_urgency_class }}">
                                            <x-pupi.icon.clock class="h-4 w-4 mr-1"/>
                                            {{ $department->deletion_message }}
                                            <span class="text-gray-500 dark:text-gray-400 text-xs ml-1">({{ $department->permanent_deletion_date_for_humans }})</span>
                                        </div>
                                    @endif
                                </div>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <div class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset gap-1
                                   {{ $department->model_status->colors() }}">
                                    <x-dynamic-component :component="$department->model_status->icon()"/>
                                    <div>{{ $department->model_status->label() }}</div>

                                </div>
                            </x-pupi.table.tr.cell>
                            <td x-show="!checked"
                                class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <!-- Edit Dropdown -->
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal" size="sm"
                                                 variant="ghost" inset="top bottom"/>

                                    <flux:menu class="min-w-32">
                                        <flux:menu.item wire:click="showEditModal({{ $department->id }})"
                                                        icon="pencil-square">
                                            {{ __('Edit') }}
                                        </flux:menu.item>

                                        <flux:menu.item wire:click="delete({{ $department->id }})"
                                                        wire:confirm="{{ __('Are you sure you want to remove this department?') }}"
                                                        {{--                                                        wire:confirm.prompt="Are you sure?\n\nType YES to confirm|DELETE"--}}
                                                        icon="trash" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                        @if($department->trashed())
                                            <!-- Options for departments in trash -->
                                            <flux:menu.item wire:click="restore({{ $department->id }})" icon="arrow-uturn-up">
                                                {{ __('Restore to Active') }}
                                            </flux:menu.item>

                                            <flux:menu.item wire:click="restoreToArchive({{ $department->id }})"
                                                            icon="archive-box">
                                                {{ __('Restore to Archive') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="forceDelete({{ $department->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to permanently delete this employee?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Delete Permanently') }}
                                            </flux:menu.item>
                                        @elseif($department->model_status === \App\Enums\Model\ModelStatus::ARCHIVED)
                                            <!-- Options for archived departments -->
                                            <flux:menu.item wire:click="edit({{ $department->id }})" icon="pencil-square">
                                                {{ __('Edit') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="activate({{ $department->id }})" icon="check-circle">
                                                {{ __('Set Active') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="delete({{ $department->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to move this employee to trash?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Move to Trash') }}
                                            </flux:menu.item>
                                        @else
                                            <!-- Options for active departments -->
                                            <flux:menu.item wire:click="edit({{ $department->id }})" icon="pencil-square">
                                                {{ __('Edit') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="archive({{ $department->id }})" icon="archive-box">
                                                {{ __('Archive') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="delete({{ $department->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to move this employee to trash?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Move to Trash') }}
                                            </flux:menu.item>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                            <td x-show="checked" x-cloak
                                class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <flux:button class="cursor-default" icon="ellipsis-horizontal" size="sm" variant="ghost"
                                             inset="top bottom"/>
                            </td>
                        </tr>
                    @empty
                        <x-pupi.table.tr.empty>
                            <x-pupi.table.tr.empty-cell colspan="6"/>
                        </x-pupi.table.tr.empty>
                    @endforelse
                </x-slot:body>
                <x-slot:pagination>
                    {{ $departments->links() }}
                </x-slot:pagination>
            </x-pupi.table.main>
        </div>
    </x-pupi.table.container>


    <flux:modal name="department-edit" variant="flyout" class="space-y-6">
        <form wire:submit="update" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Edit Department') }}</flux:heading>
                <flux:subheading>{{ __('edit this Department') }}</flux:subheading>
            </div>

            <flux:input wire:model="name" type="text" label="Name" placeholder="Department"/>

            <div class="flex">
                <flux:spacer/>

                <flux:button type="submit" variant="primary">{{ __('Update') }}</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
