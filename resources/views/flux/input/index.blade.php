@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'variant' => 'outline',
    'iconTrailing' => null,
    'iconLeading' => null,
    'expandable' => null,
    'clearable' => null,
    'copyable' => null,
    'viewable' => null,
    'invalid' => null,
    'type' => 'text',
    'mask' => null,
    'size' => null,
    'icon' => null,
    'kbd' => null,
    'as' => null,
])

@php
$invalid ??= ($name && $errors->has($name));

$iconLeading ??= $icon;

$hasLeadingIcon = (bool) ($iconLeading);
$hasTrailingIcon = (bool) ($iconTrailing) || (bool) $kbd || (bool) $clearable || (bool) $copyable || (bool) $viewable || (bool) $expandable;
$hasBothIcons = $hasLeadingIcon && $hasTrailingIcon;
$hasNoIcons = (! $hasLeadingIcon) && (! $hasTrailingIcon);

$classes = Flux::classes()
    ->add('w-full border rounded-md block disabled:shadow-none dark:shadow-none')
    ->add('appearance-none') // Without this, input[type="date"] on mobile doesn't respect w-full...
    ->add(match ($size) {
        default => 'sm:text-sm sm:leading-6 text-sm py-1.5 h-9 leading-6', // This makes the height of the input 40px (same as buttons and such...)
        'sm' => 'text-sm py-1.5 h-8 leading-[1.125rem]',
        'xs' => 'text-xs py-1.5 h-6 leading-[1.125rem]',
    })
    ->add(match (true) { // Spacing...
        $hasNoIcons => 'pl-3 pr-3',
        $hasBothIcons =>'pl-10 pr-10',
        $hasLeadingIcon => 'pl-10 pr-3',
        $hasTrailingIcon => 'pl-3 pr-10',
    })
    ->add(match ($variant) { // Background...
        'outline' => 'bg-white dark:bg-white/5 dark:disabled:bg-white/[7%]',
        'filled'  => 'bg-zinc-800/5 dark:bg-white/5 dark:disabled:bg-white/[7%]',
    })
    ->add(match ($variant) { // Text color
        'outline' => 'text-zinc-900 disabled:text-zinc-500 placeholder-zinc-400 disabled:placeholder-zinc-400 dark:text-zinc-300 dark:disabled:text-zinc-400 dark:placeholder-zinc-400 dark:disabled:placeholder-zinc-500',
        'filled'  => 'text-zinc-900 placeholder-zinc-500 disabled:placeholder-zinc-400 dark:text-zinc-300 dark:placeholder-white/60 dark:disabled:placeholder-white/40',
    })
    ->add(match ($variant) { // Border...
        'outline' => $invalid ? 'border-red-400' : 'block w-full border-0 dark:text-white text-zinc-900 shadow-sm ring-1 ring-inset ring-zinc-300 dark:focus-within:ring-inset dark:focus-within:ring-indigo-500 dark:bg-white/5 dark:ring-white/10 placeholder:text-zinc-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600',
        'filled'  => $invalid ? 'border-red-400' : 'border-0',
    })
    ;
@endphp

<?php if ($as !== 'button'): ?>
    <flux:with-field :$attributes :$name>
        <div {{ $attributes->only('class')->class('w-full relative block group/input') }} data-flux-input>
            <?php if (is_string($iconLeading)): ?>
                <div class="z-10 absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pl-3 left-0">
                    <flux:icon :icon="$iconLeading" variant="mini" />
                </div>
            <?php elseif ($iconLeading): ?>
                <div {{ $iconLeading->attributes->class('z-10 absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pl-3 left-0') }}>
                    {{ $iconLeading }}
                </div>
            <?php endif; ?>

            <input
                type="{{ $type }}"
                {{ $attributes->except('class')->class($classes) }}
                @isset ($name) name="{{ $name }}" @endisset
                @if ($mask) x-mask="{{ $mask }}" @endif
                @if ($invalid) aria-invalid="true" @endif
                data-flux-control
            >

            <?php if ($kbd): ?>
                <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pr-4 right-0">
                    {{ $kbd }}
                </div>
            <?php endif; ?>

            <?php if (is_string($iconTrailing)): ?>
                <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pr-3 right-0">
                    <flux:icon :icon="$iconTrailing" variant="mini" />
                </div>
            <?php elseif ($iconTrailing): ?>
                <div {{ $iconTrailing->attributes->class('absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pr-2 right-0') }}>
                    {{ $iconTrailing }}
                </div>
            <?php endif; ?>

            <?php if ($expandable): ?>
                <div class="absolute top-0 bottom-0 flex items-center justify-center pr-2 right-0">
                    <flux:input.expandable />
                </div>
            <?php endif; ?>

            <?php if ($clearable): ?>
                <div class="absolute top-0 bottom-0 flex items-center justify-center pr-2 right-0">
                    <flux:input.clearable />
                </div>
            <?php endif; ?>

            <?php if ($copyable): ?>
                <div class="absolute top-0 bottom-0 flex items-center justify-center pr-2 right-0">
                    <flux:input.copyable />
                </div>
            <?php endif; ?>

            <?php if ($viewable): ?>
                <div class="absolute top-0 bottom-0 flex items-center justify-center pr-2 right-0">
                    <flux:input.viewable />
                </div>
            <?php endif; ?>
        </div>
    </flux:with-field>
<?php else: ?>
    <button {{ $attributes->merge(['type' => 'button'])->class([$classes, 'w-full relative flex']) }}>
        <?php if (is_string($iconLeading)): ?>
            <div class="z-10 absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pl-3 left-0">
                <flux:icon :icon="$iconLeading" variant="mini" />
            </div>
        <?php elseif ($iconLeading): ?>
            <div {{ $iconLeading->attributes->class('z-10 absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pl-3 left-0') }}>
                {{ $iconLeading }}
            </div>
        <?php endif; ?>

        <?php if ($attributes->has('placeholder')): ?>
            <div class="block self-center text-left flex-1 font-medium text-zinc-400 dark:text-white/40">
                {{ $attributes->get('placeholder') }}
            </div>
        <?php else: ?>
            <div class="text-left self-center flex-1 font-medium text-zinc-900 dark:text-white">
                {{ $slot }}
            </div>
        <?php endif; ?>

        <?php if ($kbd): ?>
            <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pr-4 right-0">
                {{ $kbd }}
            </div>
        <?php endif; ?>

        <?php if (is_string($iconTrailing)): ?>
            <div class="absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pr-3 right-0">
                <flux:icon :icon="$iconTrailing" variant="mini" />
            </div>
        <?php elseif  ($iconTrailing): ?>
            <div {{ $iconTrailing->attributes->class('absolute top-0 bottom-0 flex items-center justify-center text-xs text-zinc-400 pr-2 right-0') }}>
                {{ $iconTrailing }}
            </div>
        <?php endif; ?>
    </button>
<?php endif; ?>


