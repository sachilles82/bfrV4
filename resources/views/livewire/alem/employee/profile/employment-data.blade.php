<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Employee Employment Data') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employment data') }}
    </x-slot:description>

    <x-slot name="form">
        <!-- Loading Overlay for the entire component -->

        <form wire:submit.prevent="updateEmploymentData">
            <div class="px-4 py-6 sm:p-8 relative">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <!-- AHV Number -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('AHV Number') }}"
                            for="ahv_number"
                            model="ahv_number"
                            badge="{{ __('Required') }}"
                            error="{{ $errors->first('ahv_number') }}">

                            <x-pupi.input.text
                                wire:model="ahv_number"
                                x-mask="756.9999.9999.99"
                                name="ahv_number"
                                id="ahv_number"
                                placeholder="{{ __('756.XXXX.XXXX.XX') }}"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Residence Permit -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Residence Permit') }}"
                            for="residence_permit"
                            model="residence_permit"
                            badge="{{ __('Required') }}"
                            help-text="{{ __('') }}">

                            <flux:select
                                class="!mt-2"
                                wire:model="residence_permit"
                                name="residence_permit"
                                id="residence_permit"
                                variant="listbox"
                                placeholder="{{ __('Select Residence Permit') }}">

                                @foreach($this->residencePermitOptions() as $permitOption)
                                    <flux:option
                                        wire:key="residence-option-{{ $permitOption['value'] }}"
                                        value="{{ $permitOption['value'] }}">
                                        <span>{{ $permitOption['label'] }}</span>
                                    </flux:option>
                                @endforeach

                            </flux:select>

                        </x-pupi.input.group>
                    </div>

                    <!-- Birthday -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Birthday') }}"
                            for="residence_permit"
                            model="residence_permit"
                            badge="{{ __('Required') }}"
                            help-text="{{ __('') }}">

                            <flux:select
                                class="!mt-2"
                                wire:model="residence_permit"
                                name="residence_permit"
                                id="residence_permit"
                                variant="listbox"
                                placeholder="{{ __('Select Residence Permit') }}">

                                @foreach($this->residencePermitOptions() as $permitOption)
                                    <flux:option
                                        wire:key="residence-option-{{ $permitOption['value'] }}"
                                        value="{{ $permitOption['value'] }}">
                                        <span>{{ $permitOption['label'] }}</span>
                                    </flux:option>
                                @endforeach

                            </flux:select>

                        </x-pupi.input.group>
                    </div>

                    <!-- Nationality -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Nationality') }}"
                            for="nationality"
                            model="nationality"
                            badge="{{ __('Required') }}"
                            error="{{ $errors->first('nationality') }}">

                            <div class="relative mt-2">
                                <flux:select
                                    wire:model="nationality"
                                    id="nationality"
                                    name="nationality"
                                    variant="listbox"
                                    searchable
                                    placeholder="{{ __('Select Country') }}">

                                    @foreach ($countries as $country)
                                        <flux:option
                                            wire:key="country-option-{{ $country['code'] }}"
                                            value="{{ $country['name'] }}">
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
                            error="{{ $errors->first('hometown') }}">

                            <x-pupi.input.text
                                wire:model="hometown"
                                name="hometown"
                                id="hometown"
                            />

                        </x-pupi.input.group>
                    </div>

                    <!-- Religion -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Religion') }}"
                            for="religion"
                            model="religion"
                            badge="{{ __('Required') }}"
                            help-text="{{ __('') }}">

                            <flux:select
                                class="!mt-2"
                                wire:model="religion"
                                name="religion"
                                id="religion"
                                variant="listbox"
                                placeholder="{{ __('Select Religion') }}">

                                @foreach($this->religionOptions() as $religionOption)
                                    <flux:option
                                        wire:key="religion-option-{{ $religionOption['value'] }}"
                                        value="{{ $religionOption['value'] }}">
                                        <span>{{ $religionOption['label'] }}</span>
                                    </flux:option>
                                @endforeach

                            </flux:select>

                        </x-pupi.input.group>
                    </div>

                    <!-- Civil Status -->
{{--                    <div class="sm:col-span-3">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Civil Status') }}"--}}
{{--                            for="civil_status"--}}
{{--                            badge="{{ __('Required') }}"--}}
{{--                            model="civil_status"--}}
{{--                            help-text="{{ __('') }}">--}}

{{--                            <flux:select--}}
{{--                                class="!mt-2"--}}
{{--                                wire:model="civil_status"--}}
{{--                                name="civil_status"--}}
{{--                                id="civil_status"--}}
{{--                                variant="listbox"--}}
{{--                                placeholder="{{ __('Select Civil Status') }}">--}}

{{--                                @foreach($this->civilStatusOptions() as $civilStatusOption)--}}
{{--                                    <flux:option--}}
{{--                                        wire:key="civilStatus-option-{{ $civilStatusOption['value'] }}"--}}
{{--                                        value="{{ $civilStatusOption['value'] }}">--}}
{{--                                        <span>{{ $civilStatusOption['label'] }}</span>--}}
{{--                                    </flux:option>--}}
{{--                                @endforeach--}}

{{--                            </flux:select>--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

{{--                    <!-- Residence Permit -->--}}
{{--                    <div class="sm:col-span-3">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Residence Permit') }}"--}}
{{--                            for="residence_permit"--}}
{{--                            model="residence_permit"--}}
{{--                            badge="{{ __('Required') }}"--}}
{{--                            help-text="{{ __('') }}">--}}

{{--                            <flux:select--}}
{{--                                class="!mt-2"--}}
{{--                                wire:model="residence_permit"--}}
{{--                                name="residence_permit"--}}
{{--                                id="residence_permit"--}}
{{--                                variant="listbox"--}}
{{--                                placeholder="{{ __('Select Residence Permit') }}">--}}

{{--                                @foreach($this->residencePermitOptions() as $permitOption)--}}
{{--                                    <flux:option--}}
{{--                                        wire:key="residence-option-{{ $permitOption['value'] }}"--}}
{{--                                        value="{{ $permitOption['value'] }}">--}}
{{--                                        <span>{{ $permitOption['label'] }}</span>--}}
{{--                                    </flux:option>--}}
{{--                                @endforeach--}}

{{--                            </flux:select>--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

{{--                    <!-- Residence Permit Upload -->--}}
{{--                    <div class="sm:col-span-3">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Residence Permit Upload') }}"--}}
{{--                            for="residence_permit"--}}
{{--                            model="residence_permit"--}}
{{--                            badge="{{ __('Optional') }}"--}}
{{--                            help-text="{{ __('') }}">--}}
{{--                            <div class="col-span-full flex items-center gap-x-6 mt-2">--}}
{{--                                <img--}}
{{--                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"--}}
{{--                                    alt="" class="h-8 w-8 flex-none rounded-lg bg-gray-800 object-cover">--}}
{{--                                <div>--}}
{{--                                    <button type="button"--}}
{{--                                            class="rounded-md dark:bg-white/10 px-3 py-2 text-sm font-semibold dark:text-white shadow-xs dark:hover:bg-white/20 dark:ring-transparent bg-white text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">--}}
{{--                                        Change Upload--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

                </div>
            </div>
            <!-- Button Container -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </form>
    </x-slot>
</x-pupi.layout.form>
