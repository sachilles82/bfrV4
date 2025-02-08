<x-app-layout>

    <x-pupi.layout.container>

        {{-- Sidebar-Slot nur einfügen, wenn du eine Sidebar auf diese Seite hast --}}
        <x-slot:sidebar>
            <x-navigation.settings.sidebar/>
        </x-slot:sidebar>


        {{-- Das ist die Form mit der Create und der Table Componente --}}
        <x-pupi.layout.form-index>

            <x-slot:title>
                {{ __('Roles') }}
            </x-slot:title>

            <x-slot:description>
                {{__('A list of all roles in this table') }}
            </x-slot:description>

            <!--Create Button, open the Create Component-->
            @can(App\Enums\Role\Permission::CREATE_ROLE)
                <x-slot:create>
                    <livewire:spatie.role.create-role/>
                </x-slot:create>
            @endcan

            <!-- Table -->
            <livewire:spatie.role.role-table/>

        </x-pupi.layout.form-index>

    </x-pupi.layout.container>

</x-app-layout>
