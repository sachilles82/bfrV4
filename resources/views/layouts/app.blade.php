<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    id="htmlRoot"
    class="dark h-full bg-gray-50 theme-{{ Auth::user()->theme ?? 'default' }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    @fluxAppearance

    <script>
        (function() {
            let localTheme = localStorage.getItem('theme');
            if (localTheme) {
                const htmlRoot = document.getElementById('htmlRoot');
                htmlRoot.classList.remove(
                    'theme-default', 'theme-orange', 'theme-green', 'theme-blue', 'theme-red', 'theme-lime', 'theme-pink'
                );
                htmlRoot.classList.add('theme-' + localTheme);
            }
        })
        ();
    </script>

</head>
<body class="font-sans antialiased h-full overflow-hidden dark:bg-gray-900">
<div x-data="{ menu : false }" class="flex h-full">
    <!-- Left sidebar -->
    <x-navigation.sidebar/>

    <!-- Mobile menu -->
    <x-navigation.mobile/>

    <!-- Content area -->
    <div class="flex flex-1 flex-col overflow-hidden">
        <!-- Top bar -->
        <x-navigation.header/>

        <!-- Main content -->
        <div class="flex flex-1 items-stretch overflow-hidden">
            <main class="flex-1 overflow-y-auto">
                <!-- Primary column -->
                <section aria-labelledby="primary-heading" class="flex h-full min-w-0 flex-1 flex-col lg:order-last">
                    {{ $slot }}
                </section>
            </main>
        </div>
    </div>
</div>

@stack('modals')

<flux:toast position="top right" class="pt-24"/>
@fluxScripts
@livewireScripts
<script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>
