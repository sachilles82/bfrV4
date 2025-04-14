<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Enums\Role\RoleVisibility;
use App\Http\Controllers\Controller;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Wichtig: Cache-Facade verwenden
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Redis; // Nicht mehr direkt für Caching benötigt
use Barryvdh\Debugbar\Facades\Debugbar;
use Spatie\Permission\Models\Role;

class EmployeeIndexController extends Controller
{
    /**
     * Generiert einen Cache-Schlüssel für firmenweite Listen-Daten.
     * Verwendet das konfigurierte Cache-Prefix automatisch.
     *
     * @param string $resourceName Name der Ressource (z.B. 'departments', 'teams')
     * @param int $companyId
     * @return string|null Cache-Schlüssel oder null, wenn Company fehlt
     */
    private function getCompanyCacheKey(string $resourceName, ?int $companyId): ?string
    {
        if (!$companyId) {
            Log::warning("Versuch, Cache-Schlüssel ohne Company-ID zu generieren.", ['resource' => $resourceName]);
            return null;
        }
        // Das Cache-Prefix wird von Laravel automatisch hinzugefügt
        return "list_company_{$companyId}_resource_{$resourceName}";
    }

    /**
     * Holt eine Ressource aus dem Cache oder erzeugt sie neu und speichert sie permanent.
     * Verwendet die Laravel Cache-Facade.
     *
     * @param string $cacheKey Der zu verwendende Cache-Schlüssel.
     * @param callable $callback Die Funktion, die die Daten generiert, wenn sie nicht im Cache sind.
     * @return mixed Die Daten aus dem Cache oder neu generiert.
     */
    private function rememberResourceForever(string $cacheKey, callable $callback)
    {
        // Cache::rememberForever nutzt den konfigurierten Cache-Treiber (Redis)
        // und die korrekte Cache-Datenbank (standardmäßig 1).
        // Der Eintrag bleibt bestehen, bis er explizit via Cache::forget() gelöscht wird.
        return Cache::rememberForever($cacheKey, function() use ($cacheKey, $callback) {
            Debugbar::info("Cache Miss für Key: {$cacheKey}, wird neu erstellt via Cache::rememberForever");
            // Führe den Callback aus, um die Daten zu erhalten
            return $callback();
        });
    }

    /**
     * Invalidiert den Cache für eine bestimmte Ressource unter Verwendung der Cache-Facade.
     *
     * @param string $resourceName Name der Ressource.
     * @param int $companyId Die ID der Firma.
     */
    public function invalidateCache(string $resourceName, int $companyId): void
    {
        $cacheKey = $this->getCompanyCacheKey($resourceName, $companyId);
        if ($cacheKey) {
            // Cache::forget() löscht den Eintrag sicher, auch wenn er nicht existiert.
            $wasForgotten = Cache::forget($cacheKey);
            if ($wasForgotten) {
                Debugbar::info("Cache für {$resourceName} der Firma {$companyId} invalidiert (Key: {$cacheKey}).");
            } else {
                Debugbar::info("Cache für {$resourceName} der Firma {$companyId} (Key: {$cacheKey}) war nicht vorhanden oder konnte nicht gelöscht werden.");
            }
        }
    }

    /**
     * Lädt alle Rollen aus dem Cache oder aus der Datenbank und cached sie dauerhaft
     * Filtert nach der Firmen-ID des angemeldeten Benutzers und created_by=1
     * Berücksichtigt nur visible Rollen
     * Gibt nur ID und Name zurück
     *
     * @return Collection
     */
    private function getCachedRoles()
    {
        $authCompanyId = Auth::user()?->company_id;

        if (!$authCompanyId) {
            Debugbar::warning("Keine Firmen-ID für angemeldeten Benutzer gefunden, kann Rollen nicht cachen");
            return collect();
        }

        // Cache-Key enthält die Firmen-ID
        $cacheKey = "roles_company_{$authCompanyId}_creator_1";

        return Cache::rememberForever($cacheKey, function () use ($authCompanyId) {
            Debugbar::info("Cache Miss: Lade Rollen aus der Datenbank und speichere sie dauerhaft im Cache", [
                'company_id' => $authCompanyId,
                'created_by' => 1
            ]);

            // Nur relevante Rollen mit spezifischen Bedingungen und nur ID+Namen
            return Role::where('company_id', $authCompanyId)
                      ->where('created_by', 1)
                      ->where('visible', RoleVisibility::Visible->value)
                      ->select(['id', 'name'])
                      ->get();
        });
    }

