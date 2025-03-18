<button {{ $attributes->merge(['type' => 'button', 'class' => 'rounded-md bg-white ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-white/10 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-xs dark:hover:bg-white/20 dark:ring-transparent transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
