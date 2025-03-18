@aware([ 'placeholder' ])

@props([
    'placeholder' => null,
    'invalid' => false,
    'size' => null,
])

<flux:input :$invalid :$size :$placeholder :$attributes>
    <x-slot name="iconTrailing">
        <flux:button size="sm" square variant="subtle" tabindex="-1" class="-mr-1 in-[[disabled]]:pointer-events-none">
            <flux:icon.chevron-up-down variant="mini" class="text-zinc-400 [[data-flux-input]:hover_&]:text-zinc-900 in-[[disabled]]:text-zinc-200! dark:text-white/60 dark:[[data-flux-input]:hover_&]:text-white dark:in-[[disabled]]:text-white/40!" />
        </flux:button>
    </x-slot>
</flux:input>
