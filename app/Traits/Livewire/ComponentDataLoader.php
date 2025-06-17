<?php

namespace App\Traits\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait ComponentDataLoader
{
    /**
     * Lädt Model-Daten mit Component-spezifischen Relations
     *
     * @param string $modelClass Die Model-Klasse (z.B. User::class)
     * @param int $modelId Die ID des Models
     * @param array $relations Die zu ladenden Relations
     * @param array $select Optionale select Felder
     * @return Model|null
     */
    protected function loadComponentData(string $modelClass, int $modelId, array $relations = [], array $select = []): ?Model {
        $componentName = class_basename(static::class);
        $cacheKey = $this->getComponentCacheKey($modelClass, $modelId, $componentName);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($modelClass, $modelId, $relations, $select) {
            $query = $modelClass::query();

            if (!empty($select)) {
                $query->select($select);
            }

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->find($modelId);
        });
    }

    /**
     * Invalidiert den Component-Cache
     *
     * @param string $modelClass
     * @param int $modelId
     * @param string|null $componentName
     */
    protected function invalidateComponentCache(
        string $modelClass,
        int $modelId,
        ?string $componentName = null
    ): void {
        $componentName = $componentName ?? class_basename(static::class);
        $cacheKey = $this->getComponentCacheKey($modelClass, $modelId, $componentName);

        Cache::forget($cacheKey);
    }

    /**
     * Generiert einen Component-spezifischen Cache-Key
     *
     * @param string $modelClass
     * @param int $modelId
     * @param string $componentName
     * @return string
     */
    protected function getComponentCacheKey(
        string $modelClass,
        int $modelId,
        string $componentName
    ): string {
        $modelName = class_basename($modelClass);
        return strtolower("{$modelName}:{$modelId}:{$componentName}-data");
    }

    /**
     * Lädt mehrere Models mit Component-spezifischen Relations
     *
     * @param string $modelClass
     * @param array $modelIds
     * @param array $relations
     * @return \Illuminate\Support\Collection
     */
    protected function loadMultipleComponentData(
        string $modelClass,
        array $modelIds,
        array $relations = []
    ): \Illuminate\Support\Collection {
        $componentName = class_basename(static::class);
        $cacheKey = $this->getComponentCacheKey($modelClass, md5(implode(',', $modelIds)), $componentName);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($modelClass, $modelIds, $relations) {
            $query = $modelClass::whereIn('id', $modelIds);

            if (!empty($relations)) {
                $query->with($relations);
            }

            return $query->get();
        });
    }
}
