@props(['statusFilter'])
<flux:dropdown align="end" offset="-15">
    <flux:button icon-trailing="chevron-down">
        {{ __('Export') }}
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

    </flux:menu>
</flux:dropdown>
