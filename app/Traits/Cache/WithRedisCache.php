<?php

namespace App\Traits\Cache;

use App\Models\User;
use App\Models\Spatie\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Trait WithRedisCache
 *
 * Provides Redis caching functionality for models, supporting both
 * company-specific and team-specific caching strategies.
 */
trait WithRedisCache
{

    public static function bootWithRedisCache()
    {
        static::created(fn(Model $model) => $model->clearRelatedCaches());
        static::updated(fn(Model $model) => $model->clearRelatedCaches());
        static::deleted(fn(Model $model) => $model->clearRelatedCaches());
    }

    public function getModelCacheKey(): string
    {
        if (property_exists($this, 'cacheKey')) {
            return $this->cacheKey;
        }
        $modelClass = get_class($this);
        $modelName = class_basename($modelClass);
        return strtolower($modelName) . '_general_cache'; // Klarer benennen
    }

    public function getModelCacheDuration(): int
    {
        if (property_exists($this, 'cacheDuration')) {
            return $this->cacheDuration;
        }
        return 86400; // Default 24 hours
    }

    // --- Methoden zur Schlüsselgenerierung ---

    /**
     * Generiert einen firmenspezifischen Cache-Schlüssel.
     *
     * @param int $companyId
     * @return string
     */
    protected function generateCompanyCacheKey(int $companyId): string
    {
        $modelName = class_basename(static::class);
        // Stellt sicher, dass companyId > 0 ist, sonst Fehler oder Fallback
        if ($companyId <= 0) {
            // Log::warning oder Exception hier, falls benötigt
            return 'invalid_company_' . strtolower($modelName) . '_cache'; // Fallback-Key
        }
        return "company_{$companyId}_" . strtolower($modelName) . '_cache';
    }

    /**
     * Generiert einen team-spezifischen Cache-Schlüssel.
     *
     * @param int $teamId
     * @return string
     */
    protected function generateTeamCacheKey(int $teamId): string
    {
        $modelName = class_basename(static::class);
        if ($teamId <= 0) {
            // Log::warning oder Exception hier, falls benötigt
            return 'invalid_team_' . strtolower($modelName) . '_cache'; // Fallback-Key
        }
        return "team_{$teamId}_" . strtolower($modelName) . '_cache';
    }

    /**
     * Generiert einen spezifischen Cache-Key für globale EmployeePanel-Rollen.
     *
     * @return string
     */
    protected function getGlobalEmployeePanelRoleCacheKey(): string
    {
        return "global_roles_employee_panel_cache";
    }

    /**
     * Generiert einen spezifischen Cache-Key für Manager-Benutzer einer Firma.
     *
     * @param int $companyId Die ID der Firma.
     * @return string
     */
    protected function getManagerUserCacheKey(int $companyId): string
    {
        if ($companyId <= 0) {
            // Log::warning("Invalid companyId ({$companyId}) provided for getManagerUserCacheKey."); // Entfernt
            return 'invalid_company_manager_users_cache';
        }
        return "company_{$companyId}_manager_users_cache";
    }

    // --- Caching Methoden ---

    /**
     * Cache model data using a company-specific key.
     * Applies special logic for User (Manager) and Role (Global/Company).
     *
     * @param int $companyId
     * @param callable $callback Function that returns data to cache
     * @param string|null $specificModelType Optional: 'user' or 'role' to trigger specific logic
     * @return mixed Cached data or fresh data from callback
     */
    public static function cacheCompanyResult(int $companyId, callable $callback, ?string $specificModelType = null)
    {
        $instance = new static;
        $cacheKey = null;

        try {
            // Bestimme den korrekten Schlüssel basierend auf dem Typ
            if ($specificModelType === 'user') {
                $cacheKey = $instance->getManagerUserCacheKey($companyId);
            } elseif ($specificModelType === 'role') {
                if ($companyId > 0) {
                    $cacheKey = $instance->generateCompanyCacheKey($companyId);
                    // Log::debug("Using company role cache key: {$cacheKey}"); // Entfernt
                } else {
                    $cacheKey = $instance->getGlobalEmployeePanelRoleCacheKey();
                    // Log::debug("Using global role cache key: {$cacheKey}"); // Entfernt
                }
            } else {
                $cacheKey = $instance->generateCompanyCacheKey($companyId);
            }

            if (!$cacheKey || $cacheKey === 'invalid_company_manager_users_cache' || $cacheKey === 'invalid_company_' . strtolower(class_basename(static::class)) . '_cache') {

                Log::error("Failed to generate a valid cache key for company caching.", ['companyId' => $companyId, 'model' => static::class, 'generatedKey' => $cacheKey]);
                return $callback();
            }

            // --- Cache Hit/Miss Logik ---
            $duration = $instance->getModelCacheDuration();

            // Keine Info/Debug Logs mehr hier
            return Cache::remember($cacheKey, $duration > 0 ? $duration : null, $callback); // Vereinfacht für remember/rememberForever

        } catch (\Throwable $e) {
            $logKey = $cacheKey ?? 'unknown';

            Log::error("Cache error in " . static::class . "::cacheCompanyResult for key '{$logKey}' - " . $e->getMessage());
            return $callback(); // Fallback
        }
    }

