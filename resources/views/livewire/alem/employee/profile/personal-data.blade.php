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

                    <!-- Status -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Status') }}" for="status" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="status" id="status" name="status" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Anstellungsverhältnis -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Anstellungsverhältnis') }}" for="employment_type" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="employment_type" id="employment_type" name="employment_type" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Vorgesetzter -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Vorgesetzter') }}" for="supervisor" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="supervisor" id="supervisor" name="supervisor" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Personalnummer -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Personalnummer') }}" for="personal_number" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="personal_number" id="personal_number" name="personal_number" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Eintrittsdatum -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Eintrittsdatum') }}" for="date_hired" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="date_hired" id="date_hired" name="date_hired" type="date" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Austrittsdatum (optional) -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Austrittsdatum') }}" for="date_fired">
                            <x-pupi.input.text wire:model="date_fired" id="date_fired" name="date_fired" type="date" placeholder="{{ __('Optional') }}" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Kündigungsfrist -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Kündigungsfrist') }}" for="notice_period" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="notice_period" id="notice_period" name="notice_period" />
                        </x-pupi.input.group>
                    </div>

                    <!-- Probezeit -->
                    <div class="sm:col-span-3">
                        <x-pupi.input.group label="{{ __('Probezeit') }}" for="probation" badge="{{ __('Required') }}">
                            <x-pupi.input.text wire:model="probation" id="probation" name="probation" />
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
