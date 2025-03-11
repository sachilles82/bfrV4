@props(['statusFilter'])
<flux:dropdown align="end" offset="-15">
    <flux:button class="pr-8 p-2.5 px-3 inline-flex justify-center items-center gap-2 rounded-md font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50
                        border-0 ring-1 ring-inset ring-gray-300 hover:focus:ring-2 hover:focus-within:ring-inset hover:focus-within:ring-indigo-600 focus:ring-indigo-600 dark:focus:ring-indigo-500 focus-within:ring-inset focus:ring-2 hover:dark:focus:ring-indigo-500 dark:ring-gray-700/50
                        text-sm dark:bg-gray-800/50 dark:hover:bg-gray-800 dark:text-gray-400 dark:hover:text-white cursor-pointer"
                 icon-trailing="chevron-down">
        {{ __('Status') }}
    </flux:button>

    <flux:menu class="min-w-32">
        {{-- Status-specific action items using switch statement for cleaner code --}}
        @switch($statusFilter)
            @case('trashed')
                {{-- Options for trashed users --}}
                <flux:menu.item wire:click="bulkUpdateStatus('active')" icon="arrow-uturn-up">
                    {{ __('Restore to Active') }}
                </flux:menu.item>
                <flux:menu.item wire:click="bulkUpdateStatus('restore_to_archive')" icon="archive-box">
                    {{ __('Restore to Archive') }}
                </flux:menu.item>
                {{-- Neue Option hinzufügen --}}
                <flux:menu.item wire:click="bulkUpdateStatus('restore_to_inactive')" icon="clock">
                    {{ __('Restore to Inactive') }}
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
                <flux:menu.item wire:click="bulkUpdateStatus('inactive')" icon="clock">
                    {{ __('Set as Inactive') }}
                </flux:menu.item>
                <flux:menu.item wire:click="bulkUpdateStatus('archived')" icon="archive-box">
                    {{ __('Archive') }}
                </flux:menu.item>

                <flux:separator class="my-1"/>

                <flux:menu.item wire:click="bulkUpdateStatus('trashed')"
                                wire:confirm="{{ __('Are you sure you want to move all selected employees to trash?') }}"
                                icon="trash"
                                variant="danger">
                    {{ __('Move to Trash') }}
                </flux:menu.item>
                @break

            @case('inactive')
                {{-- Options for not activated users --}}
                <flux:menu.item wire:click="bulkUpdateStatus('active')" icon="check-circle">
                    {{ __('Set Active') }}
                </flux:menu.item>
                <flux:menu.item wire:click="bulkUpdateStatus('archived')" icon="archive-box">
                    {{ __('Archive') }}
                </flux:menu.item>

                <flux:separator class="my-1"/>

                <flux:menu.item wire:click="bulkUpdateStatus('trashed')"
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
                <flux:menu.item wire:click="bulkUpdateStatus('inactive')" icon="clock">
                    {{ __('Set as Inactive') }}
                </flux:menu.item>

                <flux:separator class="my-1"/>

                <flux:menu.item wire:click="bulkUpdateStatus('trashed')"
                                wire:confirm="{{ __('Are you sure you want to move all selected employees to trash?') }}"
                                icon="trash"
                                variant="danger">
                    {{ __('Move to Trash') }}
                </flux:menu.item>
                @break

            @default
                {{-- Default options for any other status --}}
                <flux:separator class="my-1"/>

                <flux:menu.item wire:click="bulkUpdateStatus('trashed')"
                                wire:confirm="{{ __('Are you sure you want to move all selected employees to trash?') }}"
                                icon="trash"
                                variant="danger">
                    {{ __('Move to Trash') }}
                </flux:menu.item>
        @endswitch
    </flux:menu>
</flux:dropdown>
