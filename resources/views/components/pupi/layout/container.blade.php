<div {{ $attributes->merge(['class' => 'flex flex-col xl:flex-row overflow-hidden']) }}>
    {{-- Sidebar nur anzeigen, wenn $sidebar-Slot gesetzt ist --}}
    @isset($sidebar)
        <div class="w-full xl:w-auto">
            {{ $sidebar }}
        </div>
    @endisset

    {{-- Hauptinhalt --}}
    <div class="flex-1 overflow-y-auto">

        {{-- Header-Slot nur befüllen, wenn du wirklich einen Header brauchst --}}
        @isset($header)
            {{ $header }}
        @endisset

        <div class="space-y-10 divide-y dark:divide-white/5 divide-gray-900/5">
            {{ $slot }}
        </div>
    </div>
</div>
