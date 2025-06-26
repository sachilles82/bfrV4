
<div x-show="activeSection === 'url-slug'" class="space-y-8">

        <body>
        <div class="header">
            <h1>Supervisor Feature Documentation</h1>
            <p>Hierarchische Benutzerverwaltung für Laravel User Model</p>
        </div>

        <div class="section">
            <h2>🎯 Übersicht</h2>
            <p>Das Supervisor Feature ermöglicht die Zuweisung von Vorgesetzten zu Mitarbeitern im User Model.
                Es basiert auf einer einfachen Foreign Key Beziehung und nutzt das Manager-Flag aus den Rollen zur
                Bestimmung verfügbarer Supervisors.</p>

            <div class="feature-grid">
                <div class="feature-card">
                    <h4>✨ Hauptfunktionen</h4>
                    <ul>
                        <li>Supervisor-Zuweisung über <code>supervisor_id</code></li>
                        <li>Manager-basierte Supervisor-Auswahl</li>
                        <li>Validierung gegen Selbst-Zuweisung</li>
                        <li>Integration in Employee Model</li>
                    </ul>
                </div>

                <div class="feature-card">
                    <h4>🔧 Technische Details</h4>
                    <ul>
                        <li>Foreign Key: <code>supervisor_id</code> in users table</li>
                        <li>Relationship: <code>supervisorUser()</code></li>
                        <li>Manager-Flag über Rollen</li>
                        <li>Livewire V3 Components</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>📋 Voraussetzungen</h2>

            <h3>Datenbank-Migration</h3>
            <p>Das <code>supervisor_id</code> Feld wird in der Migration hinzugefügt:</p>

            <div class="code-block">
            <pre><code>// database/migrations/2025_06_19_101704_add_profession_stage_supervisor_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->after('department_id', function ($table) {
        // ... andere Felder ...

        $table->foreignId('supervisor_id')
            ->nullable()
            ->constrained('users')
            ->cascadeOnDelete();
    });
});</code></pre>
            </div>

            <h3>Model-Konfiguration</h3>
            <p>Das User Model definiert die Supervisor-Beziehung und das manager Flag:</p>

            <div class="code-block">
            <pre><code>// app/Models/User.php
class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'supervisor_id',
        'manager',        // Flag für Manager-Status
        // ... weitere Felder
    ];

    protected $casts = [
        'supervisor_id' => 'integer',
        'manager' => 'boolean',
    ];

    /**
     * Gibt den Vorgesetzten (Supervisor) als User zurück
     */
    public function supervisorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}</code></pre>
            </div>

            <h3>Employee Model Integration</h3>
            <p>Das Employee Model speichert ebenfalls die supervisor_id für Legacy-Kompatibilität:</p>

            <div class="code-block">
            <pre><code>// app/Models/Alem/Employee.php
class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'supervisor_id',  // Wird beim Erstellen gesetzt
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
                    <strong>Supervisor Relationship im User Model</strong>
                    <div class="code-block">
                    <pre><code>// app/Models/User.php
public function supervisorUser(): BelongsTo
{
    return $this->belongsTo(User::class, 'supervisor_id');
}</code></pre>
                    </div>
                </li>

                <li>
                    <strong>Manager Scope für verfügbare Supervisors</strong>
                    <div class="code-block">
                    <pre><code>// app/Models/User.php
public static function getCompanyManagers(?int $companyId): Collection
{
    if (!$companyId) {
        return collect();
    }

    return Cache::tags(['users', "company:{$companyId}"])
        ->remember("company:{$companyId}:managers", 3600, function () use ($companyId) {
            return static::query()
                ->where('company_id', $companyId)
                ->where('manager', true)  // Manager Flag
                ->where('model_status', ModelStatus::ACTIVE)
                ->select(['id', 'name', 'last_name', 'profile_photo_path'])
                ->orderBy('name')
                ->get();
        });
}</code></pre>
                    </div>
                </li>

                <li>
                    <strong>CreateEmployee Component - Supervisor Auswahl</strong>
                    <div class="code-block">
                    <pre><code>// app/Livewire/Alem/Employee/CreateEmployee.php
