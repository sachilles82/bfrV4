<div>
    <x-slot:header>
        <x-navigation.alem.employee.header
            :employee="$employee"
            :activeTab="$activeTab"
        />
    </x-slot:header>

    <!-- Content-Bereich mit ProfileManager -->
    <div class="mt-6">
        {{-- ProfileManager lädt ALLE Daten und verteilt sie an Child Components --}}
        <livewire:alem.employee.profile.profile-manager
            :employee-id="$employeeId"
            :active-tab="$activeTab"
            :key="'profile-manager-'.$employeeId.'-'.$activeTab"
            :auth-user-id="$authUserId"
            :current-team-id="$currentTeamId"
            :company-id="$companyId"
        />
    </div>
</div>
