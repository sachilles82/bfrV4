@php
$attributes = $attributes->merge([
    'variant' => 'subtle',
    'class' => '-mr-1',
    'square' => true,
    'size' => 'sm',
]);
@endphp

<flux:button
    :$attributes
    x-on:click="$el.closest('[data-flux-input]').querySelector('input').value = ''"
>
    <flux:icon.chevron-down variant="micro" />
</flux:button>
