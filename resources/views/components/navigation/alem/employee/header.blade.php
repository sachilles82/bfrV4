<x-pupi.navigation.header.index>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'employee-update'])"
            :active="$activeTab === 'employee-update'">
            {{ __('Profile Information') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'report'])"
            :active="$activeTab === 'report'">
            {{ __('Report') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'holiday'])"
            :active="$activeTab === 'holiday'">
            {{ __('Holiday') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'attendence'])"
            :active="$activeTab === 'attendence'">
            {{ __('Attendence') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'salary'])"
            :active="$activeTab === 'salary'">
            {{ __('Salary') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'documents'])"
            :active="$activeTab === 'documents'">
            {{ __('Documents') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'leave'])"
            :active="$activeTab === 'leave'">
            {{ __('Leave') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'performance'])"
            :active="$activeTab === 'performance'">
            {{ __('Performance') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'assets'])"
            :active="$activeTab === 'assets'">
            {{ __('Assets') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'training'])"
            :active="$activeTab === 'training'">
            {{ __('Training') }}
        </x-pupi.navigation.header.link>
    </li>
    <li>
        <x-pupi.navigation.header.link
            :href="route('employees.profile', [$userSlug, 'documents'])"
            :active="$activeTab === 'documents'">
            {{ __('Documents') }}
        </x-pupi.navigation.header.link>
    </li>
</x-pupi.navigation.header.index>
