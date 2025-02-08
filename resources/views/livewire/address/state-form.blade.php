<flux:modal variant="flyout" name="create-state" class="md:w-96 space-y-6">
    <div>
        <flux:heading size="lg">{{ __('State Manager') }}</flux:heading>
        <flux:subheading>{{ __('Manager your own states here') }}</flux:subheading>
    </div>

    <div
        x-data="countryDropdown()"
        x-init="init();
            $watch('$wire.country_id', value => {
                selectedCountryId = value;
                const updatedCountry = countries.find(c => c.id === value);
                selectedCountry = updatedCountry ?? null;
            });
        "
    >

        <form wire:submit.prevent="saveState">
            <div class="space-y-4 mb-4">

                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-6 md:col-span-6 flex">
                        <!-- Country-Dropdown -->
                        <div class="mt-4">
                            <x-pupi.input.group
                                label="{{ __('Country') }}"
                                for="country_id"
                                badge="{{ __('') }}"
                                :error="$errors->first('country_id')"
                                help-text="{{ __('') }}"
                                model="country_id"
                            >
                                <div class="relative mt-2">
                                    <div
                                        class="cursor-pointer flex-shrink-0 z-10 inline-flex min-w-[140px] items-center py-1
                                   text-left px-2 text-sm font-medium sm:text-sm sm:leading-5 text-gray-500
                                   bg-gray-100 border-0 border-gray-300 rounded-s-lg dark:bg-white/5
                                   ring-1 ring-inset ring-gray-300 dark:ring-white/10"
                                        @click.prevent="toggleSelectCountry"
                                        @click.away="closeSelectCountry"
                                        @keydown.escape="closeSelectCountry"
                                    >
                                        <!-- Anzeige, wenn KEIN Country ausgewählt -->
                                        <div class="flex flex-wrap" x-show="!selectedCountry">
                                            <div
                                                class="text-gray-800 dark:text-gray-400 truncate px-2 py-0.5 my-0.5 flex items-center">
                                                <svg
                                                    class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light
                                               object-cover ring-1 ring-gray-700/20 dark:ring-white/10
                                               bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400"
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

                                        <!-- Anzeige, wenn Country ausgewählt -->
                                        <div class="flex flex-wrap" x-cloak x-show="selectedCountry">
                                            <div
                                                class="text-gray-800 dark:text-white truncate px-2 py-0.5 my-0.5 flex items-center">
                                                <img
                                                    :src="selectedCountry ? '/flags/country-' + selectedCountry.code.toLowerCase() + '.svg' : ''"
                                                    class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md dark:shadow-sm-light
                                               object-cover ring-1 ring-gray-700/20 dark:ring-white/10
                                               bg-gray-500 dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                                <div class="px-2 truncate"
                                                     x-text="selectedCountry && selectedCountry.code ? selectedCountry.code.toUpperCase() : ''">
                                                </div>
                                                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true"
                                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2" d="m1 1 4 4 4-4"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dropdown-Liste -->
                                    <div
                                        class="mt-1 min-w-[200px] dark:bg-gray-800 shadow-md dark:border dark:border-gray-700
                                   bg-white rounded-b-md absolute top-full left-0 z-30"
                                        x-show="openCountryDropdown"
                                        x-trap="openCountryDropdown"
                                        x-cloak
                                    >
                                        <div class="relative z-30 w-full p-2 bg-white dark:bg-gray-800">
                                            <div class="relative w-full">
                                                <x-pupi.icon.search/>
                                                <input type="search"
                                                       x-model="searchCountry"
                                                       @click.prevent.stop="openCountryDropdown=true"
                                                       @click="searchCountry = ''"
                                                       placeholder="{{ __('Search..') }}"
                                                       class="block w-full px-2.5 pl-10 text-gray-900
                                                  placeholder:dark:text-gray-500 placeholder:text-gray-400
                                                  rounded-md text-sm border-0 ring-1 ring-inset ring-gray-300
                                                  focus:ring-2 focus-within:ring-inset focus-within:ring-indigo-600
                                                  dark:focus:ring-indigo-500 dark:ring-gray-700/50
                                                  dark:bg-white/5 dark:text-white">
                                            </div>
                                        </div>
                                        <div x-ref="dropdown" class="relative z-30 p-2 overflow-y-auto max-h-60">
                                            <div x-cloak x-show="filteredCountries().length === 0"
                                                 class="text-gray-400 dark:text-gray-400 flex justify-center items-center">
                                                {{ __('No results match your search') }}
                                            </div>
                                            <template x-for="(country, index) in filteredCountries()" :key="country.id">
                                                <div class="relative">
                                                    <div
                                                        class="py-1.5 px-3 mb-1 rounded-lg text-sm cursor-pointer"
                                                        :class="
                                                                {
                                                                    'dark:bg-gray-700/50 dark:text-gray-300 bg-gray-100 text-gray-800': selectedCountryId === country.id,
                                                                    'text-gray-600 hover:text-gray-800 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700/50 dark:hover:text-gray-300': selectedCountryId !== country.id
                                                                }
                                                                "
                                                        @click.prevent.stop="selectCountry(country)"
                                                    >
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2
                                                       text-gray-700 dark:text-gray-300">
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
                                                                    class="h-5 w-5 me-2 flex-none rounded-b-2xl shadow-md
                                                               dark:shadow-sm-light object-cover ring-1
                                                               ring-gray-700/90 dark:ring-white/10 bg-gray-500
                                                               dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                                            </template>
                                                            <span class="ml-2" x-text="country.name"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </x-pupi.input.group>
                        </div>
                        <!-- /Country-Dropdown -->

                        <div class="mt-4 relative w-full">
                            <x-pupi.input.group
                                label="{{ __('Name') }}"
                                for="name"
                                badge="{{ __('') }}"
                                :error="$errors->first('name')"
                                help-text="{{ __('') }}"
                                model="name"
                            >
                                <x-pupi.input.text
                                    id="stateName"
                                    wire:model.defer="name"
                                    class="block w-full rounded-md rounded-l-none border-0 py-1.5 dark:text-white text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:focus-within:ring-inset dark:focus-within:ring-indigo-500 dark:bg-white/5 dark:ring-white/10 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    autocomplete="off"
                                    placeholder="z.B. Bayern"
                                />
                            </x-pupi.input.group>
                        </div>

                        <!-- Hier der Hidden-Input, der an Livewire übergeben wird -->
                        <input type="hidden" wire:model="country_id" x-ref="countryIdInput">
                    </div>
                </div>

                {{--                <div class="sm:col-span-2">--}}
                {{--                    <x-pupi.input.group--}}
                {{--                        label="{{ __('Code') }}"--}}
                {{--                        for="code"--}}
                {{--                        badge="{{ __('* Required') }}"--}}
                {{--                        :error="$errors->first('code')"--}}
                {{--                        help-text="{{ __('') }}"--}}
                {{--                        model="code"--}}
                {{--                    >--}}
                {{--                        <x-pupi.input.text--}}
                {{--                            wire:model="code"--}}
                {{--                            name="code"--}}
                {{--                            id="stateCode"--}}
                {{--                            placeholder="{{ __('AG') }}"--}}
                {{--                        />--}}
                {{--                    </x-pupi.input.group>--}}
                {{--                </div>--}}
            </div>
            <x-pupi.button.container2>
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <x-pupi.button.fluxsubmit>
                    {{ __('Save changes') }}
                </x-pupi.button.fluxsubmit>
            </x-pupi.button.container2>
        </form>

        <flux:table>
            <flux:columns>
                <flux:column class="!text-sm font-semibold">{{ __('State') }}</flux:column>
                <flux:column class="!text-sm font-semibold">{{ __('Country') }}</flux:column>
                <flux:column class="!text-sm font-semibold">{{ __('') }}</flux:column>
            </flux:columns>

            <!-- Die eigentlichen Daten -->
            <flux:rows>
                @forelse ($states as $state)
                    <flux:row :key="$state->id" class="hover:bg-gray-100">

                        <flux:cell class="flex items-center gap-3 mx-2">
                            <div class="inline-flex items-center mt-1 ml-1">
                                @if ($state->country && $state->country->code)
                                    <img
                                        src="/flags/country-{{ strtolower($state->country->code) }}.svg"
                                        class="h-4 w-4 me-2 flex-none rounded-b-2xl shadow-md
                                       dark:shadow-sm-light object-cover ring-1
                                       ring-gray-700/90 dark:ring-white/10 bg-gray-500
                                       dark:bg-gray-800 text-gray-700 dark:text-gray-400">
                                @endif

                                <span
                                    class="whitespace-nowrap pr-3 text-sm  dark:text-white text-gray-700 sm:pl-0 font-medium">{{ $state->name }}</span>
                            </div>
                        </flux:cell>

                        {{--                        <flux:cell class="!whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">--}}
                        {{--                            {{ $state->code }}--}}
                        {{--                        </flux:cell>--}}

                        <flux:cell class="!whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                            {{ optional($state->country)->code }}
                        </flux:cell>

                        <!-- 4. Spalte: Aktion -->
                        <flux:cell>
                            <flux:dropdown align="end" offset="-15">
                                <flux:button
                                    class="hover:bg-gray-200/75"
                                    icon="ellipsis-horizontal"
                                    size="sm"
                                    variant="ghost"
                                    inset="top bottom"
                                />
                                <flux:menu class="min-w-32">
                                    <flux:menu.item
                                        wire:click="editState({{ $state->id }})"
                                        icon="pencil-square"
                                    >
                                        {{ __('Edit') }}
                                    </flux:menu.item>
                                    <flux:menu.item
                                        wire:click="deleteState({{ $state->id }})"
                                        wire:confirm="{{ __('Are you sure you want to remove this state?') }}"
                                        icon="trash"
                                        variant="danger"
                                    >
                                        {{ __('Delete') }}
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:cell>
                    </flux:row>
                @empty
                    <!-- Wenn keine States vorhanden sind -->
                    <flux:row>
                        <flux:cell colspan="4" class="px-4 py-2 text-gray-500">
                            <div class="flex justify-center items-center">
                                <x-pupi.icon.database/>
                                <span class="py-0 font-medium text-gray-400 dark:text-gray-400 text-lg">
                                      {{ __('no entries found...') }}
                                </span>
                            </div>
                        </flux:cell>
                    </flux:row>
                @endforelse
            </flux:rows>
        </flux:table>
        {{ $states->links() }}
    </div>

