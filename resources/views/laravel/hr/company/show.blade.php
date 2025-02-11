<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.settings.sidebar />
        </x-slot:sidebar>

        <livewire:h-r.company.company-update
            :company="$company"
        />

        <livewire:address.address-manager
            :addressable="$company"
            lazy
        />

    </x-pupi.layout.container>

</x-app-layout>
