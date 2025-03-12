<div {{ $attributes->merge(['class' => "flex h-full min-w-0 flex-1 flex-col lg:order-last overflow-x-hidden"]) }}>
    <div class="flex-1 xl:flex">
        <div class="px-4 py-4 sm:px-6 lg:pl-8 xl:flex-1 xl:pl-6">

            @isset($create)
                {{ $create }}
            @endisset
            {{-- Hier wird die Tabelle gerendert --}}
            {{ $slot }}

        </div>
    </div>
</div>
