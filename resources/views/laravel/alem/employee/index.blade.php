<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.alem.employee.sidebar />
        </x-slot:sidebar>


        {{-- Das ist die Form mit der Create und der Table Componente --}}
        <x-pupi.layout.form-index>

            <x-slot:title>
                {{ __('Employees') }}
            </x-slot:title>

            <x-slot:description>
                {{__('A list of all departments in this table') }}
            </x-slot:description>

            <!--Create Button, open the Create Component-->
            <x-slot:create>
                <livewire:alem.employee.create-employee
                />
            </x-slot:create>

            <!-- Table -->
            <livewire:alem.employee.employee-table
            />

        </x-pupi.layout.form-index>

    </x-pupi.layout.container>

</x-app-layout>
