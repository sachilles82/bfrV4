<x-pupi.layout.form>

    <x-slot:title>
        {{ __('Address') }}
    </x-slot:title>

    <x-slot:description>
        {{__('Here are stored the address information')}}
    </x-slot:description>

    <x-slot name="form">


        <form wire:submit.prevent="save"
              class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
            <div class="px-4 py-6 sm:p-8">

                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <div class="sm:col-span-6 md:col-span-5 flex">
                        <div class="mt-4">

                            <x-pupi.input.group
                                label="{{ __('Country') }}"
                                for="country"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('country')"
                                help-text="{{ __('') }}"
                                model="country"
                            >
                                <div class="relative mt-2">
                                    <flux:select
                                        wire:model.live="selectedCountry"
                                        id="country"
                                        variant="listbox"
                                        searchable
                                        placeholder="Country"
                                        size="br-none"
                                    >
                                        @foreach ($countries as $country)
                                            <flux:option value="{{ $country->id }}">
                                                <div
                                                    class="text-gray-800 dark:text-white truncate px-2 py-0 my-0.5 flex items-center">
                                                    <img
                                                        src="/flags/country-{{ strtolower($country->code) }}.svg"
                                                        class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                                    <div class="px-2 truncate">
                                                        {{ $country->name }}
                                                    </div>
                                                </div>
                                            </flux:option>
                                        @endforeach
                                    </flux:select>
                                </div>
                            </x-pupi.input.group>
                        </div>
                        <!-- Country Dropdown End-->

                        <div class="mt-4 relative w-full">
                            <x-pupi.input.group
                                label="{{ __('State') }}"
                                for="selectedState"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('selectedState')"
                                help-text="{{ __('') }}"
                                model="selectedState"
                            >
                                <div class="relative mt-2">
                                    <flux:select
                                        wire:model.live="selectedState"
                                        id="state"
                                        size="bl-none"
                                        variant="listbox"
                                        searchable
                                        empty=" "
                                        placeholder="Choose States"
                                    >
                                        {{-- Optionen --}}
                                        @foreach ($states as $state)
                                            <flux:option value="{{ $state->id }}">
                                                <div class="text-gray-800 dark:text-white truncate px-2 py-0 my-0.5 flex items-center">
                                                    <img
                                                        src="/flags/state/{{ strtolower($state->code) }}.svg"
                                                        class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-zinc-800 text-gray-700 dark:text-gray-400"
                                                    >
                                                    <div class="px-2 truncate">
                                                        {{ $state->name }}
                                                    </div>
                                                </div>
                                            </flux:option>
                                        @endforeach

                                        {{-- Benannter Slot "my" für den Trigger --}}
                                        <x-slot name="add">
                                            <flux:modal.trigger name="create-state">
                                                <flux:button
                                                    icon="plus"
                                                    class="w-full rounded-lg dark:bg-white/5 bg-gray-50 hover:bg-gray-100 dark:hover:bg-gray-700/90 dark:hover:text-gray-300"
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

                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Street + Number') }}"
                            for="street_number"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('street_number')"
                            help-text="{{ __('') }}"
                            model="street_number"
                        >
                            <x-pupi.input.text
                                wire:model="street_number"
                                name="street_number"
                                id="street_number"
                                placeholder="{{ __('Musterstrasse 25') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('ZIP + City') }}"
                            for="city"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('city')"
                            help-text="{{ __('') }}"
                            model="city"
                        >
                            <div class="relative mt-2">
                                <flux:select
                                    wire:model="selectedCity"
                                    id="city"
                                    variant="listbox"
                                    searchable
                                    placeholder="Choose Zip & City"
                                >
                                    @if ($cities->count() == 0)
                                        <flux:option value="">-- choose state first --</flux:option>
                                    @endif
                                    @foreach ($cities as $city)
                                        <flux:option value="{{ $city->id }}">{{ $city->name }}</flux:option>
                                    @endforeach
                                    <x-slot name="add">
                                        <flux:modal.trigger name="create-city">
                                            <flux:button
                                                icon="plus"
                                                class="w-full rounded-lg dark:bg-white/5 bg-gray-50 hover:bg-gray-100 dark:hover:bg-gray-700/90 dark:hover:text-gray-300"
                                                variant="filled"
                                            >
                                                {{ __('Open Zip&City Menu') }}
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </x-slot>
                                </flux:select>
                            </div>
                        </x-pupi.input.group>
                    </div>
                </div>

            </div>

            <!-- Button Container with Action Buttons -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </form>

    </x-slot>

</x-pupi.layout.form>
