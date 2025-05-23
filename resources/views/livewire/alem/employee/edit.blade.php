<div>
    <flux:modal
        name="edit-employee"
        variant="flyout"
        position="left"
        class="space-y-6 lg:min-w-3xl"
    >
        <div>
            <flux:heading size="lg">{{ __('Edit Employee') }}</flux:heading>
            <flux:subheading>{{ __('Update employee information') }}</flux:subheading>
        </div>

        <!-- Form: User & Employee Data -->
        <form wire:submit.prevent="updateEmployee">

            <div wire:loading
                 wire:target.except="updateEmployee"
                 class="space-y-4 relative w-full inset-0 backdrop-blur-sm z-10 overflow-auto">

                <!-- Personal Information Skeleton -->
                <div class="py-4">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                        <!-- Gender -->
                        <div class="sm:col-span-4">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- First Name -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Last Name -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Teams -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Department -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Supervisor -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Roles -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Profession -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Stage -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Joined Date -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                    </div>
                </div>

                <!-- Status Section -->
                <div class="py-4 border-t border-gray-200 dark:border-white/10">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                        <!-- Employee Status -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                        <!-- Account Status -->
                        <div class="sm:col-span-3">
                            <x-pupi.placeholder.input/>
                        </div>

                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                    <x-pupi.placeholder.buttons/>
                </div>

            </div>

            <!-- Personal Information Section -->
            <div wire:loading.remove
                 wire:target.except="updateEmployee"
                 class="space-y-4 relative"
            >
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
                                <div class="relative">

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

                                </div>
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

                                <div class="relative">
                                    <x-pupi.input.text
                                        wire:model="name"
                                        id="name"
                                        placeholder="{{ __('Enter name') }}"
                                    />
                                </div>

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

                                <x-pupi.input.text
                                    wire:model="last_name"
                                    id="last_name"
                                    placeholder="{{ __('Enter last name') }}"
                                />

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
                                    placeholder="{{ __('meine@email.ch') }}"
                                />

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

                                    @forelse($this->teams() as $team)
                                        <flux:option
                                            wire:key="team-option-{{ $team->id }}"
                                            value="{{ $team->id }}">
                                            <span class="truncate">{{ $team->name }}</span>
                                        </flux:option>
                                    @empty
                                        <flux:option value="">{{ __('No teams found') }}</flux:option>
                                    @endforelse

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
                                    placeholder="{{ __('Select Department') }}">

                                    @forelse($this->departments() as $dept)
                                        <flux:option
                                            wire:key="department-option-{{ $dept->id }}"
                                            value="{{ $dept->id }}">
                                            <span class="truncate">{{ $dept->name }}</span>
                                        </flux:option>
                                    @empty
                                        <flux:option value="">{{ __('No departments found') }}</flux:option>
                                    @endforelse
                                    <!-- Trigger zum Öffnen des Profession-Modals -->
                                    <x-slot name="add">
                                        <livewire:alem.department.create-department
                                            lazy
                                        />
                                    </x-slot>
                                </flux:select>

                            </x-pupi.input.group>
                        </div>

                        <!-- Supervisor -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Supervisor') }}"
                                for="supervisor"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('supervisor')"
                                model="supervisor"
                                help-text="{{ __('') }}">

                                <flux:select
                                    class="mt-2"
                                    wire:model="supervisor"
                                    id="supervisor"
                                    variant="listbox"
                                    searchable
                                    placeholder="{{ __('Select Supervisor') }}">

                                    @forelse($this->supervisors() as $supervisor)
                                        <flux:option wire:key="supervisor-option-{{ $supervisor->id }}"
                                                     value="{{ $supervisor->id }}">
                                            <div class="flex items-center gap-2 whitespace-nowrap">
                                                <flux:avatar
                                                    name="{{ $supervisor->name }} {{ $supervisor->last_name }}"
                                                    circle size="xs"
                                                    src="{{ $supervisor->profile_photo_path ? asset('storage/' . $supervisor->profile_photo_path) : null }}"
                                                    alt="{{ $supervisor->name }}"
                                                />
                                                {{ $supervisor->name }} {{ $supervisor->last_name }}
                                            </div>
                                        </flux:option>
                                    @empty
                                        <flux:option value="">{{ __('No supervisors found') }}</flux:option>
                                    @endforelse

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

                                    @forelse($this->roles() as $roleOption)
                                        <flux:option
                                            wire:key="role-option-{{ $roleOption->id }}"
                                            value="{{ $roleOption->id }}">
                                            {{ __($roleOption->name) }}
                                            @if($roleOption->is_manager)
                                                <span
                                                    class="ml-4 inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-500/10 dark:text-green-400">
                                                {{ __('Manager') }}
                                            </span>
                                            @endif
                                        </flux:option>
                                    @empty
                                        <flux:option value="">{{ __('No roles found') }}</flux:option>
                                    @endforelse

                                </flux:select>
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
                                help-text="{{ __('') }}">

                                <flux:select
                                    class="mt-2"
                                    wire:model="profession"
                                    id="profession"
                                    variant="listbox"
                                    searchable
                                    placeholder="{{ __('Select Profession') }}">

                                    @forelse($this->professions() as $prof)
                                        <flux:option
                                            wire:key="profession-option-{{ $prof->id }}"
                                            value="{{ $prof->id }}">
                                            <span class="truncate">{{ $prof->name }}</span>
                                        </flux:option>
                                    @empty
                                        <flux:option value="">{{ __('No professions found') }}</flux:option>
                                    @endforelse
                                    <!-- Trigger zum Öffnen des Profession-Modals -->
                                    <x-slot name="add">
                                        <livewire:alem.quick-crud.profession.profession-form
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
                                help-text="{{ __('') }}">

                                <flux:select
                                    class="mt-2"
                                    wire:model="stage"
                                    id="stage"
                                    variant="listbox"
                                    searchable
                                    placeholder="{{ __('Select Stage') }}">

                                    @forelse($this->stages() as $st)
                                        <flux:option
                                            wire:key="stage-option-{{ $st->id }}"
                                            value="{{ $st->id }}">
                                            <span class="truncate">{{ $st->name }}</span>
                                        </flux:option>
                                    @empty
                                        <flux:option value="">{{ __('No stages found') }}</flux:option>
                                    @endforelse
                                    <!-- Trigger zum Öffnen des Stage-Modals -->
                                    <x-slot name="add">
                                        <livewire:alem.quick-crud.stage.stage-form
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
                                :error="$errors->first('joined_at')">

                                <flux:date-picker
                                    wire:model="joined_at"
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

                                    @foreach($this->employeeStatusOptions() as $statusOption)
                                        <flux:option
                                            wire:key="employee-status-option-{{ $statusOption['value'] }}"
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

                        <!-- Model Status -->
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

                                    @foreach($this->modelStatusOptionsForForms() as $statusOption)
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

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                    <flux:button wire:click="closeEditEmployeeModal" type="button" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit">
                        {{ __('Update') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
