
@php
$classes = Flux::classes()
    ->add('shrink-0 size-[1.125rem] rounded-full')
    ->add('text-sm text-zinc-700 dark:text-zinc-800')
    ->add('flex justify-center items-center [ui-option[data-selected]_&>div]:block')
    ->add([
        'border',
        'border-zinc-300 dark:border-white/10',
        '[ui-option[disabled]_&]:border-zinc-200 dark:[ui-option[disabled]_&]:border-white/5',
        '[ui-option[data-selected]_&]:border-transparent data-indeterminate:border-transparent',
        '[ui-option[data-selected]_&]:[ui-option[disabled]_&]:border-transparent data-indeterminate:border-transparent',
    ])
    ->add([
        'bg-white dark:bg-white/10',
        'dark:[ui-option[disabled]_&]:bg-white/5',
        '[ui-option[data-selected]_&]:bg-zinc-800 dark:[ui-option[data-selected]_&]:bg-white',
        '[ui-option[disabled][data-selected]_&]:bg-zinc-500 dark:[ui-option[disabled][data-selected]_&]:bg-white/60',
        'hover:[ui-option[data-selected]_&]:bg-zinc-800 dark:hover:[ui-option[data-selected]_&]:bg-white',
        'focus:[ui-option[data-selected]_&]:bg-zinc-800 dark:focus:[ui-option[data-selected]_&]:bg-white',
    ])
    ;
@endphp

<div {{ $attributes->class($classes) }}>
    <div class="hidden size-2 rounded-full bg-white dark:bg-zinc-800"></div>
</div>
