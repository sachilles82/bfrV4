<button
    aria-label="context menu"
    aria-haspopup="true"
    :aria-expanded="isOpen || openedWithKeyboard"
    @click="isOpen = ! isOpen"
    @keydown.space.prevent="openedWithKeyboard = true"
    @keydown.enter.prevent="openedWithKeyboard = true"
    @keydown.down.prevent="openedWithKeyboard = true"
    class="py-1.5 px-1.5 dropdown-toggle inline-flex justify-center items-center gap-2 rounded-full text-gray-700 align-middle disabled:opacity-50 disabled:pointer-events-none focus:outline-none hover:bg-gray-200 dark:hover:bg-gray-800 focus:ring-2 focus:ring-offset-white focus:ring-indigo-600 transition-all text-sm dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800"
    :class="isOpen || openedWithKeyboard ? 'text-gray-700 dark:text-white' : 'text-gray-700 dark:text-gray-400'"
    {{ $attributes->merge(['type' => 'button']) }}
>
    {{ $slot }}
</button>
