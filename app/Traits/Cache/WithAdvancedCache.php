<?php

namespace App\Traits\Cache;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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
    // app/Traits/Cache/WithAdvancedCache.php

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

        $requestKey = $instance->generateRequestCacheKey($context, $contextId, $options);
        $persistentKey = $instance->generatePersistentCacheKey($context, $contextId, $options);

        // DEBUG: Zeige welche Keys verwendet werden
        \Log::info('🔑 Cache-Keys', [
            'model' => static::class,
            'request_key' => $requestKey,
            'persistent_key' => $persistentKey,
            'context' => $context,
            'context_id' => $contextId
        ]);

        // 1. Check Request-Level Cache
        if (isset(self::$requestCache[$requestKey])) {
            \Log::info('Aus Request-Cache geladen', [
                'key' => $requestKey
            ]);
            return self::$requestCache[$requestKey];
        }

        // 2. Check Persistent Cache (Redis)
        $duration = $options['duration'] ?? $config['duration'];

        // DEBUG: Log vor Cache-Abfrage
        \Log::info('📊 Prüfe Redis-Cache', [
            'key' => $persistentKey,
            'exists' => Cache::has($persistentKey)
        ]);

        $loadedFromDb = false;

        $data = $duration === -1
            ? Cache::rememberForever($persistentKey, function() use ($dataCallback, &$loadedFromDb, $persistentKey) {
                \Log::info('🔴 Cache::rememberForever Callback ausgeführt', ['key' => $persistentKey]);
                $loadedFromDb = true;
                return $dataCallback();
            })
            : Cache::remember($persistentKey, $duration, function() use ($dataCallback, &$loadedFromDb, $persistentKey) {
                \Log::info('🔴 Cache::remember Callback ausgeführt', ['key' => $persistentKey]);
                $loadedFromDb = true;
                return $dataCallback();
            });

        // In WithAdvancedCache trait
        if ($loadedFromDb) {
            \Log::info("Aus Datenbank geladen: {$config['prefix']} (Company: {$contextId})");
        } else {
            \Log::info("Aus Redis-Cache geladen: {$config['prefix']} (Company: {$contextId})");
        }

        // 3. Store in Request Cache
        self::$requestCache[$requestKey] = $data;

        return $data;
    }

    /**
     * Shortcut-Methoden für häufige Kontexte
     */
    // Alternative Implementation in WithAdvancedCache

    public static function getCachedByCompany(?int $companyId, callable $dataCallback, array $options = []): Collection
    {
        if (!$companyId) return collect();

        // Einmal loggen beim Eintritt
        \Log::info('🎯 getCachedByCompany aufgerufen', [
            'model' => static::class,
            'company_id' => $companyId,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'Unknown'
        ]);

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

    /**
     * Leert den Request-Level Cache komplett
     * Wichtig für Livewire-Components die mehrere Requests in einer Session haben
     */
    public static function flushRequestCache(): void
    {
        self::$requestCache = [];
    }

    /**
     * Leert Request-Cache für einen spezifischen Kontext
     */
    public static function flushRequestCacheForContext(string $context, $contextId): void
    {
        $instance = new static;
        $requestKey = $instance->generateRequestCacheKey($context, $contextId);

        if (isset(self::$requestCache[$requestKey])) {
            unset(self::$requestCache[$requestKey]);
        }
    }
}
