<x-app-layout>

    <x-pupi.layout.container>

        <x-slot:sidebar>
            <x-navigation.alem.employee.sidebar />
        </x-slot:sidebar>

        <livewire:alem.employee.dynamic-navigation
            :user="$user"
            :activeTab="$activeTab"

            :auth-user-id="$authUserId"
            :current-team-id="$currentTeamId"
            :company-id="$companyId"
        />

    </x-pupi.layout.container>

</x-app-layout>
