<div class="space-y-10 divide-y dark:divide-white/5 divide-gray-900/5">
    @if($activeTab === 'employee-update')

        {{-- Details Component - nur IDs übergeben! --}}
        <livewire:alem.employee.profile.account.details
            :employee-id="$employeeId"
            :key="'details-'.$employeeId"
            :auth-user-id="$authUserId"
            :current-team-id="$currentTeamId"
            :company-id="$companyId"
        />

        {{-- Employment Data Component --}}
        <livewire:alem.employee.profile.employment-data.employment-data
            :employee-id="$employeeId"
            :key="'employment-'.$employeeId"
            :auth-user-id="$authUserId"
            :current-team-id="$currentTeamId"
            :company-id="$companyId"
        />

        {{-- Personal Data Component --}}
        <livewire:alem.employee.profile.personal-data
            :employee-id="$employeeId"
            :key="'personal-'.$employeeId"
            :auth-user-id="$authUserId"
            :current-team-id="$currentTeamId"
            :company-id="$companyId"
        />

        {{-- Address Manager wenn benötigt --}}
        {{-- <livewire:address.address-manager
            :addressable="$employee"
            :key="'address-'.$employeeId"
        /> --}}

    @elseif($activeTab === 'report')
        <livewire:alem.employee.report.report-table
            :user="$employee"
            :key="'report-'.$employeeId"
        />

    @elseif($activeTab === 'holiday')
        <livewire:alem.employee.holiday.holiday-table
            :user="$employee"
            :key="'holiday-'.$employeeId"
        />

    @elseif($activeTab === 'attendence')
        <livewire:alem.employee.attendence.attendence-table
            :user="$employee"
            :key="'attendence-'.$employeeId"
        />
    @endif
</div>
