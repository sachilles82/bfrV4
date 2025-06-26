<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Model - Dokumentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900">
<div class="min-h-screen" x-data="documentation()">
    @include('documentation.partials.header')
    @include('documentation.partials.navigation')

    <main class="container mx-auto px-6 py-8">
        @include('documentation.sections.overview')
        @include('documentation.sections.supervisor-system')
        @include('documentation.sections.url-slug-system')
{{--        @include('documentation.sections.traits')--}}
{{--        @include('documentation.sections.relationships')--}}
{{--        @include('documentation.sections.attributes')--}}
{{--        @include('documentation.sections.methods')--}}
{{--        @include('documentation.sections.implementation')--}}

    </main>
</div>

@include('documentation.scripts.main')
</body>
</html>
