<div x-show="activeSection === 'url-slug'" class="space-y-8">
    <body>
    <div class="header">
        <h1>URL Slug Feature Documentation</h1>
        <p>Automatische SEO-freundliche URL-Generierung für User Model</p>
    </div>

    <div class="section">
        <h2>🎯 Übersicht</h2>
        <p>Das URL Slug Feature ermöglicht die automatische Generierung von SEO-freundlichen URLs für Benutzerprofile.
            Anstatt technische IDs zu verwenden, werden lesbare URLs wie <code>/employees/max-mustermann</code> erstellt.</p>

        <div class="feature-grid">
            <div class="feature-card">
                <h4>✨ Hauptfunktionen</h4>
                <ul>
                    <li>Automatische Slug-Generierung beim Erstellen</li>
                    <li>Eindeutige URL-Slugs pro Benutzer</li>
                    <li>SEO-freundliche URLs</li>
                    <li>Laravel Route Model Binding Support</li>
                </ul>
            </div>

            <div class="feature-card">
                <h4>🔧 Technische Details</h4>
                <ul>
                    <li>Trait: <code>UserHasURL</code></li>
                    <li>Datenbank-Feld: <code>url_slug</code></li>
                    <li>Automatische Duplikat-Vermeidung</li>
                    <li>Cache-optimiert</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>📋 Voraussetzungen</h2>

        <h3>Datenbank-Migration</h3>
        <p>Das <code>url_slug</code> Feld muss in der <code>users</code> Tabelle vorhanden sein:</p>

        <div class="code-block">
            <pre><code>// database/migrations/0001_01_01_000000_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('url_slug')->unique()->nullable();
    // ... weitere Felder
});</code></pre>
        </div>

        <h3>Model-Konfiguration</h3>
        <p>Das User Model muss den <code>UserHasURL</code> Trait verwenden:</p>

        <div class="code-block">
            <pre><code>// app/Models/User.php
use App\Traits\User\UserHasURL;

class User extends Authenticatable
{
    use UserHasURL;

    protected $fillable = [
        'name',
        'email',
        'url_slug',
        // ... weitere Felder
    ];
}</code></pre>
        </div>
    </div>

    <div class="section">
        <h2>🚀 Implementierung</h2>

        <h3>Schritt-für-Schritt Anleitung</h3>

        <ol class="step-list">
            <li>
                <strong>Trait zum User Model hinzufügen</strong>
                <div class="code-block">
                    <pre><code>use App\Traits\User\UserHasURL;

class User extends Authenticatable
{
    use UserHasURL;
    // ...
}</code></pre>
                </div>
            </li>

            <li>
                <strong>url_slug zu fillable hinzufügen</strong>
                <div class="code-block">
                    <pre><code>protected $fillable = [
    'name',
    'email',
    'url_slug', // Hinzufügen
    // ...
];</code></pre>
                </div>
            </li>

            <li>
                <strong>Routes konfigurieren</strong>
                <div class="code-block">
                    <pre><code>// routes/web.php
use App\Models\User;
use App\Http\Controllers\Alem\Employee\EmployeeProfileController;

// Route mit explizitem url_slug Binding und optionalem activeTab Parameter
Route::get('/employees/{employee:url_slug}/{activeTab?}', [EmployeeProfileController::class, 'show'])
    ->name('employees.profile');

// Der Controller erhält automatisch das User Model über den url_slug
public function show(User $employee, string $activeTab = 'employee-update'): View
{
    $authUser = Auth::user();

    return view('laravel.alem.employee.show', [
        'employee' => $employee,
        'activeTab' => $activeTab,
        'authUserId' => $authUser->id,
        'currentTeamId' => $authUser->current_team_id,
        'companyId' => $authUser->company_id,
    ]);
}</code></pre>
                </div>
            </li>

            <li>
                <strong>Links in Views generieren</strong>
                <div class="code-block">
                    <pre><code>@verbatim
                                // In Blade Templates
                                <a wire:navigate href="{{ route('employees.profile', $user) }}"
                                   class="font-medium text-gray-900 dark:text-gray-300 hover:text-indigo-700 decoration-1 hover:underline dark:hover:text-indigo-400">
    {{ $user->name }}
</a>

                                // Generiert: /employees/max-mustermann
                                // Mit Tab: /employees/max-mustermann/documents
                            @endverbatim</code></pre>
                </div>
            </li>
        </ol>
    </div>

    <div class="section">
        <h2>💡 Verwendung</h2>

        <h3>Automatische Slug-Generierung</h3>
        <p>Beim Erstellen eines neuen Benutzers wird automatisch ein Slug generiert:</p>

        <div class="code-block">
            <pre><code>// Beim Erstellen
$user = User::create([
    'name' => 'Max Mustermann',
    'email' => 'max@example.com',
    // url_slug wird automatisch zu 'max-mustermann'
]);

// Oder manuell setzen
$user = User::create([
    'name' => 'Max Mustermann',
    'email' => 'max@example.com',
    'url_slug' => 'max-m', // Manuell gesetzt
]);</code></pre>
        </div>

        <h3>Eindeutigkeit</h3>
        <p>Bei Duplikaten wird automatisch eine Nummer angehängt:</p>

        <div class="code-block">
            <pre><code>// Erster User: max-mustermann
