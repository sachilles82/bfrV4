<?php

namespace App\Traits\Cache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

/**
 * Enhanced Redis Cache Trait mit generischen Methoden für alle Models
 *
 * Bietet eine dreistufige Cache-Strategie:
 * 1. Request-Level Cache (schnellstes, pro Request)
 * 2. Persistenter Cache (z.B. Redis)
 * 3. Datenbankabfrage (Fallback)
 */
trait WithRedisCache
{
    /**
     * Request-Level Cache Array für alle Models
     * Wird pro Request neu initialisiert
     */
    private static array $requestCache = [];

    /**
     * Boot-Methode für automatische Cache-Invalidierung
     */
    public static function bootWithRedisCache(): void
    {
        static::created(fn(Model $model) => $model->clearRelatedCaches());
        static::updated(fn(Model $model) => $model->clearRelatedCaches());
        static::deleted(fn(Model $model) => $model->clearRelatedCaches());
    }

    /**
     * Generische Methode um Company-basierte Daten mit dreistufigem Cache zu holen
     *
     * @param int|null $companyId Company ID
     * @param string $method Name der statischen Methode die die Query durchführt
     * @param array $params Zusätzliche Parameter für die Query-Methode
     * @return Collection
     */
    public static function getCachedCompanyData(?int $companyId, string $method = 'getQueryForCompany', array $params = []): Collection
    {
        if (!$companyId) {
            return collect();
        }

        $modelName = class_basename(static::class);
        $requestCacheKey = self::getRequestCacheKey($companyId, $modelName);

        /** 1. Prüfe Request-Level Cache */
        if (isset(self::$requestCache[$requestCacheKey])) {
            return self::$requestCache[$requestCacheKey];
        }

        /** 2. Prüfe persistenten Cache (Redis) */
        $persistentCacheKey = self::getPersistentCacheKey($companyId, $modelName);

        $data = Cache::rememberForever($persistentCacheKey, function () use ($companyId, $method, $params) {
            /** 3. Fallback: Datenbankabfrage */
            if (method_exists(static::class, $method)) {
                return static::$method($companyId, ...$params);
            }

            /** Standard Query falls keine spezielle Methode existiert */
            return static::query()
                ->where('company_id', $companyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });

        /** 3. Speichere im Request-Level Cache */
        self::$requestCache[$requestCacheKey] = $data;

        return $data;
    }

    /**
     * Generische Methode um Team-basierte Daten mit dreistufigem Cache zu holen
     *
     * @param int|null $teamId Team ID
     * @param string $method Name der statischen Methode die die Query durchführt
     * @param array $params Zusätzliche Parameter für die Query-Methode
     * @return Collection
     */
    public static function getCachedTeamData(?int $teamId, string $method = 'getQueryForTeam', array $params = []): Collection
    {
        if (!$teamId) {
            return collect();
        }

        $modelName = class_basename(static::class);
        $requestCacheKey = self::getRequestCacheKey($teamId, $modelName, 'team');

        /** 1. Prüfe Request-Level Cache */
        if (isset(self::$requestCache[$requestCacheKey])) {
            return self::$requestCache[$requestCacheKey];
        }

        /** 2. Prüfe persistenten Cache (Redis) */
        $persistentCacheKey = self::getPersistentCacheKey($teamId, $modelName, 'team');

        $data = Cache::rememberForever($persistentCacheKey, function () use ($teamId, $method, $params) {
            /** 3. Fallback: Datenbankabfrage */
            if (method_exists(static::class, $method)) {
                return static::$method($teamId, ...$params);
            }

            /** Standard Query falls keine spezielle Methode existiert */
            return static::query()
                ->where('team_id', $teamId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();
        });

        /** 3. Speichere im Request-Level Cache */
        self::$requestCache[$requestCacheKey] = $data;

        return $data;
    }

    /**
     * Generiert Request-Cache-Schlüssel
     */
    private static function getRequestCacheKey($id, string $modelName, string $type = 'company'): string
    {
        return "request_{$type}_{$modelName}_{$id}";
    }

    /**
     * Generiert persistenten Cache-Schlüssel
     */
    private static function getPersistentCacheKey($id, string $modelName, string $type = 'company'): string
    {
        return "{$type}_{$id}_{$modelName}_cache";
    }

    /**
     * Leert alle Cache-Ebenen für das aktuelle Model
     */
    public function clearRelatedCaches(): void
    {
        try {
            $modelName = class_basename(static::class);

            /** Company-basierte Caches leeren */
            if (property_exists($this, 'company_id') && !empty($this->company_id)) {
                $this->flushCacheForId($this->company_id, $modelName, 'company');
            }

            /** Team-basierte Caches leeren */
            if (property_exists($this, 'team_id') && !empty($this->team_id)) {
                $this->flushCacheForId($this->team_id, $modelName, 'team');
            }

        } catch (\Throwable $e) {
            Log::error("Error clearing caches for " . get_class($this) . " ID {$this->getKey()}: " . $e->getMessage());
        }
    }

    /**
     * Leert Cache für eine spezifische ID und Typ
     */
    private function flushCacheForId($id, string $modelName, string $type): void
    {
        /** 1. Persistenten Cache leeren */
        $persistentCacheKey = self::getPersistentCacheKey($id, $modelName, $type);
        Cache::forget($persistentCacheKey);

        /** 2. Request-Level Cache leeren */
        $requestCacheKey = self::getRequestCacheKey($id, $modelName, $type);
        if (isset(self::$requestCache[$requestCacheKey])) {
            unset(self::$requestCache[$requestCacheKey]);
        }
    }

    /**
     * Statische Methode um Company-Cache manuell zu leeren
     */
    public static function flushCompanyCache(?int $companyId): void
    {
        if (!$companyId) return;

        $modelName = class_basename(static::class);
        $instance = new static;
        $instance->flushCacheForId($companyId, $modelName, 'company');
    }

    /**
     * Statische Methode um Team-Cache manuell zu leeren
     */
    public static function flushTeamCache(?int $teamId): void
    {
        if (!$teamId) return;

        $modelName = class_basename(static::class);
        $instance = new static;
        $instance->flushCacheForId($teamId, $modelName, 'team');
    }

    /**
     * Leert den gesamten Request-Cache (nützlich für Tests)
     */
    public static function flushEntireRequestCache(): void
    {
        self::$requestCache = [];
    }

    // ===========================================
    // Legacy Methoden für Rückwärtskompatibilität
    // ===========================================

    public function getModelCacheKey(): string
    {
        if (property_exists($this, 'cacheKey')) {
            return $this->cacheKey;
        }
        $modelClass = get_class($this);
        $modelName = class_basename($modelClass);
        return strtolower($modelName) . '_general_cache';
    }

    public function getModelCacheDuration(): int
    {
        if (property_exists($this, 'cacheDuration')) {
            return $this->cacheDuration;
        }
        return 86400; // Default 24 hours
    }

    protected function generateCompanyCacheKey(int $companyId): string
    {
        $modelName = class_basename(static::class);
        if ($companyId <= 0) {
            return 'invalid_company_' . strtolower($modelName) . '_cache';
        }
        return "company_{$companyId}_" . strtolower($modelName) . '_cache';
    }

    protected function generateTeamCacheKey(int $teamId): string
    {
        $modelName = class_basename(static::class);
        if ($teamId <= 0) {
            return 'invalid_team_' . strtolower($modelName) . '_cache';
        }
        return "team_{$teamId}_" . strtolower($modelName) . '_cache';
    }

    protected function getGlobalEmployeePanelRoleCacheKey(): string
    {
        return "global_roles_employee_panel_cache";
    }

    protected function getManagerUserCacheKey(int $companyId): string
    {
        if ($companyId <= 0) {
            return 'invalid_company_manager_users_cache';
        }
        return "company_{$companyId}_manager_users_cache";
    }

    public static function cacheCompanyResult(int $companyId, callable $callback, ?string $specificModelType = null)
    {
        $instance = new static;
        $cacheKey = null;

        try {
            if ($specificModelType === 'user') {
                $cacheKey = $instance->getManagerUserCacheKey($companyId);
            } elseif ($specificModelType === 'role') {
                if ($companyId > 0) {
                    $cacheKey = $instance->generateCompanyCacheKey($companyId);
                } else {
                    $cacheKey = $instance->getGlobalEmployeePanelRoleCacheKey();
                }
            } else {
                $cacheKey = $instance->generateCompanyCacheKey($companyId);
            }

            if (!$cacheKey || str_contains($cacheKey, 'invalid_')) {
                Log::error("Failed to generate a valid cache key for company caching.", ['companyId' => $companyId, 'model' => static::class, 'generatedKey' => $cacheKey]);
                return $callback();
            }

            $duration = $instance->getModelCacheDuration();
            return Cache::remember($cacheKey, $duration > 0 ? $duration : null, $callback);

        } catch (\Throwable $e) {
            $logKey = $cacheKey ?? 'unknown';
            Log::error("Cache error in " . static::class . "::cacheCompanyResult for key '{$logKey}' - " . $e->getMessage());
            return $callback();
        }
    }

    public static function cacheTeamResult(int $teamId, callable $callback)
    {
        $instance = new static;
        $cacheKey = null;

        try {
            $cacheKey = $instance->generateTeamCacheKey($teamId);

            if (!$cacheKey || str_contains($cacheKey, 'invalid_')) {
                Log::error("Failed to generate a valid cache key for team caching.", ['teamId' => $teamId, 'model' => static::class, 'generatedKey' => $cacheKey]);
                return $callback();
            }

            $duration = $instance->getModelCacheDuration();
            return Cache::remember($cacheKey, $duration > 0 ? $duration : null, $callback);

        } catch (\Throwable $e) {
            $logKey = $cacheKey ?? 'unknown';
            Log::error("Cache error in " . static::class . "::cacheTeamResult for key '{$logKey}' - " . $e->getMessage());
            return $callback();
        }
    }

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
