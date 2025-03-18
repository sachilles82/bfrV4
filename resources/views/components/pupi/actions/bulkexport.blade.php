@props(['statusFilter'])
<flux:dropdown align="end" offset="-15">
    <flux:button class="p-2.5 px-3 inline-flex justify-center items-center gap-2 rounded-md font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50
                        border-0 ring-1 ring-inset ring-gray-300 hover:ring-2 hover:ring-inset hover:ring-indigo-600 dark:hover:ring-indigo-500 dark:hover:ring-indigo-600 dark:ring-gray-700/50
                        text-sm dark:bg-gray-800/50 dark:hover:bg-gray-800 dark:text-gray-400 dark:hover:text-white cursor-pointer"
                 icon-trailing="chevron-down">
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
