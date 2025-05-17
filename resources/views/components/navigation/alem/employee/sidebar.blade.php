<div
    class="2xl:min-h-screen xl:min-h-screen sticky top-0 z-98 bg-gray-100 dark:bg-gray-900 dark:border-white/5 border-gray-200 px-4 xl:py-6 sm:py-2 sm:px-2 lg:pl-6 xl:w-64 xl:shrink-0 xl:border-r xl:pl-6">

    <nav class="flex-none px-0 sm:px-0 lg:px-0 overflow-x-auto">
        <ul role="list" class="flex gap-x-3 gap-y-1 whitespace-nowrap xl:flex-col">
            <li>
                <a href="{{ route('alem.employees') }}" wire:navigate.hover
                   class="{{ (request()->routeIs('alem.employees') || request()->routeIs('employees.profile')) ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm/6 font-semibold">
                    <flux:icon.user-group
                        class="h-6 w-6 {{ (request()->routeIs('alem.employees') || request()->routeIs('employees.profile')) ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>
                    <span class="truncate">{{ __('Employees') }}</span>
                </a>


                {{--            <a href="--}}
                {{--            {{ route('settings.company.show', ['company' => $company->id]) }}" --}}
                {{--               wire:navigate.hover--}}
                {{--               class="{{ request()->routeIs('settings.company.show') ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm/6 font-semibold">--}}
                {{--                <x-pupi.icon.building-office class="h-6 w-6 {{ request()->routeIs('settings.company.show') ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>--}}
                {{--                <span class="truncate">{{ __('Company') }}</span>--}}
                {{--            </a>--}}

            </li>
            <li>
                <a href="{{ route('settings.profile') }}" wire:navigate.hover
                   class="{{ request()->routeIs('settings.profile') ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm/6 font-semibold">
                    <flux:icon.user
                        class="h-6 w-6 {{ request()->routeIs('settings.profile') ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>
                    <span class="truncate">{{ __('Profile') }}</span>
                </a>
            </li>
            @can(\App\Enums\Role\Permission::LIST_ROLE)
                <li>
                    <a href="{{ route('settings.roles') }}" wire:navigate.hover
                       class="{{ request()->routeIs(['settings.roles', 'settings.roles.show']) ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm/6 font-semibold">
                        <flux:icon.shield-check
                            class="h-6 w-6 {{ request()->routeIs(['settings.roles', 'settings.roles.show']) ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>
                        <span class="truncate">{{ __('Roles') }}</span>
                    </a>
                </li>
            @endcan

            <li>
                <a href="{{ route('settings.departments') }}" wire:navigate.hover
                   class="{{ request()->routeIs(['departments.roles', 'settings.departments.show']) ? 'text-indigo-600 bg-gray-50 dark:text-white dark:bg-gray-800' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm/6 font-semibold">
                    <flux:icon.shield-check
                        class="h-6 w-6 {{ request()->routeIs(['departments.roles', 'settings.departments.show']) ? 'dark:text-white dark:bg-gray-800' : 'hover:bg-gray-50 group-hover:text-indigo-600 dark:hover:bg-gray-800 text-gray-400 dark:text-gray-500 dark:group-hover:text-white' }}"/>
                    <span class="truncate">{{ __('Departments') }}</span>
                    <span
                        class="ml-auto w-9 min-w-max whitespace-nowrap rounded-full bg-gray-200 px-2.5 py-0.5 text-center text-xs font-medium leading-5 text-gray-600 outline-1 -outline-offset-1 outline-gray-200"
                        aria-hidden="true">
                        5
                    </span>
                </a>
            </li>

        </ul>
    </nav>
</div>
