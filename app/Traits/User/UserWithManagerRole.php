<?php

namespace App\Traits\User;

use App\Models\Spatie\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait UserWithManagerRole
{

    /**
     * 1. Hilfsmethode: Hat User eine Manager-Rolle?
     */
    public function hasManagerRole(): bool
    {
        return $this->roles()->where('is_manager', true)->exists();
    }

    /**
     * 2. Leert den Manager-Cache für eine Company mit AdvancedCache
     * Nutzt die korrekten AdvancedCache-Methoden für konsistentes Cache-Management
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

        Log::info("Manager cache cleared for company {$companyId}", [
            'persistent_key' => $persistentKey,
            'request_key' => $requestKey
        ]);
    }

    /**
     * 4. Override assignRole - Prüft ob Manager-Rolle zugewiesen wird
     * Cache wird NUR geleert wenn User zum Manager wird
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

            Log::info("User {$this->id} became a manager via assignRole", [
                'company_id' => $this->company_id,
                'roles' => collect($roles)->pluck('name', 'id')->toArray()
            ]);
        }

        return $result;
    }

    /**
     * 5. Override removeRole - Prüft ob Manager-Rolle entfernt wird
     * Cache wird NUR geleert wenn User Manager-Status verliert
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

            Log::info("User {$this->id} is no longer a manager", [
                'company_id' => $this->company_id,
                'removed_role' => $roleModel ? $roleModel->name : 'unknown'
            ]);
        }

        return $result;
    }

    /**
     * Get managers for a specific company with caching
     * Nutzt AdvancedCache mit suffix für spezifischen Cache-Key
     */
    public static function getCompanyManagers(int $companyId): Collection
    {
        \Debugbar::startMeasure('manager-cache', 'Loading Company Managers');

        $cacheKey = "users:company:{$companyId}:managers";

        // Check Cache Status
        if (\Cache::has($cacheKey)) {
            \Debugbar::info("CACHE HIT - Managers for company {$companyId}");
        } else {
            \Debugbar::warning("CACHE MISS - Loading managers from DB for company {$companyId}");
        }

        $result = static::getCachedByCompany($companyId, function() use ($companyId) {
            \Log::debug("Loading managers from database for company {$companyId}");
            \Debugbar::addMessage("DB Query for managers", 'queries');

            return self::select([
                'users.id',
                'users.name',
                'users.last_name',
                'users.profile_photo_path'
            ])
                ->join('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', User::class);
                })
                ->join('roles', function ($join) {
                    $join->on('model_has_roles.role_id', '=', 'roles.id')
                        ->where('roles.is_manager', true);
                })
                ->where('users.company_id', $companyId)
                ->whereNull('users.deleted_at')
                ->orderBy('users.name')
                ->distinct()
                ->get();
        }, ['suffix' => 'managers']);

        \Debugbar::stopMeasure('manager-cache');
        \Debugbar::info("Found {$result->count()} managers");

        return $result;
    }

}
