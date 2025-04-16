<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.alem.employee.sidebar/>
        </x-slot:sidebar>


        {{-- Das ist die Form mit der Create und der Table Componente --}}
        <x-pupi.layout.form-index>

            <!--Create Button, open the Create Component-->
            <x-slot:create>
                <livewire:alem.employee.create-employee
                    lazy
                />
            </x-slot:create>

{{--            <flux:modal.trigger name="create-employee">--}}
{{--                <div class="ml-auto flex items-center gap-x-1 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 cursor-pointer">--}}
{{--                    <x-pupi.icon.create class="-ml-1.5 size-5"/>--}}
{{--                    {{ __('Create') }}--}}
{{--                </div>--}}
{{--            </flux:modal.trigger>--}}

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
