<div>

    <flux:modal.trigger name="create-profile">
        <flux:button>Create profile</flux:button>
    </flux:modal.trigger>

    <flux:modal name="create-profile" class="md:w-96" variant="flyout">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Update profile</flux:heading>
                <flux:text class="mt-2">Make changes to your personal details.</flux:text>
            </div>

            <form wire:submit="add" class="flex flex-col gap-4">
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
</div>
