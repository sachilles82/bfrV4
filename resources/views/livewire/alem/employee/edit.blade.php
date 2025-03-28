<div wire:ignore.self>
    <flux:modal name="edit-employee" variant="flyout" position="left" class="space-y-6 lg:min-w-3xl">
        <div>
            <flux:heading size="lg">{{ __('Edit Employee') }}</flux:heading>
            <flux:subheading>{{ __('Update employee information') }}</flux:subheading>
        </div>

        <!-- Formular: User- & Employee-Daten -->
        <form wire:submit.prevent="updateEmployee" class="space-y-4">
            <!-- Personal Information Section -->
            <div class="py-4">
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
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
                                @foreach(\App\Enums\User\Gender::cases() as $genderStatus)
                                    <flux:option value="{{ $genderStatus }}">
                                        {{ $genderStatus->label() }}
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- First Name -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('First Name') }}"
                            for="name"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('name')"
                            help-text="{{ __('') }}"
                            model="name">
                            <x-pupi.input.text wire:model="name" id="name" placeholder="{{ __('Enter name') }}"/>
                        </x-pupi.input.group>
                    </div>

                    <!-- Last Name -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Last Name') }}"
                            for="last_name"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('last_name')"
                            help-text="{{ __('') }}"
                            model="last_name">
                            <x-pupi.input.text wire:model="last_name" id="last_name"
                                               placeholder="{{ __('Enter last name') }}"/>
                        </x-pupi.input.group>
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Email') }}"
                            for="email"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('email')"
                            help-text="{{ __('') }}"
                            model="email">
                            <x-pupi.input.text
                                wire:model="email"
                                type="email"
                                name="email"
                                id="email"
                                placeholder="{{ __('meine@email.ch') }}"/>
                        </x-pupi.input.group>
                    </div>

                    <!-- Supervisor -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Supervisor') }}"
                            for="supervisor_id"
                            badge="{{ __('Optional') }}"
                            :error="$errors->first('supervisor_id')"
                            model="supervisor_id"
                            help-text="{{ __('') }}">
                            <flux:select
                                class="mt-2"
                                wire:model="supervisor_id"
                                id="supervisor_id"
                                variant="listbox"
                                searchable
                                placeholder="{{ __('Select Supervisor') }}">
                                @foreach($this->supervisors as $supervisor)
                                    <flux:option value="{{ $supervisor->id }}">
                                        <div class="flex items-center gap-2 whitespace-nowrap">
                                            <flux:avatar
                                                name="{{ $supervisor->name }} {{ $supervisor->last_name }}"
                                                circle
                                                size="xs"
                                                src="{{ $supervisor->profile_photo_path ? asset('storage/' . $supervisor->profile_photo_path) : null }}"
                                                alt="{{ $supervisor->name }}"
                                            />
                                            {{ $supervisor->name }} {{ $supervisor->last_name }}
                                        </div>
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Roles -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Roles') }}"
                            for="selectedRoles"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('selectedRoles')"
                            model="selectedRoles">
                            <flux:select
                                class="mt-2"
                                wire:model="selectedRoles"
                                id="selectedRoles"
                                variant="listbox"
                                multiple
                                placeholder="{{ __('Select roles') }}">
                                @foreach($this->roles as $roleOption)
                                    <flux:option
                                        value="{{ $roleOption->id }}">{{ __($roleOption->name) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Teams -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Teams') }}"
                            for="selectedTeams"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('selectedTeams')"
                            model="selectedTeams"
                            help-text="{{ __('') }}">
                            <flux:select
                                class="mt-2"
                                wire:model="selectedTeams"
                                id="selectedTeams"
                                variant="listbox"
                                multiple
                                placeholder="{{ __('Teams auswählen') }}">
                                @foreach($this->teams as $team)
                                    <flux:option value="{{ $team->id }}">{{ $team->name }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Department -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Department') }}"
                            for="department"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('department')"
                            model="department"
                            help-text="{{ __('') }}">
                            <flux:select
                                class="mt-2"
                                wire:model="department"
                                id="department"
                                variant="listbox"
                                searchable
                                placeholder="{{ __('Department auswählen') }}">
                                @forelse($this->departments as $department)
                                    <flux:option value="{{ $department->id }}">
                                        <span class="truncate">{{ $department->name }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No Departments found') }}</flux:option>
                                @endforelse

                                <!-- Trigger zum Öffnen des Department-Modals -->
                                <x-slot name="add">
                                    <livewire:alem.department.create-department
                                        lazy
                                    />
                                </x-slot>
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
                            help-text="{{ __('') }}">
                            <flux:select
                                class="mt-2"
                                wire:model="profession"
                                id="profession"
                                variant="listbox"
                                searchable
                                placeholder="{{ __('Select Profession') }}">
                                @forelse($this->professions as $prof)
                                    <flux:option value="{{ $prof->id }}">
                                        <span class="truncate">{{ $prof->name }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No professions found') }}</flux:option>
                                @endforelse

                                <!-- Trigger zum Öffnen des Profession-Modals -->
                                <x-slot name="add">
                                    <livewire:alem.employee.setting.profession.profession-form lazy/>
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
                            help-text="{{ __('') }}">
                            <flux:select
                                class="mt-2"
                                wire:model="stage"
                                id="stage"
                                variant="listbox"
                                searchable
                                placeholder="{{ __('Select Stage') }}">
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

                    <!-- Joined Date -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Joined Date') }}"
                            for="joined_at"
                            badge="{{ __('Required') }}"
                            model="joined_at"
                            :error="$errors->first('joined_at')"
                        >
                            <flux:date-picker
                                wire:model.defer="joined_at"
                                with-today
                                week-numbers
                                id="joined_at"
                                type="date">
                                <x-slot name="trigger">
                                    <flux:date-picker.input class="mt-2"/>
                                </x-slot>
                            </flux:date-picker>
                        </x-pupi.input.group>
                    </div>
                </div>
            </div>

            <!-- Status Section -->
            <div class="py-4 border-t border-gray-200 dark:border-white/10">
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <!-- Employee Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Employee Status') }}"
                            for="employee_status"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('employee_status')"
                            model="employee_status"
                            help-text="{{ __('') }}">
                            <flux:select
                                class="mt-2"
                                wire:model="employee_status"
                                id="employee_status"
                                variant="listbox">
                                @foreach(\App\Enums\Employee\EmployeeStatus::cases() as $empStatus)
                                    <flux:option value="{{ $empStatus->value }}">
                                        <div class="flex items-center">
                                            <span class="mr-2">
                                                <x-dynamic-component
                                                    :component="$empStatus->icon()"
                                                    class="h-4 w-4 {{ $empStatus->colors() ?? '' }}"/>
                                            </span>
                                            <span>{{ $empStatus->label() }}</span>
                                        </div>
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Model Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Account Status') }}"
                            for="model_status"
                            badge="{{ __('Required') }}"
                            model="model_status"
                            help-text="{{ __('') }}"
                            :error="$errors->first('model_status')">
                            <flux:select
                                wire:model="model_status"
                                id="model_status"
                                name="model_status"
                                variant="listbox"
                                placeholder="{{ __('Account Status') }}">
                                @foreach($this->modelStatusOptions as $status)
                                    <flux:option value="{{ $status['value'] }}">
                                        <div class="flex items-center">
                                            <span class="mr-2">
                                                <x-dynamic-component
                                                    :component="$status['icon'] ?? 'heroicon-o-question-mark-circle'"
                                                    class="h-4 w-4 {{ $status['colors'] ?? '' }}"/>
                                            </span>
                                            <span>{{ $status['label'] }}</span>
                                        </div>
                                    </flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                <flux:button wire:click="closeModal" type="button" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit">
                    {{ __('Update') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
