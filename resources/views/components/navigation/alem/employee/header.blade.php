<x-pupi.navigation.header.index>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'employee-update'])"
            :active="$activeTab === 'employee-update'">
            {{ __('Profile Information') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'report'])"
            :active="$activeTab === 'report'">
            {{ __('Report') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'holiday'])"
            :active="$activeTab === 'holiday'">
            {{ __('Holiday') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'attendence'])"
            :active="$activeTab === 'attendence'">
            {{ __('Attendence') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'salary'])"
            :active="$activeTab === 'salary'">
            {{ __('Salary') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'documents'])"
            :active="$activeTab === 'documents'">
            {{ __('Documents') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'leave'])"
            :active="$activeTab === 'leave'">
            {{ __('Leave') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'performance'])"
            :active="$activeTab === 'performance'">
            {{ __('Performance') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'assets'])"
            :active="$activeTab === 'assets'">
            {{ __('Assets') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'training'])"
            :active="$activeTab === 'training'">
            {{ __('Training') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$user, 'documents'])"
            :active="$activeTab === 'documents'">
            {{ __('Documents') }}
        </x-pupi.navigation.header.link>
    </li>
</x-pupi.navigation.header.index>
