<?php

namespace App\Traits\User;

use App\Models\Spatie\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait UserWithManagerRole
{
    /**
     * 1. Prüft ob der User eine Manager-Rolle hat
     *
     * @return bool True wenn User mindestens eine Rolle mit is_manager = true hat
     */
    public function hasManagerRole(): bool
    {
        return $this->roles()->where('is_manager', true)->exists();
    }

    /**
     * 2. Leert den Manager-Cache für eine Company
     *
     * Löscht sowohl den persistenten Cache (Redis/File) als auch den Request-Cache
     *
     * @param int $companyId Die Company ID für die der Cache geleert werden soll
     */
    public static function clearManagerCache(int $companyId): void
    {
        $instance = new static;
        $config = $instance->getCacheConfig();

        // Generiere den korrekten Cache-Key mit suffix
        $persistentKey = "{$config['prefix']}:company:{$companyId}:managers";
        $requestKey = "request_{$config['prefix']}_company_{$companyId}_managers";

        // Leere persistent cache
        Cache::forget($persistentKey);

        // Leere request cache
        if (isset(self::$requestCache[$requestKey])) {
            unset(self::$requestCache[$requestKey]);
        }
    }

    /**
     * 3. Override assignRole - Erweitert die Spatie assignRole Methode
     *
     * Leert automatisch den Manager-Cache wenn ein User zum Manager wird
     *
     * @param mixed ...$roles Ein oder mehrere Rollen (string, id oder Role Model)
     * @return mixed Das Ergebnis der parent assignRole Methode
     */
    public function assignManagerRole(...$roles)
    {
        // Prüfe ob User bereits Manager ist
        $wasManager = $this->hasManagerRole();

        // Prüfe ob eine der neuen Rollen eine Manager-Rolle ist
        $assigningManagerRole = collect($roles)
            ->map(function($role) {
                if (is_string($role)) {
                    return Role::where('name', $role)->first();
                } elseif (is_numeric($role)) {
                    return Role::find($role);
                }
                return $role;
            })
            ->filter()
            ->contains(fn($role) => $role && $role->is_manager);

        // Führe die Rollenzuweisung durch
        $result = parent::assignRole(...$roles);

        // Cache leeren wenn User zum Manager wird
        if (!$wasManager && $assigningManagerRole && $this->company_id) {
            static::clearManagerCache($this->company_id);
        }

        return $result;
    }

    /**
     * 4. Override removeRole - Erweitert die Spatie removeRole Methode
     *
     * Leert automatisch den Manager-Cache wenn ein User den Manager-Status verliert
     *
     * @param mixed $role Die zu entfernende Rolle (string, id oder Role Model)
     * @return mixed Das Ergebnis der parent removeRole Methode
     */
    public function removeManagerRole($role)
    {
        // Konvertiere Rolle zu Model-Objekt
        $roleModel = is_string($role)
            ? Role::where('name', $role)->first()
            : (is_numeric($role) ? Role::find($role) : $role);

        // Prüfe ob es eine Manager-Rolle ist
        $isRemovingManagerRole = $roleModel && $roleModel->is_manager;

        // Prüfe ob User noch andere Manager-Rollen hat
        $otherManagerRoles = $this->roles()
            ->where('is_manager', true)
            ->where('id', '!=', $roleModel->id ?? 0)
            ->exists();

        // Führe die Rollenentfernung durch
        $result = parent::removeRole($role);

        // Cache leeren wenn User keinen Manager-Status mehr hat
        if ($isRemovingManagerRole && !$otherManagerRoles && $this->company_id) {
            static::clearManagerCache($this->company_id);
        }

        return $result;
    }

    /**
     * 5. Holt alle Manager einer Company mit Cache
     *
     * Verwendet einen separaten Cache-Key mit 'managers' Suffix
     * um Manager-Daten getrennt von anderen User-Daten zu cachen
     *
     * @param int $companyId Die Company ID
     * @return Collection Collection von User Models die Manager sind
     */
    public static function getCompanyManagers(int $companyId): Collection
    {
        return static::getCachedByCompany($companyId, function() use ($companyId) {
            // Nutze whereExists statt JOIN für bessere Performance
            return self::select([
                'users.id',
                'users.name',
                'users.profile_photo_path'
            ])
                ->where('users.company_id', $companyId)
                ->whereNull('users.deleted_at')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->whereColumn('model_has_roles.model_id', 'users.id')
                        ->where('model_has_roles.model_type', User::class)
                        ->where('roles.is_manager', true);
                })
                ->orderBy('users.name')
                ->get();
        }, ['suffix' => 'managers']);
    }
}
