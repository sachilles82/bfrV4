<div>
    <flux:modal
        name="create-profession"
        variant="flyout"
        position="left"
        class="space-y-6 lg:min-w-3xl">
        <!-- Modal-Header -->
        <div>
            <flux:heading size="lg">
                {{ __('Profession Manager') }}
            </flux:heading>
            <flux:subheading>
                {{ $editing ? __('Edit the profession details.') : __('Fill out the details to create a new profession.') }}
            </flux:subheading>
        </div>

        <!-- Formular: Create/Update Profession -->
        <form wire:submit.prevent="saveProfession" class="space-y-4">
            <div class="sm:col-span-3">
                <x-pupi.input.group
                    label="{{ __('Profession') }}"
                    for="name"
                    badge="{{ __('Required') }}"
                    :error="$errors->first('name')">
                    <x-pupi.input.text
                        wire:model.defer="name"
                        id="name"
                        placeholder="{{ __('Enter profession name') }}"
                        :disabled="!$dataLoaded"
                    />
                </x-pupi.input.group>
            </div>

            <!-- Formular-Buttons -->
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button
                        wire:click="closeProfessionFormModal"
                        variant="ghost"
                        :disabled="!$dataLoaded"
                    >
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button
                    type="submit"
                    variant="primary"
                    :disabled="!$dataLoaded"
                    wire:loading.attr="disabled"
                    wire:target="saveProfession"
                >
                    {{ $editing ? __('Update') : __('Create') }}
                </flux:button>
            </div>
        </form>

        @if(!$dataLoaded)
            <!-- Skeleton Loader -->
            <div class="flex flex-col pt-6" role="status">
                <div class="-m-1.5 overflow-x-auto">
                    <div class="p-1.5 min-w-full inline-block align-middle">
                        <div
                            class="border border-gray-200 rounded-lg shadow-xs overflow-hidden dark:border-neutral-700 dark:shadow-gray-900">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead class="bg-gray-50 dark:bg-neutral-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start">
                                        <div
                                            class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-20 animate-pulse"></div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-start">
                                        <div
                                            class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-24 animate-pulse"></div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-end">
                                        <div
                                            class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-16 ml-auto animate-pulse"></div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @for($i = 0; $i < 5; $i++)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div
                                                class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-32 animate-pulse"></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div
                                                class="h-4 bg-gray-200 rounded-full dark:bg-gray-700 w-28 animate-pulse"></div>
                                        </td>
                                        <td class="px-6 py-4 text-end">
                                            <div class="flex justify-end items-center gap-2">
                                                <div
                                                    class="h-4 bg-gray-200 rounded dark:bg-gray-700 w-12 animate-pulse"></div>
                                                <div
                                                    class="h-4 bg-gray-200 rounded dark:bg-gray-700 w-14 animate-pulse"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mt-4 px-4 flex justify-center">
                    <div class="h-8 bg-gray-200 rounded dark:bg-gray-700 w-64 animate-pulse"></div>
                </div>
                <span class="sr-only">Loading...</span>
            </div>
        @else
            {{--            <div class="flex gap-2 mb-4">--}}
            {{--                <button--}}
            {{--                    wire:click="setFilterMode('user')"--}}
            {{--                    class="px-3 py-1 rounded {{ $filterMode === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}"--}}
            {{--                >--}}
            {{--                    {{ __('My Professions') }}--}}
            {{--                </button>--}}
            {{--                <button--}}
            {{--                    wire:click="setFilterMode('team')"--}}
            {{--                    class="px-3 py-1 rounded {{ $filterMode === 'team' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}"--}}
            {{--                >--}}
            {{--                    {{ __('Team professions') }}--}}
            {{--                </button>--}}
            {{--                <button--}}
            {{--                    wire:click="setFilterMode('company')"--}}
            {{--                    class="px-3 py-1 rounded {{ $filterMode === 'company' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}"--}}
            {{--                >--}}
            {{--                    {{ __('Company Professions') }}--}}
            {{--                </button>--}}
            {{--            </div>--}}
            <!-- Daten-Tabelle -->
            <div class="flex flex-col pt-6" wire:key="professions-table-{{ $dataLoaded ? 'dataLoaded' : 'empty' }}">
                <div class="-m-1.5 overflow-x-auto">
                    <div class="p-1.5 min-w-full inline-block align-middle">
                        <div
                            class="border border-gray-200 rounded-lg shadow-xs overflow-hidden dark:border-neutral-700 dark:shadow-gray-900">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead class="bg-gray-50 dark:bg-neutral-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                        {{ __('Updated') }}
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                @forelse($professions as $profession)
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-800"
                                        wire:key="profession-{{ $profession->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            {{ $profession->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            <div class="text-gray-500 dark:text-gray-400">
                                                <flux:tooltip class="cursor-default"
                                                              content="{{ __('Updated: ') }}
                                                          {{ $profession->updated_at ? $profession->updated_at->format('d.m.Y') : __('Not set') }}"
                                                              position="top">
                                                    <div class="text-gray-500 dark:text-gray-400">
                                                        {{ $profession->updated_at ? $profession->updated_at->diffForHumans() : __('Not available') }}
                                                    </div>
                                                </flux:tooltip>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <div class="flex justify-end items-center gap-2">
                                                <button
                                                    wire:click="editProfession({{ $profession->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="opacity-50"
                                                    class="text-blue-600 hover:text-blue-800 dark:text-blue-500 dark:hover:text-blue-400"
                                                >
                                                    {{ __('Edit') }}
                                                </button>
                                                <button
                                                    wire:click="deleteProfession({{ $profession->id }})"
                                                    wire:confirm="{{ __('Are you sure you want to remove this profession?') }}"
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="opacity-50"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400"
                                                >
                                                    {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center py-4">
                                                <x-pupi.icon.database class="h-8 w-8 text-gray-400 mb-2"/>
                                                <span>{{ __('No entries found...') }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <!-- Pagination Links -->
                            @if($professions->hasPages())
                                <div class="border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                                    {{ $professions->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
