{{-- resources/views/components/pupi/actions/bulkactions.blade.php --}}
@props(['statusFilter'])

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

    <flux:dropdown align="end" offset="-15">
        <flux:button class="p-2.5 px-3 inline-flex justify-center items-center gap-2 rounded-md font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50
                        border-0 ring-1 ring-inset ring-gray-300 hover:ring-2 hover:ring-inset hover:ring-indigo-600 dark:hover:ring-indigo-500 hover:dark:ring-indigo-600 dark:ring-gray-700/50
                        text-sm dark:bg-gray-800/50 dark:hover:bg-gray-800 dark:text-gray-400 dark:hover:text-white cursor-pointer"
                     icon-trailing="chevron-down">
            {{ __('More') }}
        </flux:button>

        <flux:menu class="min-w-32">
            {{-- Export Options - Common for all states --}}
            <flux:menu.item icon="pencil-square">
                {{ __('CSV Export') }}
            </flux:menu.item>

            <flux:menu.item icon="pencil-square">
                {{ __('XLSX Export') }}
            </flux:menu.item>

            <flux:menu.item icon="document">
                {{ __('PDF Export') }}
            </flux:menu.item>

            <flux:separator/>

            {{-- Status-specific action items using switch statement for cleaner code --}}
            @switch($statusFilter)
                @case('trash')
                    {{-- Options for trashed users --}}
                    <flux:menu.item wire:click="bulkUpdateStatus('active')" icon="arrow-uturn-up">
                        {{ __('Restore to Active') }}
                    </flux:menu.item>
                    <flux:menu.item wire:click="bulkUpdateStatus('restore_to_archive')" icon="archive-box">
                        {{ __('Restore to Archive') }}
                    </flux:menu.item>

                    <flux:separator class="my-1"/>

                    <flux:menu.item wire:click="bulkForceDelete()"
                                    wire:confirm="{{ __('Are you sure you want to delete all selected employees permanently?') }}"
                                    icon="trash"
                                    variant="danger">
                        {{ __('Delete Permanently') }}
                    </flux:menu.item>
                    @break

                @case('active')
                    {{-- Options for active users --}}
                    <flux:menu.item wire:click="bulkUpdateStatus('not_activated')" icon="x-circle">
                        {{ __('Set as Not Activated') }}
                    </flux:menu.item>
                    <flux:menu.item wire:click="bulkUpdateStatus('archived')" icon="archive-box">
                        {{ __('Archive') }}
                    </flux:menu.item>

                    <flux:separator class="my-1"/>

                    <flux:menu.item wire:click="bulkUpdateStatus('trash')"
                                    wire:confirm="{{ __('Are you sure you want to move all selected employees to trash?') }}"
                                    icon="trash"
                                    variant="danger">
                        {{ __('Move to Trash') }}
                    </flux:menu.item>
                    @break

                @case('not_activated')
                    {{-- Options for not activated users --}}
                    <flux:menu.item wire:click="bulkUpdateStatus('active')" icon="check-circle">
                        {{ __('Set Active') }}
                    </flux:menu.item>
                    <flux:menu.item wire:click="bulkUpdateStatus('archived')" icon="archive-box">
                        {{ __('Archive') }}
                    </flux:menu.item>

                    <flux:separator class="my-1"/>

                    <flux:menu.item wire:click="bulkUpdateStatus('trash')"
                                    wire:confirm="{{ __('Are you sure you want to move all selected employees to trash?') }}"
                                    icon="trash"
                                    variant="danger">
                        {{ __('Move to Trash') }}
                    </flux:menu.item>
                    @break

                @case('archived')
                    {{-- Options for archived users --}}
                    <flux:menu.item wire:click="bulkUpdateStatus('active')" icon="check-circle">
                        {{ __('Set Active') }}
                    </flux:menu.item>
                    <flux:menu.item wire:click="bulkUpdateStatus('not_activated')" icon="x-circle">
                        {{ __('Set as Not Activated') }}
                    </flux:menu.item>

                    <flux:separator class="my-1"/>

                    <flux:menu.item wire:click="bulkUpdateStatus('trash')"
                                    wire:confirm="{{ __('Are you sure you want to move all selected employees to trash?') }}"
                                    icon="trash"
                                    variant="danger">
                        {{ __('Move to Trash') }}
                    </flux:menu.item>
                    @break

                @default
                    {{-- Default options for any other status --}}
                    <flux:separator class="my-1"/>

                    <flux:menu.item wire:click="bulkUpdateStatus('trash')"
                                    wire:confirm="{{ __('Are you sure you want to move all selected employees to trash?') }}"
                                    icon="trash"
                                    variant="danger">
                        {{ __('Move to Trash') }}
                    </flux:menu.item>
            @endswitch
        </flux:menu>
    </flux:dropdown>
</div>
