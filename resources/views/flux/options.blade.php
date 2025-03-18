@aware([ 'searchable' ])

@props([
    'searchable' => null,
    'indicator' => null,
    'search' => null,
    'empty' => null,
    'add' => null, // diesen habe ich hinzugefügt
])

@php
    $classes = Flux::classes()
        ->add('[:where(&)]:min-w-48 [:where(&)]:max-h-[20rem] p-[.3125rem]')
        ->add('rounded-lg shadow-xs')
        ->add('border border-zinc-300 dark:border-white/10')
            ->add('bg-white dark:bg-zinc-800')
        ;


    // Searchable can also be a slot...
    if (is_object($searchable)) $search = $searchable;
@endphp

<?php if (! $searchable): ?>
<ui-options popover="manual" {{ $attributes->class($classes) }} data-flux-options>
    {{ $slot }}
</ui-options>
<?php else: ?>
<div popover="manual" class="rounded-lg shadow-xs border border-zinc-300 dark:border-white/10 bg-white dark:bg-zinc-800 p-[.3125rem]" data-flux-options>
        <?php if ($search): ?> {{ $search }} <?php else: ?>
    <flux:select.search />
    <?php endif; ?>

    <ui-options class="max-h-[20rem] overflow-y-auto -mr-[.3125rem] -mt-[.3125rem] pt-[.3125rem] pr-[.3125rem] -mb-[.3125rem] pb-[.3125rem]">
        {{ $slot }}

            <?php if ($empty): ?>
        <ui-empty class="data-hidden:hidden">{{ $empty }}</ui-empty>

        <?php else: ?>
        <flux:select.empty>{!! __('No results found') !!}</flux:select.empty>

        <?php endif; ?>
    </ui-options>

    @isset($add)
        {{ $add }}
    @endisset
</div>
<?php endif; ?>
