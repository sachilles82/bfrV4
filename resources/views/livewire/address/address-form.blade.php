<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Address') }}
    </x-slot:title>
    <x-slot:description>
        {{ __('Here are stored the address information') }}
    </x-slot:description>
    <x-slot name="form">
        <form wire:submit.prevent="updateAddress"
              class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
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
                                        wire:init="selectedCountry"
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
                                        wire:init="selectedState"
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
                                        @can('create', \App\Models\Address\State::class)
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
                                        @endcan
                                    </flux:select>
                                </div>
                            </x-pupi.input.group>
                        </div>
                    </div>

                    <!-- Street + Number -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Street + Number') }}"
                            for="street_number"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('street_number')"
                            help-text=""
                            model="street_number"
                        >
                            <x-pupi.input.text
                                wire:model.lazy="street_number"
                                name="street_number"
                                id="street_number"
                                placeholder="{{ __('Musterstrasse 25') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- ZIP + City Dropdown -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('ZIP + City') }}"
                            for="selectedCity"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('selectedCity')"
                            help-text=""
                            model="selectedCity"
                        >
                            <div class="relative mt-2">
                                <flux:select
                                    wire:init="selectedCity"
                                    wire:model="selectedCity"
                                    id="city"
                                    variant="listbox"
                                    searchable
                                    placeholder="Choose Zip & City"
                                >
                                    @if (empty($cities))
                                        <flux:option value="">-- choose state first --</flux:option>
                                    @endif
                                    @foreach ($cities as $city)
                                        <flux:option value="{{ $city['id'] }}">
                                            <div class="px-2 truncate">
                                                {{ $city['name'] }}
                                            </div>
                                        </flux:option>
                                    @endforeach
                                    @can('create', \App\Models\Address\City::class)
                                        <x-slot name="add">
                                            <flux:modal.trigger name="create-city">
                                                <flux:separator class="mt-2 mb-1"/>
                                                <flux:button
                                                    @click="$dispatch('create-city')"
                                                    icon="plus"
                                                    class="w-full rounded-b-lg rounded-t-none dark:bg-white/10 dark:hover:hover:bg-white/20 dark:text-white"
                                                    variant="filled"
                                                >
                                                    {{ __('Open Zip & City Menu') }}
                                                </flux:button>
                                            </flux:modal.trigger>
                                        </x-slot>
                                    @endcan
                                </flux:select>
                            </div>
                        </x-pupi.input.group>
                    </div>
                </div>
            </div>

            <!-- Button Container with Action Buttons -->
            @can('update', $addressable)
                <x-pupi.button.container>
                    <x-pupi.button.fluxsubmit/>
                </x-pupi.button.container>
            @endcan
        </form>
    </x-slot>
</x-pupi.layout.form>
