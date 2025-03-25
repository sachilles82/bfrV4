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

                    <!-- Personalnummer -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Personal Number') }}"
                            for="personal_number"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('personal_number')"
                            model="personal_number"
                            help-text="{{ __('') }}"
                        >
                            <x-pupi.input.text
                                wire:model="personal_number"
                                id="personal_number"
                                name="personal_number"
                                placeholder="{{ __('P.Nr.123') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Profession -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Profession') }}"
                            for="profession"
                            badge="{{ __('Required') }}"
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
                            badge="{{ __('Required') }}"
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

                    <!-- Anstellungsverhältnis -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Employment Type') }}"
                            for="employment_type"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('employment_type')"
                            model="employment_type"
                            help-text="{{ __('') }}"
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
                            label="{{ __('Supervisor') }}"
                            for="supervisor"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('supervisor')"
                            model="supervisor"
                            help-text="{{ __('') }}"
                        >
                            <flux:select
                                class="mt-2"
                                wire:model="supervisor"
                                id="supervisor"
                                variant="listbox"
                                searchable
                                placeholder="{{ __('Select Supervisor') }}"
                            >
                                @forelse($supervisors as $sup)
                                    <flux:option value="{{ $sup->id }}">
                                        <div class="inline-flex items-center">
                                            @if($sup->profile_photo_url)
                                                <img src="{{ $sup->profile_photo_url }}" class="h-6 w-6 rounded-full mr-2" alt="{{ $sup->name }}">
                                            @else
                                                <div class="h-6 w-6 rounded-full bg-gray-200 mr-2 flex items-center justify-center">
                                                    <span class="text-xs">{{ substr($sup->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <span class="truncate">{{ $sup->name }} {{ $sup->last_name }}</span>
                                        </div>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No supervisors found') }}</flux:option>
                                @endforelse
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Eintrittsdatum -->
                    <div class="sm:col-span-2 sm:col-start-1">
                        <x-pupi.input.group
                            label="{{ __('Joined Date') }}"
                            for="joined_at"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('joined_at')"
                            model="joined_at"
                            help-text="{{ __('') }}"
                        >
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

                    <!-- Probezeit -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Probation Period') }}"
                            for="probation_enum"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('probation_enum')"
                            model="probation_enum"
                            help-text="{{ __('') }}"
                        >
                            <flux:select class="mt-2"
                                         wire:model="probation_enum"
                                         id="probation_enum"
                                         name="probation_enum"
                                         variant="listbox"
                                         searchable
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

                    <!-- Probation End Date -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Probation End Date') }}"
                            for="probation_at"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('probation_at')"
                            model="probation_at"
                            help-text="{{ __('') }}"
                        >
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
                            help-text="{{ __('') }}"
                        >
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
                            help-text="{{ __('') }}"
                        >
                            <flux:select class="mt-2"
                                         wire:model="notice_enum"
                                         id="notice_enum"
                                         name="notice_enum"
                                         variant="listbox"
                                         searchable
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

                    <!-- Leave Date -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group
                            label="{{ __('Leave Date') }}"
                            for="leave_at"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('leave_at')"
                            model="leave_at"
                            help-text="{{ __('') }}"
                        >
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

                    <!-- Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Employee Status') }}"
                            for="employee_status"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('employee_status')"
                            model="employee_status"
                            help-text="{{ __('') }}"
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


                </div>
            </div>
            <!-- Button Container -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </form>
    </x-slot>
</x-pupi.layout.form>
