<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Employee Employment Data') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employment data for') }} {{ $user->name }} {{ $user->last_name }}.
    </x-slot:description>

    <x-slot name="form">
        <!-- Loading Overlay for the entire component -->

        <form wire:submit.prevent="updateEmployee">
            <div class="px-4 py-6 sm:p-8 relative">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <!-- AHV Number -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('AHV Number') }}"
                            for="ahv_number"
                            model="ahv_number"
                            badge="{{ __('Required') }}"
                            error="{{ $errors->first('ahv_number') }}"
                        >
                            <x-pupi.input.text
                                wire:model="ahv_number"
                                x-mask="999.9999.9999.99"
                                name="ahv_number"
                                id="ahv_number"
                                placeholder="{{ __('756.XXXX.XXXX.XX') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Nationality -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Nationality') }}"
                            for="nationality"
                            model="nationality"
                            badge="{{ __('Required') }}"
                            error="{{ $errors->first('nationality') }}"
                        >
                            <div class="relative mt-2">
                                <flux:select
                                    wire:model="nationality"
                                    id="nationality"
                                    name="nationality"
                                    variant="listbox"
                                    searchable
                                    placeholder="{{ __('Select Country') }}"
                                >
                                    @foreach ($countries as $country)
                                        <flux:option value="{{ $country['name'] }}">
                                            <div class="text-gray-800 dark:text-white truncate px-2 py-0 my-0.5 flex items-center">
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

                    <!-- Hometown -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Hometown') }}"
                            for="hometown"
                            model="hometown"
                            badge="{{ __('Required') }}"
                            error="{{ $errors->first('hometown') }}"
                        >
                            <x-pupi.input.text
                                wire:model="hometown"
                                name="hometown"
                                id="hometown"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Birthdate -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Birthdate') }}"
                            for="birthdate"
                            model="birthdate"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('birthdate')"
                        >
                            <flux:date-picker
                                with-today
                                value="21-03-2025"
                                wire:model.defer="birthdate"
                                id="birthdate"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>

                    <!-- Religion -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Religion') }}"
                            for="religion"
                            model="religion"
                            badge="{{ __('Required') }}"
                            error="{{ $errors->first('religion') }}"
                        >
                            <flux:select
                                wire:model="religion"
                                name="religion"
                                id="religion"
                                variant="listbox"
                                placeholder="{{ __('Select Religion') }}"
                            >
                                @foreach(\App\Enums\Employee\Religion::cases() as $religionOption)
                                    <flux:option
                                        value="{{ $religionOption->value }}">{{ __($religionOption->label()) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Civil Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Civil Status') }}"
                            for="civil_status"
                            model="civil_status"
                            badge="{{ __('Required') }}"
                            error="{{ $errors->first('civil_status') }}"
                        >
                            <flux:select
                                wire:model="civil_status"
                                name="civil_status"
                                id="civil_status"
                                variant="listbox"
                                placeholder="{{ __('Select Civil Status') }}"
                            >
                                @foreach(\App\Enums\Employee\CivilStatus::cases() as $statusOption)
                                    <flux:option
                                        value="{{ $statusOption->value }}">{{ __($statusOption->label()) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Residence Permit -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Residence Permit') }}"
                            for="residence_permit"
                            model="residence_permit"
                            badge="{{ __('Required') }}"
                            error="{{ $errors->first('residence_permit') }}"
                        >
                            <flux:select
                                wire:model="residence_permit"
                                name="residence_permit"
                                id="residence_permit"
                                variant="listbox"
                                placeholder="{{ __('Select Residence Permit') }}"
                            >
                                @foreach(\App\Enums\Employee\Residence::cases() as $permitOption)
                                    <flux:option
                                        value="{{ $permitOption->value }}">{{ __($permitOption->label()) }}</flux:option>
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