// Zweiter User mit gleichem Namen: max-mustermann-1
// Dritter User: max-mustermann-2</code></pre>
        </div>

        <div class="alert alert-info">
            <strong>Info:</strong> Die Eindeutigkeit wird durch die <code>generateUniqueSlug()</code> Methode im Trait sichergestellt.
        </div>
    </div>

    <div class="section">
        <h2>🔍 Beispiele</h2>

        <h3>In Controllern</h3>
        <div class="code-block">
            <pre><code>// app/Http/Controllers/Alem/Employee/EmployeeProfileController.php
public function show(User $employee, string $activeTab = 'employee-update'): View
{
    $authUser = Auth::user();

    return view('laravel.alem.employee.show', [
        'employee' => $employee, // Automatisch über Slug geladen
        'activeTab' => $activeTab,
        'authUserId' => $authUser->id,
        'currentTeamId' => $authUser->current_team_id,
        'companyId' => $authUser->company_id,
    ]);
}</code></pre>
        </div>

        <h3>In Livewire Komponenten</h3>
        <div class="code-block">
            <pre><code>@verbatim
                        // In der Tabellen-Ansicht
                        <a wire:navigate href="{{ route('employees.profile', $user) }}"
                           class="font-medium text-gray-900 hover:text-indigo-700">
    {{ $user->name }}
</a>
                    @endverbatim</code></pre>
        </div>

        <h3>In Seedern</h3>
        <div class="code-block">
            <pre><code>// database/seeders/User/DummyUserSeeder.php
User::create([
    'name' => 'Super Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('secret'),
    'url_slug' => Str::slug('Super Admin'),
]);

// database/seeders/User/TestDataSeeder.php
// Bulk-Insert mit automatischer Slug-Generierung
return DB::table('users')->insertGetId([
    'name' => $firstName,
    'email' => $email,
    'url_slug' => Str::slug($firstName . $suffix . '-' . $index),
    // ... weitere Felder
]);</code></pre>
        </div>
        <h3>Routendefinition mit optionalen Parametern</h3>
        <p>Die Route unterstützt einen optionalen <code>activeTab</code> Parameter für Tab-Navigation:</p>

        <div class="code-block">
            <pre><code>// Route mit url_slug Binding
Route::get('/employees/{employee:url_slug}/{activeTab?}', [EmployeeProfileController::class, 'show'])
    ->name('employees.profile');

// Generiert URLs wie:
// /employees/max-mustermann
// /employees/max-mustermann/report
// /employees/max-mustermann/documents</code></pre>
        </div>

        <div class="section">
            <h2>📚 Zusammenfassung</h2>

            <p>Das URL Slug Feature bietet eine elegante Lösung für SEO-freundliche URLs in Laravel-Anwendungen.
                Durch die Verwendung des <code>UserHasURL</code> Traits wird die Implementierung vereinfacht und konsistent gehalten.</p>

            <h3>Hauptvorteile:</h3>
            <ul>
                <li>🔍 <strong>SEO-Optimierung:</strong> Lesbare URLs verbessern das Ranking</li>
                <li>👤 <strong>Benutzerfreundlichkeit:</strong> Einfacher zu merken und zu teilen</li>
                <li>🛡️ <strong>Sicherheit:</strong> Keine Preisgabe von technischen IDs</li>
                <li>⚡ <strong>Performance:</strong> Optimiert durch Indizierung und Caching</li>
            </ul>

            <div class="alert alert-info">
                <strong>Tipp:</strong> Kombiniere das URL Slug Feature mit anderen SEO-Maßnahmen wie Meta-Tags
                und strukturierten Daten für optimale Ergebnisse.
            </div>
        </div>
    </div>

    <div class="section">
        <h2>⚙️ Erweiterte Funktionen</h2>

        <h3>UserHasURL Trait Methoden</h3>
        <table>
            <thead>
            <tr>
                <th>Methode</th>
                <th>Beschreibung</th>
                <th>Beispiel</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><code>getRouteKeyName()</code></td>
                <td>Definiert das Feld für Route Model Binding</td>
                <td>Gibt 'url_slug' zurück</td>
            </tr>
            <tr>
                <td><code>createSlug()</code></td>
                <td>Generiert einen Slug aus dem Namen</td>
                <td>Max Mustermann → max-mustermann</td>
            </tr>
            <tr>
                <td><code>generateUniqueSlug()</code></td>
                <td>Stellt Eindeutigkeit sicher</td>
                <td>Fügt Zahlen bei Duplikaten hinzu</td>
            </tr>
            </tbody>
        </table>

        <h3>Model Events</h3>
        <p>Der Trait nutzt das <code>creating</code> Event, um automatisch Slugs zu generieren:</p>

        <div class="code-block">
            <pre><code>// Automatisch im UserHasURL Trait
