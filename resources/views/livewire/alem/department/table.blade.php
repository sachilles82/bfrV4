<div>
    <div class="flex flex-wrap items-center gap-6 sm:flex-nowrap my-4 sm:my-2">
        <h1 class="text-base font-semibold text-gray-900">
            {{ __('Departments') }}
        </h1>

        <x-pupi.actions.status-filter
            :statusFilter="$statusFilter"
        />
        <!-- Create Button für die Componente-->
        <flux:modal.trigger name="create-department">
            <div
                class="ml-auto flex items-center gap-x-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-pointer">

                <x-pupi.icon.create class="-ml-1.5 size-5"/>
                {{ __('Create') }}
            </div>
        </flux:modal.trigger>
    </div>

    <!-- Such- und Filterleiste -->
    <div
        class="xl:mt-5 mt-1 h-full flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4">
        <div class="w-full lg:w-1/3 md:w-1/2">
            <div class="mt-3 flex sm:mt-0">
                <x-pupi.actions.search wire:model.live.debounce.400ms="search"/>
                <x-pupi.actions.reset-filters wire:click="resetFilters"/>
            </div>
        </div>
        <div
            class="flex flex-col items-stretch justify-end shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
            <div class="flex items-center justify-end w-full space-x-1 md:w-auto">
                <!-- Bulk-Actions Dropdown wird hier eingebunden -->

                <div class="flex space-x-1" x-show="$wire.selectedIds.length > 0" x-cloak>
                    <div class="hidden sm:flex items-center justify-center">
                        <span class="text-indigo-600">
                            <span x-text="$wire.selectedIds.length"
                                  class="pr-2 text-sm font-semibold text-indigo-600 border-r border-gray-200"></span>
                            <span class="pl-2 pr-2">
                                {{ __('Selected') }}
                            </span>
                        </span>
                    </div>

                    <x-pupi.actions.bulkstatus
                        :statusFilter="$statusFilter"
                    />
                </div>

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
                        {{ __('Department') }}
                    </x-pupi.table.th.sort>
                    <x-pupi.table.th.notsort>
                        {{ __('Users') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.sort column="created_at" :$sortCol :$sortAsc class="pl-2">
                        {{ __('Created') }}
                    </x-pupi.table.th.sort>
                    <x-pupi.table.th.actions/>
                </x-slot:head>
                <x-slot:body>
                    @if($statusFilter === 'trashed')
                        <x-pupi.table.tr.trash-warning :colspan="5" />
                    @endif
                    @forelse($departments as $department)
                            <x-pupi.table.tr.selectable-row :id="$department->id">
                            <td class="relative px-7 sm:w-12 sm:px-6">
                                <div x-show="checked" x-cloak
                                     class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600"></div>
                                <x-pupi.table.tr.checkbox x-model="checked" wire:model="selectedIds"
                                                          value="{{ $department->id }}"/>
                            </td>
                            <x-pupi.table.tr.cell>
                                <a wire:navigate.hover
                                   href="{{ route('settings.departments.show', $department->id) }}"
                                   class="font-medium text-gray-900 dark:text-gray-300 hover:text-indigo-700 decoration-1 hover:underline dark:hover:text-indigo-400">
                                    {{$department->name}}
                                </a>
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                @php $userAvatars = $this->prepareUserAvatars($department); @endphp

                                @if($userAvatars['has_users'])
                                    <div class="flex items-center space-x-2">
                                        <div class="flex shrink-0 -space-x-1">
                                            @foreach($userAvatars['visible_users'] as $user)
                                                <flux:tooltip class="cursor-default"
                                                              content="{{ $user['full_name'] }}"
                                                              position="top">
                                                    <img
                                                        class="size-6 max-w-none rounded-full ring-2 ring-white dark:ring-gray-700"
                                                        src="{{ $user['avatar_url'] }}"
                                                        alt="{{ $user['full_name'] }}">
                                                </flux:tooltip>
                                            @endforeach
                                        </div>

                                        @if($userAvatars['remaining_count'] > 0)
                                            <flux:tooltip class="cursor-default" position="top">
                                                <span
                                                    class="shrink-0 text-xs/5 font-medium text-gray-600 dark:text-gray-400">+{{ $userAvatars['remaining_count'] }}</span>

                                                <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                                    @foreach($userAvatars['remaining_user_groups'] as $userName)
                                                        <p>{{ $userName }}</p>
                                                    @endforeach
                                                </flux:tooltip.content>
                                            </flux:tooltip>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('No Employees') }}</span>
                                @endif

                                @if($department->trashed())
                                    <div
                                        class="flex items-center mt-1 {{ $department->deletion_urgency_class ?? 'text-amber-600' }}">
                                        <x-pupi.icon.clock class="h-4 w-4 mr-1"/>
                                        {{ $department->deletion_message ?? __('Pending deletion') }}
                                        <span class="text-gray-500 text-xs ml-1">({{ $department->permanent_deletion_date_for_humans ?? __('7 days') }})</span>
                                    </div>
                                @endif
                            </x-pupi.table.tr.cell>
                            <x-pupi.table.tr.cell>
                                <div class="text-gray-500 dark:text-gray-400">
                                    {{ $department->created_at ? $department->created_at->diffForHumans() : __('Not available') }}
                                </div>
                            </x-pupi.table.tr.cell>
                            <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal" size="sm"
                                                 variant="ghost" inset="top bottom"/>

                                    <flux:menu class="min-w-32">
                                        <flux:modal.trigger name="edit-department">
                                            <flux:menu.item wire:click="edit({{ $department->id }})"
                                                            icon="pencil-square">
                                                {{ __('Edit') }}
                                            </flux:menu.item>
                                        </flux:modal.trigger>

                                        @if($department->trashed())
                                            <!-- Options for departments in trash -->
                                            <flux:menu.item wire:click="restore({{ $department->id }})"
                                                            icon="arrow-uturn-up">
                                                {{ __('Restore to Active') }}
                                            </flux:menu.item>

                                            <flux:menu.item wire:click="restoreToArchive({{ $department->id }})"
                                                            icon="archive-box">
                                                {{ __('Restore to Archive') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="forceDelete({{ $department->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to permanently delete this department?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Delete Permanently') }}
                                            </flux:menu.item>
                                        @elseif($department->model_status === \App\Enums\Model\ModelStatus::ARCHIVED)
                                            <!-- Options for archived departments -->

                                            <flux:modal.trigger name="edit-department">
                                                <flux:menu.item wire:click="edit({{ $department->id }})"
                                                                icon="pencil-square">
                                                    {{ __('Edit') }}
                                                </flux:menu.item>
                                            </flux:modal.trigger>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="activate({{ $department->id }})"
                                                            icon="check-circle">
                                                {{ __('Set Active') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="delete({{ $department->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to move this department to trash?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Move to Trash') }}
                                            </flux:menu.item>
                                        @else
                                            <flux:menu.item wire:click="archive({{ $department->id }})"
                                                            icon="archive-box">
                                                {{ __('Archive') }}
                                            </flux:menu.item>

                                            <flux:separator class="my-1"/>

                                            <flux:menu.item wire:click="delete({{ $department->id }})"
                                                            wire:confirm="{{ __('Are you sure you want to delete this department?') }}"
                                                            icon="trash" variant="danger">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </x-pupi.table.tr.selectable-row>
                    @empty
                        <x-pupi.table.tr.empty>
                            <x-pupi.table.tr.empty-cell colspan="5"/>
                        </x-pupi.table.tr.empty>
                    @endforelse
                </x-slot:body>
                <x-slot:pagination>
                    {{ $departments->links() }}
                </x-slot:pagination>
            </x-pupi.table.main>
        </div>
    </x-pupi.table.container>
</div>
