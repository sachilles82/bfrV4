<?php
//
//namespace App\Http\Controllers\Alem\Employee;
//
//use App\Enums\Role\RoleVisibility;
//use App\Http\Controllers\Controller;
//use App\Models\Alem\Department;
//use App\Models\Alem\Employee\Setting\Profession;
//use App\Models\Alem\Employee\Setting\Stage;
//use App\Models\Team;
//use Barryvdh\Debugbar\Facades\Debugbar;
//use Illuminate\Contracts\View\View;
//use Illuminate\Support\Collection;
//use Illuminate\Support\Facades\Auth; // Wichtig: Cache-Facade verwenden
//use Illuminate\Support\Facades\Cache;
//// use Illuminate\Support\Facades\Redis; // Nicht mehr direkt für Caching benötigt
//use Illuminate\Support\Facades\Log;
//use Spatie\Permission\Models\Role;
//
//class EmployeeIndexController extends Controller
//{
//    /**
//     * Generiert einen Cache-Schlüssel für firmenweite Listen-Daten.
//     * Verwendet das konfigurierte Cache-Prefix automatisch.
//     *
//     * @param  string  $resourceName  Name der Ressource (z.B. 'departments', 'teams')
//     * @return string|null Cache-Schlüssel oder null, wenn Company fehlt
//     */
//    private function getCompanyCacheKey(string $resourceName, ?int $companyId): ?string
//    {
//        if (! $companyId) {
//            Log::warning('Versuch, Cache-Schlüssel ohne Company-ID zu generieren.', ['resource' => $resourceName]);
//
//            return null;
//        }
//
//        // Das Cache-Prefix wird von Laravel automatisch hinzugefügt
//        return "list_company_{$companyId}_resource_{$resourceName}";
//    }
//
//    /**
//     * Holt eine Ressource aus dem Cache oder erzeugt sie neu und speichert sie permanent.
//     * Verwendet die Laravel Cache-Facade und fügt Debugbar-Infos hinzu.
//     *
//     * @param  string  $cacheKey  Der zu verwendende Cache-Schlüssel.
//     * @param  callable  $callback  Die Funktion, die die Daten generiert, wenn sie nicht im Cache sind.
//     * @param  string  $resourceTypeForDebug  Ein beschreibender Name für Debugbar-Nachrichten (z.B. 'Departments').
//     * @return mixed Die Daten aus dem Cache oder neu generiert.
//     */
//    private function rememberResourceForever(string $cacheKey, callable $callback, string $resourceTypeForDebug)
//    {
//        // Prüfen ob im Cache, *bevor* rememberForever aufgerufen wird, für klareres Debugging
//        if (Cache::has($cacheKey)) {
//            Debugbar::info("Cache Hit für {$resourceTypeForDebug} (Key: {$cacheKey}) - Daten aus Cache geladen.");
//
//            // Hole die Daten direkt, da wir wissen, dass sie da sind
//            return Cache::get($cacheKey);
//        } else {
//            // Wenn nicht im Cache, nutze rememberForever zum Holen, Speichern und Zurückgeben
//            Debugbar::info("Cache Miss für {$resourceTypeForDebug} (Key: {$cacheKey}) - Daten werden aus DB geladen und permanent gecached.");
//
//            // Der Callback wird nur ausgeführt, wenn der Key *jetzt gerade* nicht existiert.
//            // Die Debug-Nachricht im Callback ist weiterhin nützlich, falls es zu Race Conditions käme
//            // oder zur Bestätigung, dass der Callback tatsächlich lief.
//            return Cache::rememberForever($cacheKey, function () use ($callback, $resourceTypeForDebug, $cacheKey) {
//                Debugbar::info("-> Callback für Cache::rememberForever ausgeführt ({$resourceTypeForDebug}, Key: {$cacheKey}).");
//
//                // Führe den Callback aus, um die Daten zu erhalten
//                return $callback();
//            });
//        }
//    }
//
//    /**
//     * Invalidiert den Cache für eine bestimmte Ressource unter Verwendung der Cache-Facade.
//     *
//     * @param  string  $resourceName  Name der Ressource.
//     * @param  int  $companyId  Die ID der Firma.
//     */
//    public function invalidateCache(string $resourceName, int $companyId): void
//    {
//        $cacheKey = $this->getCompanyCacheKey($resourceName, $companyId);
//        if ($cacheKey) {
//            // Cache::forget() löscht den Eintrag sicher, auch wenn er nicht existiert.
//            $wasForgotten = Cache::forget($cacheKey);
//            if ($wasForgotten) {
//                Debugbar::info("Cache für {$resourceName} der Firma {$companyId} invalidiert (Key: {$cacheKey}).");
//            } else {
//                Debugbar::info("Cache für {$resourceName} der Firma {$companyId} (Key: {$cacheKey}) war nicht vorhanden oder konnte nicht gelöscht werden.");
//            }
//        }
//    }
//
//    /**
//     * Lädt alle Rollen aus dem Cache oder aus der Datenbank und cached sie dauerhaft
//     * Filtert nach der Firmen-ID des angemeldeten Benutzers und created_by=1
//     * Berücksichtigt nur visible Rollen
//     * Gibt nur ID und Name zurück
//     *
//     * @return Collection
//     */
//    private function getCachedRoles()
//    {
//        $authCompanyId = Auth::user()?->company_id;
//
//        if (! $authCompanyId) {
//            Debugbar::warning('Keine Firmen-ID für angemeldeten Benutzer gefunden, kann Rollen nicht cachen');
//
//            return collect();
//        }
//
//        $cacheKey = "roles_company_{$authCompanyId}_creator_1";
//        $resourceTypeForDebug = 'Rollen (Visible, Creator 1)';
//
//        // Prüfen ob im Cache, *bevor* rememberForever aufgerufen wird
//        if (Cache::has($cacheKey)) {
//            Debugbar::info("Cache Hit für {$resourceTypeForDebug} (Key: {$cacheKey}) - Daten aus Cache geladen.");
//
//            return Cache::get($cacheKey);
//        } else {
//            Debugbar::info("Cache Miss für {$resourceTypeForDebug} (Key: {$cacheKey}) - Daten werden aus DB geladen und permanent gecached.");
//
//            return Cache::rememberForever($cacheKey, function () use ($authCompanyId, $resourceTypeForDebug, $cacheKey) {
//                Debugbar::info("-> Callback für Cache::rememberForever ausgeführt ({$resourceTypeForDebug}, Key: {$cacheKey}).");
//
//                // Nur relevante Rollen mit spezifischen Bedingungen und nur ID+Namen
//                return Role::where('company_id', $authCompanyId)
//                    ->where('created_by', 1)
//                    ->where('visible', RoleVisibility::Visible->value)
//                    ->select(['id', 'name'])
//                    ->get();
//            });
//        }
//    }
//
//    /**
//     * Lädt alle Professionen aus dem Cache oder aus der Datenbank und cached sie dauerhaft
//     * Filtert nach der Firmen-ID des angemeldeten Benutzers
//     *
//     * @return Collection
//     */
//    private function getCachedProfessions()
//    {
//        $authCompanyId = Auth::user()?->company_id;
//
//        if (! $authCompanyId) {
//            Debugbar::warning('Keine Firmen-ID für angemeldeten Benutzer gefunden, kann Professionen nicht cachen');
//
//            return collect();
//        }
//
//        $cacheKey = "professions_company_{$authCompanyId}";
//        $resourceTypeForDebug = 'Professionen';
//
//        // Prüfen ob im Cache, *bevor* rememberForever aufgerufen wird
//        if (Cache::has($cacheKey)) {
//            Debugbar::info("Cache Hit für {$resourceTypeForDebug} (Key: {$cacheKey}) - Daten aus Cache geladen.");
//
//            return Cache::get($cacheKey);
//        } else {
//            Debugbar::info("Cache Miss für {$resourceTypeForDebug} (Key: {$cacheKey}) - Daten werden aus DB geladen und permanent gecached.");
//
//            return Cache::rememberForever($cacheKey, function () use ($authCompanyId, $resourceTypeForDebug, $cacheKey) {
//                Debugbar::info("-> Callback für Cache::rememberForever ausgeführt ({$resourceTypeForDebug}, Key: {$cacheKey}).");
//
//                // Professionen nach Firmen-ID filtern
//                return Profession::where('company_id', $authCompanyId)
//                    ->select(['id', 'name'])
//                    ->orderBy('name')
//                    ->get();
//            });
//        }
//    }
//
//    /**
//     * Lädt alle Stages aus dem Cache oder aus der Datenbank und cached sie dauerhaft
//     * Filtert nach der Firmen-ID des angemeldeten Benutzers
//     *
//     * @return Collection
//     */
//    private function getCachedStages()
//    {
//        $authCompanyId = Auth::user()?->company_id;
//
//        if (! $authCompanyId) {
//            Debugbar::warning('Keine Firmen-ID für angemeldeten Benutzer gefunden, kann Stages nicht cachen');
//
//            return collect();
//        }
//
//        $cacheKey = "stages_company_{$authCompanyId}";
//        $resourceTypeForDebug = 'Stages';
//
//        // Prüfen ob im Cache, *bevor* rememberForever aufgerufen wird
//        if (Cache::has($cacheKey)) {
//            Debugbar::info("Cache Hit für {$resourceTypeForDebug} (Key: {$cacheKey}) - Daten aus Cache geladen.");
//
//            return Cache::get($cacheKey);
//        } else {
//            Debugbar::info("Cache Miss für {$resourceTypeForDebug} (Key: {$cacheKey}) - Daten werden aus DB geladen und permanent gecached.");
//
//            return Cache::rememberForever($cacheKey, function () use ($authCompanyId, $resourceTypeForDebug, $cacheKey) {
//                Debugbar::info("-> Callback für Cache::rememberForever ausgeführt ({$resourceTypeForDebug}, Key: {$cacheKey}).");
//
//                // Stages nach Firmen-ID filtern
//                return Stage::where('company_id', $authCompanyId)
//                    ->select(['id', 'name'])
//                    ->orderBy('name')
//                    ->get();
//            });
//        }
//    }
//
//    /**
//     * Zeigt die Index-Seite für Mitarbeiter an und lädt benötigte Lookup-Daten aus dem Cache.
//     * Die eigentliche Mitarbeiterliste wird von der Livewire Komponente geladen und gefiltert.
//     */
//    public function index(): View
//    {
//        $authCompanyId = Auth::user()?->company_id;
//
//        if (! $authCompanyId) {
//            abort(403, 'Keine Firma zugeordnet.');
//        }
//
//        // Departments laden (Lookup-Daten für Filter etc.)
//        $departmentsKey = $this->getCompanyCacheKey('departments', $authCompanyId);
//        $departments = [];
//        if ($departmentsKey) {
//            // Verwende die verbesserte Caching-Methode mit Debug-Ausgabe
//            $departments = $this->rememberResourceForever($departmentsKey, function () use ($authCompanyId) {
//                return Department::where('company_id', $authCompanyId)
//                    ->orderBy('name')
//                    ->pluck('name', 'id') // pluck direkt hier ist effizienter für key-value
//                    ->all();
//            }, 'Departments (Lookup)'); // Eindeutiger Name für Debugging
//            // Debugbar::info wird jetzt in rememberResourceForever gehandhabt
//        } else {
//            Log::error("Konnte Department Cache Key nicht generieren für Company ID: {$authCompanyId}");
//        }
//
//        // Teams laden (Lookup-Daten für Filter etc.)
//        $teamsKey = $this->getCompanyCacheKey('teams', $authCompanyId);
//        $teams = [];
//        if ($teamsKey) {
//            // Verwende die verbesserte Caching-Methode mit Debug-Ausgabe
//            $teams = $this->rememberResourceForever($teamsKey, function () use ($authCompanyId) {
//                return Team::where('company_id', $authCompanyId)
//                    ->orderBy('name')
//                    ->pluck('name', 'id') // pluck direkt hier ist effizienter für key-value
//                    ->all();
//            }, 'Teams (Lookup)'); // Eindeutiger Name für Debugging
//            // Debugbar::info wird jetzt in rememberResourceForever gehandhabt
//        } else {
//            Log::error("Konnte Team Cache Key nicht generieren für Company ID: {$authCompanyId}");
//        }
//
//        // Professions laden (Lookup-Daten, nutzt eigene Methode mit Debugging)
//        $professionsCollection = $this->getCachedProfessions();
//        $professions = $professionsCollection->pluck('name', 'id')->toArray();
//        Debugbar::info('Professions (Lookup) an View übergeben.', ['count' => count($professions)]);
//
//        // Stages laden (Lookup-Daten, nutzt eigene Methode mit Debugging)
//        $stagesCollection = $this->getCachedStages();
//        $stages = $stagesCollection->pluck('name', 'id')->toArray();
//        Debugbar::info('Stages (Lookup) an View übergeben.', ['count' => count($stages)]);
//
//        // Rollen laden (Lookup-Daten, nutzt eigene Methode mit Debugging)
//        $rolesCollection = $this->getCachedRoles();
//        $roles = $rolesCollection->pluck('name', 'id')->toArray();
//        Debugbar::info('Rollen (Lookup) an View übergeben.', ['count' => count($roles)]);
//
//        // Die View rendern und die Lookup-Daten übergeben
//        // Die Livewire-Komponente ('alem.employee.employee-table') holt sich dann die eigentlichen Benutzerdaten
//        return view('laravel.alem.employee.index', [
//            'departments' => $departments,
//            'teams' => $teams,
//            'roles' => $roles, // Rollen werden oft auch als Filter gebraucht
//            'professions' => $professions,
//            'stages' => $stages,
//        ]);
//    }
//
//    /**
//     * Cache für Rollen, Professionen und Stages löschen
//     * Nützlich nach Änderungen oder bei Debugging
//     *
//     * @return \Illuminate\Http\RedirectResponse
//     */
//    public function invalidateLookupsCache() // Umbenannt für Klarheit
//    {
//        $authCompanyId = Auth::user()?->company_id;
//
//        if ($authCompanyId) {
//            // Lösche alle relevanten Lookup-Caches für die Firma
//            Cache::forget("roles_company_{$authCompanyId}_creator_1");
//            Cache::forget("professions_company_{$authCompanyId}");
//            Cache::forget("stages_company_{$authCompanyId}");
//            // Potenzielle weitere Caches für Departments und Teams löschen, falls diese Funktion allgemeiner sein soll
//            $this->invalidateCache('departments', $authCompanyId);
//            $this->invalidateCache('teams', $authCompanyId);
//
//            Debugbar::info('Alle Lookup-Caches (Rollen, Professionen, Stages, Departments, Teams) wurden gelöscht', ['company_id' => $authCompanyId]);
//        }
//
//        // Zurück zur vorherigen Seite mit Erfolgsmeldung
//        return back()->with('message', 'Lookup-Caches wurden erfolgreich zurückgesetzt.');
//    }
//}


