<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.alem.employee.sidebar/>
        </x-slot:sidebar>


        {{-- Das ist die Form mit der Create und der Table Componente --}}
        <x-pupi.layout.form-index>

{{--            <!--Create Button, open the Create Component-->--}}
            <x-slot:create>
                <livewire:alem.employee.create-employee
                    wire:key="create-employee-component"
                    lazy
                />
            </x-slot:create>


            <livewire:alem.employee.employee-table
                :auth-user-id="$authUserId"
                :current-team-id="$currentTeamId"
                :company-id="$companyId"
                wire:key="employee-table-component"
            />

{{--             <!-- Edit Component -->--}}
            <livewire:alem.employee.edit-employee
                :auth-user-id="$authUserId"
                :current-team-id="$currentTeamId"
                :company-id="$companyId"
                wire:key="edit-employee-component"
                lazy
            />

        </x-pupi.layout.form-index>

    </x-pupi.layout.container>

</x-app-layout>
