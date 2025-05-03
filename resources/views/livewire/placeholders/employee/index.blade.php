<div>
    <div>
        <div class="flex flex-wrap items-center gap-6 sm:flex-nowrap my-4 sm:my-2">
            <h1 class="dark:text-base text-base/7 font-semibold dark:text-white text-gray-900">
                {{ __('Employees') }}
            </h1>

            <div
                class="order-last flex w-full gap-x-8 text-sm/6 font-semibold sm:order-none sm:w-auto sm:border-l sm:border-gray-200 sm:dark:border-gray-500 sm:pl-6 sm:text-sm/7">
                <a href="#"
                   class="dark:text-indigo-400 text-indigo-600">
                    {{ __('Active') }}
                </a>
                <a href="#"
                   class="dark:text-gray-400 text-gray-700">
                    {{ __('Archived') }}
                </a>
                <a href="#"
                   class="dark:text-gray-400 text-gray-700">
                    {{ __('In Trash') }}
                </a>
            </div>

            <flux:modal.trigger name="create-employee">
                <div
                    class="ml-auto flex items-center gap-x-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-pointer">

                    <x-pupi.icon.create class="-ml-1.5 size-5"/>
                    {{ __('Create') }}
                </div>
            </flux:modal.trigger>

        </div>
    </div>

    <!-- Such- und Filterleiste -->
    <div
        class="xl:mt-5 mt-1 h-full flex flex-col items-center justify-between space-y-3 md:flex-row md:space-y-0 md:space-x-4">
        <div class="w-full lg:w-1/3 md:w-1/2">
            <div class="mt-3 flex sm:mt-0">
                <div class="relative w-full">
                    <x-pupi.icon.search
                        class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-5 text-gray-400 sm:size-4"/>
                    <input
                           type="text"
                           placeholder="{{ __('Search #') }}"
                           class="block w-full rounded-md rounded-r-none bg-white border-0 py-1.5 pl-10 pr-3 text-sm text-gray-900 placeholder:text-gray-400 dark:placeholder:text-gray-500 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 dark:focus:outline-indigo-500 dark:outline-gray-700/50 dark:bg-gray-800/50 dark:text-white sm:pl-9 sm:text-sm/6">
                </div>

                <button
                        type="button"
                        class="py-1.5 px-3 sm:text-sm/6 rounded-l-none inline-flex justify-center items-center gap-2 rounded-md font-medium bg-white text-gray-500 shadow-sm align-middle hover:bg-gray-50
                         border-0 outline-1 -outline-offset-1 outline-gray-300 hover:focus:outline-2 hover:focus-within:-outline-offset-2 hover:focus-within:outline-indigo-600 dark:hover:focus:outline-indigo-500 dark:outline-gray-700/50
                         text-sm dark:bg-gray-800/50 dark:hover:bg-gray-800 dark:text-gray-400 dark:hover:text-white">
                    <x-pupi.icon.arrow-path/>
                </button>

            </div>
        </div>

        <div
            class="flex flex-col items-stretch justify-end shrink-0 w-full space-y-2 md:w-auto md:flex-row md:space-y-0 md:items-center md:space-x-3">
            <div class="flex items-center justify-end w-full space-x-1 md:w-auto">
                <!-- Bulk-Actions Dropdown wird hier eingebunden -->

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
                </div>

                <div>
                    <flux:select
                        variant="listbox"
                        placeholder="{{ __('All Status') }}">
                    </flux:select>
                </div>
                <div>
                    <flux:select
                        variant="listbox"
                        placeholder="{{ __('7') }}">
                    </flux::select>
                </div>
            </div>
        </div>
    </div>


    <!-- Tabelle -->
    <x-pupi.table.container>
        <div>
            <x-pupi.table.main>
                <x-slot:head>
                    <th scope="col" class="relative border-b dark:border-gray-700/50 border-gray-200 px-7 sm:w-12 sm:px-6 rounded-tl-lg">
                        <div
                             class="flex rounded-md shadow-xs">
                            <div class="group grid size-4 grid-cols-1">
                                <input
                                       type="checkbox"
                                       class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-600 dark:checked:border-indigo-600 dark:focus:ring-offset-gray-800">
                                <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                    <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </th>
                    <x-pupi.table.th.notsort>
                        {{ __('Name') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>
                        {{ __('Contact') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>
                        {{ __('Department') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>
                        {{ __('Role') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>
                        {{ __('Joined Date') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>
                        {{ __('Status') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.notsort>
                        {{ __('Created') }}
                    </x-pupi.table.th.notsort>
                    <x-pupi.table.th.actions/>
                </x-slot:head>
                <x-slot:body>
                    <!-- Skeleton rows -->
                    @for ($i = 0; $i < 5; $i++)
                        <tr class="animate-pulse dark:bg-gray-900 bg-white">
                            <!-- Checkbox -->
                            <td class="relative px-7 sm:w-12 sm:px-6">
                                <div class="h-4 w-4 mx-auto rounded bg-gray-200 dark:bg-gray-700"></div>
                            </td>
                            <!-- Name with Avatar -->
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                                    <div class="ml-4">
                                        <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                        <div class="mt-1 h-3 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                    </div>
                                </div>
                            </td>
                            <!-- Contact -->
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                <div>
                                    <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                    <div class="mt-1 h-3 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                </div>
                            </td>
                            <!-- Department -->
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            </td>
                            <!-- Role -->
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            </td>
                            <!-- Joined Date -->
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                <div class="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            </td>
                            <!-- Status -->
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                <div class="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            </td>
                            <!-- Created -->
                            <td class="px-3 py-3.5 text-sm text-gray-500 dark:text-gray-400">
                                <div class="h-4 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            </td>
                            <!-- Actions -->
                            <td class="px-3 py-3.5 text-sm">
                                <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded-md mx-auto"></div>
                            </td>
                        </tr>
                    @endfor
                </x-slot:body>
                <x-slot:pagination>
                    <div class="flex items-center justify-between py-2">
                        <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="flex space-x-2">
                            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                    </div>
                </x-slot:pagination>
            </x-pupi.table.main>
        </div>
    </x-pupi.table.container>
</div>
