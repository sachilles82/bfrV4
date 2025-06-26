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
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-6 py-4">
            <h1 class="text-3xl font-bold text-gray-900">User Model Dokumentation</h1>
            <p class="text-gray-600 mt-2">Vollständige Übersicht über Features, Traits und Funktionen</p>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white border-b sticky top-0 z-40">
        <div class="container mx-auto px-6">
            <div class="flex space-x-8 overflow-x-auto">
                <button
                    @click="activeSection = 'overview'"
                    :class="activeSection === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Übersicht
                </button>
                <button
                    @click="activeSection = 'supervisor-system'"
                    :class="activeSection === 'supervisor-system' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Supervisor System
                </button>
                <button
                    @click="activeSection = 'url-slug'"
                    :class="activeSection === 'url-slug' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    URL Slug System
                </button>
                <button
                    @click="activeSection = 'traits'"
                    :class="activeSection === 'traits' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Traits
                </button>
                <button
                    @click="activeSection = 'relationships'"
                    :class="activeSection === 'relationships' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Relationships
                </button>
                <button
                    @click="activeSection = 'attributes'"
                    :class="activeSection === 'attributes' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Attribute
                </button>
                <button
                    @click="activeSection = 'methods'"
                    :class="activeSection === 'methods' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Methoden
                </button>
                <button
                    @click="activeSection = 'implementation'"
                    :class="activeSection === 'implementation' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    Implementation
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-8">
        <!-- Overview Section -->
        <div x-show="activeSection === 'overview'" class="space-y-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-4">User Model Übersicht</h2>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium mb-3">Hauptfunktionen</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Authentifizierung & Autorisierung
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Hierarchisches Supervisor-System
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                URL Slug System (SEO-freundlich)
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Rollen & Berechtigungen (Spatie)
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Team-Management (Jetstream)
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Erweiterte Caching-Funktionen
                            </li>
                            <li class="flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                Volltext-Suche (Laravel Scout)
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium mb-3">Unterstützte User-Typen</h3>
                        <div class="space-y-2">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <span class="font-medium text-blue-900">Employee</span>
                                <p class="text-sm text-blue-700">Mitarbeiter mit erweiterten Funktionen</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="font-medium text-gray-600">Partner</span>
                                <p class="text-sm text-gray-500">Geplant für zukünftige Versionen</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="font-medium text-gray-600">Customer</span>
                                <p class="text-sm text-gray-500">Geplant für zukünftige Versionen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supervisor System Section -->
        <div x-show="activeSection === 'supervisor-system'" class="space-y-8">
            <!-- System Overview -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">🏢 Supervisor/Manager System</h2>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-blue-800 mb-2">🎯 Kernkonzept</h3>
                    <p class="text-blue-700">Das System basiert auf einer Doppelstrategie: <strong>Rollen-basierte
                            Manager-Erkennung</strong> kombiniert mit <strong>direkter Supervisor-Zuweisung</strong> für
                        maximale Flexibilität und Performance.</p>
                </div>

                <!-- System Architecture -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                        <h3 class="text-lg font-medium text-green-800 mb-3">🔄 Automatisches System</h3>
                        <ul class="space-y-2 text-green-700 text-sm">
                            <li>• Rolle hat <code>is_manager = true</code></li>
                            <li>• User bekommt automatisch <code>manager = true</code></li>
                            <li>• Erscheint in Supervisor-Dropdown</li>
                            <li>• Cache-optimiert für Performance</li>
                        </ul>
                    </div>

                    <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                        <h3 class="text-lg font-medium text-blue-800 mb-3">👤 Direkte Zuweisung</h3>
                        <ul class="space-y-2 text-blue-700 text-sm">
                            <li>• User wählt konkreten Supervisor</li>
                            <li>• <code>supervisor_id</code> wird gesetzt</li>
                            <li>• Hierarchie wird etabliert</li>
                            <li>• Flexible Organisationsstrukturen</li>
                        </ul>
                    </div>
                </div>

                <!-- Data Flow Diagram -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">📊 Datenfluss-Diagramm</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="bg-red-100 text-red-800 px-3 py-1 rounded text-sm font-medium">Role Model</div>
                            <span class="text-gray-400">→</span>
                            <code class="bg-gray-200 px-2 py-1 rounded text-sm">is_manager: boolean</code>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm font-medium">User Roles
                            </div>
                            <span class="text-gray-400">→</span>
                            <code class="bg-gray-200 px-2 py-1 rounded text-sm">syncRolesWithManagerCheck()</code>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-medium">User Model
                            </div>
                            <span class="text-gray-400">→</span>
                            <code class="bg-gray-200 px-2 py-1 rounded text-sm">manager: boolean</code>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-purple-100 text-purple-800 px-3 py-1 rounded text-sm font-medium">Supervisor
                                Selection
                            </div>
                            <span class="text-gray-400">→</span>
                            <code class="bg-gray-200 px-2 py-1 rounded text-sm">supervisor_id: integer</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Implementation -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">⚙️ Technische Implementierung</h2>

                <div class="space-y-6">
                    <!-- Database Schema -->
                    <div>
                        <h3 class="text-lg font-medium mb-3">🗄️ Datenbankschema</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="border rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">users Tabelle</h4>
                                <div class="space-y-1 text-sm font-mono">
                                    <div>supervisor_id: <span class="text-blue-600">integer nullable</span></div>
                                    <div>manager: <span class="text-green-600">boolean default false</span></div>
                                    <div>company_id: <span class="text-blue-600">integer</span></div>
                                </div>
                            </div>
                            <div class="border rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-2">roles Tabelle</h4>
                                <div class="space-y-1 text-sm font-mono">
                                    <div>is_manager: <span class="text-green-600">boolean default false</span></div>
                                    <div>name: <span class="text-blue-600">string</span></div>
                                    <div>guard_name: <span class="text-blue-600">string</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Key Components -->
                    <div>
                        <h3 class="text-lg font-medium mb-3">🧩 Hauptkomponenten</h3>
                        <div class="grid gap-4">
                            <template x-for="component in supervisorComponents" :key="component.name">
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-medium" x-text="component.name"></h4>
                                        <span
                                            :class="component.type === 'Model' ? 'bg-red-100 text-red-800' : component.type === 'Trait' ? 'bg-blue-100 text-blue-800' : component.type === 'Component' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                            class="px-2 py-1 rounded text-xs font-medium"
                                            x-text="component.type"
                                        ></span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2" x-text="component.description"></p>
                                    <div class="bg-gray-50 rounded p-2">
                                        <code class="text-xs" x-text="component.code"></code>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance & Caching -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">🚀 Performance & Caching</h2>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-yellow-800 mb-2">⚡ Performance-Optimierungen</h3>
                    <p class="text-yellow-700">Das System nutzt intelligentes Caching und Query-Optimierung für maximale
                        Performance bei großen Organisationen.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium mb-3">🗃️ Cache-Strategien</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 mt-2"></span>
                                <div>
                                    <strong>Company Manager Cache:</strong><br>
                                    <code class="text-xs bg-gray-100 px-1 rounded">company_managers_{company_id}</code>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-3 mt-2"></span>
                                <div>
                                    <strong>Auto-Invalidation:</strong><br>
                                    <span class="text-sm">Bei Rollen- oder Manager-Status Änderungen</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-purple-500 rounded-full mr-3 mt-2"></span>
                                <div>
                                    <strong>Collection Reload:</strong><br>
                                    <span class="text-sm">Livewire Components werden automatisch aktualisiert</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium mb-3">📈 Query-Optimierung</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2"></span>
                                <div>
                                    <strong>Keine zusätzlichen DB-Queries:</strong><br>
                                    <span class="text-sm">checkManagerInRoles() nutzt bereits geladene Daten</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-orange-500 rounded-full mr-3 mt-2"></span>
                                <div>
                                    <strong>Eager Loading:</strong><br>
                                    <span class="text-sm">Beziehungen werden vorgeladen</span>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="w-2 h-2 bg-teal-500 rounded-full mr-3 mt-2"></span>
                                <div>
                                    <strong>Smart Filtering:</strong><br>
                                    <span class="text-sm">Benutzer können sich nicht selbst als Supervisor wählen</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Real-world Examples -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">💼 Praxis-Beispiele</h2>

                <div class="space-y-6">
                    <template x-for="example in supervisorExamples" :key="example.title">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium mb-2" x-text="example.title"></h3>
                            <p class="text-gray-600 mb-3" x-text="example.description"></p>
                            <div class="bg-gray-50 rounded p-3">
                                <pre><code class="language-php text-sm" x-text="example.code"></code></pre>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- URL Slug System Section -->
        <div x-show="activeSection === 'url-slug'" class="space-y-8">
            <!-- System Overview -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">🔗 URL Slug System</h2>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-blue-800 mb-2">🎯 Kernkonzept</h3>
                    <p class="text-blue-700">Das URL Slug System ermöglicht <strong>SEO-freundliche und benutzerfreundliche URLs</strong> für User-Profile. Statt <code>/users/123</code> werden URLs wie <code>/users/john-doe</code> verwendet.</p>
                </div>

                <!-- System Architecture -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                        <h3 class="text-lg font-medium text-green-800 mb-3">✨ Hauptfunktionen</h3>
                        <ul class="space-y-2 text-green-700 text-sm">
                            <li>• Automatische Slug-Generierung aus Namen</li>
                            <li>• Eindeutigkeitsprüfung (keine Duplikate)</li>
                            <li>• Route Model Binding mit Slugs</li>
                            <li>• Custom Slug Support</li>
                            <li>• Automatische Updates bei Namensänderung</li>
                        </ul>
                    </div>

                    <div class="border border-purple-200 rounded-lg p-4 bg-purple-50">
                        <h3 class="text-lg font-medium text-purple-800 mb-3">🚀 Vorteile</h3>
                        <ul class="space-y-2 text-purple-700 text-sm">
                            <li>• SEO-optimierte URLs</li>
                            <li>• Benutzerfreundliche Links</li>
                            <li>• Datenschutz (keine IDs in URLs)</li>
                            <li>• Professionelles Erscheinungsbild</li>
                            <li>• Wiederverwendbar für andere Models</li>
                        </ul>
                    </div>
                </div>

                <!-- Data Flow Diagram -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">📊 Datenfluss-Diagramm</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded text-sm font-medium">User Creation</div>
                            <span class="text-gray-400">→</span>
                            <code class="bg-gray-200 px-2 py-1 rounded text-sm">name: "John Doe"</code>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-medium">Slug Generation</div>
                            <span class="text-gray-400">→</span>
                            <code class="bg-gray-200 px-2 py-1 rounded text-sm">url_slug: "john-doe"</code>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-purple-100 text-purple-800 px-3 py-1 rounded text-sm font-medium">Duplicate Check</div>
                            <span class="text-gray-400">→</span>
                            <code class="bg-gray-200 px-2 py-1 rounded text-sm">exists? → "john-doe-1"</code>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="bg-orange-100 text-orange-800 px-3 py-1 rounded text-sm font-medium">Route Binding</div>
                            <span class="text-gray-400">→</span>
                            <code class="bg-gray-200 px-2 py-1 rounded text-sm">/users/john-doe</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Implementation -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">⚙️ Technische Implementierung</h2>

                <div class="space-y-6">
                    <!-- Key Components -->
                    <div>
                        <h3 class="text-lg font-medium mb-3">🧩 Hauptkomponenten</h3>
                        <div class="grid gap-4">
                            <template x-for="component in urlSlugComponents" :key="component.name">
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-medium" x-text="component.name"></h4>
                                        <span
                                            :class="component.type === 'Trait' ? 'bg-blue-100 text-blue-800' : component.type === 'Method' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                            class="px-2 py-1 rounded text-xs font-medium"
                                            x-text="component.type"
                                        ></span>
                                    </div>
                                    <p class="text-gray-600 text-sm mb-2" x-text="component.description"></p>
                                    <div class="bg-gray-50 rounded p-2">
                                        <code class="text-xs" x-text="component.code"></code>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-world Examples -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">💼 Praxis-Beispiele</h2>

                <div class="space-y-6">
                    <template x-for="example in urlSlugExamples" :key="example.title">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="text-lg font-medium mb-2" x-text="example.title"></h3>
                            <p class="text-gray-600 mb-3" x-text="example.description"></p>
                            <div class="bg-gray-50 rounded p-3">
                                <pre><code class="language-php text-sm" x-text="example.code"></code></pre>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Traits Section -->
        <div x-show="activeSection === 'traits'" class="space-y-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">🧩 Traits Übersicht</h2>

                <div class="grid gap-4">
                    <template x-for="trait in traits" :key="trait.name">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-medium" x-text="trait.name"></h3>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">Trait</span>
                            </div>
                            <p class="text-gray-600 mb-3" x-text="trait.description"></p>
                            <div class="bg-gray-50 rounded p-3">
                                <code class="text-sm" x-text="trait.file"></code>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Relationships Section -->
        <div x-show="activeSection === 'relationships'" class="space-y-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">🔗 Model Relationships</h2>

                <div class="grid gap-4">
                    <template x-for="relationship in relationships" :key="relationship.name">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-medium" x-text="relationship.name"></h3>
                                <span
                                    :class="relationship.type === 'belongsTo' ? 'bg-blue-100 text-blue-800' : relationship.type === 'hasMany' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800'"
                                    class="px-2 py-1 rounded text-xs font-medium"
                                    x-text="relationship.type"
                                ></span>
                            </div>
                            <p class="text-gray-600 mb-3" x-text="relationship.description"></p>
                            <div class="bg-gray-50 rounded p-3">
                                <pre><code class="language-php text-sm" x-text="relationship.code"></code></pre>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Attributes Section -->
        <div x-show="activeSection === 'attributes'" class="space-y-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">📊 Model Attributes</h2>

                <div class="grid gap-4">
                    <template x-for="attribute in attributes" :key="attribute.name">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-medium" x-text="attribute.name"></h3>
                                <span
                                    :class="attribute.type === 'accessor' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'"
                                    class="px-2 py-1 rounded text-xs font-medium"
                                    x-text="attribute.type"
                                ></span>
                            </div>
                            <p class="text-gray-600 mb-3" x-text="attribute.description"></p>
                            <div class="bg-gray-50 rounded p-3">
                                <pre><code class="language-php text-sm" x-text="attribute.code"></code></pre>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Methods Section -->
        <div x-show="activeSection === 'methods'" class="space-y-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">⚙️ Model Methods</h2>

                <div class="grid gap-4">
                    <template x-for="method in methods" :key="method.name">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-medium" x-text="method.name"></h3>
                                <span
                                    :class="method.category === 'supervisor' ? 'bg-blue-100 text-blue-800' : method.category === 'slug' ? 'bg-green-100 text-green-800' : method.category === 'helper' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'"
                                    class="px-2 py-1 rounded text-xs font-medium"
                                    x-text="method.category"
                                ></span>
                            </div>
                            <p class="text-gray-600 mb-3" x-text="method.description"></p>
                            <div class="bg-gray-50 rounded p-3">
                                <pre><code class="language-php text-sm" x-text="method.code"></code></pre>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Implementation Section -->
        <div x-show="activeSection === 'implementation'" class="space-y-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">🛠️ Implementation Guide</h2>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-green-800 mb-2">✅ Vollständige Implementierung</h3>
                    <p class="text-green-700">Schritt-für-Schritt Anleitung zur Implementierung aller User Model Features in deiner Laravel-Anwendung.</p>
                </div>

                <div class="space-y-6">
                    <template x-for="(step, index) in implementationSteps" :key="step.title">
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div
                                    class="bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium mr-4"
                                    x-text="index + 1"
                                ></div>
                                <div>
                                    <h3 class="text-lg font-medium" x-text="step.title"></h3>
                                    <p class="text-gray-600 text-sm" x-text="step.description"></p>
                                </div>
                            </div>

                            <div class="ml-12 space-y-4">
                                <div class="bg-gray-50 rounded p-3">
                                    <pre><code class="language-php text-sm" x-text="step.code"></code></pre>
                                </div>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-3" x-show="step.notes">
                                    <p class="text-yellow-700 text-sm" x-text="step.notes"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function documentation() {
        return {
            activeSection: 'overview',

            supervisorComponents: [
                {
                    name: 'UserHasSupervisor Trait',
                    type: 'Trait',
                    description: 'Stellt alle supervisor-relevanten Methoden zur Verfügung',
                    code: 'use App\\Traits\\User\\UserHasSupervisor;'
                },
                {
                    name: 'SupervisorManagerCacheService',
                    type: 'Service',
                    description: 'Verwaltet das Caching von Manager-Listen pro Unternehmen',
                    code: 'SupervisorManagerCacheService::getManagersForCompany($companyId)'
                },
                {
                    name: 'SupervisorSelect Component',
                    type: 'Component',
                    description: 'Livewire-Komponente für die Supervisor-Auswahl',
                    code: ''
                }
            ],

            supervisorExamples: [
                {
                    title: 'Manager Status überprüfen',
                    description: 'Wie man prüft, ob ein User ein Manager ist',
                    code: `// Direkt über das Attribut
if ($user->manager) {
    // User ist Manager
}

// Über die Trait-Methode
if ($user->isManager()) {
    // User ist Manager
}`
                },
                {
                    title: 'Verfügbare Supervisors abrufen',
                    description: 'Alle verfügbaren Supervisors für einen User abrufen',
                    code: `// Für ein bestimmtes Unternehmen
$supervisors = $user->getAvailableSupervisors();

// Manuell für ein anderes Unternehmen
$supervisors = User::getAvailableSupervisorsForCompany($companyId);`
                },
                {
                    title: 'Supervisor zuweisen',
                    description: 'Einem User einen Supervisor zuweisen',
                    code: `// Direktzuweisung
$user->supervisor_id = $supervisorId;
$user->save();

// Über Relationship
$user->supervisor()->associate($supervisor);
$user->save();`
                }
            ],

            urlSlugComponents: [
                {
                    name: 'UserHasURL Trait',
                    type: 'Trait',
                    description: 'Stellt alle URL-Slug-relevanten Methoden zur Verfügung',
                    code: 'use App\\Traits\\User\\UserHasURL;'
                },
                {
                    name: 'generateUniqueSlug()',
                    type: 'Method',
                    description: 'Generiert einen eindeutigen Slug basierend auf dem Namen',
                    code: 'public function generateUniqueSlug(): string'
                },
                {
                    name: 'getRouteKeyName()',
                    type: 'Method',
                    description: 'Laravel Route Model Binding mit Slug statt ID',
                    code: 'public function getRouteKeyName(): string { return "url_slug"; }'
                }
            ],

            urlSlugExamples: [
                {
                    title: 'Automatische Slug-Generierung',
                    description: 'Bei der User-Erstellung wird automatisch ein Slug generiert',
                    code: `$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    // url_slug wird automatisch zu "john-doe"
]);

echo $user->url_slug; // "john-doe"`
                },
                {
                    title: 'Route Model Binding',
                    description: 'URLs verwenden Slugs statt IDs',
                    code: `// Route Definition
Route::get('/users/{user}', [UserController::class, 'show']);

// Controller
public function show(User $user) {
    // $user wird automatisch über url_slug gefunden
    return view('users.show', compact('user'));
}

// URL: /users/john-doe`
                },
                {
                    title: 'Custom Slug setzen',
                    description: 'Manuell einen benutzerdefinierten Slug setzen',
                    code: `// Custom Slug setzen
$user->setCustomSlug('custom-slug-name');

// Prüfen ob Custom Slug
if ($user->hasCustomSlug()) {
    // User hat einen benutzerdefinierten Slug
}`
                }
            ],

            traits: [
                {
                    name: 'UserHasSupervisor',
                    description: 'Verwaltet die Supervisor-Hierarchie und Manager-Funktionalität',
                    file: 'app/Traits/User/UserHasSupervisor.php'
                },
                {
                    name: 'UserHasURL',
                    description: 'Implementiert SEO-freundliche URL-Slugs für User-Profile',
                    file: 'app/Traits/User/UserHasURL.php'
                },
                {
                    name: 'UserHasHelpers',
                    description: 'Stellt verschiedene Helper-Methoden für User-Operationen bereit',
                    file: 'app/Traits/User/UserHasHelpers.php'
                }
            ],

            relationships: [
                {
                    name: 'supervisor',
                    type: 'belongsTo',
                    description: 'Beziehung zum direkten Supervisor des Users',
                    code: 'public function supervisor(): BelongsTo\n{\n    return $this->belongsTo(User::class, "supervisor_id");\n}'
                },
                {
                    name: 'subordinates',
                    type: 'hasMany',
                    description: 'Alle Users, die diesem User als Supervisor zugeordnet sind',
                    code: 'public function subordinates(): HasMany\n{\n    return $this->hasMany(User::class, "supervisor_id");\n}'
                },
                {
                    name: 'company',
                    type: 'belongsTo',
                    description: 'Beziehung zum Unternehmen des Users',
                    code: 'public function company(): BelongsTo\n{\n    return $this->belongsTo(Company::class);\n}'
                }
            ],

            attributes: [
                {
                    name: 'initials',
                    type: 'accessor',
                    description: 'Generiert Initialien aus dem Namen des Users',
                    code: 'protected function initials(): Attribute\n{\n    return Attribute::make(\n        get: fn () => $this->generateInitials()\n    );\n}'
                },
                {
                    name: 'fullName',
                    type: 'accessor',
                    description: 'Kombiniert Vor- und Nachname zu einem vollständigen Namen',
                    code: 'protected function fullName(): Attribute\n{\n    return Attribute::make(\n        get: fn () => trim($this->first_name . " " . $this->last_name)\n    );\n}'
                }
            ],

            methods: [
                {
                    name: 'isManager()',
                    category: 'supervisor',
                    description: 'Prüft, ob der User ein Manager ist',
                    code: 'public function isManager(): bool\n{\n    return $this->manager;\n}'
                },
                {
                    name: 'getAvailableSupervisors()',
                    category: 'supervisor',
                    description: 'Gibt alle verfügbaren Supervisors für diesen User zurück',
                    code: 'public function getAvailableSupervisors(): Collection\n{\n    return self::getAvailableSupervisorsForCompany($this->company_id)\n        ->where("id", "!=", $this->id);\n}'
                },
                {
                    name: 'generateUniqueSlug()',
                    category: 'slug',
                    description: 'Generiert einen eindeutigen URL-Slug für den User',
                    code: 'public function generateUniqueSlug(): string\n{\n    $baseSlug = Str::slug($this->name);\n    return $this->makeSlugUnique($baseSlug);\n}'
                },
                {
                    name: 'getProfileUrl()',
                    category: 'helper',
                    description: 'Gibt die vollständige URL zum User-Profil zurück',
                    code: 'public function getProfileUrl(): string\n{\n    return route("users.show", $this->url_slug);\n}'
                }
            ],

            implementationSteps: [
                {
                    title: 'Datenbank-Migrationen',
                    description: 'Benötigte Spalten zu users-Tabelle hinzufügen',
                    code: `Schema::table('users', function (Blueprint $table) {
    $table->unsignedBigInteger('supervisor_id')->nullable();
    $table->boolean('manager')->default(false);
    $table->string('url_slug')->unique()->nullable();
    $table->index('url_slug');

    $table->foreign('supervisor_id')->references('id')->on('users');
});`,
                    notes: 'Vergiss nicht, php artisan migrate auszuführen'
                },
                {
                    title: 'Traits in User Model einbinden',
                    description: 'Alle benötigten Traits zum User Model hinzufügen',
                    code: `use App\\Traits\\User\\UserHasSupervisor;
use App\\Traits\\User\\UserHasURL;
use App\\Traits\\User\\UserHasHelpers;

class User extends Authenticatable
{
    use UserHasSupervisor, UserHasURL, UserHasHelpers;

    // Rest der Model-Definition...
}`,
                    notes: 'Stelle sicher, dass alle Trait-Dateien existieren'
                },
                {
                    title: 'Route Model Binding konfigurieren',
                    description: 'Routes für URL-Slug-basierte Navigation einrichten',
                    code: `Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

// Das User Model überschreibt getRouteKeyName() automatisch`,
                    notes: 'URLs verwenden jetzt Slugs statt IDs: /users/john-doe'
                },
                {
                    title: 'Supervisor-System aktivieren',
                    description: 'Manager-Status und Supervisor-Beziehungen einrichten',
                    code: `// In einer Service-Klasse oder Controller
$user->syncRolesWithManagerCheck();

// Manager-Liste für Dropdown abrufen
$managers = User::getAvailableSupervisorsForCompany($companyId);

// Supervisor zuweisen
$user->supervisor_id = $supervisorId;
$user->save();`,
                    notes: 'Das System erkennt Manager automatisch basierend auf Rollen'
                }
            ]
        }
    }
</script>

</body>
</html>
