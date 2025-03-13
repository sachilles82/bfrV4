<div
     class="relative w-full">
    <x-pupi.icon.search
        class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-5 text-gray-400 sm:size-4"/>
    <input  wire:model.live.debounce.400ms="search"
            type="text"
            placeholder="{{ __('Search #') }}"
           class="block w-full rounded-md rounded-r-none border-0 py-1.5 pl-10 pr-3 text-sm text-gray-900 placeholder:text-gray-400 placeholder:dark:text-gray-500 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 dark:focus:ring-indigo-500 dark:ring-gray-700/50 dark:bg-gray-800/50 dark:text-white sm:pl-9 sm:text-sm/6">
</div>
