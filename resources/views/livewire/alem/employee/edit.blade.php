<div
    x-data="{}"
    @open-edit-employee-modal.window="$wire.editEmployee($event.detail.userId)"
>
    <flux:modal
        name="edit-employee"
        variant="flyout"
        position="left"
        class="space-y-6 lg:min-w-3xl"
        wire:model="showEditModal"
    >
        <div>
            <flux:heading size="lg">{{ __('Edit Employee') }}</flux:heading>
            <flux:subheading>{{ __('Update employee information') }}</flux:subheading>
        </div>

        <!-- Form: User & Employee Data -->
        <form wire:submit.prevent="updateEmployee" class="space-y-4 relative">
            <!-- Loading Overlay -->
            {{--            <div--}}
            {{--                wire:loading--}}
            {{--                class="absolute inset-0 z-10 flex items-center justify-center"--}}
            {{--bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm rounded-lg"--}}
            {{--            >--}}
            {{--                <!-- Centered Spinner -->--}}
            {{--                <svg class="animate-spin h-12 w-12 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">--}}
            {{--                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>--}}
            {{--                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>--}}
            {{--                </svg>--}}
            {{--                <div class="py-4">--}}
            {{--                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">--}}
            {{--                        <div class="sm:col-span-4">--}}
            {{--                            <div label="{{ __('Gender') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('First Name') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Last Name') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Email') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Teams') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Department') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Supervisor') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Roles') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Profession') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Stage') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Joined Date') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}

            {{--                <div class="py-4 border-t border-gray-200 dark:border-white/10">--}}
            {{--                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">--}}
            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Employee Status') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}

            {{--                        <div class="sm:col-span-3">--}}
            {{--                            <div label="{{ __('Account Status') }}">--}}
            {{--                                <div class="mt-2 h-10 w-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700"></div>--}}
            {{--                            </div>--}}
            {{--                        </div>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}

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
                            {{--                            <flux:select--}}
                            {{--                                class="mt-2"--}}
                            {{--                                wire:model="gender"--}}
                            {{--                                id="gender"--}}
                            {{--                                variant="listbox"--}}
                            {{--                                placeholder="{{ __('Select gender') }}">--}}
                            {{--                                @foreach(\App\Enums\User\Gender::cases() as $genderStatus)--}}
                            {{--                                    <flux:option value="{{ $genderStatus->value }}">--}}
                            {{--                                        {{ $genderStatus->label() }}--}}
                            {{--                                    </flux:option>--}}
                            {{--                                @endforeach--}}
                            {{--                            </flux:select>--}}
                            <div class="relative">
                                <flux:select
                                    class="mt-2"
                                    wire:model="gender"
                                    id="gender"
                                    variant="listbox"
                                    placeholder="{{ __('Select gender') }}">
                                    @foreach(\App\Enums\User\Gender::cases() as $genderStatus)
                                        <flux:option value="{{ $genderStatus->value }}">
                                            {{ $genderStatus->label() }}
                                        </flux:option>
                                    @endforeach
                                </flux:select>

                                <div
                                    wire:loading
                                    wire:target="editEmployee"
                                    class="absolute inset-0  flex items-center justify-center">
                                    <div class="w-full h-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 block px-3 py-1.5 text-base dark:text-white text-gray-900 outline-1 -outline-offset-1 outline-gray-300 dark:outline-white/10 "></div>
                                </div>
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
                                <x-pupi.input.text wire:model="name" id="name" placeholder="{{ __('Enter name') }}"/>
                                <div
                                    wire:loading
                                    wire:target="editEmployee"
                                    class="absolute inset-0  flex items-center justify-center">
                                    <div class="w-full h-full animate-pulse rounded-md bg-gray-200 dark:bg-gray-700 block px-3 py-1.5 text-base dark:text-white text-gray-900 outline-1 -outline-offset-1 outline-gray-300 dark:outline-white/10 "></div>
                                </div>
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
                                @forelse($this->teams ?? [] as $team)
                                    <flux:option value="{{ $team->id }}">{{ $team->name }}</flux:option>
                                @empty
                                    <flux:option value="">{{ __('No Teams found') }}</flux:option>
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
                                @forelse($this->departments ?? [] as $department)
                                    <flux:option value="{{ $department->id }}">
                                        <span class="truncate">{{ $department->name }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No Departments found') }}</flux:option>
                                @endforelse
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Supervisor -->
{{--                    <div class="sm:col-span-3">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Supervisor') }}"--}}
{{--                            for="supervisor"--}}
{{--                            badge="{{ __('Required') }}"--}}
{{--                            :error="$errors->first('supervisor')"--}}
{{--                            model="supervisor"--}}
{{--                            help-text="{{ __('') }}">--}}
{{--                            <flux:select--}}
{{--                                class="mt-2"--}}
{{--                                wire:model="supervisor"--}}
{{--                                id="supervisor"--}}
{{--                                variant="listbox"--}}
{{--                                searchable--}}
{{--                                placeholder="{{ __('Select Supervisor') }}">--}}
{{--                                @forelse($this->supervisors ?? [] as $supervisor)--}}
{{--                                    <flux:option value="{{ $supervisor->id }}">--}}
{{--                                        <div class="flex items-center gap-2 whitespace-nowrap">--}}
{{--                                            <flux:avatar--}}
{{--                                                name="{{ $supervisor->name }} {{ $supervisor->last_name }}"--}}
{{--                                                circle--}}
{{--                                                size="xs"--}}
{{--                                                src="{{ $supervisor->profile_photo_path ? asset('storage/' . $supervisor->profile_photo_path) : null }}"--}}
{{--                                                alt="{{ $supervisor->name }}"--}}
{{--                                            />--}}
{{--                                            {{ $supervisor->name }} {{ $supervisor->last_name }}--}}
{{--                                        </div>--}}
{{--                                    </flux:option>--}}
{{--                                @empty--}}
{{--                                    <flux:option value="">{{ __('No supervisors found') }}</flux:option>--}}
{{--                                @endforelse--}}
{{--                            </flux:select>--}}
{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

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
                                @forelse($this->roles ?? [] as $roleOption)
                                    <flux:option value="{{ $roleOption->id }}">
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
                                @forelse($this->professions ?? [] as $prof)
                                    <flux:option value="{{ $prof->id }}">
                                        <span class="truncate">{{ $prof->name }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No professions found') }}</flux:option>
                                @endforelse
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
                                @forelse($this->stages ?? [] as $st)
                                    <flux:option value="{{ $st->id }}">
                                        <span class="truncate">{{ $st->name }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No stages found') }}</flux:option>
                                @endforelse
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
                                @foreach($employeeStatusOptions as $statusOption)
                                    <flux:option value="{{ $statusOption['value'] }}">
                                        <div class="flex items-center">
                                            <span class="mr-2">
                                                <x-dynamic-component
                                                    :component="$statusOption['icon']"
                                                    class="h-5 w-5 rounded-md {{ $statusOption['colors'] ?? '' }}"/>
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
                            model="model_status"
                        >
                            <flux:select
                                class="mt-2"
                                wire:model="model_status"
                                id="model_status"
                                variant="listbox">
                                @foreach($modelStatusOptions as $statusOption)
                                    <flux:option value="{{ $statusOption['value'] }}">
                                        <div class="flex items-center">
                                            <span class="mr-2">
                                                <x-dynamic-component
                                                    :component="$statusOption['icon']"
                                                    class="{{ $statusOption['colors'] ?? '' }}"/>
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
                <flux:button
                    wire:click="closeModal"
                    type="button"
                    variant="ghost"
                    wire:loading.attr="disabled"
                    wire:target="updateEmployee"
                >
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button
                    type="submit"
                    variant="primary"
                    wire:loading.attr="disabled"
                    wire:target="updateEmployee"
                >
                    <div wire:loading.flex wire:target="updateEmployee" class="items-center">
                        <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Saving...') }}
                    </div>
                    <span wire:loading.remove wire:target="updateEmployee">{{ __('Update') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
