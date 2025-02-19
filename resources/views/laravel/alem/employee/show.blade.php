<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.alem.employee.sidebar />
        </x-slot:sidebar>

        <livewire:alem.employee.employee-manager
            :user="$user"
            :activeTab="$activeTab"
        />

    </x-pupi.layout.container>

</x-app-layout>
