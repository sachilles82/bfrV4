<x-app-layout>
    <x-pupi.layout.container>
        <x-slot:sidebar>
            <x-navigation.alem.employee.sidebar />
        </x-slot:sidebar>

        {{-- Header Navigation --}}
        <x-slot:header>
            <x-navigation.alem.employee.header
                :user-id="$userId"
                :activeTab="$activeTab"
            />
        </x-slot:header>

        {{-- Content --}}
        <div class="mt-6 space-y-10 divide-y dark:divide-white/5 divide-gray-900/5">
            @if($activeTab === 'employee-update')
{{--                 Account Details - Sofort geladen --}}
                <livewire:alem.employee.profile.account.details
                    :user-id="$userId"
                    :auth-user-id="$authUserId"
                    :current-team-id="$currentTeamId"
                    :company-id="$companyId"
                    lazy
                />


                <livewire:alem.employee.profile.employment-data.employment-data
                    :user-id="$userId"
                    :auth-user-id="$authUserId"
                    :current-team-id="$currentTeamId"
                    :company-id="$companyId"
                    lazy
                />

                <livewire:alem.employee.profile.personal.personal-data
                    :user-id="$userId"
                    :auth-user-id="$authUserId"
                    :current-team-id="$currentTeamId"
                    :company-id="$companyId"
                    lazy
                />

            @elseif($activeTab === 'report')
                {{-- Andere Tabs... --}}
            @endif
        </div>
    </x-pupi.layout.container>
</x-app-layout>
