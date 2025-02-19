<x-pupi.layout.form>
    <x-slot name="title">
        {{ __('Department Information')}}
    </x-slot>

    <x-slot name="description">
        {{ __('Here are stored the department information')}}
    </x-slot>

    <x-slot name="form">

        <!-- Department Form -->
        <form wire:submit.prevent="updateDepartment">
            <div class="px-4 py-6 sm:p-8">
                <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                    <div class="sm:col-span-4">
                        <x-pupi.input.group label="{{ __('Name')}}"
                                            for="name"
                                            :error="$errors->first('name')"
                                            help-text="{{ __('') }}">
                            <x-pupi.input.text autofocus wire:model="name"
                                               name="name" id="name"
                                               placeholder="{{ __('Name')}}"/>
                        </x-pupi.input.group>
                    </div>
                </div>
            </div>
            <!-- Update Button -->
            <!-- Button Container with Action Buttons -->
            <x-pupi.button.container>
                <x-pupi.button.fluxback/>
                <x-pupi.button.fluxsubmit/>
            </x-pupi.button.container>
        </form>

    </x-slot>

</x-pupi.layout.form>
