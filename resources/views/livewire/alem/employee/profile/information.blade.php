<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Employee Account Details') }}
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

                    <div class="sm:col-span-4">
                        <x-pupi.input.group label="{{ __('Gender') }}" for="gender" badge="{{ __('Required') }}" :error="$errors->first('gender')">
                            <flux:select wire:model="gender" id="gender" name="gender" variant="listbox" placeholder="{{ __('Select Gender') }}">
                                @foreach(\App\Enums\User\Gender::options() as $value => $label)
                                    <flux:option value="{{ $value }}">{{ __($label) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Employee Name -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('First Name') }}" for="name" badge="{{ __('Required') }}" :error="$errors->first('name')">
                            <x-pupi.input.text wire:model="name" name="name" id="name" placeholder="{{ __('Employee Name') }}"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Employee Name -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Last Name') }}" for="last_name" badge="{{ __('Required') }}" :error="$errors->first('last_name')">
                            <x-pupi.input.text wire:model="last_name" name="last_name" id="last_name" placeholder="{{ __('Employee Last Name') }}"/>
                        </x-pupi.input.group>
                    </div>
                    <!-- Email -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Email') }}" for="email" badge="{{ __('Required') }}" :error="$errors->first('email')">
                            <x-pupi.input.text wire:model="email" name="email" id="email" placeholder="{{ __('Email') }}"/>
                        </x-pupi.input.group>
                    </div>

                    <!-- Phone -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Phone') }}" for="phone_1" :error="$errors->first('phone_1')">
                            <x-pupi.input.text wire:model="phone_1" name="phone_1" id="phone_1" placeholder="{{ __('Phone') }}"/>
                        </x-pupi.input.group>
                    </div>

                    <!-- Model Status Select -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Account Status') }}"
                                            for="model_status"
                                            badge="{{ __('Required') }}"
                                            :error="$errors->first('model_status')">
                            <flux:select wire:model="model_status"
                                         id="model_status" name="model_status" variant="listbox" placeholder="{{ __('Account Status') }}">
                                @foreach($this->modelStatusOptions as $status)
                                    <flux:option value="{{ $status['value'] }}">
                                        <div class="flex items-center gap-2">
                                            <svg class="size-1.5 {{ $status['dotColor'] }}" viewBox="0 0 6 6"
                                                 aria-hidden="true">
                                                <circle cx="3" cy="3" r="3"/>
                                            </svg>
                                            <span>{{ $status['label'] }}</span>
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
                <x-pupi.button.fluxsubmit />
            </x-pupi.button.container>
        </form>
    </x-slot>
</x-pupi.layout.form>