//namespace App\Http\Controllers\Alem\Employee;
//
//use App\Enums\Role\RoleVisibility;
//use App\Http\Controllers\Controller;
//use App\Models\Alem\Department;
//use App\Models\Alem\Employee\Setting\Profession;
//use App\Models\Alem\Employee\Setting\Stage;
//use App\Models\Team;
//use Barryvdh\Debugbar\Facades\Debugbar;
//use Illuminate\Contracts\View\View;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Log;
//use Spatie\Permission\Models\Role;
//
//class EmployeeIndexController extends Controller
//{
//    /**
//     * Zeigt die Index-Seite für Mitarbeiter an und lädt benötigte Lookup-Daten direkt aus der Datenbank.
//     * Die eigentliche Mitarbeiterliste wird von der Livewire Komponente geladen und gefiltert.
//     */
//    public function index(): View
//    {
//        $authCompanyId = Auth::user()?->company_id;
//
//        if (!$authCompanyId) {
//            abort(403, 'Keine Firma zugeordnet.');
//        }
//
//        // Departments laden (Lookup-Daten für Filter etc.) - direkt aus der Datenbank
//        $departments = [];
//        try {
//            Debugbar::info("Loading Departments directly from database");
//            $departments = Department::where('company_id', $authCompanyId)
//                ->orderBy('name')
//                ->pluck('name', 'id')
//                ->all();
//            Debugbar::info('Departments (Lookup) an View übergeben.', ['count' => count($departments)]);
//        } catch (\Exception $e) {
//            Log::error("Fehler beim Laden der Departments: " . $e->getMessage());
//        }
//
//        // Teams laden (Lookup-Daten für Filter etc.) - direkt aus der Datenbank
//        $teams = [];
//        try {
//            Debugbar::info("Loading Teams directly from database");
//            $teams = Team::where('company_id', $authCompanyId)
//                ->orderBy('name')
//                ->pluck('name', 'id')
//                ->all();
//            Debugbar::info('Teams (Lookup) an View übergeben.', ['count' => count($teams)]);
//        } catch (\Exception $e) {
//            Log::error("Fehler beim Laden der Teams: " . $e->getMessage());
//        }
//
//        // Professionen laden (Lookup-Daten) - direkt aus der Datenbank
//        $professions = [];
//        try {
//            Debugbar::info("Loading Professions directly from database");
//            $professionsCollection = Profession::where('company_id', $authCompanyId)
//                ->select(['id', 'name'])
//                ->orderBy('name')
//                ->get();
//            $professions = $professionsCollection->pluck('name', 'id')->toArray();
//            Debugbar::info('Professions (Lookup) an View übergeben.', ['count' => count($professions)]);
//        } catch (\Exception $e) {
//            Log::error("Fehler beim Laden der Professionen: " . $e->getMessage());
//        }
//
//        // Stages laden (Lookup-Daten) - direkt aus der Datenbank
//        $stages = [];
//        try {
//            Debugbar::info("Loading Stages directly from database");
//            $stagesCollection = Stage::where('company_id', $authCompanyId)
//                ->select(['id', 'name'])
//                ->orderBy('name')
//                ->get();
//            $stages = $stagesCollection->pluck('name', 'id')->toArray();
//            Debugbar::info('Stages (Lookup) an View übergeben.', ['count' => count($stages)]);
//        } catch (\Exception $e) {
//            Log::error("Fehler beim Laden der Stages: " . $e->getMessage());
//        }
//
//        // Rollen laden (Lookup-Daten) - direkt aus der Datenbank
//        $roles = [];
//        try {
//            Debugbar::info("Loading Roles directly from database");
//            $rolesCollection = Role::where('company_id', $authCompanyId)
//                ->where('created_by', 1)
//                ->where('visible', RoleVisibility::Visible->value)
//                ->select(['id', 'name'])
//                ->get();
//            $roles = $rolesCollection->pluck('name', 'id')->toArray();
//            Debugbar::info('Rollen (Lookup) an View übergeben.', ['count' => count($roles)]);
//        } catch (\Exception $e) {
//            Log::error("Fehler beim Laden der Rollen: " . $e->getMessage());
//        }
//
//        // Die View rendern und die Lookup-Daten übergeben
//        // Die Livewire-Komponente ('alem.employee.employee-table') holt sich dann die eigentlichen Benutzerdaten
//        return view('laravel.alem.employee.index', [
//            'departments' => $departments,
//            'teams' => $teams,
//            'roles' => $roles, // Rollen werden oft auch als Filter gebraucht
//            'professions' => $professions,
//            'stages' => $stages,
//        ]);
//    }
//
//    /**
//     * Hilfsmethode zum Invalidieren von Caches (obwohl wir sie hier nicht verwenden)
//     * Behalten wir für Kompatibilität bei
//     */
//    public function invalidateLookupsCache()
//    {
//        Debugbar::info('Cache ist deaktiviert, keine Invalidierung nötig');
//        return back()->with('message', 'Cache ist deaktiviert. Alle Daten werden direkt aus der Datenbank geladen.');
//    }
//}


namespace App\Http\Controllers\Alem\Employee;

use App\Http\Controllers\Controller;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class EmployeeIndexController extends Controller
{
    /**
     * Zeigt nur die Index-Seite für Mitarbeiter an ohne Daten zu laden.
     * Reine Test-Implementation für Ladezeiten-Tests der View.
     */
    public function index(): View
    {
        $authCompanyId = Auth::user()?->company_id;

        if (!$authCompanyId) {
            abort(403, 'Keine Firma zugeordnet.');
        }

        Debugbar::info("Test-Modus: Gebe nur die View zurück ohne Daten zu laden");


        // Die View rendern ohne Lookup-Daten
        return view('laravel.alem.employee.index');
    }

}
