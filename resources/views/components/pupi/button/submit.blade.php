<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500border border-transparent rounded-md text-sm font-semibold text-white shadow-xs hover:bg-indigo-600 dark:hover:bg-indigo-400 active:bg-skin-fill focus:outline-hidden focus:ring-2 dark:focus-visible:outline-indigo-600 focus:ring-indigo-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
