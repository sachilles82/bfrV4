<?php

namespace App\Traits\Livewire;

use Illuminate\Support\Facades\Cache;

trait WithEmployeeCacheManagement
{
    /**
     * Generate standardized cache keys
     */
    protected function getEmployeeCacheKey(int $employeeId, string $type = 'full'): string
    {
        return "employee:{$employeeId}:{$type}";
    }

    /**
     * Clear all employee-related caches
     */
    protected function clearEmployeeCache(int $employeeId): void
    {
        $cacheTypes = ['base', 'full', 'employment-data', 'personal-data', 'details'];

        foreach ($cacheTypes as $type) {
            Cache::forget($this->getEmployeeCacheKey($employeeId, $type));
        }
    }

    /**
     * Clear specific cache type
     */
    protected function clearSpecificCache(int $employeeId, string $type): void
    {
        Cache::forget($this->getEmployeeCacheKey($employeeId, $type));
    }

    /**
     * Cache employee data with standardized TTL
     */
    protected function cacheEmployeeData(int $employeeId, string $type, $data, int $minutes = 10): void
    {
        Cache::put($this->getEmployeeCacheKey($employeeId, $type), $data, now()->addMinutes($minutes));
    }

    /**
     * Get cached data or default
     */
    protected function getCachedEmployeeData(int $employeeId, string $type, $default = null)
    {
        return Cache::get($this->getEmployeeCacheKey($employeeId, $type), $default);
    }
}
