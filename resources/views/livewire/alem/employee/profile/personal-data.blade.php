<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Personal Data') }}
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
                        <x-pupi.input.group label="{{ __('Employee Status') }}" for="employee_status"
                                            badge="{{ __('Required') }}" :error="$errors->first('employee_status')">
                            <flux:select wire:model="employee_status" id="employee_status" name="employee_status"
                                         variant="listbox" placeholder="{{ __('Select Status') }}">
                                @foreach($employeeStatusOptions as $status)
                                    <flux:option value="{{ $status['value'] }}">
                                        <div class="flex items-center">
                                            <span class="mr-2">
                                                <svg viewBox="0 0 6 6" class="h-1.5 w-1.5 {{ $status['dotColor'] }}">
                                                    <circle cx="3" cy="3" r="3"></circle>
                                                </svg>
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
                        <x-pupi.input.group label="{{ __('Personalnummer') }}" for="personal_number" badge="{{ __('Required') }}" :error="$errors->first('personal_number')">
                            <x-pupi.input.text wire:model="personal_number" id="personal_number" name="personal_number" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Anstellungsverhältnis -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group label="{{ __('Anstellungsverhältnis') }}" for="employment_type" badge="{{ __('Required') }}" :error="$errors->first('employment_type')">
                            <x-pupi.input.text wire:model="employment_type" id="employment_type" name="employment_type" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Team Select -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Team') }}" for="team" badge="{{ __('Required') }}" :error="$errors->first('team')">
                            <flux:select wire:model="team" id="team" name="team" variant="listbox" placeholder="{{ __('Select Team') }}">
                                @foreach($availableTeams as $availableTeam)
                                    <flux:option value="{{ $availableTeam->id }}">
                                        {{ $availableTeam->name }}
                                        @if($employee->user->current_team_id == $availableTeam->id)
                                            ({{ __('Current') }})
                                        @endif
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Vorgesetzter -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Vorgesetzter') }}" for="supervisor" badge="{{ __('Required') }}" :error="$errors->first('supervisor')">
                            <x-pupi.input.text wire:model="supervisor" id="supervisor" name="supervisor" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Eintrittsdatum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Eintrittsdatum') }}" for="date_hired" badge="{{ __('Required') }}" :error="$errors->first('date_hired')">
                            <x-pupi.input.text wire:model="date_hired" id="date_hired" name="date_hired" type="date" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Austrittsdatum (optional) -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Austrittsdatum') }}" for="date_fired" :error="$errors->first('date_fired')">
                            <x-pupi.input.text wire:model="date_fired" id="date_fired" name="date_fired" type="date" placeholder="{{ __('Optional') }}" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Probezeit Datum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Probezeit Datum') }}" for="probation" badge="{{ __('Required') }}" :error="$errors->first('probation')">
                            <x-pupi.input.text wire:model="probation" id="probation" name="probation" type="date" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Kündigungsfrist Datum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Kündigungsfrist Datum') }}" for="notice_period" badge="{{ __('Required') }}" :error="$errors->first('notice_period')">
                            <x-pupi.input.text wire:model="notice_period" id="notice_period" name="notice_period" type="date" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Probezeit Enum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Probezeit Dauer') }}" for="probation_enum" badge="{{ __('Required') }}" :error="$errors->first('probation_enum')">
                            <flux:select wire:model="probation_enum" id="probation_enum" name="probation_enum" variant="listbox" placeholder="{{ __('Select Probation Period') }}">
                                @foreach($probationOptions as $value => $label)
                                    <flux:option value="{{ $value }}">
                                        {{ $label }}
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Kündigungsfrist Enum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Kündigungsfrist Dauer') }}" for="notice_period_enum" badge="{{ __('Required') }}" :error="$errors->first('notice_period_enum')">
                            <flux:select wire:model="notice_period_enum" id="notice_period_enum" name="notice_period_enum" variant="listbox" placeholder="{{ __('Select Notice Period') }}">
                                @foreach($noticePeriodOptions as $value => $label)
                                    <flux:option value="{{ $value }}">
                                        {{ $label }}
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                </div>
            </div>
            <!-- Button Container -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit />
            </x-pupi.button.container>
        </form>
    </x-slot>
</x-pupi.layout.form>
