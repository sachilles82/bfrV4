@props([
    'id' => null,
    'extraEvents' => [],
    'extraClasses' => '',
])

<tr
    wire:key="selectable-row-{{ $id }}"
    x-data="{
        checked: $wire.get('selectedIds').map(String).includes(String('{{ $id }}'))
    }"
    x-init="
        $watch('$wire.selectedIds', (newSelectedIds) => {
            checked = newSelectedIds.map(String).includes(String('{{ $id }}'));
        });
    "
    x-on:check-all.window="checked = $event.detail"
    x-on:update-table.window="checked = $wire.get('selectedIds').map(String).includes(String('{{ $id }}'))"
    @foreach($extraEvents as $eventName)
        x-on:{{ $eventName }}.window="checked = false"
    @endforeach
    x-bind:class="{
        'bg-gray-100 dark:bg-gray-800/50': checked,
        'hover:bg-gray-100 dark:hover:bg-gray-800/50': !checked
    }"
    {{ $attributes->merge(['class' => $extraClasses]) }}
>
    {{ $slot }}
</tr>
