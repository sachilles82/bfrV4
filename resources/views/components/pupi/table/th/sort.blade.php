@props([
    'column' => null,
    'sortCol' => null,
    'sortAsc' => null,
])

<th
    {{ $attributes->merge(['class' => 'py-3.5 pr-3 text-left text-sm font-semibold group text-gray-900 dark:text-white'])->only('class') }}
>

    <button wire:click="sortBy('{{ $column }}')" {{ $attributes->merge(['class' => 'flex items-center gap-2 group']) }}>
        {{ $slot }}
        @if ($sortCol === $column)
            <div class="text-gray-400 opacity-0 group-hover:opacity-100">
                @if ($sortAsc)
                    <x-pupi.icon.arrow-long-up />
                @else
                    <x-pupi.icon.arrow-long-down />
                @endif
            </div>
        @else
            <div class="text-gray-400 opacity-0 group-hover:opacity-100">
                <x-pupi.icon.arrows-up-down />
            </div>
        @endif
    </button>
</th>
