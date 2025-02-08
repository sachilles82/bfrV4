<div class="sticky top-0 z-10 border-b dark:border-white/5 border-gray-200 dark:bg-gray-900 bg-gray-50 w-full">
{{--    <header class="border-b border-white/5">--}}
{{--        <!-- Secondary navigation -->--}}
{{--        <nav class="flex overflow-x-auto py-2">--}}
{{--            <div class="flex min-w-full flex-none gap-x-6 px-2 text-sm font-semibold leading-6 dark:text-gray-400 text-gray-500 sm:px-6 lg:px-8">--}}

{{--                <a href="{{ route('settings.profile') }}" wire:navigate.hover--}}
{{--                   class="{{ request()->routeIs('settings.profile') ? 'text-indigo-600 bg-gray-100 dark:text-white dark:bg-gray-800' : 'text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-medium px-3">--}}
{{--                    <span class="truncate">{{ __('Profile') }}</span>--}}
{{--                </a>--}}
{{--                <a href="{{ route('settings.compliance') }}" wire:navigate.hover--}}
{{--                   class="{{ request()->routeIs('settings.compliance') ? 'text-indigo-600 bg-gray-100 dark:text-white dark:bg-gray-800' : 'text-gray-500 hover:text-indigo-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white' }} group flex gap-x-3 rounded-md py-2 pl-2 pr-3 text-sm leading-6 font-medium px-3">--}}
{{--                    <span class="truncate">{{ __('Compliance Holiday') }}</span>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </nav>--}}
{{--    </header>--}}

    <header class="border-b border-white/5">
        <!-- Secondary navigation -->
        <nav class="flex overflow-x-auto py-4">
            <ul role="list" class="flex min-w-full flex-none gap-x-6 px-4 text-sm font-semibold leading-6 dark:text-gray-400 text-gray-500 sm:px-6 lg:px-8">
                <li>
                    <a href="#" class="dark:text-indigo-400 text-indigo-600">{{ __('Authorization')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Report')}}</a>

                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Holiday')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Project')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Events')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('CRM')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Blog')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Events')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Expenses')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Arbeitszuweisung')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Dateien')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Spesen')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Employees')}}</a>
                </li>
                <li>
                    <a href="#" class="hover:text-indigo-600 dark:hover:text-indigo-400">{{ __('Tickets')}}</a>
                </li>
            </ul>
        </nav>
    </header>
</div>
