<?php

namespace App\Traits\Cache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Advanced Generic Cache Trait für Laravel Eloquent Models
 *
 * Bietet flexible Caching-Strategien für verschiedene Kontexte:
 * - Company-basiert
 * - Team-basiert
 * - User-basiert
 * - Global
 * - Custom-Kontext
 *
 * Features:
 * - Dreistufiger Cache (Request, Persistent, Database)
 * - Automatische Cache-Invalidierung
 * - Konfigurierbare Cache-Dauer und Keys
 * - Support für verschiedene Cache-Strategien
 */
trait WithAdvancedCache
{
    /**
     * Request-Level Cache Array
     * @var array
     */
    private static array $requestCache = [];

    /**
     * Cache-Konfiguration
     * Kann in jedem Model überschrieben werden
     */
    protected function getCacheConfig(): array
    {
        return [
            'enabled' => true,
            'duration' => $this->cacheDuration ?? 86400, // 24 Stunden default
            'prefix' => $this->cachePrefix ?? strtolower(class_basename(static::class)),
            'auto_flush' => true, // Automatisches Cache-Leeren bei Model-Events
        ];
    }

    /**
     * Boot-Methode für automatische Cache-Invalidierung
     */
    public static function bootWithAdvancedCache(): void
    {
        $config = (new static)->getCacheConfig();

        if ($config['auto_flush']) {
            static::created(fn(Model $model) => $model->flushModelCaches());
            static::updated(fn(Model $model) => $model->flushModelCaches());
            static::deleted(fn(Model $model) => $model->flushModelCaches());
        }
    }

    /**
     * Universelle Cache-Methode mit flexiblen Kontext-Optionen
     *
     * @param string $context Der Cache-Kontext (company, team, user, global, custom)
     * @param int|string|null $contextId Die ID des Kontexts
     * @param callable $dataCallback Callback für Datenbankabfrage
     * @param array $options Zusätzliche Optionen
     * @return Collection
     */
    public static function getCached(
        string $context,
               $contextId,
        callable $dataCallback,
        array $options = []
    ): Collection {
        $instance = new static;
        $config = $instance->getCacheConfig();

        if (!$config['enabled']) {
            return $dataCallback();
        }

        // Generate cache keys
        $requestKey = $instance->generateRequestCacheKey($context, $contextId, $options);
        $persistentKey = $instance->generatePersistentCacheKey($context, $contextId, $options);

        // 1. Check Request-Level Cache
        if (isset(self::$requestCache[$requestKey])) {
            return self::$requestCache[$requestKey];
        }

        // 2. Check Persistent Cache
        $duration = $options['duration'] ?? $config['duration'];

        $data = $duration === -1
            ? Cache::rememberForever($persistentKey, $dataCallback)
            : Cache::remember($persistentKey, $duration, $dataCallback);

        // 3. Store in Request Cache
        self::$requestCache[$requestKey] = $data;

        return $data;
    }

    /**
     * Shortcut-Methoden für häufige Kontexte
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
     * Cache-Key Generierung
     */
    protected function generateRequestCacheKey(string $context, $contextId, array $options = []): string
    {
        $config = $this->getCacheConfig();
        $suffix = $options['suffix'] ?? '';
        return "request_{$config['prefix']}_{$context}_{$contextId}" . ($suffix ? "_{$suffix}" : '');
    }

    protected function generatePersistentCacheKey(string $context, $contextId, array $options = []): string
    {
        $config = $this->getCacheConfig();
        $suffix = $options['suffix'] ?? '';
        return "{$config['prefix']}:{$context}:{$contextId}" . ($suffix ? ":{$suffix}" : '');
    }

    /**
     * Cache-Flush Methoden
     */
    public function flushModelCaches(): void
    {
        $contexts = $this->getCacheContexts();

        foreach ($contexts as $context => $idField) {
            if (property_exists($this, $idField) && $this->$idField) {
                $this->flushCacheContext($context, $this->$idField);
            }
        }
    }

    /**
     * Definiert welche Kontexte für dieses Model relevant sind
     * Kann in jedem Model überschrieben werden
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
     * Leert Cache für einen spezifischen Kontext
     */
    public function flushCacheContext(string $context, $contextId): void
    {
        $persistentKey = $this->generatePersistentCacheKey($context, $contextId);
        $requestKey = $this->generateRequestCacheKey($context, $contextId);

        // Clear persistent cache
        Cache::forget($persistentKey);

        // Clear request cache
        if (isset(self::$requestCache[$requestKey])) {
            unset(self::$requestCache[$requestKey]);
        }

        // Clear with wildcards if Redis is available
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $pattern = $this->generatePersistentCacheKey($context, $contextId, ['suffix' => '*']);
            $keys = Cache::getStore()->connection()->keys($pattern);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Statische Helper-Methoden
     */
    public static function flushAllCaches(): void
    {
        $instance = new static;
        $config = $instance->getCacheConfig();

        // Clear all persistent caches for this model
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $pattern = "{$config['prefix']}:*";
            $keys = Cache::getStore()->connection()->keys($pattern);
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }

        // Clear request cache
        self::$requestCache = [];
    }

    /**
     * Cache-Tags Support (wenn verfügbar)
     */
    protected function getCacheTags(string $context, $contextId): array
    {
        $config = $this->getCacheConfig();
        return [
            $config['prefix'],
            "{$config['prefix']}:{$context}",
            "{$config['prefix']}:{$context}:{$contextId}"
        ];
    }
}
