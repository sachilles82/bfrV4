@props([
    'id' => null,
    'extraEvents' => [],
    'extraClasses' => '',
])

<tr
    wire:key="{{ $id }}"
    x-on:check-all.window="checked = $event.detail"
    x-on:update-table.window="checked = false"
    @foreach($extraEvents as $event)
        x-on:{{ $event }}.window="checked = false"
    @endforeach
    x-data="{ checked: false }"
    x-init="checked = $wire.selectedIds.includes('{{ $id }}')"
    x-bind:class="{
        'bg-gray-100 dark:bg-gray-800/50': checked,
        'hover:bg-gray-100 dark:hover:bg-gray-800/50': !checked
    }"
    {{ $attributes->merge(['class' => $extraClasses]) }}
>
    {{ $slot }}
</tr>
