<flux:header container
             class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-white/5">
    <flux:sidebar.toggle
        x-on:click="menu = true"
        variant="ghost"
        class="md:hidden text-gray-700 dark:text-white" icon="bars-3" inset="left"
    />

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 ml-2">
        <form class="flex flex-1" action="#" method="GET">
            <label for="search-field" class="sr-only">Search</label>
            <div class="relative w-full">
                <svg class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-500" viewBox="0 0 20 20"
                     fill="currentColor" aria-hidden="true" data-slot="icon">
                    <path fill-rule="evenodd"
                          d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                          clip-rule="evenodd"/>
                </svg>
                <input id="search-field"
                       class="block h-full w-full border-0 bg-transparent py-0 pl-8 pr-0 dark:text-white text-gray-500 focus:outline-none sm:text-sm"
                       placeholder="Search..." type="search" name="search">
            </div>
        </form>
    </div>

    <flux:navbar class="mr-4">

        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="sun" variant="subtle" aria-label="Toggle dark mode" />

        <flux:navbar.item class="max-lg:hidden" icon="bell" href="#" label="notification"/>
    </flux:navbar>

    <flux:separator vertical class="my-4"/>
    <!-- Teams Dropdown -->
    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
        <flux:dropdown position="top" align="start">
            <flux:button variant="ghost" class="mx-2"
                         icon-trailing="chevron-up-down">{{ Auth::user()->currentTeam->name }}</flux:button>
            <flux:menu class="w-64">
                <!-- Team Management -->
                <flux:menu.heading>{{ __('Manage Team') }}</flux:menu.heading>

                <flux:menu.item wire:navigate.hover href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                    <flux:icon.cog-6-tooth variant="outline" class="size-5"/>{{ __('Team Settings') }}
                </flux:menu.item>

                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                    <flux:menu.item wire:navigate.hover href="{{ route('teams.create') }}">
                        <flux:icon.plus variant="outline" class="size-5"/>{{ __('Create Team') }}
                    </flux:menu.item>
                @endcan

                <!-- Team Switcher -->
                @if (Auth::user()->allTeams()->count() > 0)

                    <flux:menu.separator/>

                    <flux:menu.heading> {{ __('Switch Teams') }}</flux:menu.heading>

                    @foreach (Auth::user()->allTeams() as $team)
                        <x-switchable-team :team="$team"/>
                    @endforeach
                @endif

            </flux:menu>
        </flux:dropdown>
    @endif

    <flux:dropdown position="top" align="start">

        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <button
                class="flex text-sm border-2 border-transparent rounded-full focus:outline-2 focus:border-gray-300 transition">
                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                     alt="{{ Auth::user()->name }}"/>
            </button>
        @else
            <span class="inline-flex rounded-md">
             <button type="button"
                     class="inline-flex items-center px-3 py-2 border border-transparent text-sm/4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-2 focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                      {{ Auth::user()->name }}
                 <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                 </svg>

             </button>
            </span>
        @endif

        <flux:menu class="w-64">

            <flux:menu.heading>{{ __('Manage Account') }}</flux:menu.heading>

            <flux:menu.item wire:navigate.hover href="{{ route('profile.show') }}">
                <flux:icon.user variant="outline" class="size-5"/>{{ __('Profile') }}
            </flux:menu.item>

            <flux:menu.separator/>

            <flux:menu.heading> {{ __('Finance') }}</flux:menu.heading>

            <flux:menu.item wire:navigate.hover href="/billing">
                <flux:icon.credit-card variant="outline" class="size-5"/>{{ __('Subscription') }}
            </flux:menu.item>

            <flux:menu.item wire:navigate.hover href="/billing">
                <flux:icon.banknotes variant="outline" class="size-5"/> {{ __('Payments') }}
            </flux:menu.item>


            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                <flux:menu.separator/>

                <flux:menu.heading> {{ __('Security') }}</flux:menu.heading>

                <flux:menu.item wire:navigate.hover href="{{ route('api-tokens.index') }}">
                    <flux:icon.lock-closed variant="outline" class="size-5"/>
                    {{ __('API Tokens') }}
                </flux:menu.item>
            @endif
            <flux:menu.separator/>

            <!-- Authentication -->
            <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf

                <flux:menu.item variant="danger" href="{{ route('logout') }}"
                                @click.prevent="$root.submit();">
                    <flux:icon.arrow-right-start-on-rectangle variant="outline" class="size-5"/>
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>
