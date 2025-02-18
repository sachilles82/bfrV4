<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Employee Details') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employee information below.') }}
    </x-slot:description>

    <x-slot name="form">
        <form wire:submit.prevent="updateEmployee">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <!-- Employee Name -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group label="{{ __('Employee Name') }}" for="employee_name" badge="{{ __('Required') }}" :error="$errors->first('employee_name')">
                            <x-pupi.input.text wire:model="employee_name" name="employee_name" id="employee_name" placeholder="{{ __('Employee Name') }}"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Email -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group label="{{ __('Email') }}" for="email" badge="{{ __('Required') }}" :error="$errors->first('email')">
                            <x-pupi.input.text wire:model="email" name="email" id="email" placeholder="{{ __('Email') }}"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Gender -->
                    <div class="sm:col-span-2">
                        <x-pupi.input.group label="{{ __('Gender') }}" for="gender" badge="{{ __('Required') }}" :error="$errors->first('gender')">
                            <flux:select wire:model="gender" id="gender" name="gender" variant="listbox" placeholder="{{ __('Select Gender') }}">
                                <flux:option value="male">{{ __('Male') }}</flux:option>
                                <flux:option value="female">{{ __('Female') }}</flux:option>
                                <flux:option value="other">{{ __('Other') }}</flux:option>
                            </flux:select>
                        </x-pupi.input.group>
                    </div>
                    <!-- Date Hired -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Date Hired') }}" for="date_hired" badge="{{ __('Required') }}" :error="$errors->first('date_hired')">
                            <x-pupi.input.text wire:model="date_hired" name="date_hired" id="date_hired" type="date"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Date Fired (optional) -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Date Fired') }}" for="date_fired" :error="$errors->first('date_fired')">
                            <x-pupi.input.text wire:model="date_fired" name="date_fired" id="date_fired" type="date"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Probation End (optional) -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Probation End') }}" for="probation" :error="$errors->first('probation')">
                            <x-pupi.input.text wire:model="probation" name="probation" id="probation" type="date"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Social Number -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Social Number') }}" for="social_number" :error="$errors->first('social_number')">
                            <x-pupi.input.text wire:model="social_number" name="social_number" id="social_number" placeholder="{{ __('Enter social number') }}"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Personal Number -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Personal Number') }}" for="personal_number" :error="$errors->first('personal_number')">
                            <x-pupi.input.text wire:model="personal_number" name="personal_number" id="personal_number" placeholder="{{ __('Enter personal number') }}"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Profession -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Profession') }}" for="profession" :error="$errors->first('profession')">
                            <x-pupi.input.text wire:model="profession" name="profession" id="profession" placeholder="{{ __('Enter profession') }}"/>
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