#[Computed]
public function supervisors(): Collection
{
    if (!$this->showCreateModal || !$this->companyId) {
        return collect();
    }

    if ($this->cachedSupervisors === null) {
        $this->cachedSupervisors = User::getCompanyManagers($this->companyId);
    }

    // Filter aktuellen User raus
    $currentUserId = $this->userId ?? 0;
    return $this->cachedSupervisors->reject(function ($supervisor) use ($currentUserId) {
        return $supervisor && isset($supervisor->id) && $supervisor->id === $currentUserId;
    });
}</code></pre>
                    </div>
                </li>

                <li>
                    <strong>Speichern des Supervisors beim Erstellen</strong>
                    <div class="code-block">
                    <pre><code>// In CreateEmployee::store()
// User erstellen (supervisor_id wird hier NICHT gesetzt)
$user = User::create([
    'name' => $this->name,
    'email' => $this->email,
    // ... andere Felder
]);

// Employee erstellen mit supervisor_id
Employee::create([
    'user_id' => $user->id,
    'supervisor_id' => $this->supervisor,  // Hier wird supervisor_id gesetzt
    'profession_id' => $this->profession,
    'stage_id' => $this->stage,
    'employee_status' => $this->employee_status,
]);</code></pre>
                    </div>
                </li>

                <li>
                    <strong>Validierung gegen Selbst-Zuweisung</strong>
                    <div class="code-block">
                    <pre><code>// app/Livewire/Alem/Employee/Helper/ValidateEmployee.php
'supervisor' => ['required', 'integer', 'exists:users,id',
    function (string $attribute, mixed $value, \Closure $fail) {
        if ($value == $this->userId) {
            $fail(__('You cannot be your own supervisor.'));
        }
    },
],</code></pre>
                    </div>
                </li>
            </ol>
        </div>

        <div class="section">
            <h2>💡 Verwendung</h2>

            <h3>Manager Flag durch Rollen</h3>
            <p>Das Manager-Flag wird automatisch basierend auf den zugewiesenen Rollen gesetzt:</p>
            <div class="code-block">
            <pre><code>// Rolle hat is_manager = true
Role::create([
    'name' => 'Manager',
    'is_manager' => true,  // Diese Rolle macht User zu Managern
    // ... andere Felder
]);

// Bei Rollen-Sync wird manager Flag aktualisiert
private function syncRolesWithManagerCheck(): void
{
    $oldHasManager = $this->checkManagerInRoles($originalRoleIds);

    $this->user->roles()->sync($this->selectedRoles);

    $newHasManager = $this->checkManagerInRoles($this->selectedRoles);

    if ($oldHasManager !== $newHasManager) {
        $this->user->update(['manager' => $newHasManager]);
        User::clearManagerCache($this->user->company_id);
    }
}</code></pre>
            </div>

            <h3>Supervisor zuweisen</h3>
            <div class="code-block">
            <pre><code>// Beim Erstellen eines Employees
Employee::create([
    'user_id' => $user->id,
    'supervisor_id' => $this->supervisor,  // ID eines Managers
    // ... andere Felder
]);

