<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.alem.employee.sidebar/>
        </x-slot:sidebar>


        {{-- Das ist die Form mit der Create und der Table Componente --}}
        <x-pupi.layout.form-index>

{{--            <!--Create Button, open the Create Component-->--}}
{{--            <x-slot:create>--}}
{{--                <livewire:alem.employee.create-employee--}}
{{--                    lazy--}}
{{--                />--}}
{{--            </x-slot:create>--}}


            <livewire:alem.employee.employee-table
                :departments="$departments"
                :teams="$teams"
                :professions="$professions"
                :stages="$stages"
                 lazy
            />

{{--            <!-- Edit Component -->--}}
{{--            <livewire:alem.employee.edit-employee--}}
{{--                lazy--}}
{{--            />--}}

        </x-pupi.layout.form-index>

    </x-pupi.layout.container>

</x-app-layout>
