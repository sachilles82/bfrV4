<button {{ $attributes->merge(['class' => 'rounded-md bg-white px-4 py-2 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-white/10 text-sm font-semibold text-gray-900 dark:text-white shadow-sm dark:hover:bg-white/20 dark:ring-transparent transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
