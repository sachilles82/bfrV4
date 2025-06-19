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
                    :auth-user-id="$authUserId"
                    :current-team-id="$currentTeamId"
                    :company-id="$companyId"
                    wire:key="create-employee-component"
                />
            </x-slot:create>

{{--            <flux:modal.trigger name="create-employee">--}}
{{--                <div--}}
{{--                    @click="$dispatch('create-employee-modal')"--}}
{{--                    class="ml-auto flex items-center gap-x-1 rounded-md bg-indigo-600 dark:bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 dark:hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:focus-visible:outline-indigo-500 cursor-pointer">--}}
{{--                    <x-pupi.icon.create class="-ml-1.5 size-5"/>--}}
{{--                    {{ __('Create') }}--}}
{{--                </div>--}}
{{--            </flux:modal.trigger>--}}


            <livewire:alem.employee.employee-table
                :auth-user-id="$authUserId"
                :current-team-id="$currentTeamId"
                :company-id="$companyId"
                wire:key="employee-table-component"
            />

            <!-- Edit Component -->
            <livewire:alem.employee.edit-employee
                :auth-user-id="$authUserId"
                :current-team-id="$currentTeamId"
                :company-id="$companyId"
                wire:key="edit-employee-component"
            />

            <livewire:alem.quick-crud.profession.profession-form
                :auth-user-id="$authUserId"
                :current-team-id="$currentTeamId"
                :company-id="$companyId"
                wire:key="profession-form-component"
            />

            <livewire:alem.quick-crud.stage.stage-form
                :auth-user-id="$authUserId"
                :current-team-id="$currentTeamId"
                :company-id="$companyId"
                wire:key="stage-form-component"
            />

        </x-pupi.layout.form-index>

    </x-pupi.layout.container>

</x-app-layout>
