    <div>
        <!-- Button Container -->
        <x-pupi.button.container>
            <flux:modal.trigger name="create-child">
                <flux:button
                    variant="primary">
                    {{ __('Add Child') }}
                </flux:button>
            </flux:modal.trigger>
        </x-pupi.button.container>

        <flux:modal
            name="create-child"
            variant="flyout"
            position="left"
            class="space-y-6 lg:min-w-3xl"
        >
            <div>
                <flux:heading size="lg">{{ __('Add Child') }}</flux:heading>
                <flux:subheading>{{ __('Register a child for family allowance') }}</flux:subheading>
            </div>

            <!-- Formular: Child Data -->
            <form wire:submit="add" class="space-y-4">

                <!-- Personal Information Section -->
                <div class="py-4">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">

                        <!-- Gender -->
                        <div class="sm:col-span-4">
                            <x-pupi.input.group
                                label="{{ __('Gender') }}"
                                for="gender"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('gender')"
                                model="form.gender"
                                help-text="{{ __('') }}">

                                <flux:select
                                    class="mt-2"
                                    wire:model="form.gender"
                                    id="gender"
                                    variant="listbox"
                                    placeholder="{{ __('Select gender') }}">

                                    @foreach($this->genderOptions() as $genderOption)
                                        <flux:option
                                            wire:key="gender-option-{{ $genderOption['value'] }}"
                                            value="{{ $genderOption['value'] }}">
                                            <span>{{ $genderOption['label'] }}</span>
                                        </flux:option>
                                    @endforeach

                                </flux:select>

                            </x-pupi.input.group>
                        </div>

                        <!-- First Name -->
                        <div class="sm:col-span-5">
                            <x-pupi.input.group
                                label="{{ __('Full Name') }}"
                                for="name"
                                badge="{{ __('Required') }}"
                                :error="$errors->first('name')"
                                help-text="{{ __('') }}"
                                model="form.name">

                                <x-pupi.input.text
                                    wire:model="form.name"
                                    id="name"
                                    required
                                    placeholder="{{ __('Your Child Name') }}"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- Birthdate -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Birthdate') }}"
                                for="birthdate"
                                badge="{{ __('Required') }}"
                                model="form.birthdate"
                                :error="$errors->first('birthdate')"
                                help-text="{{ __('Child must be under 25 years old') }}">

                                <flux:input
                                    wire:model="form.birthdate"
                                    id="birthdate"
                                    required
                                    type="date"
                                    max="{{ now()->format('Y-m-d') }}"
                                    min="{{ now()->subYears(25)->format('Y-m-d') }}"
                                    class="mt-2"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- AHV Number -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('AHV Number') }}"
                                for="ahv_number"
                                badge="{{ __('Optional') }}"
                                :error="$errors->first('ahv_number')"
                                help-text="{{ __('Format: 756.xxxx.xxxx.xx') }}"
                                model="form.ahv_number">

                                <x-pupi.input.text
                                    wire:model="form.ahv_number"
                                    id="ahv_number"
                                    placeholder="756.1234.5678.90"
                                    x-mask="999.9999.9999.99"
                                />

                            </x-pupi.input.group>
                        </div>

                        <!-- Valid Until -->
                        <div class="sm:col-span-3">
                            <x-pupi.input.group
                                label="{{ __('Valid Until') }}"
                                for="valid_until"
                                badge="{{ __('Optional') }}"
                                model="form.valid_until"
                                :error="$errors->first('valid_until')"
                                help-text="{{ __('Automatically set to 18th birthday if not specified') }}">

                                <flux:input
                                    wire:model="form.valid_until"
                                    id="valid_until"
                                    type="date"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="mt-2"
                                />

                            </x-pupi.input.group>
                        </div>

                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">
                    <flux:button wire:click="closeCreateChildModal" type="button" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Add Child') }}
                    </flux:button>
                </div>
            </form>
        </flux:modal>
    </div>


    {{--    <flux:modal.trigger name="create-profile">--}}
{{--        <flux:button>Create profile</flux:button>--}}
{{--    </flux:modal.trigger>--}}

{{--    <flux:modal name="create-profile" class="md:w-96" variant="flyout">--}}
{{--        <div class="space-y-6">--}}
{{--            <div>--}}
{{--                <flux:heading size="lg">Update profile</flux:heading>--}}
{{--                <flux:text class="mt-2">Make changes to your personal details.</flux:text>--}}
{{--            </div>--}}

{{--            <form wire:submit="add" class="flex flex-col gap-4">--}}
{{--                <h2 class="text-3xl font-bold mb-1">Write your new post!</h2>--}}

{{--                <hr class="w-[75%]">--}}

{{--                <label class="flex flex-col gap-2">--}}
{{--                    Name--}}
{{--                    <input autofocus wire:model="form.name" class="px-3 py-2 border font-normal rounded-lg border-slate-300 read-only:opacity-50 read-only:cursor-not-allowed">--}}
{{--                    @error('form.name')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror--}}
{{--                </label>--}}

