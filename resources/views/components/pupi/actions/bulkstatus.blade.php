@props(['statusFilter'])
<div>
    <flux:dropdown align="end" offset="-15">
        <flux:button
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
</div>