static::creating(function ($model) {
    if (empty($model->url_slug)) {
        $model->url_slug = $model->generateUniqueSlug();
    }
});</code></pre>
        </div>
    </div>

    <div class="section">
        <h2>🛡️ Best Practices</h2>

        <div class="feature-grid">
            <div class="feature-card">
                <h4>✅ Do's</h4>
                <ul>
                    <li>Immer den Trait verwenden für konsistente URLs</li>
                    <li>Route Model Binding für saubere Controller</li>
                    <li>Slugs in Links verwenden statt IDs</li>
                    <li>Bei Updates den alten Slug behalten (SEO)</li>
                </ul>
            </div>

            <div class="feature-card">
                <h4>❌ Don'ts</h4>
                <ul>
                    <li>Slugs nicht manuell ohne Validierung ändern</li>
                    <li>Keine Sonderzeichen in Slugs verwenden</li>
                    <li>Slugs nicht als primären Schlüssel verwenden</li>
                    <li>Keine zu langen Slugs generieren</li>
                </ul>
            </div>
        </div>

        <div class="alert alert-warning">
            <strong>Wichtig:</strong> Bei bestehenden Benutzern ohne Slug sollte ein Migrations-Script erstellt werden,
            um nachträglich Slugs zu generieren.
        </div>
    </div>

    <div class="section">
        <h2>🔧 Fehlerbehebung</h2>

        <h3>Häufige Probleme</h3>

        <table>
            <thead>
            <tr>
                <th>Problem</th>
                <th>Ursache</th>
                <th>Lösung</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>404 Error bei Routen</td>
                <td>Slug nicht vorhanden</td>
                <td>Migration für bestehende User ausführen</td>
            </tr>
            <tr>
                <td>Duplicate Entry Error</td>
                <td>Slug bereits vergeben</td>
                <td>generateUniqueSlug() verwenden</td>
            </tr>
            <tr>
                <td>Leerer Slug</td>
                <td>Name nicht gesetzt</td>
                <td>Validierung für Name sicherstellen</td>
            </tr>
            </tbody>
        </table>

        <h3>Migration für bestehende Benutzer</h3>
        <div class="code-block">
            <pre><code>// Artisan Command erstellen
php artisan make:command GenerateUserSlugs

// In der handle() Methode:
User::whereNull('url_slug')->chunk(100, function ($users) {
    foreach ($users as $user) {
        $user->url_slug = $user->generateUniqueSlug();
        $user->save();
    }
});</code></pre>
        </div>
    </div>

    <div class="section">
        <h2>📁 Betroffene Dateien</h2>

        <h3>Core-Implementierung</h3>
        <table>
            <thead>
            <tr>
                <th>Datei</th>
                <th>Zweck</th>
                <th>Wichtige Funktionen</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><code>app/Traits/User/UserHasURL.php</code></td>
                <td>Haupt-Trait für URL Slug Funktionalität</td>
                <td>
                    • <code>getRouteKeyName()</code><br>
                    • <code>createSlug()</code><br>
                    • <code>generateUniqueSlug()</code><br>
                    • Boot-Methode für automatische Generierung
                </td>
            </tr>
            <tr>
                <td><code>app/Models/User.php</code></td>
                <td>User Model mit Trait-Integration</td>
                <td>
                    • Verwendet <code>UserHasURL</code> Trait<br>
                    • <code>url_slug</code> in fillable array<br>
                    • Route Model Binding ready
                </td>
            </tr>
            <tr>
                <td><code>database/migrations/..._create_users_table.php</code></td>
                <td>Datenbank-Schema</td>
                <td>
                    • <code>$table->string('url_slug')->unique()->nullable()</code><br>
                    • Index für Performance
                </td>
            </tr>
            </tbody>
        </table>

        <h3>Verwendung in der Anwendung</h3>
        <table>
            <thead>
            <tr>
                <th>Datei</th>
                <th>Verwendung</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><code>routes/web.php</code></td>
                <td>@verbatim
                        Route Definition mit <code>{employee:url_slug}</code>
                    @endverbatim</td>
            </tr>
            <tr>
                <td><code>app/Http/Controllers/Alem/Employee/EmployeeProfileController.php</code></td>
                <td>Controller empfängt User Model über URL Slug</td>
            </tr>
            <tr>
                <td><code>resources/views/livewire/alem/employee/table.blade.php</code></td>
                <td>@verbatim
                        Links mit <code>route('employees.profile', $user)</code>
                    @endverbatim</td>
            </tr>
            <tr>
                <td><code>app/Livewire/Alem/Employee/CreateEmployee.php</code></td>
                <td>Automatische Slug-Generierung beim Erstellen</td>
            </tr>
            <tr>
                <td><code>database/seeders/User/DummyUserSeeder.php</code></td>
                <td>Manuelle Slug-Generierung für Test-Daten</td>
            </tr>
            <tr>
                <td><code>database/seeders/User/TestDataSeeder.php</code></td>
                <td>Bulk-Insert mit URL Slugs</td>
            </tr>
            </tbody>
        </table>

        <div class="alert alert-info">
            <strong>Info:</strong> Das URL Slug Feature ist tief in die Anwendung integriert und wird konsistent
            für alle User-bezogenen Funktionen verwendet.
        </div>
    </div>
    </body>
</div>