{{--                <!-- Gender -->--}}
{{--                <div class="sm:col-span-4">--}}
{{--                    <x-pupi.input.group--}}
{{--                        label="{{ __('Gender') }}"--}}
{{--                        for="gender"--}}
{{--                        badge="{{ __('Required') }}"--}}
{{--                        :error="$errors->first('gender')"--}}
{{--                        model="gender"--}}
{{--                        help-text="{{ __('') }}">--}}

{{--                        <flux:select--}}
{{--                            class="mt-2"--}}
{{--                            wire:model="gender"--}}
{{--                            id="gender"--}}
{{--                            variant="listbox"--}}
{{--                            placeholder="{{ __('Select gender') }}">--}}

{{--                            @foreach($this->genderOptions() as $genderOption)--}}
{{--                                <flux:option--}}
{{--                                    wire:key="gender-option-{{ $genderOption['value'] }}"--}}
{{--                                    value="{{ $genderOption['value'] }}">--}}
{{--                                    <span>{{ $genderOption['label'] }}</span>--}}
{{--                                </flux:option>--}}
{{--                            @endforeach--}}

{{--                        </flux:select>--}}

{{--                    </x-pupi.input.group>--}}
{{--                </div>--}}

{{--                <label class="flex flex-col gap-2">--}}
{{--                    Birthdate--}}
{{--                    <input type="date" wire:model="form.birthdate" class="px-3 py-2 border font-normal rounded-lg border-slate-300">--}}
{{--                    @error('form.birthdate')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror--}}
{{--                </label>--}}

{{--                <label class="flex flex-col gap-2">--}}
{{--                    AHV Number--}}
{{--                    <input wire:model="form.ahv_number" placeholder="756.1234.5678.90" class="px-3 py-2 border font-normal rounded-lg border-slate-300">--}}
{{--                    @error('form.ahv_number')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror--}}
{{--                </label>--}}

{{--                <label class="flex flex-col gap-2">--}}
{{--                    Valid Until--}}
{{--                    <input type="date" wire:model="form.valid_until" class="px-3 py-2 border font-normal rounded-lg border-slate-300">--}}
{{--                    @error('form.valid_until')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror--}}
{{--                </label>--}}





{{--                <x-dialog.footer>--}}
{{--                    <x-dialog.close-button>--}}
{{--                        <button type="button" class="text-center rounded-xl bg-slate-300 text-slate-800 px-6 py-2 font-semibold">Cancel</button>--}}
{{--                    </x-dialog.close-button>--}}

{{--                    <button type="submit" class="text-center rounded-xl bg-blue-500 text-white px-6 py-2 font-semibold disabled:cursor-not-allowed disabled:opacity-50">Save</button>--}}
{{--                </x-dialog.footer>--}}
{{--            </form>--}}

{{--            <div class="flex">--}}
{{--                <flux:spacer />--}}

{{--                <flux:button type="submit" variant="primary">Save changes</flux:button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </flux:modal>--}}


{{--    <x-dialog wire:model="show">--}}
{{--        <x-dialog.button>--}}
{{--            <button type="button" class="text-white bg-blue-500 rounded-xl px-4 py-2 text-sm">New Post</button>--}}
{{--        </x-dialog.button>--}}

{{--        <flux:button variant="ghost" size="sm" >--}}
{{--            <x-pupi.icon.success  class="h-5 w-5"--}}
{{--            />--}}
{{--        </flux:button>--}}

{{--        <flux:button>Button</flux:button>--}}

{{--        <x-dialog.panel>--}}
{{--            <form wire:submit="add" class="flex flex-col gap-4">--}}
{{--                <h2 class="text-3xl font-bold mb-1">Write your new post!</h2>--}}

{{--                <hr class="w-[75%]">--}}

{{--                <label class="flex flex-col gap-2">--}}
{{--                    Title--}}
{{--                    <input autofocus wire:model="form.title" class="px-3 py-2 border font-normal rounded-lg border-slate-300 read-only:opacity-50 read-only:cursor-not-allowed">--}}
{{--                    @error('form.title')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror--}}
{{--                </label>--}}

{{--                <label class="flex flex-col gap-2">--}}
{{--                    Content--}}
{{--                    <textarea wire:model="form.content" rows="5" class="px-3 py-2 border font-normal rounded-lg border-slate-300 read-only:opacity-50 read-only:cursor-not-allowed"></textarea>--}}
{{--                    @error('form.content')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror--}}
{{--                </label>--}}

{{--                <x-dialog.footer>--}}
{{--                    <x-dialog.close-button>--}}
{{--                        <button type="button" class="text-center rounded-xl bg-slate-300 text-slate-800 px-6 py-2 font-semibold">Cancel</button>--}}
{{--                    </x-dialog.close-button>--}}

{{--                    <button type="submit" class="text-center rounded-xl bg-blue-500 text-white px-6 py-2 font-semibold disabled:cursor-not-allowed disabled:opacity-50">Save</button>--}}
{{--                </x-dialog.footer>--}}
{{--            </form>--}}
{{--        </x-dialog.panel>--}}
{{--    </x-dialog>--}}
