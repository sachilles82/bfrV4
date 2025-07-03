<x-pupi.layout.form>

    <x-slot:title>
        {{ __('Account Details') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employee account details.') }}
    </x-slot:description>

    <x-slot name="form">
        <form wire:submit.prevent="updateEmployeeDetails">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <div class="col-span-full flex items-center gap-x-8">
                        <img
                            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                            alt="" class="h-24 w-24 flex-none rounded-lg bg-gray-800 object-cover">
                        <div>
                            <button type="button"
                                    class="rounded-md dark:bg-white/10 px-3 py-2 text-sm font-semibold dark:text-white shadow-xs dark:hover:bg-white/20 dark:ring-transparent bg-white text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Change avatar
                            </button>
                            <p class="mt-2 text-xs leading-5 dark:text-gray-400 text-gray-500">JPG,
                                GIF or PNG. 1MB max.</p>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Gender') }}"
                            for="gender"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('gender')"
                            model="gender"
                            help-text="{{ __('') }}">

                            <flux:select
                                class="mt-2"
                                wire:model="gender"
                                id="gender"
                                variant="listbox"
                                placeholder="{{ __('Select gender') }}">

                                @foreach($this->genderOptions() as $genderOption)
                                    <flux:option
                                        wire:key="gender-option-{{ $genderOption['value'] }}"
                                        value="{{ $genderOption['value'] }}">
                                        <span>{{ $genderOption['label'] }}</span>
                                    </flux:option>
                                @endforeach

                            </flux:select>

                        </x-pupi.input.group>
                    </div>

                    <!-- User Name -->
                    <div class="sm:col-span-5">
                        <x-pupi.input.group
                            label="{{ __('Full Name') }}"
                            for="name"
                            badge="{{ __('Required') }}"
                            model="name"
                            help-text="{{ __('') }}"
                            :error="$errors->first('name')">

                            <x-pupi.input.text
                                wire:model="name"
                                name="name"
                                id="name"
                                placeholder="{{ __('Name Lastname') }}"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Email') }}"
                            for="email"
                            badge="{{ __('Required') }}"
                            model="email"
                            help-text="{{ __('') }}"
                            :error="$errors->first('email')">

                            <x-pupi.input.text
                                wire:model="email"
                                name="email"
                                type="email"
                                id="email"
                                placeholder="{{ __('Email') }}"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Phone -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Business Phone') }}"
                            for="phone_1"
                            badge="{{ __('Optional') }}"
                            model="phone_1"
                            help-text="{{ __('') }}"
                            :error="$errors->first('phone_1')">

                            <x-pupi.input.text
                                wire:model="phone_1"
                                name="phone_1"
                                type="phone"
                                id="phone_1"
                                placeholder="{{ __('Phone') }}"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Phone -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Private Phone') }}"
                            for="phone_2"
                            badge="{{ __('Optional') }}"
                            model="phone_2"
                            help-text="{{ __('') }}"
                            :error="$errors->first('phone_2')">

                            <x-pupi.input.text
                                wire:model="phone_2"
                                name="phone_2"
                                type="phone"
                                id="phone_2"
                                placeholder="{{ __('Phone') }}"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Phone -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Last Login') }}"
                            for="phone_1"
                            badge="{{ __('Optional') }}"
                            model="phone_1"
                            help-text="{{ __('') }}"
                            :error="$errors->first('phone_1')">

                            <x-pupi.input.text
                                wire:model="phone_1"
                                name="phone_1"
                                type="phone"
                                id="phone_1"
                                placeholder="{{ __('Phone') }}"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Model Status Select -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Account Status') }}"
                            for="model_status"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('model_status')"
                            model="model_status">

                            <flux:select
                                class="mt-2"
                                wire:model="model_status"
                                id="model_status"
                                variant="listbox">

                                @foreach($this->modelStatusOptions() as $statusOption)
                                    <flux:option
                                        wire:key="model-status-option-{{ $statusOption['value'] }}"
                                        value="{{ $statusOption['value'] }}">
                                        <div class="flex items-center">
                                                <span class="mr-2">
                                                    <x-dynamic-component
                                                        :component="$statusOption['icon']"
                                                        class="h-4 w-5 rounded-md {{ $statusOption['colors'] ?? '' }}"/>
                                                </span>
                                            <span>{{ $statusOption['label'] }}</span>
                                        </div>
                                    </flux:option>
                                @endforeach
                            </flux:select>

                        </x-pupi.input.group>
                    </div>

                </div>
            </div>

            <!-- Button Container -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </form>
    </x-slot>

</x-pupi.layout.form>
