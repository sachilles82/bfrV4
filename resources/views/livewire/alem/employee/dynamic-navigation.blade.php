<div>
    <x-slot:header>
        <x-navigation.alem.employee.header
            :employee="$employee"
            :activeTab="$activeTab"
        />
    </x-slot:header>

    <!-- Content-Bereich: Wrapper mit Abstand und Trennlinien -->
    <div class="mt-6">
        <div class="space-y-10 divide-y dark:divide-white/5 divide-gray-900/5">
            @if($activeTab === 'employee-update')

                <livewire:alem.employee.profile.account.details
                    :employee-id="$employeeId"
                    :key="'details-'.$employeeId"

                    :auth-user-id="$authUserId"
                    :current-team-id="$currentTeamId"
                    :company-id="$companyId"
                />

                <livewire:alem.employee.profile.employment-data.employment-data
                    :employee-id="$employeeId"
                    :key="'employment-'.$employeeId"

                    :auth-user-id="$authUserId"
                    :current-team-id="$currentTeamId"
                    :company-id="$companyId"
                />

                {{--                <livewire:alem.employee.profile.employment-data--}}
                {{--                    :user="$user"--}}
                {{--                />--}}


                {{--                <livewire:alem.employee.profile.personal-data--}}
                {{--                    :user="$user"--}}
                {{--                />--}}




                {{--                <livewire:address.address-manager--}}
                {{--                    :addressable="$user"--}}
                {{--                />--}}
            @elseif($activeTab === 'report')
                <livewire:alem.employee.report.report-table
                    :user="$user"
                    key="report-{{ $user->id }}"
                />
            @elseif($activeTab === 'holiday')
                <livewire:alem.employee.holiday.holiday-table
                    :user="$user"
                    key="holiday-{{ $user->id }}"
                />
            @elseif($activeTab === 'attendence')
                <livewire:alem.employee.holiday.holiday-table
                    :user="$user"
                    key="holiday-{{ $user->id }}"
                />
            @endif
        </div>
    </div>
</div>
