<?php

namespace App\Traits\Cache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait AdvancedCache
{
    /**
     * Request-Level Cache Array
     * @var array
     */
    private static array $requestCache = [];

    /**
     * Cache-Konfiguration
     */
    protected function getCacheConfig(): array
    {
        return [
            'enabled' => true,
            'duration' => $this->cacheDuration ?? 86400, // 24 Stunden default
            'prefix' => $this->cachePrefix ?? strtolower(class_basename(static::class)),
            'auto_flush' => true,
        ];
    }

    /**
     * Definiert welche Cache-Kontexte automatisch geflusht werden sollen
     */
    protected function getAutoFlushContexts(): array
    {
        return ['company', 'team', 'user']; // Default: Alle
    }

    /**
     * Definiert welche Kontexte für dieses Model relevant sind
     */
    protected function getCacheContexts(): array
    {
        return [
            'company' => 'company_id',
            'team' => 'team_id',
            'user' => 'created_by',
        ];
    }

    /**
     * Boot-Methode für automatische Cache-Invalidierung
     */
    public static function bootAdvancedCache(): void
    {
        $config = (new static)->getCacheConfig();

        if ($config['auto_flush']) {
            static::created(fn(Model $model) => $model->flushRelevantCaches());
            static::updated(fn(Model $model) => $model->flushRelevantCaches());
            static::deleted(fn(Model $model) => $model->flushRelevantCaches());
        }
    }

    /**
     * Zentrale Methode zum Flushen aller relevanten Caches
     */
    protected function flushRelevantCaches(): void
    {
        $autoFlushContexts = $this->getAutoFlushContexts();
        $contextMapping = $this->getCacheContexts();

        foreach ($autoFlushContexts as $context) {
            if (!isset($contextMapping[$context])) {
                continue;
            }

            $idField = $contextMapping[$context];

            if (!empty($this->$idField)) {
                $this->flushCacheContext($context, $this->$idField);
            }
        }

        static::flushRequestCache();
    }

    /**
     * Universelle Cache-Methode
     */
    public static function getCached(
        string   $context,
                 $contextId,
        callable $dataCallback,
        array    $options = []
    ): Collection
    {
        $instance = new static;
        $config = $instance->getCacheConfig();

        if (!$config['enabled']) {
            return $dataCallback();
        }

        // Keys generieren
        $keys = $instance->generateCacheKeys($context, $contextId, $options);

        // 1. Request-Cache prüfen
        if ($data = $instance->checkRequestCache($keys['request'])) {
            return $data;
        }

        // 2. Persistent Cache prüfen oder laden
        $data = $instance->loadFromPersistentCache(
            $keys['persistent'],
            $dataCallback,
            $options['duration'] ?? $config['duration']
        );

        // 3. In Request-Cache speichern
        self::$requestCache[$keys['request']] = $data;

        return $data;
    }

    /**
     * Hilfsmethode für Cache-Keys
     */
    private function generateCacheKeys(string $context, $contextId, array $options = []): array
    {
        $suffix = $options['suffix'] ?? '';
        $config = $this->getCacheConfig();

        return [
            'request' => "request_{$config['prefix']}_{$context}_{$contextId}" . ($suffix ? "_{$suffix}" : ''),
            'persistent' => "{$config['prefix']}:{$context}:{$contextId}" . ($suffix ? ":{$suffix}" : '')
        ];
    }

    /**
     * Request-Cache prüfen
     */
    private function checkRequestCache(string $key): ?Collection
    {
        return self::$requestCache[$key] ?? null;
    }

    /**
     * Aus persistentem Cache laden
     */
    private function loadFromPersistentCache(string $key, callable $callback, int $duration): Collection
    {
        if ($duration === -1) {
            return Cache::rememberForever($key, $callback);
        }

        return Cache::remember($key, $duration, $callback);
    }

    /**
     * Shortcut-Methoden
     */
    public static function getCachedByCompany(?int $companyId, callable $dataCallback, array $options = []): Collection
    {
        if (!$companyId) return collect();
        return static::getCached('company', $companyId, $dataCallback, $options);
    }

    public static function getCachedByTeam(?int $teamId, callable $dataCallback, array $options = []): Collection
    {
        if (!$teamId) return collect();
        return static::getCached('team', $teamId, $dataCallback, $options);
    }

    public static function getCachedByUser(?int $userId, callable $dataCallback, array $options = []): Collection
    {
        if (!$userId) return collect();
        return static::getCached('user', $userId, $dataCallback, $options);
    }

    public static function getCachedGlobal(callable $dataCallback, array $options = []): Collection
    {
        return static::getCached('global', 'all', $dataCallback, $options);
    }

    /**
     * Cache für spezifischen Kontext leeren
     */
    public function flushCacheContext(string $context, $contextId): void
    {
        $keys = $this->generateCacheKeys($context, $contextId);

        // Persistent Cache leeren
        Cache::forget($keys['persistent']);

        // Request Cache leeren
        unset(self::$requestCache[$keys['request']]);

        // Redis Wildcards wenn verfügbar
        $this->flushRedisPattern($keys['persistent'] . ':*');
    }

    /**
     * Redis Pattern flushen
     */
    private function flushRedisPattern(string $pattern): void
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            try {
                $keys = Cache::getStore()->connection()->keys($pattern);
                foreach ($keys as $key) {
                    // Redis gibt Keys mit Prefix zurück, entfernen für forget()
                    $key = str_replace(config('cache.prefix') . ':', '', $key);
                    Cache::forget($key);
                }
            } catch (\Exception $e) {
                // Fehler silent loggen für Production
                report($e);
            }
        }
    }

    /**
     * Alle Caches für dieses Model leeren
     */
    public static function flushAllCaches(): void
    {
        $instance = new static;
        $config = $instance->getCacheConfig();

        // Redis Pattern flushen
        $instance->flushRedisPattern("{$config['prefix']}:*");

        // Request Cache leeren
        self::$requestCache = [];
    }

    /**
     * Request-Cache leeren
     */
    public static function flushRequestCache(): void
    {
        self::$requestCache = [];
    }

    /**
     * Request-Cache für spezifischen Kontext leeren
     */
    public static function flushRequestCacheForContext(string $context, $contextId): void
    {
        $instance = new static;
        $keys = $instance->generateCacheKeys($context, $contextId);
        unset(self::$requestCache[$keys['request']]);
    }

    /**
     * Statische Helper-Methoden für spezifische Kontexte
     */

    /**
     * Leert den Company-Cache manuell
     */
    public static function flushCompanyCache(?int $companyId): void
    {
        if (!$companyId) return;

        $instance = new static;
        $instance->flushCacheContext('company', $companyId);
        static::flushRequestCache();
    }

    /**
     * Leert den Team-Cache manuell
     */
    public static function flushTeamCache(?int $teamId): void
    {
        if (!$teamId) return;

        $instance = new static;
        $instance->flushCacheContext('team', $teamId);
        static::flushRequestCache();
    }

    /**
     * Leert den User-Cache manuell
     */
    public static function flushUserCache(?int $userId): void
    {
        if (!$userId) return;

        $instance = new static;
        $instance->flushCacheContext('user', $userId);
        static::flushRequestCache();
    }
}
