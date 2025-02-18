<div wire:ignore.self>
    <flux:modal variant="flyout" position="left" name="create-city" class="md:w-1/4 space-y-6">
        <div>
            <flux:heading size="lg">{{ __('City Manager') }}</flux:heading>
            <flux:subheading>{{ __('Manage your own cities here') }}</flux:subheading>
        </div>

        <!-- Formular zum Erstellen / Aktualisieren einer City -->
        <form wire:submit.prevent="saveCity" class="space-y-4">
            <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row md:space-x-4">
                <!-- Country Dropdown -->
                <div class="sm:col-span-6 md:col-span-5 flex">
                    <div class="mt-4">
                        <x-pupi.input.group
                            label="{{ __('Country') }}"
                            for="selectedCountry"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('selectedCountry')"
                            help-text=""
                            model="selectedCountry"
                        >
                            <div class="relative mt-2">
                                <flux:select
                                    {{--                                        wire:init="selectedCountry"--}}
                                    wire:model.live="selectedCountry"
                                    id="country"
                                    variant="listbox"
                                    searchable
                                    placeholder="Country"
                                    size="br-none"
                                >
                                    @foreach ($countries as $country)
                                        <flux:option value="{{ $country['id'] }}">
                                            <div
                                                class="text-gray-800 dark:text-white truncate px-2 py-0 my-0.5 flex items-center">
                                                <img src="/flags/country-{{ strtolower($country['code']) }}.svg"
                                                     class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                                <div class="px-2 truncate">
                                                    {{ $country['name'] }}
                                                </div>
                                            </div>
                                        </flux:option>
                                    @endforeach
                                </flux:select>
                            </div>
                        </x-pupi.input.group>
                    </div>

                    <!-- State Dropdown -->
                    <div class="mt-4 relative w-full">
                        <x-pupi.input.group
                            label="{{ __('State') }}"
                            for="selectedState"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('selectedState')"
                            help-text=""
                            model="selectedState"
                        >
                            <div class="relative mt-2">
                                <flux:select
                                    {{--                                        wire:init="selectedState"--}}
                                    wire:model.live="selectedState"
                                    id="state"
                                    size="bl-none"
                                    variant="listbox"
                                    searchable
                                    empty="Keine Resultate"
                                    placeholder="Choose States"
                                >
                                    @foreach ($states as $state)
                                        <flux:option value="{{ $state['id'] }}">
                                            <div
                                                class="text-gray-800 dark:text-white truncate px-2 py-0 my-0.5 flex items-center">
                                                <img src="/flags/state/{{ strtolower($state['code']) }}.svg"
                                                     class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-zinc-800 text-gray-700 dark:text-gray-400">
                                                <div class="px-2 truncate">
                                                    {{ $state['name'] }}
                                                </div>
                                            </div>
                                        </flux:option>
                                    @endforeach
                                    <x-slot name="add">
                                        <flux:modal.trigger name="create-state">
                                            <flux:separator class="mt-2 mb-1"/>
                                            <flux:button
                                                icon="plus"
                                                class="w-full rounded-b-lg rounded-t-none dark:bg-white/10 dark:hover:hover:bg-white/20 dark:text-white"
                                                variant="filled"
                                            >
                                                {{ __('Open State Menu') }}
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </x-slot>
                                </flux:select>
                            </div>
                        </x-pupi.input.group>
                    </div>
                </div>
            </div>

            <!-- Input für den City-Namen (z. B. ZIP City) -->
            <x-pupi.input.group
                label="{{ __('City Name') }}"
                for="name"
                badge="{{ __('Required') }}"
                :error="$errors->first('name')"
                help-text=""
                model="name">
                <x-pupi.input.text
                    wire:model.lazy="name"
                    id="name"
                    placeholder="{{ __('Enter city name, e.g. 8048 City') }}"/>
            </x-pupi.input.group>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <x-pupi.button.fluxback type="button" wire:click="resetForm">
                    {{ __('Cancel') }}
                </x-pupi.button.fluxback>
                <flux:button type="submit">
                    {{ $editing ? __('Update City') : __('Create City') }}
                </flux:button>
            </div>
        </form>

        {{--    <form wire:submit.prevent="updateAddress"--}}
        {{--          class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">--}}
        {{--        <div class="px-2 py-6 sm:p-8">--}}
        {{--            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">--}}
        {{--                <!-- Country Dropdown -->--}}
        {{--                <div class="sm:col-span-6 md:col-span-5 flex">--}}
        {{--                    <div class="mt-4">--}}
        {{--                        <x-pupi.input.group--}}
        {{--                            label="{{ __('Country') }}"--}}
        {{--                            for="selectedCountry"--}}
        {{--                            badge="{{ __('Required') }}"--}}
        {{--                            :error="$errors->first('selectedCountry')"--}}
        {{--                            help-text=""--}}
        {{--                            model="selectedCountry"--}}
        {{--                        >--}}
        {{--                            <div class="relative mt-2">--}}
        {{--                                <flux:select--}}
        {{--                                    --}}{{--                                        wire:init="selectedCountry"--}}
        {{--                                    wire:model.live="selectedCountry"--}}
        {{--                                    id="country"--}}
        {{--                                    variant="listbox"--}}
        {{--                                    searchable--}}
        {{--                                    placeholder="Country"--}}
        {{--                                    size="br-none"--}}
        {{--                                >--}}
        {{--                                    @foreach ($countries as $country)--}}
        {{--                                        <flux:option value="{{ $country['id'] }}">--}}
        {{--                                            <div--}}
        {{--                                                class="text-gray-800 dark:text-white truncate px-2 py-0 my-0.5 flex items-center">--}}
        {{--                                                <img src="/flags/country-{{ strtolower($country['code']) }}.svg"--}}
        {{--                                                     class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400">--}}
        {{--                                                <div class="px-2 truncate">--}}
        {{--                                                    {{ $country['name'] }}--}}
        {{--                                                </div>--}}
        {{--                                            </div>--}}
        {{--                                        </flux:option>--}}
        {{--                                    @endforeach--}}
        {{--                                </flux:select>--}}
        {{--                            </div>--}}
        {{--                        </x-pupi.input.group>--}}
        {{--                    </div>--}}

        {{--                    <!-- State Dropdown -->--}}
        {{--                    <div class="mt-4 relative w-full">--}}
        {{--                        <x-pupi.input.group--}}
        {{--                            label="{{ __('State') }}"--}}
        {{--                            for="selectedState"--}}
        {{--                            badge="{{ __('Required') }}"--}}
        {{--                            :error="$errors->first('selectedState')"--}}
        {{--                            help-text=""--}}
        {{--                            model="selectedState"--}}
        {{--                        >--}}
        {{--                            <div class="relative mt-2">--}}
        {{--                                <flux:select--}}
        {{--                                    --}}{{--                                        wire:init="selectedState"--}}
        {{--                                    wire:model.live="selectedState"--}}
        {{--                                    id="state"--}}
        {{--                                    size="bl-none"--}}
        {{--                                    variant="listbox"--}}
        {{--                                    searchable--}}
        {{--                                    empty="Keine Resultate"--}}
        {{--                                    placeholder="Choose States"--}}
        {{--                                >--}}
        {{--                                    @foreach ($states as $state)--}}
        {{--                                        <flux:option value="{{ $state['id'] }}">--}}
        {{--                                            <div--}}
        {{--                                                class="text-gray-800 dark:text-white truncate px-2 py-0 my-0.5 flex items-center">--}}
        {{--                                                <img src="/flags/state/{{ strtolower($state['code']) }}.svg"--}}
        {{--                                                     class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-zinc-800 text-gray-700 dark:text-gray-400">--}}
        {{--                                                <div class="px-2 truncate">--}}
        {{--                                                    {{ $state['name'] }}--}}
        {{--                                                </div>--}}
        {{--                                            </div>--}}
        {{--                                        </flux:option>--}}
        {{--                                    @endforeach--}}
        {{--                                    <x-slot name="add">--}}
        {{--                                        <flux:modal.trigger name="create-state">--}}
        {{--                                            <flux:separator class="mt-2 mb-1"/>--}}
        {{--                                            <flux:button--}}
        {{--                                                icon="plus"--}}
        {{--                                                class="w-full rounded-b-lg rounded-t-none dark:bg-white/10 dark:hover:hover:bg-white/20 dark:text-white"--}}
        {{--                                                variant="filled"--}}
        {{--                                            >--}}
        {{--                                                {{ __('Open State Menu') }}--}}
        {{--                                            </flux:button>--}}
        {{--                                        </flux:modal.trigger>--}}
        {{--                                    </x-slot>--}}
        {{--                                </flux:select>--}}
        {{--                            </div>--}}
        {{--                        </x-pupi.input.group>--}}
        {{--                    </div>--}}
        {{--                </div>--}}

        {{--                <!-- Street + Number -->--}}
        {{--                <div class="col-span-full">--}}
        {{--                    <x-pupi.input.group--}}
        {{--                        label="{{ __('Street + Number') }}"--}}
        {{--                        for="street_number"--}}
        {{--                        badge="{{ __('Required') }}"--}}
        {{--                        :error="$errors->first('street_number')"--}}
        {{--                        help-text=""--}}
        {{--                        model="street_number"--}}
        {{--                    >--}}
        {{--                        <x-pupi.input.text--}}
        {{--                            wire:model.lazy="street_number"--}}
        {{--                            name="street_number"--}}
        {{--                            id="street_number"--}}
        {{--                            placeholder="{{ __('Musterstrasse 25') }}"--}}
        {{--                        />--}}
        {{--                    </x-pupi.input.group>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}

        {{--        <!-- Button Container with Action Buttons -->--}}
        {{--        <x-pupi.button.container>--}}
        {{--            <x-pupi.button.fluxsubmit/>--}}
        {{--        </x-pupi.button.container>--}}
        {{--    </form>--}}


        <!-- Tabelle: Liste der vom User (Team) erstellten Cities -->
        <div class="mt-6">
            <flux:table>
                <flux:columns>
                    <flux:column class="!text-sm font-semibold">{{ __('City') }}</flux:column>
                    <flux:column class="!text-sm font-semibold">{{ __('State') }}</flux:column>
                    <flux:column class="!text-sm font-semibold">{{ __('Country') }}</flux:column>
                    @if(auth()->user()
                       ->can(\App\Enums\Role\Permission::EDIT_ALL_STATE_CITY)
                       ||
                       auth()->user()
                       ->can(\App\Enums\Role\Permission::EDIT_OWN_STATE_CITY))
                        <flux:column class="!text-sm font-semibold">{{ __('Actions') }}</flux:column>
                    @endif
                </flux:columns>

                <flux:rows>
                    @forelse ($cities as $city)
                        <flux:row :key="$city->id" class="hover:bg-gray-100">
                            <flux:cell>{{ $city->name }}</flux:cell>
                            <flux:cell>{{ $city->state->name ?? '-' }}</flux:cell>
                            <flux:cell>{{ $city->state->country->name ?? '-' }}</flux:cell>
                            <flux:cell>
                                <div class="flex items-center space-x-2">
                                    <flux:button
                                        wire:click="editCity({{ $city->id }})"
                                        icon="pencil-square"
                                        size="sm"
                                        variant="ghost"/>
                                    <flux:button
                                        wire:click="deleteCity({{ $city->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this city?') }}"
                                        icon="trash"
                                        size="sm"
                                        variant="danger"/>
                                </div>
                            </flux:cell>
                        </flux:row>
                    @empty
                        <flux:row>
                            <flux:cell colspan="4" class="text-center text-gray-500">
                                {{ __('No entries found...') }}
                            </flux:cell>
                        </flux:row>
                    @endforelse
                </flux:rows>
            </flux:table>
        </div>
    </flux:modal>
</div>
