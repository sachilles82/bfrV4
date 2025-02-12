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
            <div x-data="addressInputs()" x-init="init()" class="px-4 py-6 sm:p-8">

                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <div class="sm:col-span-6 md:col-span-5 flex">
                        <div class="mt-4">
                            <x-pupi.input.group
                                label="{{ __('Country') }}"
                                for="country_id"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('country_id')"
                                help-text="{{ __('') }}"
                                model="country_id"
                            >
                                <div class="relative mt-2">
                                    <div
                                        class="cursor-pointer flex-shrink-0 z-10 inline-flex min-w-[140px] items-center py-1 text-left px-2 text-sm font-medium sm:text-sm sm:leading-5 text-gray-500 bg-gray-100 border-0 border-gray-300 rounded-s-lg dark:bg-white/5 ring-1 ring-inset ring-gray-300 dark:ring-white/10"
                                        @click.prevent="toggleSelectCountry"
                                        @click.away="closeSelectCountry"
                                        @keydown.escape="closeSelectCountry"
                                    >
                                        <!-- Dummy Country Flag-->
                                        <div class="flex flex-wrap" x-show="!selectedCountry">
                                            <div
                                                class="text-gray-800 dark:text-gray-400 truncate px-2 py-0.5 my-0.5 flex items-center">
                                                <svg
                                                    class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 5 36 26">
                                                    <path
                                                        d="M0 9a4 4 0 0 1 4-4h28a4 4 0 0 1 4 4v18a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V9Z"
                                                        fill="#6b7280"/>
                                                    <path
                                                        d="M17.848 25.922a1.625 1.625 0 1 0 0-3.25 1.625 1.625 0 0 0 0 3.25ZM21.372 11.88c-.792-.69-1.9-1.03-3.326-1.03-1.417 0-2.534.366-3.352 1.087a3.805 3.805 0 0 0-1.234 2.813h-.01v.65h2.925l.005-.457a1.787 1.787 0 0 1 .472-1.26c.295-.31.696-.456 1.194-.456 1.05 0 1.574.558 1.574 1.696 0 .375-.102.736-.305 1.071-.34.496-.748.94-1.213 1.32a4.59 4.59 0 0 0-1.26 1.64c-.228.56-.345 1.357-.345 2.296h2.59l.04-.665a2.742 2.742 0 0 1 .879-1.722l.822-.772a6.673 6.673 0 0 0 1.341-1.686 3.53 3.53 0 0 0 .386-1.614c-.005-1.255-.396-2.22-1.183-2.91Z"
                                                        fill="#ACABB1"/>
                                                </svg>

                                                <div class="px-2 truncate">??</div>
                                                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
                                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2" d="m1 1 4 4 4-4"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap" x-cloak x-show="selectedCountry">
                                            <div
                                                class="text-gray-800 dark:text-white truncate px-2 py-0.5 my-0.5 flex items-center">
                                                <img
                                                    :src="selectedCountry ? '/flags/country-' + selectedCountry.code.toLowerCase() + '.svg' : ''"
                                                    class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                                <div class="px-2 truncate"
                                                     x-text="selectedCountry && selectedCountry.code ? selectedCountry.code.toUpperCase() : ''"></div>
                                                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
                                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2" d="m1 1 4 4 4-4"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div
                                            class="mt-1 min-w-[200px] dark:bg-gray-800 shadow-md dark:border dark:border-gray-700 bg-white rounded-b-md absolute top-full left-0 z-30"
                                            x-show="openCountryDropdown"
                                            x-trap="openCountryDropdown"
                                            x-cloak
                                        >
                                            <div class="relative z-30 w-full p-2 bg-white dark:bg-gray-800">
                                                <div class="relative w-full">
                                                    <x-pupi.icon.search/>
                                                    <input type="search" x-model="searchCountry"
                                                           @click.prevent.stop="openCountryDropdown=true"
                                                           @click="searchCountry = ''"
                                                           placeholder="{{ __('Search..') }}"
                                                           class="block w-full px-2.5 pl-10 text-gray-900 placeholder:dark:text-gray-500 placeholder:text-gray-400 rounded-md text-sm border-0 ring-1 ring-inset ring-gray-300 focus:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 dark:focus:ring-indigo-500 dark:ring-gray-700/50 dark:bg-white/5 dark:text-white">
                                                </div>
                                            </div>
                                            <div x-ref="dropdown" class="relative z-30 p-2 overflow-y-auto max-h-60">
                                                <div x-cloak x-show="filteredCountries().length === 0"
                                                     class="text-gray-400 dark:text-gray-400 flex justify-center items-center">
                                                    {{ __('No results match your search') }}
                                                </div>
                                                <template x-for="(country, index) in filteredCountries()"
                                                          :key="country.id">
                                                    <div class="relative">
                                                        <div class="py-1.5 px-3 mb-1 rounded-lg text-sm cursor-pointer"
                                                             :class="{'dark:bg-gray-700/50 dark:text-gray-300 bg-gray-100 text-gray-800': selectedCountryId === country.id, 'text-gray-600 hover:text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-gray-300': selectedCountryId !== country.id}"
                                                             @click.prevent.stop="selectCountry(country)">
                                                                <span
                                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-700 dark:text-gray-300">
                                                                    <svg x-show="selectedCountryId === country.id" class="w-4 h-4"
                                                                         viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                        <path fill-rule="evenodd"
                                                                              d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                                                              clip-rule="evenodd"/>
                                                                    </svg>
                                                                </span>
                                                            <div class="inline-flex items-center mt-1">
                                                                <template x-if="country.code">
                                                                    <img
                                                                        :src="'/flags/country-' + country.code.toLowerCase() + '.svg'"
                                                                        alt="Flag"
                                                                        class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/90 dark:ring-white/10 bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                                                </template>
                                                                <span class="ml-2" x-text="country.name"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-pupi.input.group>
                        </div>
                        <!-- Country Dropdown End-->

                        <div class="mt-4 relative w-full">
                            <x-pupi.input.group
                                label="{{ __('State') }}"
                                for="state_id"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('state_id')"
                                help-text="{{ __('') }}"
                                model="state_id"
                            >
                                <div autofocus
                                     class="mt-2 block w-min-[180px] relative content-center w-full py-1 text-left bg-white dark:bg-white/5 border-0 rounded-l-none ring-1 ring-inset ring-gray-300 dark:ring-white/10 rounded-md sm:text-sm sm:leading-5"
                                     @click.prevent="toggleSelectState"
                                     @click.away="closeSelectState"
                                     @keydown.escape="closeSelectState"
                                >
                                    <div class="inline-block m-1 pl-2 text-sm text-gray-400 cursor-pointer"
                                         x-show="!selectedState"
                                         x-text="'{{ __('Select a state') }}'">&nbsp;
                                    </div>
                                    <div class="ml-2 flex flex-wrap cursor-pointer"
                                         x-cloak
                                         x-show="selectedState"
                                    >
                                        <div
                                            class="max-w-[160px] sm:max-w-none text-gray-800 dark:text-white truncate px-2 py-0 sm:py-0.5 my-0.5 flex items-center">

                                            <template x-if="selectedState && selectedState.code">
                                                <img :src="'/flags/state/' + selectedState.code.toLowerCase() + '.svg'"
                                                     alt="Flag"
                                                     class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                            </template>

                                            <div class="px-2 truncate" x-text="selectedStateName"></div>

                                            <svg class="w-2.5 h-2.5 ms-2.5 justify-end" aria-hidden="true"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div
                                        class="mt-1 w-full dark:bg-gray-800 shadow-md dark:border dark:border-gray-700 bg-white rounded-b-md absolute top-full left-0 z-30"
                                        x-show="openStateDropdown"
                                        x-trap="openStateDropdown"
                                        x-cloak
                                    >
                                        <div class="relative z-30 w-full p-2 bg-white dark:bg-gray-800">
                                            <div class="relative w-full">
                                                <x-pupi.icon.search/>
                                                <input type="search" x-model="searchState"
                                                       @click.prevent.stop="openStateDropdown=true"
                                                       @click="searchState = ''"
                                                       placeholder="{{ __('Search..') }}"
                                                       class="block w-full px-2.5 pl-10 text-gray-900 placeholder:dark:text-gray-500 placeholder:text-gray-400 rounded-md text-sm border-0 ring-1 ring-inset ring-gray-300 focus:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 dark:focus:ring-indigo-500 dark:ring-gray-700/50 dark:bg-white/5 dark:text-white">
                                            </div>
                                        </div>
                                        <div x-ref="dropdown" class="relative z-30 p-2 overflow-y-auto max-h-60">
                                            <div x-cloak x-show="filteredStates().length === 0"
                                                 class="text-gray-400 dark:text-gray-400 flex justify-center items-center">
                                                {{ __('No results match your search') }}
                                            </div>

                                            <template x-for="(state, index) in filteredStates()" :key="state.id">
                                                <div class="relative">
                                                    <div class="py-1.5 px-3 mb-1 rounded-lg text-sm cursor-pointer"
                                                         :class="{'dark:bg-gray-700/50 dark:text-gray-300 bg-gray-100 text-gray-800': selectedStateId === state.id, 'text-gray-600 hover:text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-gray-300': selectedStateId !== state.id}"
                                                         @click.prevent.stop="selectState(state)"
                                                    >
                                                <span
                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-700 dark:text-gray-300">
                                                    <svg x-show="selectedStateId === state.id" class="w-4 h-4"
                                                         viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                              d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                                              clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                                        <div class="inline-flex items-center mt-1">
                                                            <template x-if="state.code">
                                                                <img
                                                                    :src="'/flags/state/' + state.code.toLowerCase() + '.svg'"
                                                                    alt="Flag"
                                                                    class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light object-cover ring-1 ring-gray-700/20 dark:ring-white/10 bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                                            </template>
                                                            <span class="ml-2" x-text="state.name"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <!-- Statt hier das Livewire-Component reinzupacken, nur der Modal-Trigger-Button -->
                                        <flux:modal.trigger name="create-state">
                                            <flux:button
                                                icon="plus"
                                                class="w-full rounded-tr-none rounded-tl-none rounded-lg dark:bg-white/5 bg-gray-50 hover:bg-gray-100 dark:hover:bg-gray-700/90 dark:hover:text-gray-300 border-t border-gray-200 dark:border-gray-700"
                                                variant="filled"
                                            >
                                                {{ __('Open State Menu') }}
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </div>
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
                            for="city_id"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('city_id')"
                            help-text="{{ __('') }}"
                            model="city_id"
                        >
                            <div class="relative mt-2">
                                <div
                                    class="block relative content-center w-full py-1 text-left bg-white dark:bg-white/5 border-0 ring-1 ring-inset ring-gray-300 dark:ring-white/10 rounded-md sm:text-sm sm:leading-5"
                                    @click.prevent="toggleSelectCity"
                                    @click.away="closeSelectCity"
                                    @keydown.escape="closeSelectCity"
                                >
                                    <div class="inline-block m-1 pl-2 text-sm text-gray-400" x-show="!selectedCity"
                                         x-text="'{{ __('Select a city') }}'">&nbsp;
                                    </div>
                                    <div class="flex flex-wrap" x-cloak x-show="selectedCity">
                                        <div
                                            class="text-gray-800 dark:text-white truncate px-2 py-0.5 my-0.5 flex items-center">

                                            <div class="px-2 truncate" x-text="selectedCityName"></div>

                                            <svg class="w-2.5 h-2.5 ms-2.5 justify-end" aria-hidden="true"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div
                                        class="mt-1 w-full dark:bg-gray-800 shadow-md dark:border dark:border-gray-700 bg-white rounded-b-md absolute top-full left-0 z-30"
                                        x-show="openCityDropdown"
                                        x-trap="openCityDropdown"
                                        x-cloak
                                    >
                                        <div class="relative z-30 w-full p-2 bg-white dark:bg-gray-800">
                                            <div class="relative w-full">
                                                <x-pupi.icon.search/>
                                                <input type="search" x-model="searchCity"
                                                       @click.prevent.stop="openCityDropdown=true"
                                                       @click="searchCity = ''"
                                                       placeholder="{{ __('Search..') }}"
                                                       class="block w-full px-2.5 pl-10 text-gray-900 placeholder:dark:text-gray-500 placeholder:text-gray-400 rounded-md text-sm border-0 ring-1 ring-inset ring-gray-300 focus:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 dark:focus:ring-indigo-500 dark:ring-gray-700/50 dark:bg-white/5 dark:text-white">
                                            </div>
                                        </div>
                                        <div x-ref="dropdown" class="relative z-30 p-2 overflow-y-auto max-h-60">
                                            <div x-cloak x-show="filteredCities().length === 0"
                                                 class="text-gray-400 dark:text-gray-400 flex justify-center items-center">
                                                {{ __('No results match your search') }}
                                            </div>
                                            <template x-for="(city, index) in filteredCities()" :key="city.id">
                                                <div class="relative">
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-700 dark:text-gray-300">
                                                    <svg x-show="selectedCityId === city.id" class="w-4 h-4"
                                                         viewBox="0 0 20 20"
                                                         fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd"
                                                              d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                                              clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                                    <div class="py-1.5 px-3 mb-1 rounded-lg text-sm cursor-pointer"
                                                         :class="{'dark:bg-gray-700/50 dark:text-gray-300 bg-gray-100 text-gray-800': selectedCityId === city.id, 'text-gray-600 hover:text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-gray-300': selectedCityId !== city.id}"
                                                         @click.prevent.stop="selectCity(city)"
                                                    >
                                                        <span class="ml-2" x-text="city.name"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <!-- Statt hier das Livewire-Component reinzupacken, nur der Modal-Trigger-Button -->
                                        <flux:modal.trigger name="create-city" wire:click="$dispatch('loadData')">
                                            <flux:button
                                                icon="plus"
                                                class="w-full rounded-tr-none rounded-tl-none rounded-lg dark:bg-white/5 bg-gray-50 hover:bg-gray-100 dark:hover:bg-gray-700/90 dark:hover:text-gray-300 border-t border-gray-200 dark:border-gray-700"
                                                variant="filled"
                                            >
                                                {{ __('Open City Menu') }}
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </div>
                                </div>
                            </div>
                        </x-pupi.input.group>
                    </div>
                </div>

                <input type="hidden" wire:model="country_id" x-ref="countryIdInput">
                <input type="hidden" wire:model="state_id" x-ref="stateIdInput">
                <input type="hidden" wire:model="city_id" x-ref="cityIdInput">

            </div>

            <!-- Button Container with Action Buttons -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </form>

    </x-slot>

