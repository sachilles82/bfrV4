@props(['href', 'active' => false])

<a wire:navigate.hover href="{{ $href }}"
    {{ $attributes->merge([
        'class' => ($active
             ? 'dark:text-indigo-400 text-indigo-600'
             : 'hover:text-indigo-600 dark:hover:text-indigo-400'
        )
    ]) }}>
    {{ $slot }}
</a>
