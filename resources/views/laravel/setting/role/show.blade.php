<x-app-layout>

    <x-pupi.layout.container>

        {{-- Sidebar-Slot nur einfügen, wenn du eine Sidebar auf diese Seite hast --}}
        <x-slot:sidebar>
            <x-navigation.settings.sidebar/>
        </x-slot:sidebar>

        {{-- Header-Slot nur einfügen, wenn du einen Header auf diese Seite hast --}}
        <x-slot:header>
            <x-navigation.settings.role-header
                :role="$role"
            />
        </x-slot:header>

        <livewire:spatie.role.permission.update
            :roleId="$role->id"
            :app="$app"
            lazy
        />


    </x-pupi.layout.container>

</x-app-layout>
