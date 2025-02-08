<div class="relative">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
        <tr>
            {{ $head }}
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-900 dark:divide-white/5">
        {{ $body }}
        </tbody>
        <div wire:loading
             wire:target="sortBy, makeActive, makeInactive, search, gotoPage, nextPage, previousPage, archive, archiveSelected, resetFilters, perPage"
             class="absolute inset-0 dark:bg-gray-900 bg-gray-50 opacity-50"/>
        <div wire:loading.flex
             wire:target="sortBy, makeActive, makeInactive, search, gotoPage, nextPage, previousPage, archive, archiveSelected, resetFilters, perPage"
             class="flex justify-center items-center absolute inset-0">
            <x-pupi.icon.spinner class="text-gray-500 dark:text-indigo-600"/>
        </div>
    </table>
</div>
<div
    class="border-t border-gray-200 bg-gray-50 dark:bg-gray-800/50 dark:border-gray-700/50 px-4 py-3 sm:px-6 rounded-b-lg">
    {{ $pagination }}
</div>

