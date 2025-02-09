<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.settings.sidebar />
        </x-slot:sidebar>

        <livewire:address.address-manager
            :addressable="$company"
        />



    </x-pupi.layout.container>

</x-app-layout>






{{--        <livewire:address.select--}}
{{--            :addressable="$company"--}}
{{--        />--}}