    /**
     * Cache model data using a team-specific key.
     *
     * @param int $teamId
     * @param callable $callback Function that returns data to cache
     * @return mixed Cached data or fresh data from callback
     */
    public static function cacheTeamResult(int $teamId, callable $callback)
    {
        $instance = new static;
        $cacheKey = null;

        try {
            $cacheKey = $instance->generateTeamCacheKey($teamId);

            if (!$cacheKey || $cacheKey === 'invalid_team_' . strtolower(class_basename(static::class)) . '_cache') {

                Log::error("Failed to generate a valid cache key for team caching.", ['teamId' => $teamId, 'model' => static::class, 'generatedKey' => $cacheKey]);
                return $callback();
            }

            // --- Cache Hit/Miss Logik ---
            $duration = $instance->getModelCacheDuration();

            // Keine Info/Debug Logs mehr hier
            return Cache::remember($cacheKey, $duration > 0 ? $duration : null, $callback); // Vereinfacht

        } catch (\Throwable $e) {
            $logKey = $cacheKey ?? 'unknown';

            Log::error("Cache error in " . static::class . "::cacheTeamResult for key '{$logKey}' - " . $e->getMessage());
            return $callback(); // Fallback
        }
    }


    // --- Cache Clearing Methoden ---

    /**
     * Clears all potentially related caches for this model instance.
     * Called automatically on created, updated, deleted events.
     *
     * @return void
     */
    public function clearRelatedCaches(): void
    {
        try {
            // 1. General Cache
            $generalCacheKey = $this->getModelCacheKey();
            Cache::forget($generalCacheKey);

            // 2. Company Cache (if company_id exists)
            if (property_exists($this, 'company_id') && !empty($this->company_id) && $this->company_id > 0) {
                $companyKey = $this->generateCompanyCacheKey($this->company_id);
                Cache::forget($companyKey);

                // Special: Clear Manager User cache if this is a User
                if ($this instanceof User) {
                    $managerKey = $this->getManagerUserCacheKey($this->company_id);
                    Cache::forget($managerKey);
                }
            }

            // 3. Team Cache (if team_id exists)
            if (property_exists($this, 'team_id') && !empty($this->team_id) && $this->team_id > 0) {
                $teamKey = $this->generateTeamCacheKey($this->team_id);
                Cache::forget($teamKey);
            }

            // 4. Special: Clear Global Role Cache if this is a Role
            if ($this instanceof Role) {
                $globalRoleKey = $this->getGlobalEmployeePanelRoleCacheKey();
                Cache::forget($globalRoleKey);
            }

        } catch (\Throwable $e) {

            Log::error("Error clearing related caches for " . get_class($this) . " ID {$this->getKey()}: " . $e->getMessage());
        }
    }

    /**
     * Manually flush the general cache for this model type.
     *
     * @return void
     */
    public static function flushGeneralCache(): void
    {
        try {
            $instance = new static;
            $cacheKey = $instance->getModelCacheKey();
            Cache::forget($cacheKey);

        } catch (\Throwable $e) {

            Log::error("Error in " . static::class . "::flushGeneralCache - " . $e->getMessage());
        }
    }

    /**
     * Manually flush company-specific cache(s) for this model type.
     *
     * @param int $companyId
     * @return void
     */
    public static function flushCompanyCache(int $companyId): void
    {
        if ($companyId <= 0) return;
        $instance = new static;

        try {
            // Standard Company Key
            $companyKey = $instance->generateCompanyCacheKey($companyId);
            Cache::forget($companyKey);


            // Spezifische Keys (falls zutreffend)
            if ($instance instanceof User) {
                $managerKey = $instance->getManagerUserCacheKey($companyId);
                Cache::forget($managerKey);

            }

        } catch (\Throwable $e) {

            Log::error("Error in " . static::class . "::flushCompanyCache for company {$companyId} - " . $e->getMessage());
        }
    }

    /**
     * Manually flush team-specific cache for this model type.
     *
     * @param int $teamId
     * @return void
     */
    public static function flushTeamCache(int $teamId): void
    {
        if ($teamId <= 0) return;
        $instance = new static;

        try {
            $teamKey = $instance->generateTeamCacheKey($teamId);
            Cache::forget($teamKey);
        } catch (\Throwable $e) {
            Log::error("Error in " . static::class . "::flushTeamCache for team {$teamId} - " . $e->getMessage());
        }
    }

    /**
     * Manually flush the global role cache.
     *
     * @return void
     */
    public static function flushGlobalRoleCache(): void
    {
        try {
            $instance = new static;
            if (method_exists($instance, 'getGlobalEmployeePanelRoleCacheKey')) {
                $globalRoleKey = $instance->getGlobalEmployeePanelRoleCacheKey();
                Cache::forget($globalRoleKey);
            }
        } catch (\Throwable $e) {
            Log::error("Error in " . static::class . "::flushGlobalRoleCache - " . $e->getMessage());
        }
    }
}
