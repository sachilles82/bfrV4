@aware([ 'placeholder' ])

@props([
    'placeholder' => null,
    'clearable' => null,
    'invalid' => false,
    'suffix' => null,
    'size' => null,
    'max' => null,
])

@php
    // Clearable is not supported on xs size...
    if ($size === 'xs') $clearable = null;

    $classes = Flux::classes()
        ->add('group/select-button cursor-default py-1.5')
        ->add('overflow-hidden') // Overflow hidden is here to prevent the button from growing when selected text is too long.
        ->add('flex items-center')
        ->add('rounded-md shadow-xs border')
        ->add('bg-white dark:bg-white/5')
        // Make the placeholder match the text color of standard input placeholders...
        ->add('disabled:shadow-none')
        ->add(match ($size) {
            default => 'h-9 text-sm rounded-md mt-1 px-3 block w-full',
            'sm' => 'h-8 text-sm rounded-md pl-3 pr-2 block w-full',
            'xs' => 'h-6 text-xs rounded-md pl-3 pr-2 block w-full',
            'br-none' =>  'h-9 text-sm rounded-l-md rounded-r-none mt-1 px-3 block min-w-48', //habe ich hinzugefügt damit ich die select boxen in der address.blade.php anpassen kann
            'bl-none' =>  'h-9 text-sm rounded-r-md rounded-l-none mt-1 px-3 block w-full'
        })
        ->add($invalid
            ? 'border border-red-500'
            : 'border border-zinc-300 border-b-zinc-300 dark:border-white/10'
        )
        ;
@endphp

<button type="button" {{ $attributes->class($classes) }} @if ($invalid) data-invalid @endif data-flux-group-target data-flux-select-button>
    <?php if ($slot->isNotEmpty()): ?>
    {{ $slot }}
    <?php else: ?>
    <flux:select.selected :$placeholder :$max :$suffix />
    <?php endif; ?>

    <?php if ($clearable): ?>
    <flux:button as="div"
                 class="cursor-pointer ml-2 -mr-2 [[data-flux-select-button]:has([data-flux-select-placeholder])_&]:hidden"
                 variant="subtle"
                 :size="$size === 'sm' ? 'xs' : 'sm'"
                 square
                 tabindex="-1"
                 aria-label="Clear selected"
                 x-on:click.prevent.stop="let select = $el.closest('ui-select'); select.value = select.hasAttribute('multiple') ? [] : null; select.dispatchEvent(new Event('change', { bubbles: false })); select.dispatchEvent(new Event('input', { bubbles: false }))"
    >
        <flux:icon.x-mark variant="micro"/>
    </flux:button>
    <?php endif; ?>

    <flux:icon.chevron-down variant="mini" class="ml-2 -mr-1 text-zinc-300 [[data-flux-select-button]:hover_&]:text-zinc-800 in-[[disabled]]:text-zinc-200! dark:text-white/60 dark:[[data-flux-select-button]:hover_&]:text-white dark:in-[[disabled]]:text-white/40!" />
</button>
