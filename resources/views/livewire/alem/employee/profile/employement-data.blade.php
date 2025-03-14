<x-pupi.layout.form>
    <x-slot:title>
        {{ __('Employee Employment Data') }}
    </x-slot:title>

    <x-slot:description>
        {{ __('Update the employment data below.') }}
    </x-slot:description>

    <x-slot name="form">
        <form wire:submit.prevent="updateEmployee">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <!-- AHV Number -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group label="{{ __('AHV Number') }}" for="ahv_number" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="ahv_number" id="ahv_number" name="ahv_number" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Birthdate -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Birthdate') }}" for="birthdate" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="birthdate" id="birthdate" name="birthdate" type="date"/>
                        </x-pupi.input.group>
                    </div>

                    <!-- Nationality -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Nationality') }}" for="nationality" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="nationality" id="nationality" name="nationality" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Hometown -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Hometown') }}" for="hometown" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="hometown" id="hometown" name="hometown" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Religion -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Religion') }}" for="religion" badge="{{ __('Required') }}">
                            <flux:select class="!mt-2" wire:model="religion" id="religion" name="religion" variant="listbox" placeholder="{{ __('Select Religion') }}">
                                @foreach(\App\Enums\Employee\Religion::options() as $value => $label)
                                    <flux:option value="{{ $value }}">{{ __($label) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Civil Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Civil Status') }}" for="civil_status" badge="{{ __('Required') }}">
                            <flux:select wire:model="civil_status" id="civil_status" name="civil_status" variant="listbox" placeholder="{{ __('Select Civil Status') }}">
                                @foreach(\App\Enums\Employee\CivilStatus::options() as $value => $label)
                                    <flux:option value="{{ $value }}">{{ __($label) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Residence Permit -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Residence Permit') }}" for="residence_permit" badge="{{ __('Required') }}">
                            <flux:select wire:model="residence_permit" id="residence_permit" name="residence_permit" variant="listbox" placeholder="{{ __('Select Residence Permit') }}">
                                @foreach(\App\Enums\Employee\Residence::options() as $value => $label)
                                    <flux:option value="{{ $value }}">{{ __($label) }}</flux:option>
                                @endforeach
                            </flux:select>
                        </x-pupi.input.group>
                    </div>

                    <!-- Bank Account (IBAN) -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Bank Account (IBAN)') }}" for="iban" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="iban" id="iban" name="iban" />
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
