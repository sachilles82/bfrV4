<div wire:ignore.self>
    <flux:modal variant="flyout" position="left" name="create-state" class="md:w-1/4 space-y-6">
        <div>
            <flux:heading size="lg">{{ __('State Manager') }}</flux:heading>
            <flux:subheading>{{ __('Manager your own states here') }}</flux:subheading>
        </div>

        <div>
            <!-- Formular für State (Create/Update) -->
            <form wire:submit.prevent="saveState" class="space-y-4">
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
                                    wire:model="selectedCountry"
                                    id="selectedCountry"
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
                                <div wire:loading
                                     class="absolute inset-0 rounded-lg rounded-l-none flex items-center justify-center backdrop-blur-xs">
                                </div>
                            </div>
                        </x-pupi.input.group>
                    </div>

                    <!-- State Dropdown -->
                    <div class="mt-4 relative w-full">
                        <x-pupi.input.group
                            label="{{ __('Name') }}"
                            for="name"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('name')"
                            help-text="{{ __('') }}"
                            model="name"
                        >
                            <div class="relative">
                                <x-pupi.input.text
                                    wire:model.defer="name"
                                    id="name"
                                    class="block w-full rounded-md rounded-l-none border-0 py-1.5 dark:text-white text-gray-900 shadow-xs ring-1 ring-inset ring-gray-300 dark:focus-within:ring-inset dark:focus-within:ring-indigo-500 dark:bg-white/5 dark:ring-white/10 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    autocomplete="off"
                                    placeholder="z.B. Bayern"
                                />
                                <div wire:loading
                                     class="absolute inset-0 rounded-lg rounded-l-none flex items-center justify-center backdrop-blur-xs">
                                </div>
                            </div>
                        </x-pupi.input.group>

                    </div>
                </div>
                <!-- Buttons -->
                <div class="flex space-x-4">
                    <x-pupi.button.fluxback type="button" wire:click="finish">
                        {{ __('Cancel') }}
                    </x-pupi.button.fluxback>
                    <flux:button type="submit">
                        {{ $editing ? __('Update State') : __('Create State') }}
                    </flux:button>
                </div>
            </form>

            <flux:table>
                <flux:columns>
                    <flux:column class="text-sm! font-semibold">{{ __('State') }}</flux:column>
                    <flux:column class="text-sm! font-semibold">{{ __('Country') }}</flux:column>
                    @if(auth()->user()
                        ->can(\App\Enums\Role\Permission::EDIT_ALL_STATE_CITY)
                        ||
                        auth()->user()
                        ->can(\App\Enums\Role\Permission::EDIT_OWN_STATE_CITY))
                        <flux:column class="text-sm! font-semibold">{{ __('Actions') }}</flux:column>
                    @endif
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

                            <flux:cell class="whitespace-nowrap! px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                {{ optional($state->country)->code }}
                            </flux:cell>
                            <!-- 4. Spalte: Aktion -->
                            <flux:cell>
                                @can('update', $state)
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
                                            @can('delete', $state)
                                                <flux:menu.item
                                                    wire:click="deleteState({{ $state->id }})"
                                                    wire:confirm="{{ __('Are you sure you want to remove this state?') }}"
                                                    icon="trash"
                                                    variant="danger"
                                                >
                                                    {{ __('Delete') }}
                                                </flux:menu.item>
                                            @endcan
                                        </flux:menu>
                                    </flux:dropdown>
                                @endcan
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
</div>
