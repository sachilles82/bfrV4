<?php

namespace App\Traits\Users;

use App\Enums\Model\ModelStatus;
use App\Models\Spatie\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Trait HasManagerRole
 *
 * Stellt Manager-Funktionalität für User Model bereit
 * Beinhaltet Cache-Management und Beziehungen
 */
trait HasManagerRole
{
    /**
     * Boot-Methode für Model Events
     * Wird automatisch von Laravel aufgerufen
     */
    public static function bootHasManagerRole()
    {
        // Nach Rollen-Synchronisation prüfen
        static::pivotAttached(function ($model, $relationName, $pivotIds) {
            if ($relationName === 'roles' && $model->company_id) {
                $hasManagerRole = Role::whereIn('id', $pivotIds)
                    ->where('is_manager', true)
                    ->exists();

                if ($hasManagerRole) {
                    static::clearManagerCache($model->company_id);
                    Log::info("User {$model->id} became manager - cache cleared", [
                        'company_id' => $model->company_id
                    ]);
                }
            }
        });

        // Nach Rollen-Entfernung prüfen
        static::pivotDetached(function ($model, $relationName, $pivotIds) {
            if ($relationName === 'roles' && $model->company_id) {
                // Prüfe ob eine Manager-Rolle entfernt wurde
                $removedManagerRole = Role::whereIn('id', $pivotIds)
                    ->where('is_manager', true)
                    ->exists();

                if ($removedManagerRole) {
                    static::clearManagerCache($model->company_id);
                    Log::info("Manager role removed from user {$model->id}", [
                        'company_id' => $model->company_id
                    ]);
                }
            }
        });

        // Bei User-Löschung
        static::deleting(function ($model) {
            if ($model->company_id && $model->hasManagerRole()) {
                static::clearManagerCache($model->company_id);
            }
        });

        // Bei Status-Änderung
        static::updating(function ($model) {
            if ($model->isDirty('model_status') && $model->company_id && $model->hasManagerRole()) {
                static::clearManagerCache($model->company_id);
            }
        });
    }

    /**
     * Prüft ob User eine Manager-Rolle hat
     * Nutzt eager loaded 'roles' wenn verfügbar
     */
    public function hasManagerRole(): bool
    {
        // Wenn Rollen bereits geladen sind, nutze die Collection
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('is_manager', true);
        }

        // Ansonsten DB-Query
        return $this->roles()
            ->where('is_manager', true)
            ->exists();
    }

    /**
     * Prüft ob User ein aktiver Manager ist
     */
    public function isActiveManager(): bool
    {
        return $this->model_status === ModelStatus::ACTIVE && $this->hasManagerRole();
    }

    /**
     * Scope für Manager-Query
     */
    public function scopeManagers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('is_manager', true);
        });
    }

    /**
     * Scope für aktive Manager
     */
    public function scopeActiveManagers($query)
    {
        return $query->managers()
            ->where('model_status', ModelStatus::ACTIVE);
    }

    /**
     * Holt alle Manager einer Company mit Cache
     * Cache-Dauer: 6 Stunden
     */
    public static function getCompanyManagers(int $companyId)
    {
        $cacheKey = "users:company:{$companyId}:managers";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($companyId) {
            return static::where('company_id', $companyId)
                ->whereHas('roles', fn($q) => $q->where('is_manager', true))
                ->where('model_status', ModelStatus::ACTIVE)
                ->select(['id', 'name', 'last_name', 'profile_photo_path', 'email'])
                ->orderBy('name')
                ->orderBy('last_name')
                ->get();
        });
    }

    /**
     * Holt Manager-IDs einer Company (für Performance)
     */
    public static function getCompanyManagerIds(int $companyId): array
    {
        $cacheKey = "users:company:{$companyId}:manager_ids";

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($companyId) {
            return static::where('company_id', $companyId)
                ->whereHas('roles', fn($q) => $q->where('is_manager', true))
                ->where('model_status', ModelStatus::ACTIVE)
                ->pluck('id')
                ->toArray();
        });
    }

    /**
     * Cache-Verwaltung: Löscht alle Manager-bezogenen Caches
     */
    public static function clearManagerCache(int $companyId): void
    {
        $cacheKeys = [
            "users:company:{$companyId}:managers",
            "users:company:{$companyId}:manager_ids"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        Log::info("Manager cache cleared for company", ['company_id' => $companyId]);
    }

    /**
     * Prüft ob eine Liste von Rollen-IDs Manager-Rollen enthält
     */
    public static function hasManagerRoleInList(array $roleIds): bool
    {
        return Role::whereIn('id', $roleIds)
            ->where('is_manager', true)
            ->exists();
    }

    /**
     * Override assignRole um Cache zu verwalten
     */
    public function assignRole(...$roles)
    {
        $wasManager = $this->hasManagerRole();

        // Führe parent assignRole aus
        $result = parent::assignRole(...$roles);

        // Prüfe ob sich Manager-Status geändert hat
        if (!$wasManager && $this->hasManagerRole() && $this->company_id) {
            static::clearManagerCache($this->company_id);
        }

        return $result;
    }

    /**
     * Override removeRole um Cache zu verwalten
     */
    public function removeRole($role)
    {
        $wasManager = $this->hasManagerRole();

        // Führe parent removeRole aus
        $result = parent::removeRole($role);

        // Prüfe ob Manager-Status verloren wurde
        if ($wasManager && !$this->hasManagerRole() && $this->company_id) {
            static::clearManagerCache($this->company_id);
        }

        return $result;
    }

    /**
     * Override syncRoles um Cache zu verwalten
     */
    public function syncRoles(...$roles)
    {
        $wasManager = $this->hasManagerRole();

        // Führe parent syncRoles aus
        $result = parent::syncRoles(...$roles);

        // Reload roles relationship
        $this->load('roles');
        $isManagerNow = $this->hasManagerRole();

        // Cache leeren wenn sich Manager-Status geändert hat
        if ($wasManager !== $isManagerNow && $this->company_id) {
            static::clearManagerCache($this->company_id);
        }

        return $result;
    }
}
