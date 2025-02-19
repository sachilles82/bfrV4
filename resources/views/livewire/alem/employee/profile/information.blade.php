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

                    <div class="col-span-full flex items-center gap-x-8">
                        <img
                            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                            alt="" class="h-24 w-24 flex-none rounded-lg bg-gray-800 object-cover">
                        <div>
                            <button type="button"
                                    class="rounded-md dark:bg-white/10 px-3 py-2 text-sm font-semibold dark:text-white shadow-sm dark:hover:bg-white/20 dark:ring-transparent bg-white text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Change avatar
                            </button>
                            <p class="mt-2 text-xs leading-5 dark:text-gray-400 text-gray-500">JPG,
                                GIF or PNG. 1MB max.</p>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Gender') }}" for="gender" badge="{{ __('Required') }}" :error="$errors->first('gender')">
                            <flux:select wire:model="gender" id="gender" name="gender" variant="listbox" placeholder="{{ __('Select Gender') }}">
                                @foreach(\App\Enums\User\Gender::options() as $value => $label)
                                    <flux:option value="{{ $value }}">{{ __($label) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Employee Name -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group label="{{ __('Name Surname') }}" for="name" badge="{{ __('Required') }}" :error="$errors->first('name')">
                            <x-pupi.input.text wire:model="name" name="name" id="name" placeholder="{{ __('Employee Name') }}"/>
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
