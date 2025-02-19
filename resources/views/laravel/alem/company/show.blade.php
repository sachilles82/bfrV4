<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.settings.sidebar/>
        </x-slot:sidebar>

        <livewire:alem.company.company-update
            :company="$company"
        />

        <livewire:address.address-manager
            :addressable="$company"
        />

    </x-pupi.layout.container>

</x-app-layout>