// Beim Update direkt am User Model
$user->update(['supervisor_id' => $newSupervisor->id]);</code></pre>
            </div>

            <h3>Supervisor abfragen</h3>
            <div class="code-block">
            <pre><code>@verbatim
                        // Supervisor eines Users
                        $supervisor = $user->supervisorUser;

                        // Supervisor Name anzeigen
                        @if($user->supervisorUser)
                            <span>Supervisor: {{ $user->supervisorUser->name }}</span>
                        @endif

                        // Alle Manager einer Company
                        $managers = User::getCompanyManagers($companyId);
                    @endverbatim</code></pre>
            </div>

            <div class="alert alert-info">
                <strong>Info:</strong> Die Manager-Liste wird für 1 Stunde gecacht und automatisch bei Änderungen
                invalidiert.
            </div>
        </div>

        <div class="section">
            <h2>🔍 Beispiele</h2>

            <h3>In der CreateEmployee View</h3>
            <div class="code-block">
            <pre><code>@verbatim
                        // resources/views/livewire/alem/employee/create.blade.php
                        <!-- Supervisor -->
                        <div class="sm:col-span-3">
    <x-pupi.input.group
        label="{{ __('Supervisor') }}"
        for="supervisor"
        badge="{{ __('Required') }}"
        :error="$errors->first('supervisor_id')"
        model="supervisor">

        <flux:select
            wire:model="supervisor"
            id="supervisor"
            variant="listbox"
            searchable
            placeholder="{{ __('Select Supervisor') }}">

            @forelse($this->supervisors() as $supervisor)
                <flux:option value="{{ $supervisor->id }}">
                    <div class="flex items-center gap-2">
                        <flux:avatar
                            name="{{ $supervisor->name }} {{ $supervisor->last_name }}"
                            size="xs"
                            src="{{ $supervisor->profile_photo_path ?
                                        asset('storage/' . $supervisor->profile_photo_path) : null }}"
                        />
                        {{ $supervisor->name }} {{ $supervisor->last_name }}
                    </div>
                </flux:option>
            @empty
                <flux:option value="">{{ __('No supervisors found') }}</flux:option>
            @endforelse
        </flux:select>
    </x-pupi.input.group>
</div>
                    @endverbatim</code></pre>
            </div>

            <h3>In EditEmployee Component</h3>
            <div class="code-block">
            <pre><code>// app/Livewire/Alem/Employee/EditEmployee.php
