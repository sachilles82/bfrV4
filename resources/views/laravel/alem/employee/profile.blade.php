<x-app-layout>

    <x-pupi.layout.container>

        {{-- Sidebar-Slot nur einfügen, wenn du eine Sidebar auf diese Seite hast --}}
        <x-slot:sidebar>
            <x-navigation.settings.sidebar
            />
        </x-slot:sidebar>

        <!-- Header-Slot nur einfügen, wenn du einen Header auf diese Seite hast -->
{{--        <x-slot:header>--}}
{{--            <x-navigation.alem.employee.header--}}
{{--            />--}}
{{--        </x-slot:header>--}}

        {{--Hier werden die livewire Componenten gerendert--}}
        <livewire:setting.theme
        />

    </x-pupi.layout.container>

</x-app-layout>
