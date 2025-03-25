<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Employee Data') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employee information below.') }}
    </x-slot:description>

    <x-slot name="form">
        <form wire:submit="updateEmployee">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <!-- Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Employee Status') }}"
                            for="employee_status"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('employee_status')"
                        >
                            <flux:select class="mt-2"
                                wire:model="employee_status"
                                id="employee_status"
                                name="employee_status"
                                variant="listbox"
                                placeholder="{{ __('Select Status') }}"
                            >
                                @foreach($employeeStatusOptions as $status)
                                    <flux:option value="{{ $status['value'] }}">
                                        <div class="inline-flex items-center">
                                             <span class="mr-2">
                                                                               <x-dynamic-component
                                                                                   :component="$status['icon']"
                                                                                   class="h-4 w-4 rounded-md {{ $status['colors'] }}"/>
                                                                            </span>
                                            {{ $status['label'] }}
                                        </div>
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Personalnummer -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Personalnummer') }}"
                            for="personal_number"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('personal_number')"
                        >
                            <x-pupi.input.text
                                wire:model="personal_number"
                                id="personal_number"
                                name="personal_number"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Anstellungsverhältnis -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Anstellungsverhältnis') }}"
                            for="employment_type"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('employment_type')"
                        >
                            <x-pupi.input.text
                                wire:model="employment_type"
                                id="employment_type"
                                name="employment_type"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Vorgesetzter -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Vorgesetzter') }}"
                            for="supervisor"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('supervisor')"
                        >
                            <x-pupi.input.text
                                wire:model="supervisor"
                                id="supervisor"
                                name="supervisor"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Eintrittsdatum -->
                    <div class="sm:col-span-2 sm:col-start-1">
                        <x-pupi.input.group
                            label="{{ __('Joined Date') }}"
                            for="joined_at"
                            model="joined_at"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('joined_at')"
                        >
                            <flux:date-picker
                                with-today
                                value="21-03-2025"
                                wire:model.defer="joined_at"
                                id="joined_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>

                    <!-- Probezeit -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Probezeit Dauer') }}"
                            for="probation_enum"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('probation_enum')"
                        >
                            <flux:select class="mt-2"
                                wire:model="probation_enum"
                                id="probation_enum"
                                name="probation_enum"
                                variant="listbox"
                                placeholder="{{ __('Select Probation Period') }}"
                            >
                                @foreach($probationOptions as $value => $label)
                                    <flux:option value="{{ $value }}">
                                        {{ $label }}
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Kündigungsdatum -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Probation End Date') }}"
                            for="probation_at"
                            model="probation_at"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('probation_at')"
                        >
                            <flux:date-picker
                                with-today
                                value="21-03-2025"
                                wire:model.defer="probation_at"
                                id="probation_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>

                    </div>

                    <div class="sm:col-span-2 sm:col-start-1">
                        <x-pupi.input.group
                            label="{{ __('Kündigung Datum') }}"
                            for="notice_at"
                            model="notice_at"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('notice_at')"
                        >
                            <flux:date-picker
                                with-today
                                value="21-03-2025"
                                wire:model.defer="notice_at"
                                id="notice_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Kündigungsfrist Dauer') }}"
                            for="notice_enum"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('notice_enum')"
                        >
                            <flux:select class="mt-2"
                                         wire:model="notice_enum"
                                         id="notice_enum"
                                         name="notice_enum"
                                         variant="listbox"
                                         placeholder="{{ __('Select Notice Period') }}"
                            >
                                @foreach($noticePeriodOptions as $value => $label)
                                    <flux:option value="{{ $value }}">
                                        {{ $label }}
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Leave Date') }}"
                            for="leave_at"
                            model="leave_at"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('leave_at')"
                        >
                            <flux:date-picker
                                with-today
                                value="21-03-2025"
                                wire:model.defer="leave_at"
                                id="leave_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>

                    <!-- Profession -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Profession') }}"
                            for="profession"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('profession')"
                            model="profession"
                            help-text="{{ __('') }}"
                        >
                            <flux:select
                                class="mt-2"
                                wire:model="profession"
                                id="profession"
                                variant="listbox"
                                searchable
                                placeholder="{{ __('Select Profession') }}"
                            >
                                @forelse($this->professions as $prof)
                                    <flux:option value="{{ $prof->id }}">
                                        <span class="truncate">{{ $prof->name }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No professions found') }}</flux:option>
                                @endforelse

                                <!-- Trigger zum Öffnen des Profession-Modals -->
                                <x-slot name="add">

                                    <livewire:alem.employee.setting.profession.profession-form
                                        lazy
                                    />

                                </x-slot>
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Stage -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Stage') }}"
                            for="stage"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('stage')"
                            model="stage"
                            help-text="{{ __('') }}"
                        >
                            <flux:select
                                class="mt-2"
                                wire:model="stage"
                                id="stage"
                                variant="listbox"
                                searchable
                                placeholder="{{ __('Select Stage') }}"
                            >
                                @forelse($this->stages as $st)
                                    <flux:option value="{{ $st->id }}">
                                        <span class="truncate">{{ $st->name }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No stages found') }}</flux:option>
                                @endforelse

                                <!-- Trigger zum Öffnen des Stage-Modals -->
                                <x-slot name="add">

                                    <livewire:alem.employee.setting.profession.stage-form
                                        lazy
                                    />

                                </x-slot>
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
