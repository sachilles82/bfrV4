@props(['role'])
<div class="sticky top-0 z-10 border-b dark:border-white/5 border-gray-200 dark:bg-gray-900 bg-gray-50">
    <nav class="flex overflow-x-auto py-4">
        <ul role="list"
            class="flex min-w-full flex-none gap-x-6 px-4 text-sm/6 font-semibold dark:text-gray-400 text-gray-500 sm:px-6 lg:px-8">
            <li>
                <a wire:navigate.hover
                   href="{{ route('settings.roles.show', ['roleId' => $role->id, 'app' => 'baseApp']) }}"
                   class="{{ request()->route('app') === 'baseApp' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }} hover:text-indigo-600 dark:hover:text-indigo-400">
                    <span class="truncate">{{ __('BaseApp') }}</span>
                </a>
            </li>
            <li>
                <a wire:navigate.hover
                   href="{{ route('settings.roles.show', ['roleId' => $role->id, 'app' => 'crmApp']) }}"
                   class="{{ request()->route('app') === 'crmApp' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }} hover:text-indigo-600 dark:hover:text-indigo-400">
                    <span class="truncate">{{ __('CrmApp') }}</span>
                </a>
            </li>
            <li>
                <a wire:navigate.hover
                   href="{{ route('settings.roles.show', ['roleId' => $role->id, 'app' => 'projectApp']) }}"
                   class="{{ request()->route('app') === 'projectApp' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }} hover:text-indigo-600 dark:hover:text-indigo-400">
                    <span class="truncate">{{ __('ProjectApp') }}</span>
                </a>

            <li>
                <a wire:navigate.hover
                   href="{{ route('settings.roles.show', ['roleId' => $role->id, 'app' => 'holidayApp']) }}"
                   class="{{ request()->route('app') === 'holidayApp' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }} hover:text-indigo-600 dark:hover:text-indigo-400">
                    <span class="truncate">{{ __('HolidayApp') }}</span>
                </a>
            </li>
            <li>
                <a wire:navigate.hover
                   href="{{ route('settings.roles.show', ['roleId' => $role->id, 'app' => 'settingApp']) }}"
                   class="{{ request()->route('app') === 'settingApp' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }} hover:text-indigo-600 dark:hover:text-indigo-400">
                    <span class="truncate">{{ __('settingApp') }}</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
