<x-app-layout>

    <x-pupi.layout.container>

        {{-- Sidebar-Slot nur einfügen, wenn du eine Sidebar auf diese Seite hast --}}
        <x-slot:sidebar>
            <x-navigation.settings.sidebar />
        </x-slot:sidebar>

        {{-- ausklammern wenn nicht gebraucht --}}
        {{--        <x-slot:header>--}}
        {{--            <x-navigation.employee.header />--}}
        {{--        </x-slot:header>--}}


        {{-- Das ist die Form mit der Create und der Table Componente --}}
        <x-pupi.layout.form-index>

            <x-slot:title>
                {{ __('Departments') }}
            </x-slot:title>

            <x-slot:description>
                {{__('A list of all departments in this table') }}
            </x-slot:description>
            <!--Create Button, open the Create Component-->
            <x-slot:create>
                <livewire:alem.department.create-department
                />
            </x-slot:create>

            <!-- Table -->
            <livewire:alem.department.department-table
            />

            <!-- Edit Component -->
            <livewire:alem.department.edit-department
                lazy
            />

        </x-pupi.layout.form-index>

    </x-pupi.layout.container>

</x-app-layout>
