<x-pupi.navigation.header.index>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'employee-update'])"
            :active="$activeTab === 'employee-update'">
            {{ __('Profile Information') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'report'])"
            :active="$activeTab === 'report'">
            {{ __('Report') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'holiday'])"
            :active="$activeTab === 'holiday'">
            {{ __('Holiday') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'attendence'])"
            :active="$activeTab === 'attendence'">
            {{ __('Attendence') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'salary'])"
            :active="$activeTab === 'salary'">
            {{ __('Salary') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'documents'])"
            :active="$activeTab === 'documents'">
            {{ __('Documents') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'leave'])"
            :active="$activeTab === 'leave'">
            {{ __('Leave') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'performance'])"
            :active="$activeTab === 'performance'">
            {{ __('Performance') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'assets'])"
            :active="$activeTab === 'assets'">
            {{ __('Assets') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'training'])"
            :active="$activeTab === 'training'">
            {{ __('Training') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userId, 'documents'])"
            :active="$activeTab === 'documents'">
            {{ __('Documents') }}
        </x-pupi.navigation.header.link>
    </li>
</x-pupi.navigation.header.index>