</flux:modal>

<!-- AlpineJS-Skript: NUR die Country-Funktionen! -->
<script>
    function countryDropdown() {
        return {

            countries: @json($countries),
            states: @json($states), // falls du sie später brauchst

            selectedCountryId: @entangle('country_id'),
            selectedCountry: null,
            searchCountry: '',
            openCountryDropdown: false,

            // State- & City-Daten entfallen hier – wir wollen NUR Country-Logik.

            init() {
                // Vorbelegung, falls schon ein Country ausgewählt wurde
                const preselectedCountry = this.countries.find(c => c.id === this.selectedCountryId);
                if (preselectedCountry) {
                    this.selectedCountry = preselectedCountry;
                    this.searchCountry = '';
                } else {
                    this.selectedCountry = null;
                }
            },

            filteredCountries() {
                return this.countries.filter(country =>
                    country.name.toLowerCase().includes(this.searchCountry.toLowerCase())
                );
            },

            toggleSelectCountry() {
                this.openCountryDropdown = !this.openCountryDropdown;
                if (this.openCountryDropdown) this.searchCountry = '';
            },

            closeSelectCountry() {
                this.openCountryDropdown = false;
            },

            selectCountry(country) {
                this.selectedCountryId = country.id;
                this.selectedCountry = country;
                this.openCountryDropdown = false;
                this.searchCountry = '';

                // An Livewire übergeben – das Hidden-Field
                if (this.$refs.countryIdInput && this.$refs.countryIdInput._x_model) {
                    this.$refs.countryIdInput._x_model.set(country.id);
                }
            },
        };
    }
</script>
