<div>
    <x-pupi.layout.form>
        <x-slot:title>
            {{ __('Children') }}
        </x-slot:title>

        <x-slot:description>
            {{ __('Update the children data') }}
        </x-slot:description>

        <x-slot name="form">
            <div>
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
                            {{ __('AHV Number') }}
                        </x-pupi.table2.th.notsort>
                        <x-pupi.table2.th.notsort>
                            {{ __('Zulagen') }}
                        </x-pupi.table2.th.notsort>
                        <x-pupi.table2.th.actions/>
                    </x-slot:head>
                    <x-slot:body>
                        <x-pupi.table2.tr.body>
                            <x-pupi.table2.tr.cell1>
                                Alem Alija
                            </x-pupi.table2.tr.cell1>
                            <x-pupi.table2.tr.cell>
                                männlich
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2022
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                756.13.456.79
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2038
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.action>
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"
                                                 size="sm"
                                                 variant="ghost" inset="top bottom"/>

                                    <flux:menu class="min-w-32">
                                        <flux:menu.item
                                            {{--                                                                                            wire:click="showEditModal({{ $role->id }})"--}}
                                            icon="pencil-square">
                                            {{ __('Edit') }}
                                        </flux:menu.item>

                                        <flux:menu.item
                                            {{--                                                                                            wire:click="delete({{ $role->id }})"--}}
                                            wire:confirm="{{ __('Are you sure you want to remove this role?') }}"
                                            wire:confirm.prompt="Are you sure?\n\nType YES to confirm|YES"
                                            icon="trash" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </x-pupi.table2.tr.action>
                        </x-pupi.table2.tr.body>
                        <x-pupi.table2.tr.body>
                            <x-pupi.table2.tr.cell1>
                                Alem Alija
                            </x-pupi.table2.tr.cell1>
                            <x-pupi.table2.tr.cell>
                                Männlich
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2022
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                756.13.456.79
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2038
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.action>
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"
                                                 size="sm"
                                                 variant="ghost" inset="top bottom"/>

                                    <flux:menu class="min-w-32">
                                        <flux:menu.item
                                            {{--                                                                                            wire:click="showEditModal({{ $role->id }})"--}}
                                            icon="pencil-square">
                                            {{ __('Edit') }}
                                        </flux:menu.item>

                                        <flux:menu.item
                                            {{--                                                                                            wire:click="delete({{ $role->id }})"--}}
                                            wire:confirm="{{ __('Are you sure you want to remove this role?') }}"
                                            wire:confirm.prompt="Are you sure?\n\nType YES to confirm|YES"
                                            icon="trash" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </x-pupi.table2.tr.action>
                        </x-pupi.table2.tr.body>
                        <x-pupi.table2.tr.body>
                            <x-pupi.table2.tr.cell1>
                                Alem Alija
                            </x-pupi.table2.tr.cell1>
                            <x-pupi.table2.tr.cell>
                                Männlich
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2022
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                756.13.456.79
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2038
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.action>
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"
                                                 size="sm"
                                                 variant="ghost" inset="top bottom"/>

                                    <flux:menu class="min-w-32">
                                        <flux:menu.item
                                            {{--                                                                                            wire:click="showEditModal({{ $role->id }})"--}}
                                            icon="pencil-square">
                                            {{ __('Edit') }}
                                        </flux:menu.item>

                                        <flux:menu.item
                                            {{--                                                                                            wire:click="delete({{ $role->id }})"--}}
                                            wire:confirm="{{ __('Are you sure you want to remove this role?') }}"
                                            wire:confirm.prompt="Are you sure?\n\nType YES to confirm|YES"
                                            icon="trash" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </x-pupi.table2.tr.action>
                        </x-pupi.table2.tr.body>
                        <x-pupi.table2.tr.body>
                            <x-pupi.table2.tr.cell1>
                                Alem Alija
                            </x-pupi.table2.tr.cell1>
                            <x-pupi.table2.tr.cell>
                                Männlich
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2022
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                756.13.456.79
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2038
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.action>
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"
                                                 size="sm"
                                                 variant="ghost" inset="top bottom"/>

                                    <flux:menu class="min-w-32">
                                        <flux:menu.item
                                            {{--                                                                                            wire:click="showEditModal({{ $role->id }})"--}}
                                            icon="pencil-square">
                                            {{ __('Edit') }}
                                        </flux:menu.item>

                                        <flux:menu.item
                                            {{--                                                                                            wire:click="delete({{ $role->id }})"--}}
                                            wire:confirm="{{ __('Are you sure you want to remove this role?') }}"
                                            wire:confirm.prompt="Are you sure?\n\nType YES to confirm|YES"
                                            icon="trash" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </x-pupi.table2.tr.action>
                        </x-pupi.table2.tr.body>
                        <x-pupi.table2.tr.body>
                            <x-pupi.table2.tr.cell1>
                                Alem Alija
                            </x-pupi.table2.tr.cell1>
                            <x-pupi.table2.tr.cell>
                                Männlich
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2022
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                756.13.456.79
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.cell>
                                11.01.2038
                            </x-pupi.table2.tr.cell>
                            <x-pupi.table2.tr.action>
                                <flux:dropdown align="end" offset="-15">
                                    <flux:button class="hover:bg-gray-200/75" icon="ellipsis-horizontal"
                                                 size="sm"
                                                 variant="ghost" inset="top bottom"/>

                                    <flux:menu class="min-w-32">
                                        <flux:menu.item
                                            {{--                                                                                            wire:click="showEditModal({{ $role->id }})"--}}
                                            icon="pencil-square">
                                            {{ __('Edit') }}
                                        </flux:menu.item>

                                        <flux:menu.item
                                            {{--                                                                                            wire:click="delete({{ $role->id }})"--}}
                                            wire:confirm="{{ __('Are you sure you want to remove this role?') }}"
                                            wire:confirm.prompt="Are you sure?\n\nType YES to confirm|YES"
                                            icon="trash" variant="danger">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </x-pupi.table2.tr.action>
                        </x-pupi.table2.tr.body>
                    </x-slot:tbody>
                </x-pupi.table2.main>
            </div>

            <!-- Button Container -->
            <x-pupi.button.container>
                <flux:button size="base" type="submit" variant="primary">
                    {{ __('Create')}}
                </flux:button>
            </x-pupi.button.container>

        </x-slot>
    </x-pupi.layout.form>
</div>
