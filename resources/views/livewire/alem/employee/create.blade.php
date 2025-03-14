<div wire:ignore.self>
    <flux:modal name="create-employee" variant="flyout" position="left" class="space-y-6" wire:model="showModal">
        <div>
            <flux:heading size="lg">{{ __('Create Employee') }}</flux:heading>
            <flux:subheading>{{ __('Fill out the details to create a new employee') }}</flux:subheading>
        </div>

        <!-- Formular: User- & Employee-Daten -->
        <form wire:submit.prevent="saveEmployee" class="space-y-4">

            <div class="py-4">
                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <x-pupi.input.group label="{{ __('Gender') }}" for="gender" badge="{{ __('Required') }}" :error="$errors->first('gender')">
                            <flux:select wire:model="gender" id="gender" variant="listbox" placeholder="{{ __('Select gender') }}">
                                @foreach($this->genders as $gender)
                                    <flux:option value="{{ $gender['value'] }}">{{ __($gender['name']) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('First Name') }}" for="name" badge="{{ __('Required') }}" :error="$errors->first('name')">
                            <x-pupi.input.text wire:model.lazy="name" id="name" placeholder="{{ __('Enter name') }}" />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Last Name') }}" for="last_name" badge="{{ __('Required') }}" :error="$errors->first('last_name')">
                            <x-pupi.input.text wire:model.lazy="last_name" id="last_name" placeholder="{{ __('Enter last name') }}" />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Email') }}" for="email" badge="{{ __('Required') }}" :error="$errors->first('email')">
                            <x-pupi.input.text wire:model.lazy="email" id="email" placeholder="{{ __('Enter email') }}" />
                        </x-pupi.input.group>
                    </div>
                    <div class="sm:col-span-2">
                        <x-pupi.input.group label="{{ __('Password') }}" for="password" badge="{{ __('Required') }}" :error="$errors->first('password')">
                            <x-pupi.input.text wire:model.lazy="password" id="password" type="password" placeholder="{{ __('Enter password') }}" />
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Role') }}" for="role" badge="{{ __('Required') }}" :error="$errors->first('role')">
                            <flux:select wire:model="role" id="role" variant="listbox" placeholder="{{ __('Select role') }}">
                                @foreach($this->roles as $roleOption)
                                    <flux:option value="{{ $roleOption->name }}">{{ __($roleOption->name) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Teams') }}" for="selectedTeams" :error="$errors->first('selectedTeams')">
                            <flux:select wire:model="selectedTeams" id="selectedTeams" variant="listbox" multiple placeholder="{{ __('Teams auswählen') }}">
                                @foreach($this->teams as $team)
                                    <flux:option value="{{ $team->id }}">{{ $team->name }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                    <x-pupi.input.group label="{{ __('Profession') }}" for="profession" badge="{{ __('Required') }}" :error="$errors->first('profession')">
                        <flux:select searchable wire:model="profession" id="profession" variant="listbox" placeholder="{{ __('Select Profession') }}">
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

                    <div class="sm:col-span-3">
                        <x-pupi.input.group
                            label="{{ __('Stage') }}"
                            for="stage"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('stage')">
                            <flux:select searchable wire:model="stage" id="stage" variant="listbox" placeholder="{{ __('Select Stage') }}">
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
                                        <flux:button icon="plus" class="w-full rounded-b-lg rounded-t-none" variant="filled">
                                            {{ __('Create Stage') }}
                                        </flux:button>
                                    </flux:modal.trigger>
                                </x-slot>
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Employee Status') }}" for="employee_status" badge="{{ __('Required') }}" :error="$errors->first('employee_status')">
                            <flux:select wire:model="employee_status" id="employee_status" variant="listbox" placeholder="{{ __('Select status') }}">
                                @foreach($this->employeeStatuses as $status)
                                    <flux:option value="{{ $status['value'] }}">{{ __($status['name']) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Joined Date') }}" for="joined_at" badge="{{ __('Required') }}" :error="$errors->first('joined_at')">
                            <x-pupi.input.text wire:model.defer="joined_at" id="joined_at" type="date" />
                        </x-pupi.input.group>
                    </div>

                </div>
                <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <!-- Privacy section -->
                    <div class="col-span-full divide-y divide-gray-200 dark:divide-white/10 pt-0">
                        <div class="px-0 sm:px-0">

                            <ul role="list"
                                class="mt-2 divide-y divide-gray-200 dark:divide-white/10">
                                <li class="flex items-center justify-between pt-0 pb-4">
                                    <div class="flex flex-col">
                                        <p class="text-sm font-medium leading-6 dark:text-white text-gray-900" id="account-status-label">
                                            {{ __('Account Status') }}
                                        </p>
                                        <p class="text-sm dark:text-gray-400 text-gray-500" id="account-status-description">
                                            {{ __('Toggle to set the Account Status to active. Default is Not Activated.') }}
                                        </p>
                                    </div>
                                    <flux:switch wire:model.live="isActive" label="{{ $isActive ? __('Active') : __('Not Activated') }}" />
                                </li>
                                <li class="flex items-center justify-between py-4">
                                    <div class="flex flex-col">
                                        <p class="text-sm font-medium leading-6 dark:text-white text-gray-900"
                                           id="privacy-option-1-label">{{ __('Send Email Invitation')}}</p>
                                        <p class="text-sm dark:text-gray-400 text-gray-500"
                                           id="privacy-option-1-description">{{ __('Toggle to send User a invitation Email')}}</p>
                                    </div>
                                    <flux:switch wire:model.live="notifications" label="Enable notifications" />
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <flux:button wire:click="resetForm" type="submit" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit">
                    {{ __('Save') }}
                </flux:button>
            </div>

{{--            <div--}}
{{--                class="bg-gray-50 dark:border-t dark:border-white/10 dark:bg-gray-700/10 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">--}}
{{--                <x-pupi.button.save>--}}
{{--                    {{ __('Save')}}--}}
{{--                </x-pupi.button.save>--}}
{{--                <x-pupi.button.cancel wire:click="resetForm" >--}}
{{--                    {{ __('Cancel')}}--}}
{{--                </x-pupi.button.cancel>--}}
{{--            </div>--}}

        </form>
    </flux:modal>
</div>
