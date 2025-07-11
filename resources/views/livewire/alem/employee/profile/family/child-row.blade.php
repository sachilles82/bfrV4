{{--<div>--}}
{{--    <x-pupi.table2.tr.body>--}}
{{--        <x-pupi.table2.tr.cell1>--}}
{{--            {{ $child->name }}--}}
{{--        </x-pupi.table2.tr.cell1>--}}
{{--        <x-pupi.table2.tr.cell>--}}
{{--            {{ __($child->gender?->label() ?? '-') }}--}}
{{--        </x-pupi.table2.tr.cell>--}}
{{--        <x-pupi.table2.tr.cell>--}}
{{--            <flux:tooltip class="cursor-default"--}}
{{--                          content="{{ __('Age: ') . $child->age . ' ' . __('years') }}"--}}
{{--                          position="top">--}}
{{--                {{ $child->birthdate?->format('d.m.Y') ?? '-' }}--}}
{{--            </flux:tooltip>--}}
{{--        </x-pupi.table2.tr.cell>--}}
{{--        <x-pupi.table2.tr.cell>--}}
{{--            {{ $child->age }} {{ __('years') }}--}}
{{--        </x-pupi.table2.tr.cell>--}}
{{--        <x-pupi.table2.tr.cell>--}}
{{--            {{ $child->ahv_number ?? '-' }}--}}
{{--        </x-pupi.table2.tr.cell>--}}
{{--        <x-pupi.table2.tr.cell>--}}
{{--            @if($child->valid_until)--}}
{{--                {{ $child->valid_until->format('d.m.Y') }}--}}
{{--                @if($child->is_valid)--}}
{{--                    <span class="text-green-600 dark:text-green-400">✓</span>--}}
{{--                @else--}}
{{--                    <span class="text-red-600 dark:text-red-400">✗</span>--}}
{{--                @endif--}}
{{--            @else--}}
{{--                ---}}
{{--            @endif--}}
{{--        </x-pupi.table2.tr.cell>--}}
{{--        <x-pupi.table2.tr.action>--}}
{{--            <flux:dropdown align="end" offset="-15">--}}
{{--                <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"--}}
{{--                             size="sm"--}}
{{--                             variant="ghost" inset="top bottom"/>--}}

{{--                <flux:menu class="min-w-32">--}}
{{--                    <flux:modal.trigger name="edit-child-{{ $child->id }}">--}}
{{--                        <flux:menu.item--}}
{{--                            icon="pencil">--}}
{{--                            {{ __('Edit') }}--}}
{{--                        </flux:menu.item>--}}
{{--                    </flux:modal.trigger>--}}

{{--                    <flux:separator class="my-1"/>--}}

{{--                    <flux:menu.item--}}
{{--                        wire:click="$dispatch('deleted')"--}}
{{--                        wire:confirm="{{ __('Are you sure you want to remove this child?') }}"--}}

{{--                        wire:click="$parent.delete({{ $child->id }})"--}}
{{--                        wire:click="$dispatch('deleted')"--}}
{{--                        wire:confirm="{{ __('Are you sure you want to remove this child?') }}"--}}
{{--                        wire:confirm.prompt="Are you sure?\n\nType DELETE to confirm|DELETE"--}}
{{--                        wire:click="$dispatch('deleted', { childId: {{ $child->id }} })"--}}
{{--                        icon="trash"--}}
{{--                        variant="danger">--}}
{{--                        {{ __('Delete') }}--}}
{{--                    </flux:menu.item>--}}

{{--                </flux:menu>--}}
{{--            </flux:dropdown>--}}

{{--            <button wire:click="$dispatch('deleted')" type="button" class="text-center rounded-xl bg-red-500 text-white px-6 py-2 font-semibold disabled:cursor-not-allowed disabled:opacity-50">Delete</button>--}}

{{--        </x-pupi.table2.tr.action>--}}
{{--    </x-pupi.table2.tr.body>--}}

{{--    --}}{{--    <!-- Edit Modal -->--}}
{{--    <flux:modal--}}
{{--        name="edit-child-{{ $child->id }}"--}}
{{--        variant="flyout"--}}
{{--        position="left"--}}
{{--        class="space-y-6 lg:min-w-3xl">--}}
{{--        <div>--}}
{{--            <flux:heading size="lg">{{ __('Edit Child') }}</flux:heading>--}}
{{--            <flux:subheading>{{ __('Update child information for family allowance') }}</flux:subheading>--}}
{{--        </div>--}}

{{--        <!-- Formular: Child Data -->--}}
{{--        <form wire:submit="updateChild" class="space-y-4">--}}
{{--            <!-- Personal Information Section -->--}}
{{--            <div class="py-4">--}}
{{--                <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">--}}

{{--                    <!-- Gender -->--}}
{{--                    <div class="sm:col-span-4">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Gender') }}"--}}
{{--                            for="gender"--}}
{{--                            badge="{{ __('Optional') }}"--}}
{{--                            :error="$errors->first('gender')"--}}
{{--                            model="gender"--}}
{{--                            help-text="">--}}

{{--                            <flux:select--}}
{{--                                class="mt-2"--}}
{{--                                wire:model="gender"--}}
{{--                                id="gender"--}}
{{--                                variant="listbox"--}}
{{--                                placeholder="{{ __('Select gender') }}">--}}

{{--                                @foreach($this->genderOptions() as $genderOption)--}}
{{--                                    <flux:option--}}
{{--                                        wire:key="gender-option-{{ $child->id }}-{{ $genderOption['value'] }}"--}}
{{--                                        value="{{ $genderOption['value'] }}"--}}
{{--                                        :selected="$gender === $genderOption['value']">--}}
{{--                                        <span>{{ $genderOption['label'] }}</span>--}}
{{--                                    </flux:option>--}}
{{--                                @endforeach--}}

{{--                            </flux:select>--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

{{--                    <!-- Full Name -->--}}
{{--                    <div class="sm:col-span-5">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Full Name') }}"--}}
{{--                            for="name"--}}
{{--                            badge="{{ __('Required') }}"--}}
{{--                            :error="$errors->first('name')"--}}
{{--                            help-text=""--}}
{{--                            model="name">--}}

{{--                            <x-pupi.input.text--}}
{{--                                wire:model="name"--}}
{{--                                :value="$name"--}}
{{--                                id="name"--}}
{{--                                placeholder="{{ __('Child Name') }}"--}}
{{--                            />--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

{{--                    <!-- Birthdate -->--}}
{{--                    <div class="sm:col-span-3">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Birthdate') }}"--}}
{{--                            for="birthdate"--}}
{{--                            badge="{{ __('Required') }}"--}}
{{--                            model="birthdate"--}}
{{--                            :value="$birthdate"--}}
{{--                            :error="$errors->first('birthdate')"--}}
{{--                            help-text="{{ __('Child must be under 25 years old') }}">--}}

{{--                            <flux:input--}}
{{--                                wire:model="birthdate"--}}
{{--                                id="birthdate"--}}
{{--                                type="date"--}}
{{--                                max="{{ now()->format('Y-m-d') }}"--}}
{{--                                min="{{ now()->subYears(25)->format('Y-m-d') }}"--}}
{{--                                class="mt-2"--}}
{{--                            />--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

{{--                    <!-- AHV Number -->--}}
{{--                    <div class="sm:col-span-3">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('AHV Number') }}"--}}
{{--                            for="ahv_number"--}}
{{--                            badge="{{ __('Optional') }}"--}}
{{--                            :error="$errors->first('ahv_number')"--}}
{{--                            help-text="{{ __('Format: 756.xxxx.xxxx.xx') }}"--}}
{{--                            model="ahv_number">--}}

{{--                            <x-pupi.input.text--}}
{{--                                wire:model="ahv_number"--}}
{{--                                :value="$ahv_number"--}}
{{--                                id="ahv_number"--}}
{{--                                placeholder="756.1234.5678.90"--}}
{{--                                x-mask="999.9999.9999.99"--}}
{{--                            />--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

{{--                    <!-- Valid Until -->--}}
{{--                    <div class="sm:col-span-3">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Valid Until') }}"--}}
{{--                            for="valid_until"--}}
{{--                            badge="{{ __('Optional') }}"--}}
{{--                            model="valid_until"--}}
{{--                            :error="$errors->first('valid_until')"--}}
{{--                            help-text="{{ __('Automatically set to 18th birthday if not specified') }}">--}}

{{--                            <flux:input--}}
{{--                                wire:model="valid_until"--}}
{{--                                :value="$valid_until"--}}
{{--                                id="valid_until"--}}
{{--                                type="date"--}}
{{--                                min="{{ now()->format('Y-m-d') }}"--}}
{{--                                class="mt-2"--}}
{{--                            />--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}

{{--                </div>--}}
{{--            </div>--}}

{{--            <!-- Form Buttons -->--}}
{{--            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-white/10">--}}
{{--                <flux:button--}}
{{--                    wire:click="closeEditModal"--}}
{{--                    type="button"--}}
{{--                    variant="ghost">--}}
{{--                    {{ __('Cancel') }}--}}
{{--                </flux:button>--}}

{{--                <flux:button--}}
{{--                    type="submit"--}}
{{--                    variant="primary"--}}
{{--                    wire:loading.attr="disabled">--}}
{{--                    <span wire:loading.remove>{{ __('Save Changes') }}</span>--}}
{{--                    <span wire:loading>{{ __('Saving...') }}</span>--}}
{{--                </flux:button>--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </flux:modal>--}}
{{--</div>--}}

<tr class="text-left text-slate-900">
    <td class="pl-6 py-4 pr-3 font-medium">{{ $child->name }}</td>
    <td class="pl-4 py-4 text-left text-slate-500">{{ str($child->name)->limit(50) }}</td>

    <td class="pl-6 py-4 pr-3 font-medium">{{ $child->gender }}</td>
    <td class="pl-4 py-4 text-left text-slate-500">
        <flux:tooltip class="cursor-default"
                      content="{{ __('Age: ') . $child->age . ' ' . __('years') }}"
                      position="top">
            {{ $child->birthdate?->format('d.m.Y') ?? '-' }}
        </flux:tooltip>
    </td>
    <td class="pl-4 py-4 text-left text-slate-500">
        {{ $child->age }} {{ __('years') }}
    </td>
    <td class="pl-4 py-4 text-left text-slate-500">
        {{ $child->ahv_number ?? '-' }}
    </td>
    <td class="pl-4 py-4 text-left text-slate-500">
        @if($child->valid_until)
            {{ $child->valid_until->format('d.m.Y') }}
            @if($child->is_valid)
                <span class="text-green-600 dark:text-green-400">✓</span>
            @else
                <span class="text-red-600 dark:text-red-400">✗</span>
            @endif
        @else
            ---
        @endif
    </td>
    <td class="pl-4 py-4 text-right pr-6">
        <flux:button variant="ghost" size="sm" wire:click="$set('showEditDialog', true)">
            <x-pupi.icon.success  class="h-5 w-5"
            />
        </flux:button>

        <flux:modal.trigger name="edit-child-{{ $child->id }}">
            <flux:button>Edit profile</flux:button>
        </flux:modal.trigger>

        <flux:button variant="ghost" size="sm" wire:click="$dispatch('deleted')"

                     wire:confirm="{{ __('Are you sure you want to remove this child?') }}">

        <x-pupi.icon.x-circle  class="h-5 w-5"
            />
        </flux:button>
    </td>

    <td class="pl-4 py-4 text-right pr-6 flex gap-2 justify-end">






{{--        <x-dialog wire:model="showEditDialog">--}}
{{--            <x-dialog.button>--}}
{{--                <button type="button" class="font-medium text-blue-600">--}}
{{--                    Edit--}}
{{--                </button>--}}
{{--            </x-dialog.button>--}}

{{--            <x-dialog.panel>--}}
{{--                <form wire:submit="save" class="flex flex-col gap-4">--}}
{{--                    <h2 class="text-3xl font-bold mb-1">Edit your child</h2>--}}

{{--                    <hr class="w-[75%]">--}}

{{--                    <label class="flex flex-col gap-2">--}}
{{--                        name--}}
{{--                        <input autofocus wire:model="form.name" class="px-3 py-2 border font-normal rounded-lg border-slate-300 read-only:opacity-50 read-only:cursor-not-allowed">--}}
{{--                        @error('form.name')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror--}}
{{--                    </label>--}}

{{--                    <label class="flex flex-col gap-2">--}}
{{--                        Birthdate--}}
{{--                        <input wire:model="form.birthdate" type="date" class="px-3 py-2 border font-normal rounded-lg border-slate-300 read-only:opacity-50 read-only:cursor-not-allowed">--}}
{{--                        @error('form.birthdate')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror--}}
{{--                    </label>--}}
{{--                    <div class="sm:col-span-4">--}}
{{--                        <x-pupi.input.group--}}
{{--                            label="{{ __('Gender') }}"--}}
{{--                            for="gender"--}}
{{--                            badge="{{ __('Required') }}"--}}
{{--                            :error="$errors->first('gender')"--}}
{{--                            model="gender"--}}
{{--                            help-text="">--}}

{{--                            <flux:select--}}
{{--                                class="mt-2"--}}
{{--                                wire:model="gender"--}}
{{--                                id="gender"--}}
{{--                                variant="listbox"--}}
{{--                                placeholder="{{ __('Select gender') }}"--}}
{{--                                required>--}}

{{--                                @foreach($this->genderOptions() as $genderOption)--}}
{{--                                    <flux:option--}}
{{--                                        wire:key="gender-option-{{ $genderOption['value'] }}"--}}
{{--                                        value="{{ $genderOption['value'] }}">--}}
{{--                                        <span>{{ $genderOption['label'] }}</span>--}}
{{--                                    </flux:option>--}}
{{--                                @endforeach--}}

{{--                            </flux:select>--}}

{{--                        </x-pupi.input.group>--}}
{{--                    </div>--}}


{{--                    --}}{{--                    <label class="flex flex-col gap-2">--}}











{{--                    <x-dialog.footer>--}}
{{--                        <x-dialog.close-button>--}}
{{--                            <button type="button" class="text-center rounded-xl bg-slate-300 text-slate-800 px-6 py-2 font-semibold">Cancel</button>--}}
{{--                        </x-dialog.close-button>--}}

{{--                        <button type="submit" class="text-center rounded-xl bg-blue-500 text-white px-6 py-2 font-semibold disabled:cursor-not-allowed disabled:opacity-50">Save</button>--}}
{{--                    </x-dialog.footer>--}}
{{--                </form>--}}
{{--            </x-dialog.panel>--}}
{{--        </x-dialog>--}}

{{--        <x-dialog.index>--}}
{{--            <x-dialog.button>--}}
{{--                <button type="button" class="font-medium text-red-600">--}}
{{--                    Delete--}}
{{--                </button>--}}
{{--            </x-dialog.button>--}}

{{--            <x-dialog.panel>--}}
{{--                <div class="flex flex-col gap-6" x-data="{ confirmation: '' }">--}}
{{--                    <h2 class="font-semibold text-3xl">Are you sure you?</h2>--}}
{{--                    <h2 class="text-lg text-slate-700">This operation is permanant and can be reversed. This child will be deleted forever.</h2>--}}

{{--                    <label class="flex flex-col gap-2">--}}
{{--                        Type "CONFIRM"--}}
{{--                        <input x-model="confirmation" class="px-3 py-2 border border-slate-300 rounded-lg" placeholder="CONFIRM">--}}
{{--                    </label>--}}

{{--                    <x-dialog.footer>--}}
{{--                        <x-dialog.close-button>--}}
{{--                            <button type="button" class="text-center rounded-xl bg-slate-300 text-slate-800 px-6 py-2 font-semibold">Cancel</button>--}}
{{--                        </x-dialog.close-button>--}}

{{--                        <x-dialog.close-button>--}}
{{--                            <button :disabled="confirmation !== 'CONFIRM'" wire:click="$dispatch('deleted')" type="button" class="text-center rounded-xl bg-red-500 text-white px-6 py-2 font-semibold disabled:cursor-not-allowed disabled:opacity-50">Delete</button>--}}
{{--                        </x-dialog.close-button>--}}
{{--                    </x-dialog.footer>--}}
{{--                </div>--}}
{{--            </x-dialog.panel>--}}
{{--        </x-dialog.index>--}}
        <flux:modal name="edit-child-{{ $child->id }}" class="md:w-96" variant="flyout">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Update profile</flux:heading>
                    <flux:text class="mt-2">Make changes to your personal details.</flux:text>
                </div>

                <form wire:submit="save" class="flex flex-col gap-4">
                    <h2 class="text-3xl font-bold mb-1">Write your new post!</h2>

                    <hr class="w-[75%]">

                    <label class="flex flex-col gap-2">
                        Name
                        <input autofocus wire:model="form.name" class="px-3 py-2 border font-normal rounded-lg border-slate-300 read-only:opacity-50 read-only:cursor-not-allowed">
                        @error('form.name')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror
                    </label>

                    <!-- Gender -->
                    <div class="sm:col-span-4">
                        <x-pupi.input.group
                            label="{{ __('Gender') }}"
                            for="gender"
                            badge="{{ __('Required') }}"
                            :error="$errors->first('gender')"
                            model="gender"
                            help-text="{{ __('') }}">

                            <flux:select
                                class="mt-2"
                                wire:model="gender"
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

                    <label class="flex flex-col gap-2">
                        Birthdate
                        <input type="date" wire:model="form.birthdate" class="px-3 py-2 border font-normal rounded-lg border-slate-300">
                        @error('form.birthdate')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror
                    </label>

                    <label class="flex flex-col gap-2">
                        AHV Number
                        <input wire:model="form.ahv_number" placeholder="756.1234.5678.90" class="px-3 py-2 border font-normal rounded-lg border-slate-300">
                        @error('form.ahv_number')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror
                    </label>

                    <label class="flex flex-col gap-2">
                        Valid Until
                        <input type="date" wire:model="form.valid_until" class="px-3 py-2 border font-normal rounded-lg border-slate-300">
                        @error('form.valid_until')<div class="text-sm text-red-500 font-normal">{{ $message }}</div>@enderror
                    </label>





                    <x-dialog.footer>
                        <x-dialog.close-button>
                            <button type="button" class="text-center rounded-xl bg-slate-300 text-slate-800 px-6 py-2 font-semibold">Cancel</button>
                        </x-dialog.close-button>

                        <button type="submit" class="text-center rounded-xl bg-blue-500 text-white px-6 py-2 font-semibold disabled:cursor-not-allowed disabled:opacity-50">Save</button>
                    </x-dialog.footer>
                </form>

                <div class="flex">
                    <flux:spacer />

                    <flux:button type="submit" variant="primary">Save changes</flux:button>
                </div>
            </div>
        </flux:modal>
    </td>
</tr>
