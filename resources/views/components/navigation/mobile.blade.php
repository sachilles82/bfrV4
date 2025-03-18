<!-- Sidebar Slide-Over -->
<div x-show="menu" @click.away="menu = false" @keydown.window.escape="menu = false" x-cloak
     class="fixed inset-0 z-50 flex overflow-hidden">

    <!-- Background Overlay -->
    <div x-show="menu" @click="menu = false" x-transition.opacity.duration.350ms
         class="fixed inset-0 bg-gray-600 bg-opacity-75 dark:bg-gray-900/80"></div>
    <!-- Sidebar Content -->
    <div class="relative z-40 w-full max-w-xs bg-indigo-700 dark:bg-gray-900 dark:ring-1 dark:ring-white/10"
         x-show="menu"
         x-transition:enter="transform transition ease-in-out duration-150 sm:duration-350"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-150 sm:duration-350"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">

        <!-- Sidebar Content -->
        <div class="flex flex-col h-full py-5">
            <div class="flex items-center justify-between px-4">
                <img class="h-8 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=white"
                     alt="Your Company">
                <button @click="menu = false" class="text-navigation-txt2 focus:outline-hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="sr-only">Close sidebar</span>
                </button>
            </div>

            <!-- Sidebar Links -->
            <div class="mt-5 h-0 flex-1 overflow-y-auto px-4">
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}" @click="menu = false"
                       wire:navigate.hover
                       class="{{ request()->routeIs('dashboard') ? 'text-white bg-indigo-800 dark:text-white dark:bg-gray-800' : 'dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 text-indigo-100 hover:bg-indigo-800 hover:text-white' }} group flex items-center rounded-md px-3 py-2 text-sm font-medium">
                        <svg
                            class="{{ request()->routeIs('dashboard') ? 'dark:text-white dark:bg-gray-800' : 'text-indigo-300 dark:text-gray-400 group-hover:text-white' }} mr-3 h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                        </svg>
                        <span>{{ __('Dashboard') }}</span>
                    </a>

                    <a href="{{ route('settings.roles') }}" @click="menu = false"
                       wire:navigate.hover
                       class="{{ request()->routeIs('settings.roles') ? 'text-white bg-indigo-800 dark:text-white dark:bg-gray-800' : 'dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 text-indigo-100 hover:bg-indigo-800 hover:text-white' }} group flex items-center rounded-md px-3 py-2 text-sm font-medium">
                        <svg
                            class="{{ request()->routeIs('settings.roles') ? 'dark:text-white dark:bg-gray-800' : 'text-indigo-300 dark:text-gray-400 group-hover:text-white' }} mr-3 h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205L12 12m6.894 5.785l-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864l-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495"/>
                        </svg>
                        <span>{{ __('Settings') }}</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Sidebar Close Click Area -->
    <div x-show="menu" class="w-14 shrink-0" @click="menu = false"></div>
</div>
