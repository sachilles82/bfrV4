<div>
    <x-slot:header>
        <x-navigation.alem.employee.header
            :user="$user"
            :activeTab="$activeTab"
        />
    </x-slot:header>

    <!-- Content-Bereich: Wrapper mit Abstand und Trennlinien -->
    <div class="mt-6">
        <div class="space-y-10 divide-y dark:divide-white/5 divide-gray-900/5">
            @if($activeTab === 'employee-update')
                <livewire:alem.employee.profile.information
                    :user="$user"
                    key="employee-update-{{ $user->id }}"
                />

                <livewire:alem.employee.profile.personal-data
                    :user="$user"
                />


                <livewire:address.address-manager
                    :addressable="$user"
                />

                <livewire:alem.employee.profile.employement-data
                    :user="$user"
                />
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
            @endif
        </div>
    </div>
</div>
