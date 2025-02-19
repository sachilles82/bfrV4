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
</x-pupi.navigation.header.index>
