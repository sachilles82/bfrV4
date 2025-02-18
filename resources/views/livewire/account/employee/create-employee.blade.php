<div wire:ignore.self>
    <flux:modal.trigger name="create-employee">
        <flux:button variant="primary" class="ml-2">{{ __('Add Role') }}</flux:button>
    </flux:modal.trigger>
    <flux:modal name="create-employee" variant="flyout" position="left" class="md:w-1/3 space-y-6" wire:model="showModal">
        <div>
            <flux:heading size="lg">{{ __('Create Employee') }}</flux:heading>
            <flux:subheading>{{ __('Fill out the details to create a new employee') }}</flux:subheading>
        </div>

        <!-- Formular: User- & Employee-Daten -->
        <form wire:submit.prevent="saveEmployee" class="space-y-4">
            <!-- User-Daten -->
            <x-pupi.input.group label="{{ __('Name') }}" for="name" badge="{{ __('Required') }}" :error="$errors->first('name')">
                <x-pupi.input.text wire:model.lazy="name" id="name" placeholder="{{ __('Enter name') }}" />
            </x-pupi.input.group>

            <x-pupi.input.group label="{{ __('Email') }}" for="email" badge="{{ __('Required') }}" :error="$errors->first('email')">
                <x-pupi.input.text wire:model.lazy="email" id="email" placeholder="{{ __('Enter email') }}" />
            </x-pupi.input.group>

            <x-pupi.input.group label="{{ __('Password') }}" for="password" badge="{{ __('Required') }}" :error="$errors->first('password')">
                <x-pupi.input.text wire:model.lazy="password" id="password" type="password" placeholder="{{ __('Enter password') }}" />
            </x-pupi.input.group>

            <x-pupi.input.group label="{{ __('Gender') }}" for="gender" badge="{{ __('Required') }}" :error="$errors->first('gender')">
                <flux:select wire:model="gender" id="gender" variant="listbox" placeholder="{{ __('Select gender') }}">
                    <flux:option value="male">{{ __('Male') }}</flux:option>
                    <flux:option value="female">{{ __('Female') }}</flux:option>
                    <flux:option value="other">{{ __('Other') }}</flux:option>
                </flux:select>
            </x-pupi.input.group>

            <x-pupi.input.group label="{{ __('Role') }}" for="role" badge="{{ __('Required') }}" :error="$errors->first('role')">
                <flux:select wire:model="role" id="role" variant="listbox" placeholder="{{ __('Select role') }}">
                    <flux:option value="employee">{{ __('Employee') }}</flux:option>
                    <flux:option value="worker">{{ __('Worker') }}</flux:option>
                    <flux:option value="manager">{{ __('Manager') }}</flux:option>
                    <flux:option value="editor">{{ __('Editor') }}</flux:option>
                    <flux:option value="temporary">{{ __('Temporary') }}</flux:option>
                </flux:select>
            </x-pupi.input.group>

            <!-- Employee-Daten -->
            <x-pupi.input.group label="{{ __('Date Hired') }}" for="date_hired" badge="{{ __('Required') }}" :error="$errors->first('date_hired')">
                <x-pupi.input.text wire:model.defer="date_hired" id="date_hired" type="date" />
            </x-pupi.input.group>

            <x-pupi.input.group label="{{ __('Social Number') }}" for="social_number" :error="$errors->first('social_number')">
                <x-pupi.input.text wire:model.lazy="social_number" id="social_number" placeholder="{{ __('Enter social number') }}" />
            </x-pupi.input.group>

            <x-pupi.input.group label="{{ __('Personal Number') }}" for="personal_number" :error="$errors->first('personal_number')">
                <x-pupi.input.text wire:model.lazy="personal_number" id="personal_number" placeholder="{{ __('Enter personal number') }}" />
            </x-pupi.input.group>

            <x-pupi.input.group label="{{ __('Profession') }}" for="profession" :error="$errors->first('profession')">
                <x-pupi.input.text wire:model.lazy="profession" id="profession" placeholder="{{ __('Enter profession') }}" />
            </x-pupi.input.group>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <x-pupi.button.fluxback type="button" wire:click="resetForm">
                    {{ __('Cancel') }}
                </x-pupi.button.fluxback>
                <flux:button type="submit">
                    {{ __('Create Employee') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
