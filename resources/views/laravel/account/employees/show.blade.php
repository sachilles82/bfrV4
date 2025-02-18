<x-app-layout>

    <x-pupi.layout.container>

        {{-- Sidebar-Slot nur einfügen, wenn du eine Sidebar auf diese Seite hast --}}
        <x-slot:sidebar>
            <x-navigation.accounts.sidebar />
        </x-slot:sidebar>

        {{-- Header-Slot nur einfügen, wenn du einen Header auf diese Seite hast --}}


        {{--Hier werden die livewire Componenten gerendert--}}
        <livewire:account.employee.employee-update
            :employeeId="$employee->id"
        />

    </x-pupi.layout.container>

</x-app-layout>
