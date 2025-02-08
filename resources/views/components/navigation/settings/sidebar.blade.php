<div
    class="2xl:min-h-screen xl:min-h-screen sticky top-0 z-98 bg-gray-100 dark:bg-gray-900 dark:border-gray-700/50 border-gray-200 px-4 xl:py-6 sm:py-2 sm:px-2 lg:pl-6 xl:w-64 xl:shrink-0 xl:border-r xl:pl-6">

    <nav class="flex-none px-0 sm:px-0 lg:px-0 overflow-x-auto">
        <ul role="list" class="flex gap-x-3 gap-y-1 whitespace-nowrap xl:flex-col">
            <li>
                <a href="{{ route('settings.company') }}" wire:navigate.hover
                   class="{{ request()->routeIs('settings.company') ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-semibold">
                    <flux:icon.building-office
                        class="h-6 w-6 {{ request()->routeIs('settings.company') ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>
                    <span class="truncate">{{ __('Company') }}</span>
                </a>

                {{--            <a href="--}}
                {{--            {{ route('settings.company.show', ['company' => $company->id]) }}" --}}
                {{--               wire:navigate.hover--}}
                {{--               class="{{ request()->routeIs('settings.company.show') ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-semibold">--}}
                {{--                <x-pupi.icon.building-office class="h-6 w-6 {{ request()->routeIs('settings.company.show') ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>--}}
                {{--                <span class="truncate">{{ __('Company') }}</span>--}}
                {{--            </a>--}}

            </li>
            <li>
                <a href="{{ route('settings.profile') }}" wire:navigate.hover
                   class="{{ request()->routeIs('settings.profile') ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-semibold">
                    <flux:icon.user
                        class="h-6 w-6 {{ request()->routeIs('settings.profile') ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>
                    <span class="truncate">{{ __('Profile') }}</span>
                </a>
            </li>
            @can(\App\Enums\Role\Permission::LIST_ROLE)
                <li>
                    <a href="{{ route('settings.roles') }}" wire:navigate.hover
                       class="{{ request()->routeIs(['settings.roles', 'settings.roles.show']) ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-semibold">
                        <flux:icon.shield-check
                            class="h-6 w-6 {{ request()->routeIs(['settings.roles', 'settings.roles.show']) ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>
                        <span class="truncate">{{ __('Roles') }}</span>
                    </a>
                </li>
            @endcan
            <li>
                <a href="{{ route('settings.departments') }}" wire:navigate.hover
                   class="{{ request()->routeIs(['departments.roles', 'settings.departments.show']) ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-semibold">
                    <flux:icon.shield-check
                        class="h-6 w-6 {{ request()->routeIs(['departments.roles', 'settings.departments.show']) ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>
                    <span class="truncate">{{ __('Departments') }}</span>
                </a>
            </li>
            <li>
                <a href="" wire:navigate.hover
                   class="{{ request()->routeIs('departments.index') ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-semibold">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                         class="h-6 w-6
                     {{ request()->routeIs('departments.index') ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                    </svg>
                    <span class="truncate">{{ __('Departments') }}</span>
                    <span
                        class="ml-auto w-9 min-w-max whitespace-nowrap rounded-full bg-gray-200 px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-gray-600 ring-1 ring-inset ring-gray-200"
                        aria-hidden="true">5</span>
                </a>

            </li>

        </ul>
    </nav>
</div>
