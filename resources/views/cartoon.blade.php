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
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
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

        <!-- Traits Section -->
        <div x-show="activeSection === 'traits'" class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">Verwendete Traits</h2>
                <div class="grid gap-6">
                    <template x-for="trait in traits" :key="trait.name">
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium" x-text="trait.name"></h3>
                                    <p class="text-gray-600 mt-1" x-text="trait.description"></p>
                                    <div class="mt-3">
                                        <span class="text-sm font-medium text-gray-700">Herkunft: </span>
                                        <span class="text-sm text-blue-600" x-text="trait.source"></span>
                                    </div>
                                    <div class="mt-2" x-show="trait.methods.length > 0">
                                        <span class="text-sm font-medium text-gray-700">Wichtige Methoden: </span>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <template x-for="method in trait.methods" :key="method">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                    x-text="method"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                        <span
                                            :class="trait.type === 'Laravel' ? 'bg-red-100 text-red-800' : trait.type === 'Custom' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            x-text="trait.type"
                                        ></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Relationships Section -->
        <div x-show="activeSection === 'relationships'" class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">Model Relationships</h2>
                <div class="grid gap-4">
                    <template x-for="relationship in relationships" :key="relationship.name">
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-medium" x-text="relationship.name"></h3>
                                <span
                                    :class="getRelationshipTypeClass(relationship.type)"
                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    x-text="relationship.type"
                                ></span>
                            </div>
                            <p class="text-gray-600 mb-3" x-text="relationship.description"></p>
                            <div class="bg-gray-50 rounded p-3">
                                <code class="text-sm" x-text="relationship.usage"></code>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Attributes Section -->
        <div x-show="activeSection === 'attributes'" class="space-y-6">
            <div class="grid gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-2xl font-semibold mb-6">Dynamischer Status</h2>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <h3 class="font-medium text-yellow-800 mb-2">🔥 Besonderheit</h3>
                        <p class="text-yellow-700">Das User Model hat einen dynamischen Status, der sich je nach
                            User-Typ ändert.</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium mb-2">Employee Status</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">active</span>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">probation</span>
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">terminated</span>
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">suspended</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-2xl font-semibold mb-6">Fillable Attributes</h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="attribute in fillableAttributes" :key="attribute.name">
                            <div class="border rounded p-3">
                                <code class="text-sm font-medium text-blue-600" x-text="attribute.name"></code>
                                <p class="text-xs text-gray-500 mt-1" x-text="attribute.type"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Methods Section -->
        <div x-show="activeSection === 'methods'" class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">Custom Methods</h2>
                <div class="space-y-6">
                    <template x-for="method in customMethods" :key="method.name">
                        <div class="border rounded-lg p-4">
                            <h3 class="text-lg font-medium mb-2" x-text="method.name + '()'"></h3>
                            <p class="text-gray-600 mb-3" x-text="method.description"></p>
                            <div class="bg-gray-50 rounded p-3">
                                <pre><code class="language-php text-sm" x-text="method.example"></code></pre>
                            </div>
                            <div class="mt-3" x-show="method.returns">
                                <span class="text-sm font-medium text-gray-700">Returns: </span>
                                <code class="text-sm text-blue-600" x-text="method.returns"></code>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Implementation Guide Section -->
        <div x-show="activeSection === 'implementation'" class="space-y-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-semibold mb-6">📋 Schritt-für-Schritt Implementierung</h2>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-green-800 mb-2">🎯 Ziel</h3>
                    <p class="text-green-700">Ein vollständiges Supervisor/Manager-System implementieren, das
                        automatisch Manager basierend auf Rollen erkennt und direkte Supervisor-Zuweisungen
                        ermöglicht.</p>
                </div>

                <div class="space-y-8">
                    <template x-for="(step, index) in implementationSteps" :key="step.title">
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div
                                    :class="step.completed ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600'"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium mr-4"
                                    x-text="index + 1"
                                ></div>
                                <div>
                                    <h3 class="text-lg font-medium" x-text="step.title"></h3>
                                    <p class="text-gray-600 text-sm" x-text="step.description"></p>
                                </div>
                            </div>

                            <div class="ml-12 space-y-4">
                                <template x-for="task in step.tasks" :key="task.name">
                                    <div class="border border-gray-100 rounded p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-medium text-sm" x-text="task.name"></h4>
                                            <span
                                                :class="task.type === 'Migration' ? 'bg-red-100 text-red-800' : task.type === 'Model' ? 'bg-blue-100 text-blue-800' : task.type === 'Trait' ? 'bg-green-100 text-green-800' : task.type === 'Component' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'"
                                                class="px-2 py-1 rounded text-xs font-medium"
                                                x-text="task.type"
                                            ></span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-2" x-text="task.description"></p>
                                        <div class="bg-gray-50 rounded p-2" x-show="task.code">
                                            <pre><code class="language-php text-xs" x-text="task.code"></code></pre>
                                        </div>
                                        <div class="mt-2" x-show="task.notes">
                                            <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                                <p class="text-yellow-700 text-xs" x-text="task.notes"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Quick Reference -->
                <div class="bg-gray-50 rounded-lg p-6 mt-8">
                    <h3 class="text-lg font-medium mb-4">🔍 Quick Reference</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium mb-2">Wichtige Dateien</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                <li><code>app/Models/User.php</code></li>
                                <li><code>app/Models/Role.php</code></li>
                                <li><code>app/Traits/User/UserWithManagerRole.php</code></li>
                                <li><code>app/Traits/WithDropDownRelations.php</code></li>
                                <li><code>app/Livewire/EditEmployee.php</code></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Wichtige Methoden</h4>
                            <ul class="space-y-1 text-sm text-gray-600">
                                <li><code>getCompanyManagers()</code></li>
                                <li><code>syncRolesWithManagerCheck()</code></li>
                                <li><code>checkManagerInRoles()</code></li>
                                <li><code>clearManagerCache()</code></li>
                                <li><code>loadSupervisors()</code></li>
                            </ul>
                        </div>
                    </div>
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
                    name: 'UserWithManagerRole Trait',
                    type: 'Trait',
                    description: 'Stellt Methoden für Manager-Funktionalität bereit',
                    code: 'getCompanyManagers($companyId), clearManagerCache($companyId)'
                },
                {
                    name: 'WithDropDownRelations Trait',
                    type: 'Trait',
                    description: 'Lädt und cached Supervisor-Optionen für Dropdowns',
                    code: 'loadSupervisors(), forceReloadCollection(\'supervisors\')'
                },
                {
                    name: 'EditEmployee Component',
                    type: 'Component',
                    description: 'Synchronisiert Rollen mit Manager-Status',
                    code: 'syncRolesWithManagerCheck(), checkManagerInRoles()'
                },
                {
                    name: 'Role Model',
                    type: 'Model',
                    description: 'Definiert welche Rollen Manager-Berechtigung haben',
                    code: 'is_manager: boolean, fillable: [\'is_manager\']'
                }
            ],

            supervisorExamples: [
                {
                    title: '👥 Manager automatisch erkennen',
                    description: 'Wenn einem User eine Manager-Rolle zugewiesen wird, wird automatisch das manager-Flag gesetzt.',
                    code: `// 1. Role erstellen mit is_manager = true
$managerRole = Role::create([
    'name' => 'Team Lead',
    'is_manager' => true
]);

// 2. User die Rolle zuweisen
$user->assignRole($managerRole);
// Automatisch: $user->manager = true`
                },
                {
                    title: '🎯 Supervisor zuweisen',
                    description: 'Einem Employee einen konkreten Supervisor zuweisen.',
                    code: `// Supervisor aus Company Managern laden
$supervisors = User::getCompanyManagers($user->company_id);

// Supervisor zuweisen
$user->update([
    'supervisor_id' => $supervisorId
]);

// Beziehung nutzen
$supervisor = $user->supervisorUser;`
                },
                {
                    title: '🚀 Performance-optimierte Ladung',
                    description: 'Supervisor-Dropdown effizient laden ohne zusätzliche Queries.',
                    code: `// In WithDropDownRelations Trait
protected function loadSupervisors(): Collection
{
    return User::getCompanyManagers($this->companyId)
        ->reject(fn($sup) => $sup->id === $this->userId)
        ->map(fn($sup) => [
            'id' => $sup->id,
            'full_name' => $sup->name,
            'profile_photo_path' => $sup->profile_photo_path
        ]);
}`
                }
            ],

            implementationSteps: [
                {
                    title: 'Database Schema erweitern',
                    description: 'Füge die notwendigen Felder zu Users und Roles Tabellen hinzu',
                    completed: false,
                    tasks: [
                        {
                            name: 'Migration für users Tabelle',
                            type: 'Migration',
                            description: 'Supervisor und Manager Felder hinzufügen',
                            code: `Schema::table('users', function (Blueprint $table) {
    $table->unsignedBigInteger('supervisor_id')->nullable();
    $table->boolean('manager')->default(false);
    $table->foreign('supervisor_id')->references('id')->on('users');
});`,
                            notes: 'supervisor_id referenziert auf users.id für Self-Relation'
                        },
                        {
                            name: 'Migration für roles Tabelle',
                            type: 'Migration',
                            description: 'Manager-Flag für Rollen hinzufügen',
                            code: `Schema::table('roles', function (Blueprint $table) {
    $table->boolean('is_manager')->default(false);
});`,
                            notes: 'Bestimmt, ob eine Rolle Manager-Berechtigung hat'
                        }
                    ]
                },
                {
                    title: 'Models erweitern',
                    description: 'User und Role Models um neue Funktionalität erweitern',
                    completed: false,
                    tasks: [
                        {
                            name: 'User Model erweitern',
                            type: 'Model',
                            description: 'Supervisor Relationship und Fillable Attributes',
                            code: `// In User Model
protected $fillable = [
    // ... andere Felder
    'supervisor_id',
    'manager',
];

protected $casts = [
    // ... andere Casts
    'manager' => 'boolean',
];

public function supervisorUser(): BelongsTo
{
    return $this->belongsTo(User::class, 'supervisor_id');
}`,
                            notes: 'supervisorUser() für die Beziehung zum Vorgesetzten'
                        },
                        {
                            name: 'Role Model erweitern',
                            type: 'Model',
                            description: 'Manager-Flag hinzufügen',
                            code: `// In Role Model
protected $fillable = [
    // ... andere Felder
    'is_manager',
];

protected $casts = [
    // ... andere Casts
    'is_manager' => 'boolean',
];`,
                            notes: 'is_manager bestimmt Manager-Status der Rolle'
                        }
                    ]
                },
{{--                {--}}
{{--                    title: 'UserWithManagerRole Trait erstellen',--}}
{{--                    description: 'Trait für Manager-spezifische Funktionen erstellen',--}}
{{--                    completed: false,--}}
{{--                    tasks: [--}}
{{--                        {--}}
{{--                            name: 'Trait Datei erstellen',--}}
{{--                            type: 'Trait',--}}
{{--                            description: 'app/Traits/User/UserWithManagerRole.php',--}}
{{--                            code: `<?php--}}

{{--                                   namespace App\\Traits\\User;--}}

{{--                                   use Illuminate\\Database\\Eloquent\\Collection;--}}

{{--                                   use Illuminate\Support\Collection;--}}
{{--                                   use Illuminate\Support\Facades\Cache;--}}
{{--                                   use Illuminate\use Illuminate\Support\Collection;--}}

{{--                                   \Support\\Facades\\Cache;--}}

{{--                                   trait UserWithManagerRole--}}
{{--                                   {--}}
{{--                                       public static function getCompanyManagers(int $companyId): Collection--}}
{{--                                       {--}}
{{--                                           return Cache::remember(--}}
{{--                                               "company_managers_{$companyId}",--}}
{{--                                               3600, // 1 Stunde--}}
{{--                                               fn() => static::where('company_id', $companyId)--}}
{{--                                                   ->where('manager', true)--}}
{{--                                                   ->select(['id', 'name', 'profile_photo_path'])--}}
{{--                                                   ->get()--}}
{{--                                           );--}}
{{--                                       }--}}

{{--                                       public static function clearManagerCache(int $companyId): void--}}
{{--                                       {--}}
{{--                                           Cache::forget("company_managers_{$companyId}");--}}
{{--                                       }--}}
{{--                                   }`,--}}
{{--                                notes: 'Caching für bessere Performance bei großen Teams'--}}
{{--                            }--}}
{{--                        ]--}}
{{--                    },--}}
{{--                    {--}}
{{--                        title: 'EditEmployee Component erweitern',--}}
{{--                        description: 'Rollen-Synchronisation mit Manager-Check implementieren',--}}
{{--                        completed: false,--}}
{{--                        tasks: [--}}
{{--                            {--}}
{{--                                name: 'syncRolesWithManagerCheck Methode',--}}
{{--                                type: 'Component',--}}
{{--                                description: 'Optimierte Rollen-Synchronisation ohne zusätzliche Queries',--}}
{{--                                code: `private function syncRolesWithManagerCheck(): void--}}
{{--                            {--}}
{{--                                $originalRoleIds = $this->originalData['roleIds'] ?? [];--}}

{{--                                if (!$this->arraysAreDifferent($originalRoleIds, $this->selectedRoles)) {--}}
{{--                                    return;--}}
{{--                                }--}}

{{--                                $oldHasManager = $this->checkManagerInRoles($originalRoleIds);--}}
{{--                                $this->user->roles()->sync($this->selectedRoles);--}}
{{--                                $newHasManager = $this->checkManagerInRoles($this->selectedRoles);--}}

{{--                                if ($oldHasManager !== $newHasManager) {--}}
{{--                                    $this->user->update(['manager' => $newHasManager]);--}}
{{--                                    User::clearManagerCache($this->user->company_id);--}}
{{--                                    $this->forceReloadCollection('supervisors');--}}
{{--                                }--}}
{{--                            }`,--}}
{{--                                notes: 'Nutzt bereits geladene Daten - keine extra DB-Queries'--}}
{{--                            },--}}
{{--                            {--}}
{{--                                name: 'checkManagerInRoles Helper',--}}
{{--                                type: 'Component',--}}
{{--                                description: 'Prüft Manager-Status ohne Datenbankzugriff',--}}
{{--                                code: `private function checkManagerInRoles(array $roleIds): bool--}}
{{--                            {--}}
{{--                                return collect($this->roles)--}}
{{--                                    ->whereIn('id', $roleIds)--}}
{{--                                    ->contains('is_manager', true);--}}
{{--                            }`,--}}
{{--                                notes: 'Arbeitet mit bereits geladenen Rollen-Daten'--}}
{{--                            }--}}
{{--                        ]--}}
{{--                    },--}}
{{--                    {--}}
{{--                        title: 'WithDropDownRelations erweitern',--}}
{{--                        description: 'Supervisor-Dropdown-Funktionalität implementieren',--}}
{{--                        completed: false,--}}
{{--                        tasks: [--}}
{{--                            {--}}
{{--                                name: 'Supervisor Configuration',--}}
{{--                                type: 'Trait',--}}
{{--                                description: 'Dropdown-Konfiguration für Supervisors',--}}
{{--                                code: `// In getDropdownConfig() Methode--}}
{{--                                   'supervisors' => [--}}
{{--                                'collection' => 'supervisors',--}}
{{--                                'selected' => 'supervisor',--}}
{{--                                'loader' => fn() => $this->loadSupervisors(),--}}
{{--                                'mapper' => null,--}}
{{--                                'dependencies' => ['companyId', 'authUserId'],--}}
{{--                            ], `,--}}
{{--                                notes: 'Dependencies sorgen für automatisches Reload bei Änderungen'--}}
{{--                            },--}}
{{--                            {--}}
{{--                                name: 'loadSupervisors Methode',--}}
{{--                                type: 'Trait',--}}
{{--                                description: 'Effiziente Supervisor-Ladung',--}}
{{--                                code: `protected function loadSupervisors(): Collection--}}
{{--                            {--}}
{{--                                $excludeId = $this->userId ?? $this->authUserId;--}}

{{--                                return User::getCompanyManagers($this->companyId)--}}
{{--                                    ->reject(fn($sup) => $sup->id === $excludeId)--}}
{{--                                    ->map(fn($sup) => [--}}
{{--                                        'id' => $sup->id,--}}
{{--                                        'full_name' => $sup->name,--}}
{{--                                        'profile_photo_path' => $sup->profile_photo_path--}}
{{--                                    ]);--}}
{{--                            }`,--}}
{{--                                notes: 'User können sich nicht selbst als Supervisor wählen'--}}
{{--                            }--}}
{{--                        ]--}}
{{--                    },--}}
{{--                    {--}}
{{--                        title: 'Frontend Integration',--}}
{{--                        description: 'Blade Templates und UI-Komponenten implementieren',--}}
{{--                        completed: false,--}}
{{--                        tasks: [--}}
{{--                            {--}}
{{--                                name: 'Supervisor Dropdown',--}}
{{--                                type: 'Blade',--}}
{{--                                description: 'Supervisor-Auswahl in Employee Forms',--}}
{{--                                code: ` < flux:select--}}
{{--    wire:model = "supervisor"--}}
{{--    searchable--}}
{{--    placeholder = "Select Supervisor" >--}}

{{--                            @forelse($supervisors as $supervisor)--}}
{{--                            <flux:option value="{{ $supervisor['id'] }}">--}}
{{--            <div class="flex items-center gap-2">--}}
{{--                <flux:avatar--}}
{{--                    name="{{ $supervisor['full_name'] }}"--}}
{{--                    src="{{ $supervisor['profile_photo_path'] ? asset('storage/' . $supervisor['profile_photo_path']) : null }}"--}}
{{--                    size="xs"--}}
{{--                    circle--}}
{{--                />--}}
{{--                {{ $supervisor['full_name'] }}--}}
{{--                            </div>--}}
{{--                        </flux:option>--}}
{{--@empty--}}
{{--                            <flux:option value="">No supervisors found</flux:option>--}}
{{--@endforelse--}}
{{--                            </flux:select>`,--}}
{{--                            notes: 'Mit Avatar und Suchfunktion für bessere UX'--}}
{{--                        }--}}
{{--                    ]--}}
{{--                },--}}
                {
                    title: 'Testing & Validierung',
                    description: 'Tests schreiben und System validieren',
                    completed: false,
                    tasks: [
                        {
                            name: 'Unit Tests',
                            type: 'Test',
                            description: 'Tests für Manager-Erkennung und Supervisor-Zuweisung',
                            code: `// Feature Test Beispiel
public function test_user_becomes_manager_when_assigned_manager_role()
{
    $user = User::factory()->create();
    $managerRole = Role::create(['name' => 'Manager', 'is_manager' => true]);

    $user->assignRole($managerRole);

    $this->assertTrue($user->fresh()->manager);
}`,
                            notes: 'Teste alle kritischen Funktionen: Rollen-Sync, Cache-Clearing, etc.'
                        },
                        {
                            name: 'Performance Tests',
                            type: 'Test',
                            description: 'Cache-Performance und Query-Optimierung testen',
                            code: `public function test_supervisor_loading_uses_cache()
{
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(collect());

    User::getCompanyManagers(1);
}`,
                            notes: 'Stelle sicher, dass Caching korrekt funktioniert'
                        }
                    ]
                }
            ],

            traits: [
                {
                    name: 'HasAddress',
                    description: 'Fügt Adressfunktionalität hinzu',
                    source: 'App\\Traits\\HasAddress',
                    type: 'Custom',
                    methods: ['address()', 'getFullAddressAttribute()']
                },
                {
                    name: 'HasApiTokens',
                    description: 'Laravel Sanctum API Token Management',
                    source: 'Laravel\\Sanctum\\HasApiTokens',
                    type: 'Laravel',
                    methods: ['createToken()', 'tokens()']
                },
                {
                    name: 'HasRoles',
                    description: 'Spatie Rollen- und Berechtigungssystem',
                    source: 'Spatie\\Permission\\Traits\\HasRoles',
                    type: 'Package',
                    methods: ['assignRole()', 'hasRole()', 'can()']
                },
                {
                    name: 'AdvancedCache',
                    description: 'Erweiterte Caching-Funktionen mit automatischem Cache-Management',
                    source: 'App\\Traits\\Cache\\AdvancedCache',
                    type: 'Custom',
                    methods: ['getCached()', 'flushCache()', 'remember()']
                },
                {
                    name: 'ModelStatusManagement',
                    description: 'Verwaltet Model-Status mit Soft Delete Integration',
                    source: 'App\\Traits\\Model\\ModelStatusManagement',
                    type: 'Custom',
                    methods: ['activate()', 'deactivate()', 'restore()']
                },
                {
                    name: 'UserWithManagerRole',
                    description: 'Manager-spezifische Funktionen: Automatische Manager-Erkennung, Caching von Company Managern, Cache-Management',
                    source: 'App\\Traits\\User\\UserWithManagerRole',
                    type: 'Custom',
                    methods: ['getCompanyManagers()', 'clearManagerCache()', 'isManager()']
                },
                {
                    name: 'WithDropDownRelations',
                    description: 'Optimierte Dropdown-Ladung mit Caching und automatischem Reload bei Abhängigkeitsänderungen',
                    source: 'App\\Traits\\WithDropDownRelations',
                    type: 'Custom',
                    methods: ['loadSupervisors()', 'forceReloadCollection()', 'getDropdownConfig()']
                }
            ],

            relationships: [
                {
                    name: 'supervisorUser()',
                    type: 'BelongsTo',
                    description: 'Gibt den Vorgesetzten des Users zurück (Self-Referencing Relationship)',
                    usage: '$user->supervisorUser // User Model des Supervisors'
                },
                {
                    name: 'employee()',
                    type: 'HasOne',
                    description: 'Employee-Profil des Users (falls vorhanden)',
                    usage: '$user->employee // Employee Model'
                },
                {
                    name: 'department()',
                    type: 'BelongsTo',
                    description: 'Abteilung des Users',
                    usage: '$user->department // Department Model'
                },
                {
                    name: 'company()',
                    type: 'BelongsTo',
                    description: 'Unternehmen des Users',
                    usage: '$user->company // Company Model'
                },
                {
                    name: 'profession()',
                    type: 'BelongsTo',
                    description: 'Beruf/Position des Users',
                    usage: '$user->profession // Profession Model'
                },
                {
                    name: 'stage()',
                    type: 'BelongsTo',
                    description: 'Karrierestufe des Users',
                    usage: '$user->stage // Stage Model'
                }
            ],

            fillableAttributes: [
                {name: 'name', type: 'string'},
                {name: 'email', type: 'string'},
                {name: 'phone_1', type: 'string'},
                {name: 'phone_2', type: 'string'},
                {name: 'company_id', type: 'integer'},
                {name: 'department_id', type: 'integer'},
                {name: 'supervisor_id', type: 'integer (nullable)'},
                {name: 'manager', type: 'boolean (default: false)'},
                {name: 'user_type', type: 'UserType (Enum)'},
                {name: 'gender', type: 'Gender (Enum)'},
                {name: 'birthdate', type: 'date'},
                {name: 'joined_at', type: 'date'}
            ],

            customMethods: [
                {
                    name: 'getYearsOfServiceAttribute',
                    description: 'Berechnet die Betriebszugehörigkeit in Jahren basierend auf joined_at',
                    example: '$user->years_of_service // 5',
                    returns: 'int'
                },
                {
                    name: 'getStatusAttribute',
                    description: 'Dynamischer Status-Accessor - gibt den korrekten Enum-Typ zurück',
                    example: '$user->status // EmployeeStatus::Active',
                    returns: 'BackedEnum|null'
                },
                {
                    name: 'setStatusAttribute',
                    description: 'Status-Mutator für die Datenbankkonvertierung',
                    example: '$user->status = EmployeeStatus::Active;',
                    returns: 'void'
                },
                {
                    name: 'getCompanyManagers (static)',
                    description: 'Lädt alle Manager einer Company mit Caching für Performance',
                    example: 'User::getCompanyManagers($companyId) // Collection von Managern',
                    returns: 'Collection'
                },
                {
                    name: 'clearManagerCache (static)',
                    description: 'Löscht den Manager-Cache für eine bestimmte Company',
                    example: 'User::clearManagerCache($companyId)',
                    returns: 'void'
                }
            ],

            getRelationshipTypeClass(type) {
                switch (type) {
                    case 'BelongsTo':
                        return 'bg-blue-100 text-blue-800';
                    case 'HasOne':
                        return 'bg-green-100 text-green-800';
                    case 'HasMany':
                        return 'bg-purple-100 text-purple-800';
                    default:
                        return 'bg-gray-100 text-gray-800';
                }
            }
        }
    }
</script>
</body>
</html>