</x-pupi.layout.form>

@script
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('address-updated', () => {
            $wire.dispatchSelf('address-form');
        });
    });
    Livewire.on('update-address', () => {
        window.location.reload();
    });

</script>

@endscript

<script>
    function addressInputs() {
        return {
            countries: @json($countries),
            selectedCountryId: @json($country_id),
            selectedCountry: null,
            selectedCountryName: '',
            searchCountry: '',
            openCountryDropdown: false,

            states: @json($states),
            selectedStateId: @json($state_id),
            selectedState: null,
            selectedStateName: '',
            searchState: '',
            openStateDropdown: false,

            cities: @json($cities),
            selectedCityId: @json($city_id),
            selectedCity: null,
            selectedCityName: '',
            searchCity: '',
            openCityDropdown: false,

            init() {
                // Handle pre-selection for countries
                const preselectedCountry = this.countries.find(c => c.id === this.selectedCountryId);
                if (preselectedCountry) {
                    this.selectedCountryName = preselectedCountry.name;
                    this.selectedCountry = preselectedCountry;
                    this.searchCountry = '';
                }

                // Handle pre-selection for states
                const preselectedState = this.states.find(s => s.id === this.selectedStateId);
                if (preselectedState) {
                    this.selectedStateName = preselectedState.name;
                    this.selectedState = preselectedState;
                    this.searchState = '';
                }

                // Handle pre-selection for cities
                const preselectedCity = this.cities.find(city => city.id === this.selectedCityId);
                if (preselectedCity) {
                    this.selectedCityName = preselectedCity.name;
                    this.selectedCity = preselectedCity;
                    this.searchCity = '';
                }
            },

            filteredCountries() {
                return this.countries.filter(country =>
                    country.name.toLowerCase().includes(this.searchCountry.toLowerCase())
                );
            },

            filteredStates() {
                if (!this.selectedCountry) return [];
                return this.states.filter(state =>
                    state.country_id === this.selectedCountry.id &&
                    state.name.toLowerCase().includes(this.searchState.toLowerCase())
                );
            },

            filteredCities() {
                if (!this.selectedState || !this.cities) return [];
                return this.cities.filter(city =>
                    city.state_id === this.selectedState.id &&
                    city.name && city.name.toLowerCase().includes(this.searchCity.toLowerCase())
                );
            },

            toggleSelectCountry() {
                this.openCountryDropdown = !this.openCountryDropdown;
                if (this.openCountryDropdown) this.searchCountry = '';
            },

            closeSelectCountry() {
                this.openCountryDropdown = false;
            },

            toggleSelectState() {
                this.openStateDropdown = !this.openStateDropdown;
                if (this.openStateDropdown) this.searchState = '';
            },

            closeSelectState() {
                this.openStateDropdown = false;
            },

            toggleSelectCity() {
                this.openCityDropdown = !this.openCityDropdown;
                if (this.openCityDropdown) this.searchCity = '';
            },

            closeSelectCity() {
                this.openCityDropdown = false;
            },

            selectCountry(country) {
                this.selectedCountryId = country.id;
                this.selectedCountryName = country.name;
                this.selectedCountry = country;
                this.openCountryDropdown = false;
                this.searchCountry = '';

                if (this.$refs.countryIdInput && this.$refs.countryIdInput._x_model) {
                    this.$refs.countryIdInput._x_model.set(country.id);
                }

                // Clear selected state and city when country changes
                this.selectedStateId = null;
                this.selectedState = null;
                this.selectedStateName = '';

                this.selectedCityId = null;
                this.selectedCity = null;
                this.selectedCityName = '';
            },

            selectState(state) {
                this.selectedStateId = state.id;
                this.selectedStateName = state.name;
                this.selectedState = state;
                this.openStateDropdown = false;
                this.searchState = '';

                if (this.$refs.stateIdInput && this.$refs.stateIdInput._x_model) {
                    this.$refs.stateIdInput._x_model.set(state.id);
                }

                // Clear selected city when the state changes
                this.selectedCityId = null;
                this.selectedCity = null;
                this.selectedCityName = '';
            },

            selectCity(city) {
                this.selectedCityId = city.id;
                this.selectedCityName = city.name;
                this.selectedCity = city;
                this.openCityDropdown = false;
                this.searchCity = '';

                if (this.$refs.cityIdInput && this.$refs.cityIdInput._x_model) {
                    this.$refs.cityIdInput._x_model.set(city.id);
                }
            },
        };
    }

</script>

