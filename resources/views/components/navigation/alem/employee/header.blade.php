<x-pupi.navigation.header.index>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'employee-update'])"
            :active="$activeTab === 'employee-update'">
            {{ __('Profile Information') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'report'])"
            :active="$activeTab === 'report'">
            {{ __('Report') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'holiday'])"
            :active="$activeTab === 'holiday'">
            {{ __('Holiday') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'attendence'])"
            :active="$activeTab === 'attendence'">
            {{ __('Attendence') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'salary'])"
            :active="$activeTab === 'salary'">
            {{ __('Salary') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'documents'])"
            :active="$activeTab === 'documents'">
            {{ __('Documents') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'leave'])"
            :active="$activeTab === 'leave'">
            {{ __('Leave') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'performance'])"
            :active="$activeTab === 'performance'">
            {{ __('Performance') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'assets'])"
            :active="$activeTab === 'assets'">
            {{ __('Assets') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'training'])"
            :active="$activeTab === 'training'">
            {{ __('Training') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$employeeId, 'documents'])"
            :active="$activeTab === 'documents'">
            {{ __('Documents') }}
        </x-pupi.navigation.header.link>
    </li>
</x-pupi.navigation.header.index>
