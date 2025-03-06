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

{{--            <!--Create Button, open the Create Component-->--}}
{{--            <x-slot:create>--}}
{{--                <livewire:alem.employee.create-employee--}}
{{--                />--}}
{{--            </x-slot:create>--}}

{{--            <flux:modal.trigger name="create-profession">--}}
{{--                <flux:separator class="mt-2 mb-1"/>--}}
{{--                <flux:button--}}
{{--                    icon="plus"--}}
{{--                    class="w-full rounded-b-lg rounded-t-none"--}}
{{--                    variant="filled">--}}
{{--                    {{ __('Create Profession') }}--}}
{{--                </flux:button>--}}
{{--            </flux:modal.trigger>--}}

{{--            <livewire:alem.employee.setting.profession.profession-form--}}
{{--            />--}}

{{--            <flux:modal.trigger name="create-stage">--}}
{{--                <flux:separator class="mt-2 mb-1"/>--}}
{{--                <flux:button--}}
{{--                    icon="plus"--}}
{{--                    class="w-full rounded-b-lg rounded-t-none"--}}
{{--                    variant="filled">--}}
{{--                    {{ __('Stage Manager') }}--}}
{{--                </flux:button>--}}
{{--            </flux:modal.trigger>--}}

{{--            <livewire:alem.employee.setting.profession.stage-form--}}
{{--            />--}}

            <!-- Table -->
            <livewire:alem.employee.employee-table
            />

        </x-pupi.layout.form-index>

    </x-pupi.layout.container>

</x-app-layout>
