<x-pupi.layout.form>

    <x-slot:title>
        {{ __('Team Member Information') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the team member information.') }}
    </x-slot:description>

    <x-slot name="form">
        <form wire:submit.prevent="updateMemberInformation">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

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

                                @forelse($teams as $team)
                                    <flux:option
                                        wire:key="team-option-{{ $team['id'] }}"
                                        value="{{ $team['id'] }}">
                                        <span class="truncate">{{ $team['name'] }}</span>
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

                                @forelse($departments as $dept)
                                    <flux:option
                                        wire:key="department-option-{{ $dept['id'] }}"
                                        value="{{ $dept['id'] }}">
                                        <span class="truncate">{{ $dept['name'] }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No departments found') }}</flux:option>
                                @endforelse

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

                                @forelse($supervisors as $supervisor)
                                    <flux:option
                                        wire:key="supervisor-option-{{ $supervisor['id'] }}"
                                        value="{{ $supervisor['id'] }}">
                                        <div class="flex items-center gap-2 whitespace-nowrap">
                                            <flux:avatar
                                                name="{{ $supervisor['full_name'] }}"
                                                circle
                                                size="xs"
                                                src="{{ isset($supervisor['profile_photo_path']) && $supervisor['profile_photo_path'] ? asset('storage/' . $supervisor['profile_photo_path']) : null }}"
                                                alt="{{ $supervisor['full_name'] }}"
                                            />
                                            {{ $supervisor['full_name'] }}
                                        </div>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No supervisors found') }}</flux:option>
                                @endforelse

                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Role -->
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

                                @forelse($roles as $roleOption)
                                    <flux:option
                                        wire:key="role-option-{{ $roleOption['id'] }}"
                                        value="{{ $roleOption['id'] }}">
                                        {{ __($roleOption['name']) }}
                                        @if(isset($roleOption['is_manager']) && $roleOption['is_manager'])
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

                                @forelse($professions as $prof)
                                    <flux:option
                                        wire:key="profession-option-{{ $prof['id'] }}"
                                        value="{{ $prof['id'] }}">
                                        <span class="truncate">{{ $prof['name'] }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No professions found') }}</flux:option>
                                @endforelse

                                <x-slot name="add">

                                    <flux:modal.trigger
                                        name="create-profession"
                                        @click="$dispatch('open-profession-manager')"
                                    >
                                        <x-pupi.button.open-manager/>
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

                                @forelse($stages as $st)
                                    <flux:option
                                        wire:key="stage-option-{{ $st['id'] }}"
                                        value="{{ $st['id'] }}">
                                        <span class="truncate">{{ $st['name'] }}</span>
                                    </flux:option>
                                @empty
                                    <flux:option value="">{{ __('No stages found') }}</flux:option>
                                @endforelse

                                <x-slot name="add">

                                    <flux:modal.trigger
                                        name="create-stage"
                                        @click="$dispatch('open-stage-manager')"
                                    >
                                        <x-pupi.button.open-manager/>
                                    </flux:modal.trigger>

                                </x-slot>

                            </flux:select>

                        </x-pupi.input.group>
                    </div>

                    <!-- Employee Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Employee Status') }}"
                            for="status"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('status')"
                            model="status"
                            help-text="{{ __('') }}">

                            <flux:select
                                class="mt-2"
                                wire:model="status"
                                id="status"
                                variant="listbox">

                                @foreach($this->employeeStatusOptions() as $statusOption)
                                    <flux:option
                                        wire:key="employeeDetails-status-option-{{ $statusOption['value'] }}"
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

            <!-- Button Container -->
            <x-pupi.button.container>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </form>
    </x-slot>

</x-pupi.layout.form>
