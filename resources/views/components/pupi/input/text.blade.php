@props([
    'type' => 'text', // Default ist text, kann aber 'email', 'phone', etc. sein
])

@if($type === 'email')
    <div class="mt-2 grid grid-cols-1">
        <input {{ $attributes->merge([
        'type' => 'email',
        'class' => 'col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-3 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 dark:bg-white/5 dark:text-white dark:outline-white/10 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 dark:focus:outline-indigo-500 sm:pl-9 sm:text-sm/6'
    ]) }} autocomplete="off">
        <svg class="pointer-events-none col-start-1 row-start-1 ml-3 size-5 self-center text-gray-400 sm:size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
            <path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" />
            <path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" />
        </svg>
    </div>
@elseif($type === 'phone')
    <div class="mt-2 grid grid-cols-1">
        <input {{ $attributes->merge([
        'type' => 'tel',
        'class' => 'col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-3 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 dark:bg-white/5 dark:text-white dark:outline-white/10 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 dark:focus:outline-indigo-500 sm:pl-9 sm:text-sm/6'
    ]) }} autocomplete="off">
        <svg class="pointer-events-none col-start-1 row-start-1 ml-3 size-5 self-center text-gray-400 sm:size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" />
        </svg>
    </div>
@elseif($type === 'password')
    <div class="mt-2 grid grid-cols-1">
        <input {{ $attributes->merge([
        'type' => 'password',
        'class' => 'col-start-1 row-start-1 block w-full rounded-md bg-white py-1.5 pr-3 pl-10 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 dark:bg-white/5 dark:text-white dark:outline-white/10 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 dark:focus:outline-indigo-500 sm:pl-9 sm:text-sm/6'
    ]) }} autocomplete="off">
        <svg class="pointer-events-none col-start-1 row-start-1 ml-3 size-5 self-center text-gray-400 sm:size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
        </svg>
    </div>
@else
    <div class="mt-2">
        <input {{ $attributes->merge([
        'type' => $type,
        'class' => 'block w-full rounded-md bg-white/5 px-3 py-1.5 text-base dark:text-white text-gray-900 outline-1 -outline-offset-1 outline-gray-300 dark:outline-white/10 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 dark:focus:outline-indigo-500 sm:text-sm/6'
    ]) }} autocomplete="off">
    </div>
@endif
