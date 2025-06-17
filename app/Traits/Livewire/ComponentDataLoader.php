<?php

namespace App\Traits\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait ComponentDataLoader
{
    /**
     * Lädt Model-Daten und nutzt AdvancedCache wenn verfügbar
     */
    protected function loadComponentData(
        string $modelClass,
        int $modelId,
        array $relations = [],
        array $select = []
    ): ?Model {
        // Prüfe ob Model den AdvancedCache Trait nutzt
        $usesAdvancedCache = in_array(
            'App\Traits\Cache\AdvancedCache',
            class_uses_recursive($modelClass)
        );

        if ($usesAdvancedCache) {
            // Nutze AdvancedCache's getCached Methode
            $collection = $modelClass::getCached(
                context: 'component',
                contextId: $modelId,
                dataCallback: function () use ($modelClass, $modelId, $relations, $select) {
                    return $this->loadFromDatabase($modelClass, $modelId, $relations, $select);
                },
                options: [
                    'suffix' => $this->getComponentCacheSuffix($relations),
                    'duration' => 600 // 10 Minuten
                ]
            );

            return $collection->first();
        }

        // Fallback: Standard Cache wenn Model kein AdvancedCache hat
        return $this->loadWithStandardCache($modelClass, $modelId, $relations, $select);
    }

    /**
     * Lädt Daten aus der Datenbank
     */
    private function loadFromDatabase(
        string $modelClass,
        int $modelId,
        array $relations,
        array $select
    ): \Illuminate\Support\Collection {
        $query = $modelClass::query();

        if (!empty($select)) {
            $query->select($select);
        }

        if (!empty($relations)) {
            $query->with($relations);
        }

        $model = $query->find($modelId);

        return collect($model ? [$model] : []);
    }

    /**
     * Standard Cache-Implementierung als Fallback
     */
    private function loadWithStandardCache(
        string $modelClass,
        int $modelId,
        array $relations,
        array $select
    ): ?Model {
        $componentName = class_basename(static::class);
        $cacheKey = $this->getComponentCacheKey($modelClass, $modelId, $componentName);

        return Cache::remember($cacheKey, 600, function () use ($modelClass, $modelId, $relations, $select) {
            return $this->loadFromDatabase($modelClass, $modelId, $relations, $select)->first();
        });
    }

    /**
     * Generiert Cache-Suffix basierend auf Relations
     */
    private function getComponentCacheSuffix(array $relations): string
    {
        if (empty($relations)) {
            return 'base';
        }

        // Sortiere Relations für konsistente Keys
        sort($relations);
        return md5(implode('|', $relations));
    }

    /**
     * Invalidiert Component Cache
     */
    protected function invalidateComponentCache(
        string $modelClass,
        int $modelId,
        ?string $componentName = null
    ): void {
        $usesAdvancedCache = in_array(
            'App\Traits\Cache\AdvancedCache',
            class_uses_recursive($modelClass)
        );

        if ($usesAdvancedCache) {
            // Nutze AdvancedCache's flush Methode
            $instance = new $modelClass;
            $instance->flushCacheContext('component', $modelId);
        } else {
            // Standard Cache forget
            $componentName = $componentName ?? class_basename(static::class);
            $cacheKey = $this->getComponentCacheKey($modelClass, $modelId, $componentName);
            Cache::forget($cacheKey);
        }
    }

    /**
     * Standard Cache-Key Generator
     */
    protected function getComponentCacheKey(
        string $modelClass,
        int $modelId,
        string $componentName
    ): string {
        $modelName = strtolower(class_basename($modelClass));
        return "{$modelName}:{$modelId}:{$componentName}-data";
    }
}
