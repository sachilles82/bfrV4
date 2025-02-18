<div class="hidden w-30 overflow-y-auto bg-indigo-700 dark:bg-gray-900 md:block border-r border-white/5">
    <div class="flex w-28 flex-col items-center py-6">
        <div class="flex flex-shrink-0 items-center">
            <img class="h-8 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=white" alt="Your Company">
        </div>
        <div class="mt-6 w-full flex-1 space-y-1 px-2">

            <a wire:navigate href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'text-white bg-indigo-800 dark:text-white dark:bg-gray-800' : 'dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 text-indigo-100 hover:bg-indigo-800 hover:text-white' }} group flex w-full flex-col items-center rounded-md p-3 text-xs font-medium">
                <flux:icon.home
                    class="{{ request()->routeIs('dashboard') ? 'dark:text-white dark:bg-gray-800' : 'text-indigo-300 dark:text-gray-400 group-hover:text-white' }} h-6 w-6"/>
                <span class="mt-2">{{ __('Dashboard') }}</span>
            </a>

            <a wire:navigate href="{{ route('hr.employees') }}"
               class="{{ request()->routeIs([
                                            'hr.employees',
                                            'employee.profile.show'
                                            ])
                                                ? 'text-white bg-indigo-800 dark:text-white dark:bg-gray-800' : 'dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 text-indigo-100 hover:bg-indigo-800 hover:text-white' }} group flex w-full flex-col items-center rounded-md p-3 text-xs font-medium">
                <flux:icon.user-group
                    class="{{ request()->routeIs([
                                            'hr.employees',
                                            'settings.departments.show'
                                            ]) ? 'dark:text-white dark:bg-gray-800' : 'text-indigo-300 dark:text-gray-400 group-hover:text-white' }} h-6 w-6"/>
                <span class="mt-2">{{ __('Account`s')}}</span>
            </a>

            <a wire:navigate href="{{ route('settings.profile') }}"
               class="{{ request()->routeIs([
                                            'settings.profile',
                                            'settings.company',
                                            'settings.roles',
                                            'settings.roles.show',
                                            'settings.departments',
                                            'settings.departments.show'
                                            ])
                                                ? 'text-white bg-indigo-800 dark:text-white dark:bg-gray-800' : 'dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 text-indigo-100 hover:bg-indigo-800 hover:text-white' }} group flex w-full flex-col items-center rounded-md p-3 text-xs font-medium">
                <flux:icon.cog-6-tooth
                    class="{{ request()->routeIs([
                                            'settings.profile',
                                            'settings.company',
                                            'settings.roles',
                                            'settings.departments',
                                            'settings.departments.show'
                                            ]) ? 'dark:text-white dark:bg-gray-800' : 'text-indigo-300 dark:text-gray-400 group-hover:text-white' }} h-6 w-6"/>
                <span class="mt-2">{{ __('Settings')}}</span>
            </a>

        </div>
    </div>
</div>