    /**
     * Lädt alle Professionen aus dem Cache oder aus der Datenbank und cached sie dauerhaft
     * Filtert nach der Firmen-ID des angemeldeten Benutzers
     *
     * @return Collection
     */
    private function getCachedProfessions()
    {
        $authCompanyId = Auth::user()?->company_id;

        if (!$authCompanyId) {
            Debugbar::warning("Keine Firmen-ID für angemeldeten Benutzer gefunden, kann Professionen nicht cachen");
            return collect();
        }
        
        // Cache-Key enthält die Firmen-ID
        $cacheKey = "professions_company_{$authCompanyId}";

        return Cache::rememberForever($cacheKey, function () use ($authCompanyId) {
            Debugbar::info("Cache Miss: Lade Professionen aus der Datenbank und speichere sie dauerhaft im Cache", [
                'company_id' => $authCompanyId
            ]);

            // Professionen nach Firmen-ID filtern
            return Profession::where('company_id', $authCompanyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Lädt alle Stages aus dem Cache oder aus der Datenbank und cached sie dauerhaft
     * Filtert nach der Firmen-ID des angemeldeten Benutzers
     *
     * @return Collection
     */
    private function getCachedStages()
    {
        $authCompanyId = Auth::user()?->company_id;

        if (!$authCompanyId) {
            Debugbar::warning("Keine Firmen-ID für angemeldeten Benutzer gefunden, kann Stages nicht cachen");
            return collect();
        }
        
        // Cache-Key enthält die Firmen-ID
        $cacheKey = "stages_company_{$authCompanyId}";

        return Cache::rememberForever($cacheKey, function () use ($authCompanyId) {
            Debugbar::info("Cache Miss: Lade Stages aus der Datenbank und speichere sie dauerhaft im Cache", [
                'company_id' => $authCompanyId
            ]);

            // Stages nach Firmen-ID filtern
            return Stage::where('company_id', $authCompanyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Zeigt die Index-Seite für Mitarbeiter an und lädt benötigte Daten aus dem Cache.
     *
     * @return View
     */
    public function index(): View
    {
        $authCompanyId = Auth::user()?->company_id;

        if (!$authCompanyId) {
            abort(403, 'Keine Firma zugeordnet.');
        }

        // Departments laden (bisherige Methode nutzen)
        $departmentsKey = $this->getCompanyCacheKey('departments', $authCompanyId);
        $departments = [];
        if ($departmentsKey) {
            $departments = $this->rememberResourceForever($departmentsKey, function () use ($authCompanyId) {
                return Department::where('company_id', $authCompanyId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
            });
            Debugbar::info("Departments geladen.", ['key' => $departmentsKey, 'count' => count($departments)]);
        } else {
            Log::error("Konnte Department Cache Key nicht generieren für Company ID: {$authCompanyId}");
        }

        // Teams laden (bisherige Methode nutzen)
        $teamsKey = $this->getCompanyCacheKey('teams', $authCompanyId);
        $teams = [];
        if ($teamsKey) {
            $teams = $this->rememberResourceForever($teamsKey, function () use ($authCompanyId) {
                return Team::where('company_id', $authCompanyId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
            });
            Debugbar::info("Teams geladen.", ['key' => $teamsKey, 'count' => count($teams)]);
        } else {
            Log::error("Konnte Team Cache Key nicht generieren für Company ID: {$authCompanyId}");
        }

        // Professions und Stages laden (ursprüngliche Methoden beibehalten)
        $professions = $this->getCachedProfessions()->pluck('name', 'id')->toArray();
        Debugbar::info("Professions geladen.", ['count' => count($professions)]);

        $stages = $this->getCachedStages()->pluck('name', 'id')->toArray();
        Debugbar::info("Stages geladen.", ['count' => count($stages)]);

        // Nur die Rollen aus der speziellen Cache-Methode laden
        $roles = $this->getCachedRoles()->pluck('name', 'id')->toArray();
        Debugbar::info("Rollen aus Cache geladen", ['count' => count($roles)]);

        return view('laravel.alem.employee.index', [
            'departments' => $departments,
            'teams' => $teams,
            'roles' => $roles,
            'professions' => $professions,
            'stages' => $stages,
        ]);
    }

    /**
     * Cache für Rollen löschen
     * Nützlich nach Rollenänderungen oder bei Debugging
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function invalidateRolesCache()
    {
        $authCompanyId = Auth::user()?->company_id;

        if ($authCompanyId) {
            Cache::forget("roles_company_{$authCompanyId}_creator_1");
            Cache::forget("professions_company_{$authCompanyId}");
            Cache::forget("stages_company_{$authCompanyId}");
            Debugbar::info("Rollen- und zugehörige Caches wurden gelöscht", ['company_id' => $authCompanyId]);
        }

        // Zurück zur vorherigen Seite mit Erfolgsmeldung
        return back()->with('message', 'Caches wurden erfolgreich zurückgesetzt.');
    }
}
