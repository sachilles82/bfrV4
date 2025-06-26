<nav class="bg-white border-b sticky top-0 z-40">
    <div class="container mx-auto px-6">
        <div class="flex space-x-8 overflow-x-auto">
            @php
                $navItems = [
                    'overview' => 'Übersicht',
                    'supervisor-system' => 'Supervisor System',
                    'url-slug' => 'URL Slug System',
                    'traits' => 'Traits',
                    'relationships' => 'Relationships',
                    'attributes' => 'Attribute',
                    'methods' => 'Methoden',
                    'implementation' => 'Implementation'
                ];
            @endphp

            @foreach($navItems as $key => $label)
                <button
                    @click="activeSection = '{{ $key }}'"
                    :class="activeSection === '{{ $key }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
</nav>
