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
                            <flux:select
                                wire:model="employee_status"
                                id="employee_status"
                                name="employee_status"
                                variant="listbox"
                                placeholder="{{ __('Select Status') }}"
                            >
                                @foreach($employeeStatusOptions as $status)
                                    <flux:option value="{{ $status['value'] }}">
                                        <div class="flex items-center">
                                            <div
                                                class="mr-2 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset gap-1 {{ $status['colors'] }}">
                                                <x-dynamic-component class="h-4 w-4" :component="$status['icon']"/>
                                            </div>
                                            <div>{{ $status['label'] }}</div>
                                        </div>
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Employee Status') }}"
                            for="employee_status"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('employee_status')"
                        >
                            <flux:select
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
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Eintrittsdatum') }}"
                            for="date_hired"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('date_hired')"
                        >
                            <x-pupi.input.text
                                wire:model="date_hired"
                                id="date_hired"
                                name="date_hired"
                                type="date"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Austrittsdatum (optional) -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Austrittsdatum') }}"
                            for="date_fired"
                            :error="$errors->first('date_fired')"
                        >
                            <x-pupi.input.text
                                wire:model="date_fired"
                                id="date_fired"
                                name="date_fired"
                                type="date"
                                placeholder="{{ __('Optional') }}"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Probezeit Datum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Probezeit Datum') }}"
                            for="probation"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('probation')"
                        >
                            <x-pupi.input.text
                                wire:model="probation"
                                id="probation"
                                name="probation"
                                type="date"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Kündigungsfrist Datum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Kündigungsfrist Datum') }}"
                            for="notice_period"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('notice_period')"
                        >
                            <x-pupi.input.text
                                wire:model="notice_period"
                                id="notice_period"
                                name="notice_period"
                                type="date"
                            />
                        </x-pupi.input.group>
                    </div>

                    <!-- Probezeit Enum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Probezeit Dauer') }}"
                            for="probation_enum"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('probation_enum')"
                        >
                            <flux:select
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

                    <!-- Kündigungsfrist Enum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Kündigungsfrist Dauer') }}"
                            for="notice_period_enum"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('notice_period_enum')"
                        >
                            <flux:select
                                wire:model="notice_period_enum"
                                id="notice_period_enum"
                                name="notice_period_enum"
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
                                    <flux:modal.trigger name="create-profession">
                                        <flux:separator class="mt-2 mb-1"/>
                                        <flux:button
                                            icon="plus"
                                            class="w-full rounded-b-lg rounded-t-none"
                                            variant="filled">
                                            {{ __('Create Profession') }}
                                        </flux:button>
                                    </flux:modal.trigger>
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
                                    <flux:modal.trigger name="create-stage">
                                        <flux:separator class="mt-2 mb-1"/>
                                        <flux:button
                                            icon="plus"
                                            class="w-full rounded-b-lg rounded-t-none"
                                            variant="filled">
                                            {{ __('Create Stage') }}
                                        </flux:button>
                                    </flux:modal.trigger>
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
