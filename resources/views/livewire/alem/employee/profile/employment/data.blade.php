<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Employment Data') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employment data.') }}
    </x-slot:description>

    <x-slot name="form">
        <form wire:submit.prevent="updatePersonalData">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 ">

                    <!-- Personal Number -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Personal Number') }}"
                            for="personal_number"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('personal_number')"
                            model="personal_number"
                            help-text="{{ __('') }}">

                            <x-pupi.input.text
                                wire:model="personal_number"
                                id="personal_number"
                                name="personal_number"
                                placeholder="{{ __('P.Nr.123') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    {{--                    <!-- Employment Type -->--}}
                    {{--                    <div class="sm:col-span-3">--}}
                    {{--                        <x-pupi.input.group--}}
                    {{--                            label="{{ __('Employment Type') }}"--}}
                    {{--                            for="employment_type"--}}
                    {{--                            badge="{{ __('Required') }}"--}}
                    {{--                            :error="$errors->first('employment_type')"--}}
                    {{--                            model="employment_type"--}}
                    {{--                            help-text="{{ __('') }}">--}}

                    {{--                            <x-pupi.input.text--}}
                    {{--                                wire:model="employment_type"--}}
                    {{--                                id="employment_type"--}}
                    {{--                                name="employment_type"--}}
                    {{--                            />--}}
                    {{--                        </x-pupi.input.group>--}}
                    {{--                    </div>--}}



                    <!-- Joined Date -->
                    <div class="sm:col-span-2 sm:col-start-1">
                        <x-pupi.input.group
                            label="{{ __('Joined Date') }}"
                            for="joined_at"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('joined_at')"
                            model="joined_at"
                            help-text="{{ __('') }}">

                            <flux:date-picker
                                with-today
                                wire:model="joined_at"
                                id="joined_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>

                    <!-- Probation Period -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Probation Period') }}"
                            for="probation_enum"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('probation_enum')"
                            model="probation_enum"
                            help-text="{{ __('') }}">

                            <flux:select class="mt-2"
                                         wire:model="probation_enum"
                                         id="probation_enum"
                                         name="probation_enum"
                                         variant="listbox"
                                         placeholder="{{ __('Select Probation Period') }}">

                                @foreach($this->probationOptions() as $probationOption)
                                    <flux:option
                                        wire:key="probation-option-{{ $probationOption['value'] }}"
                                        value="{{ $probationOption['value'] }}">
                                        <span>{{ $probationOption['label'] }}</span>
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Probation End Date -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Probation End Date') }}"
                            for="probation_at"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('probation_at')"
                            model="probation_at"
                            help-text="{{ __('') }}">

                            <flux:date-picker
                                with-today
                                wire:model="probation_at"
                                id="probation_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>

                    <!-- Notice Date -->
                    <div class="sm:col-span-2 sm:col-start-1">
                        <x-pupi.input.group
                            label="{{ __('Notice Date') }}"
                            for="notice_at"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('notice_at')"
                            model="notice_at"
                            help-text="{{ __('') }}">

                            <flux:date-picker
                                with-today
                                wire:model="notice_at"
                                id="notice_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>

                    <!-- Notice Period -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Notice Period') }}"
                            for="notice_enum"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('notice_enum')"
                            model="notice_enum"
                            help-text="{{ __('') }}">

                            <flux:select class="mt-2"
                                         wire:model="notice_enum"
                                         id="notice_enum"
                                         name="notice_enum"
                                         variant="listbox"
                                         placeholder="{{ __('Select Notice Period') }}">

                                @foreach($this->noticePeriodOptions() as $noticeOption)
                                    <flux:option
                                        wire:key="notice-option-{{ $noticeOption['value'] }}"
                                        value="{{ $noticeOption['value'] }}">
                                        <span>{{ $noticeOption['label'] }}</span>
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Leave Date -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Leave Date') }}"
                            for="leave_at"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('leave_at')"
                            model="leave_at"
                            help-text="{{ __('') }}">

                            <flux:date-picker
                                with-today
                                wire:model="leave_at"
                                id="leave_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>

                </div>
            </div>

            <!-- Form Separator -->
            <!-- Button Container -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </form>
    </x-slot>
</x-pupi.layout.form>
