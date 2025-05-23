@props([
    'variant' => 'default',
    'indent' => false,
    'suffix' => null,
    'label' => null,
    'icon' => null,
    'kbd' => null,
])

@php
if ($kbd) $suffix = $kbd;

$classes = Flux::classes()
    ->add('group/menu-radio flex items-center px-2 py-1.5 w-full focus:outline-hidden')
    ->add('rounded-md')
    ->add('text-left text-sm font-medium')
    ->add('in-[[disabled]]:opacity-50 [&[disabled]]:opacity-50')
    ->add([
        'text-zinc-800 data-active:bg-zinc-50 dark:text-white dark:data-active:bg-zinc-600',
        '**:data-flux-menu-item-icon:text-zinc-400 dark:**:data-flux-menu-item-icon:text-white/60 [&[data-active]_[data-flux-menu-item-icon]]:text-current',
    ])
    ;
@endphp

<ui-menu-radio {{ $attributes->class($classes) }} data-flux-menu-item-has-icon data-flux-menu-radio>
    <div class="w-7">
        <div class="hidden group-data-checked/menu-radio:block">
            <flux:icon variant="mini" icon="check" data-flux-menu-item-icon />
        </div>
    </div>

    {{ $label ?? $slot }}

    <?php if ($suffix): ?>
        <div class="ml-auto opacity-50 text-xs">
            {{ $suffix }}
        </div>
    <?php endif; ?>
</ui-menu-radio>
