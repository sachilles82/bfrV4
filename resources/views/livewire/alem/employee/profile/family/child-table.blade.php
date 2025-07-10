<div>
    <x-pupi.layout.form>
        <x-slot:title>
            {{ __('Children') }}
        </x-slot:title>

        <x-slot:description>
            {{ __('Update the children data') }}
        </x-slot:description>

        <x-slot name="form">
            <x-pupi.table2.container
                wire:key="user-child-table-{{ now() }}"
            >
                <x-slot:table>
                    <x-pupi.table2.main>
                        <x-slot:head>
                            <x-pupi.table2.th.th>
                                {{ __('Full Name') }}
                            </x-pupi.table2.th.th>
                            <x-pupi.table2.th.notsort>
                                {{ __('Gender') }}
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                {{ __('Birthdate') }}
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                {{ __('Age') }}
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                {{ __('AHV Number') }}
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                {{ __('Valid Until') }}
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.actions/>
                        </x-slot:head>

                        <x-slot:body>
                            @forelse($children as $child)
                                <x-pupi.table2.tr.body wire:key="child-row-{{ $child->id }}">
                                    <x-pupi.table2.tr.cell1>
                                        {{ $child->name }}
                                    </x-pupi.table2.tr.cell1>
                                    <x-pupi.table2.tr.cell>
                                        {{ __($child->gender?->label() ?? '-') }}
                                    </x-pupi.table2.tr.cell>
                                    <x-pupi.table2.tr.cell>
                                        <flux:tooltip class="cursor-default"
                                                      content="{{ __('Age: ') . $child->age . ' ' . __('years') }}"
                                                      position="top">
                                            {{ $child->birthdate?->format('d.m.Y') ?? '-' }}
                                        </flux:tooltip>
                                    </x-pupi.table2.tr.cell>
                                    <x-pupi.table2.tr.cell>
                                        {{ $child->age }} {{ __('years') }}
                                    </x-pupi.table2.tr.cell>
                                    <x-pupi.table2.tr.cell>
                                        {{ $child->ahv_number ?? '-' }}
                                    </x-pupi.table2.tr.cell>
                                    <x-pupi.table2.tr.cell>
                                        @if($child->valid_until)
                                            {{ $child->valid_until->format('d.m.Y') }}
                                            @if($child->is_valid)
                                                <span class="text-green-600 dark:text-green-400">✓</span>
                                            @else
                                                <span class="text-red-600 dark:text-red-400">✗</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </x-pupi.table2.tr.cell>
                                    <x-pupi.table2.tr.action>
                                        <flux:dropdown align="end" offset="-15">
                                            <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"
                                                         size="sm"
                                                         variant="ghost" inset="top bottom"/>

                                            <flux:menu class="min-w-32">
                                                <flux:modal.trigger name="edit-child">
                                                    <flux:menu.item
                                                        wire:click="$dispatch('edit-child-modal', { childId: {{ $child->id }} })"   >                                                     {{ __('Edit') }}
                                                    </flux:menu.item>
                                                </flux:modal.trigger>

                                                <flux:separator class="my-1"/>

                                                <flux:menu.item
                                                    wire:click="deleteChild({{ $child->id }})"
                                                    wire:confirm="{{ __('Are you sure you want to remove this child?') }}"
                                                    icon="trash" variant="danger">
                                                    {{ __('Delete') }}
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                    </x-pupi.table2.tr.action>
                                </x-pupi.table2.tr.body>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No children registered yet.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot:body>
                    </x-pupi.table2.main>
                </x-slot:table>

                @if($children->hasMorePages())
                    <x-slot:pagination>
                        {{ $children->links() }}
                    </x-slot:pagination>
                @endif

            </x-pupi.table2.container>

            <!-- Button Container -->
            <x-pupi.button.container>
                <flux:modal.trigger name="create-child">
                    <flux:button
                        @click="$dispatch('create-child-modal', { userId: {{ $userId }} })"
                        variant="primary">
                        {{ __('Add Child') }}
                    </flux:button>
                </flux:modal.trigger>
            </x-pupi.button.container>

        </x-slot>
    </x-pupi.layout.form>
</div>
