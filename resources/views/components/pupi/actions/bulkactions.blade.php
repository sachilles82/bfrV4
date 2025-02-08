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
            {{ __('Moore') }}
        </flux:button>

        <flux:menu class="min-w-32">
            <flux:menu.item icon="pencil-square">
                {{ __('CSV Export') }}
            </flux:menu.item>

            <flux:menu.item icon="pencil-square">
                {{ __('XLSX Export') }}
            </flux:menu.item>

            <flux:menu.item icon="document">
                {{ __('PDF Export') }}
            </flux:menu.item>

            <flux:menu.item
                wire:confirm="{{ __('Are you sure you want to remove this department?') }}"
                {{--                                                        wire:confirm.prompt="Are you sure?\n\nType YES to confirm|DELETE"--}}
                icon="trash" variant="danger">
                {{ __('Delete') }}
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</div>