public function updateEmployee(): void
{
    $this->validate();

    try {
        DB::transaction(function () {
            $this->user->update([
                'gender' => $this->gender,
                'name' => $this->name,
                'email' => $this->email,
                'supervisor_id' => $this->supervisor,  // Supervisor Update
                // ... andere Felder
            ]);

            $this->updateTeamsRoles();
        });

        Flux::toast(
            text: __('Employee Profile updated successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );
    } catch (\Throwable $e) {
        $this->handleEditingError($e);
    }
}</code></pre>
            </div>

            <h3>In Test Seedern</h3>
            <div class="code-block">
            <pre><code>// database/seeders/User/HighPerformanceTestDataSeeder.php
// Bestimme Supervisor für Bulk-Insert
$supervisorId = ($j < $chunkSize * 0.1)
    ? $ownerId  // Erste 10% haben Owner als Supervisor
    : $supervisorIds[array_rand($supervisorIds)];  // Rest zufälliger Supervisor

$userData[] = [
    'name' => $fullName,
    'email' => $email,
    'supervisor_id' => $supervisorId,
    'manager' => $isManager,  // Basierend auf Role
    // ... andere Felder
];</code></pre>
            </div>
        </div>

        <div class="section">
            <h2>⚙️ Erweiterte Funktionen</h2>

            <h3>User Model Methoden</h3>
            <table>
                <thead>
                <tr>
                    <th>Methode</th>
                    <th>Beschreibung</th>
                    <th>Rückgabe</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><code>supervisorUser()</code></td>
                    <td>Relationship zum Supervisor</td>
                    <td>BelongsTo&lt;User&gt;</td>
                </tr>
                <tr>
                    <td><code>getCompanyManagers()</code></td>
                    <td>Holt alle Manager einer Company (gecacht)</td>
                    <td>Collection</td>
                </tr>
                <tr>
                    <td><code>clearManagerCache()</code></td>
                    <td>Löscht den Manager Cache einer Company</td>
                    <td>void</td>
                </tr>
                </tbody>
            </table>

            <h3>Manager-Status über Rollen</h3>
            <p>Der Manager-Status wird durch die <code>is_manager</code> Flag in den Rollen bestimmt:</p>
            <table>
                <thead>
                <tr>
                    <th>Rolle</th>
                    <th>is_manager</th>
                    <th>Kann Supervisor sein</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Manager</td>
                    <td>true</td>
                    <td>✅ Ja</td>
                </tr>
                <tr>
                    <td>Worker</td>
                    <td>false</td>
                    <td>❌ Nein</td>
                </tr>
                <tr>
                    <td>Editor</td>
                    <td>false</td>
                    <td>❌ Nein</td>
                </tr>
                </tbody>
            </table>

            <h3>Cache-Management</h3>
            <div class="code-block">
            <pre><code>// Manager Cache wird automatisch invalidiert bei:
// 1. Rollen-Änderung eines Users
// 2. Änderung des manager Flags

// Manuelles Cache-Clearing
User::clearManagerCache($companyId);

// Cache-Tags für granulare Kontrolle
Cache::tags(['users', "company:{$companyId}"])->flush();</code></pre>
            </div>
        </div>

        <div class="section">
            <h2>🛡️ Best Practices</h2>

            <div class="feature-grid">
                <div class="feature-card">
                    <h4>✅ Do's</h4>
                    <ul>
                        <li>Immer Manager-Status über Rollen verwalten</li>
                        <li>Validierung gegen Selbst-Zuweisung implementieren</li>
                        <li>Cache für Manager-Listen nutzen</li>
                        <li>supervisor_id im Employee Model für Konsistenz</li>
                    </ul>
                </div>

                <div class="feature-card">
                    <h4>❌ Don'ts</h4>
                    <ul>
                        <li>Direkte manager Flag Updates ohne Rollen-Check</li>
                        <li>Supervisor ohne Manager-Rolle zuweisen</li>
                        <li>Cache-Invalidierung vergessen</li>
                        <li>Doppelte supervisor_id Verwaltung vermeiden</li>
                    </ul>
                </div>
            </div>

            <div class="alert alert-warning">
                <strong>Wichtig:</strong> Das manager Flag sollte immer über Rollen-Synchronisation gesetzt werden,
                nicht manuell, um Konsistenz zu gewährleisten.
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
                    <td>Keine Supervisors verfügbar</td>
                    <td>Keine User mit Manager-Rolle</td>
                    <td>Rolle mit is_manager=true zuweisen</td>
                </tr>
                <tr>
                    <td>Selbst-Zuweisung Fehler</td>
                    <td>User versucht sich selbst zuzuweisen</td>
                    <td>Validierung filtert aktuellen User aus</td>
                </tr>
                <tr>
                    <td>Cache nicht aktuell</td>
                    <td>Manager-Status geändert</td>
                    <td>User::clearManagerCache($companyId)</td>
                </tr>
                <tr>
                    <td>Supervisor wird nicht angezeigt</td>
                    <td>supervisorUser Relationship nicht geladen</td>
                    <td>with('supervisorUser') verwenden</td>
                </tr>
                </tbody>
            </table>

            <h3>Debug-Hilfen</h3>
            <div class="code-block">
            <pre><code>// Prüfe Manager-Status eines Users
$user->manager; // true/false

// Prüfe ob User eine Manager-Rolle hat
$hasManagerRole = $user->roles()
    ->where('is_manager', true)
    ->exists();

// Liste alle Manager
$managers = User::where('company_id', $companyId)
    ->where('manager', true)
    ->get();

// Cache manuell leeren
Cache::tags(['users', "company:{$companyId}"])->flush();</code></pre>
            </div>
        </div>

        <div class="section">
            <h2>📚 Zusammenfassung</h2>

            <p>Das Supervisor Feature in deiner Laravel-Applikation bietet eine einfache und effiziente Lösung für die
                Zuweisung von Vorgesetzten zu Mitarbeitern. Es basiert auf dem Manager-Flag aus den Rollen und nutzt
                eine direkte Foreign Key Beziehung.</p>

            <h3>Hauptvorteile:</h3>
            <ul>
                <li>🏢 <strong>Einfache Struktur:</strong> Nur supervisor_id und manager Flag</li>
                <li>🔒 <strong>Rollen-basiert:</strong> Manager-Status über Rollen gesteuert</li>
                <li>⚡ <strong>Performance:</strong> Gecachte Manager-Listen</li>
                <li>🔄 <strong>Flexibel:</strong> Einfache Supervisor-Wechsel möglich</li>
            </ul>

            <div class="alert alert-info">
                <strong>Tipp:</strong> Das System kann später einfach erweitert werden, z.B. um mehrstufige
                Hierarchien oder Team-basierte Supervisor-Strukturen.
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
                    <td><code>app/Models/User.php</code></td>
                    <td>User Model mit Supervisor-Beziehung</td>
                    <td>
                        • <code>supervisorUser()</code> Relationship<br>
                        • <code>getCompanyManagers()</code> Scope<br>
                        • <code>manager</code> Boolean Flag<br>
                        • <code>supervisor_id</code> Foreign Key
                    </td>
                </tr>
                <tr>
                    <td><code>app/Models/Alem/Employee.php</code></td>
                    <td>Employee Model mit supervisor_id</td>
                    <td>
                        • Speichert <code>supervisor_id</code> beim Erstellen<br>
                        • Verknüpfung mit User Model
                    </td>
                </tr>
                <tr>
                    <td><code>app/Models/Spatie/Role.php</code></td>
                    <td>Rollen mit Manager-Flag</td>
                    <td>
                        • <code>is_manager</code> Boolean<br>
                        • <code>getEmployeePanelRoles()</code><br>
                        • Cache-Management für Rollen
                    </td>
                </tr>
                </tbody>
            </table>

            <h3>Livewire Components</h3>
            <table>
                <thead>
                <tr>
                    <th>Datei</th>
                    <th>Verwendung</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><code>app/Livewire/Alem/Employee/CreateEmployee.php</code></td>
                    <td>Supervisor-Auswahl beim Erstellen, supervisors() Computed Property</td>
                </tr>
                <tr>
                    <td><code>app/Livewire/Alem/Employee/EditEmployee.php</code></td>
                    <td>Supervisor Update, Manager-Flag Sync bei Rollen-Änderung</td>
                </tr>
                <tr>
                    <td><code>app/Livewire/Alem/Employee/Helper/ValidateEmployee.php</code></td>
                    <td>Validierung gegen Selbst-Zuweisung</td>
                </tr>
                </tbody>
            </table>

            <h3>Views</h3>
            <table>
                <thead>
                <tr>
                    <th>Datei</th>
                    <th>Verwendung</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><code>resources/views/livewire/alem/employee/create.blade.php</code></td>
                    <td>Flux Select für Supervisor-Auswahl mit Avatar</td>
                </tr>
                <tr>
                    <td><code>resources/views/livewire/alem/employee/edit.blade.php</code></td>
                    <td>Supervisor-Feld im Edit-Formular</td>
                </tr>
                </tbody>
            </table>

            <h3>Migrations</h3>
            <table>
                <thead>
                <tr>
                    <th>Datei</th>
                    <th>Änderungen</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><code>2025_06_19_..._add_profession_stage_supervisor_to_users_table.php</code></td>
                    <td>Fügt supervisor_id als Foreign Key hinzu</td>
                </tr>
                <tr>
                    <td><code>0001_01_01_000000_create_users_table.php</code></td>
                    <td>Enthält manager Boolean Flag</td>
                </tr>
                <tr>
                    <td><code>2024_10_09_083853_add_fields_to_roles.php</code></td>
                    <td>Fügt is_manager Flag zu Rollen hinzu</td>
                </tr>
                </tbody>
            </table>

            <div class="alert alert-info">
                <strong>Info:</strong> Das Supervisor Feature ist schlank implementiert und nutzt bestehende
                Strukturen (Rollen, User Model) optimal aus.
            </div>
        </div>
        </body>
</div>
