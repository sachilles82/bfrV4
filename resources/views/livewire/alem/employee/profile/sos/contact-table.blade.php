<div>
    <x-pupi.layout.form>
        <x-slot:title>
            {{ __('SOS Emergency Contact') }}
        </x-slot:title>

        <x-slot:description>
            {{ __('Update the emergency contact data') }}
        </x-slot:description>

        <x-slot:form>
            <x-pupi.table2.container>
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
                                {{ __('Related') }}
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                {{ __('Phone') }}
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.notsort>
                                {{ __('Email') }}
                            </x-pupi.table2.th.notsort>
                            <x-pupi.table2.th.actions/>
                        </x-slot:head>

                        <x-slot:body>
                            @forelse($contacts as $contact)

                                <livewire:alem.employee.profile.sos.contact-row
                                    :key="$contact->id"
                                    :$contact @deleted="delete({{ $contact->id }})"
                                />
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                 stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No Emergency Contact registered yet.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </x-slot:body>
                    </x-pupi.table2.main>
                </x-slot:table>

                <x-slot:pagination>
                    {{ $contacts->links() }}
                </x-slot:pagination>

                {{--                @if($children->hasMorePages() )--}}
                {{--                    <x-slot:pagination>--}}
                {{--                        {{ $children->links() }}--}}
                {{--                    </x-slot:pagination>--}}
                {{--                @endif--}}

            </x-pupi.table2.container>

            <livewire:alem.employee.profile.sos.create-contact
                :user-id="$userId"
                @added="$refresh"
            />

        </x-slot:form>
    </x-pupi.layout.form>
</div>
